#!/bin/bash
set -e

# VM Deployment Configuration
PROJECT_NAME="DigiMarket"
DOCKER_IMAGE="samirdumre/digimarket-backend:latest"
COMPOSE_FILE="docker-compose.deploy.yml"  
ENV_FILE=".env.production"
NETWORK_NAME="digimarket-net"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root or with sudo
check_permissions() {
    if [[ $EUID -ne 0 ]]; then
        log_error "This script must be run as root or with sudo"
        exit 1
    fi
}

# Check prerequisites
check_prerequisites() {
    log_info "Checking prerequisites..."
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        log_error "Docker is not installed"
        exit 1
    fi
    
    # Check Docker Compose
    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose is not installed"
        exit 1
    fi
    
    # Check if environment file exists
    if [ ! -f "$ENV_FILE" ]; then
        log_error "Environment file $ENV_FILE not found"
        exit 1
    fi
    
    log_success "Prerequisites check passed"
}

# Load environment variables
load_env() {
    log_info "Loading environment variables..."
    
    # Check if .env.production exists
    if [ ! -f "$ENV_FILE" ]; then
        log_error "Environment file $ENV_FILE not found"
        exit 1
    fi
    
    # Load environment variables and export them
    set -o allexport
    source "$ENV_FILE"
    set +o allexport
    
    log_success "Environment variables loaded"
}

# Check if Docker network exists and create if needed
check_network() {
    log_info "Checking Docker network..."
    
    if docker network ls --format "table {{.Name}}" | grep -q "^${NETWORK_NAME}$"; then
        log_success "Docker network '$NETWORK_NAME' exists"
    else
        log_info "Creating Docker network '$NETWORK_NAME'..."
        docker network create $NETWORK_NAME
        log_success "Docker network '$NETWORK_NAME' created"
    fi
}

# Pull Docker image
pull_image() {
    log_info "Pulling Docker image..."
    
    # Pull the latest image
    docker pull $DOCKER_IMAGE
    
    log_success "Docker image pulled successfully"
}

# Deploy application
deploy_application() {
    log_info "Deploying application..."
    
    # Stop existing containers (but don't remove networks)
    docker-compose -f $COMPOSE_FILE down --remove-orphans || true
    
    # Clean up containers and images (but preserve networks)
    docker container prune -f
    docker image prune -f
    
    # Start services (without building)
    docker-compose -f $COMPOSE_FILE up -d --no-build
    
    log_success "Application deployed"
}

# Setup application
setup_application() {
    log_info "Setting up application..."
    
    # Wait for database to be ready
    log_info "Waiting for database to be ready..."
    sleep 30
    
    # Check if database is ready
    max_attempts=30
    attempt=1
    while [ $attempt -le $max_attempts ]; do
        if docker-compose -f $COMPOSE_FILE exec -T postgres pg_isready -U ${DB_USERNAME} -d ${DB_DATABASE} > /dev/null 2>&1; then
            log_success "Database is ready!"
            break
        else
            log_info "Database not ready yet, waiting... (attempt $attempt/$max_attempts)"
            sleep 5
            ((attempt++))
        fi
    done
    
    if [ $attempt -gt $max_attempts ]; then
        log_error "Database failed to become ready after $max_attempts attempts"
        exit 1
    fi
    
    # Create cache table (ignore if exists)
    docker-compose -f $COMPOSE_FILE exec -T app php artisan cache:table 2>/dev/null || log_info "Cache table migration already exists"
    
    # Create queue tables (ignore if exists)
    docker-compose -f $COMPOSE_FILE exec -T app php artisan queue:table 2>/dev/null || log_info "Queue table migration already exists"
    
    # Create sessions table (ignore if exists)
    docker-compose -f $COMPOSE_FILE exec -T app php artisan session:table 2>/dev/null || log_info "Session table migration already exists"
    
    # Run migrations with better error handling
    log_info "Running database migrations..."
    if docker-compose -f $COMPOSE_FILE exec -T app php artisan migrate --force; then
        log_success "Migrations completed successfully"
    else
        log_warning "Some migrations may have failed - checking if critical tables exist"
        
        # Check if critical tables exist
        if docker-compose -f $COMPOSE_FILE exec -T postgres psql -U ${DB_USERNAME} -d ${DB_DATABASE} -c "\dt" | grep -q "users\|categories\|products"; then
            log_success "Critical tables exist, continuing with setup"
        else
            log_error "Critical tables missing, deployment failed"
            exit 1
        fi
    fi
    
    # Install Passport
    log_info "Installing Laravel Passport..."
    docker-compose -f $COMPOSE_FILE exec -T app php artisan passport:install --force
    
    # Create storage symlink
    docker-compose -f $COMPOSE_FILE exec -T app php artisan storage:link
    
    # Seed database
    log_info "Seeding database..."
    if docker-compose -f $COMPOSE_FILE exec -T app php artisan db:seed --force; then
        log_success "Database seeded successfully"
    else
        log_warning "Database seeding failed - may already be seeded"
    fi
    
    # Cache configurations
    docker-compose -f $COMPOSE_FILE exec -T app php artisan config:cache
    docker-compose -f $COMPOSE_FILE exec -T app php artisan route:cache
    docker-compose -f $COMPOSE_FILE exec -T app php artisan view:cache
    
    # Set proper permissions
    docker-compose -f $COMPOSE_FILE exec -T app chown -R www-data:www-data /var/www/html/storage
    docker-compose -f $COMPOSE_FILE exec -T app chmod -R 775 /var/www/html/storage
    
    log_success "Application setup completed"
}

# Main deployment function
main() {
    log_info "Starting $PROJECT_NAME deployment to VM..."
    
    # Run deployment steps
    check_permissions
    check_prerequisites
    load_env  # Load environment variables first
    check_network
    pull_image
    deploy_application
    setup_application
    
    log_success "$PROJECT_NAME deployment completed successfully!"
    echo ""
    echo "🌐 Application URL: $APP_URL"
    echo "📊 Container Status: docker-compose -f $COMPOSE_FILE ps"
    echo "📋 Application Logs: docker-compose -f $COMPOSE_FILE logs -f app"
    echo ""
    echo "🔧 Useful Commands:"
    echo "  Restart services: docker-compose -f $COMPOSE_FILE restart"
    echo "  Update application: sudo ./deploy.sh"
    echo "  View logs: docker-compose -f $COMPOSE_FILE logs -f"
    echo "  Check file uploads: ls -la storage/app/public/"
    echo "  Test API: curl -I $APP_URL/api/health"
}

# Run main function
main "$@"