#!/bin/bash
set -e

# Configuration
PROJECT_NAME="DigiMarket"
DOCKER_IMAGE="samirdumre/digimarket-backend:latest"
COMPOSE_FILE="docker-compose.prod.yml"
ENV_FILE=".env.production"

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

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Improved Docker login check
check_docker_login() {
    log_info "Checking Docker Hub login..."
    
    # Method 1: Check if we can access Docker Hub by testing a simple API call
    if docker info 2>/dev/null | grep -q "Username:"; then
        log_success "Docker Hub login verified (method 1)"
        return 0
    fi
    
    # Method 2: Try to authenticate with Docker Hub registry
    if docker login --username dummy --password dummy 2>/dev/null | grep -q "Login Succeeded"; then
        log_success "Docker Hub login verified (method 2)"
        return 0
    fi
    
    # Method 3: Check Docker config file
    if [ -f "$HOME/.docker/config.json" ] && grep -q "index.docker.io" "$HOME/.docker/config.json"; then
        log_success "Docker Hub login verified (method 3)"
        return 0
    fi
    
    # Method 4: Try to pull a test image (this will fail if not logged in for private repos)
    if docker pull hello-world:latest >/dev/null 2>&1; then
        log_info "Docker daemon is accessible, but login status uncertain"
        log_info "Attempting to proceed with build and push..."
        return 0
    fi
    
    log_error "Please login to Docker Hub first: docker login"
    log_error "Then run this script again"
    exit 1
}

# Build using docker-compose
build_with_compose() {
    log_info "Building $PROJECT_NAME using docker-compose..."
    
    # Load environment variables
    export $(cat $ENV_FILE | grep -v '^#' | xargs) 2>/dev/null || {
        log_error "Failed to load environment variables from $ENV_FILE"
        exit 1
    }
    
    # Build the image using docker-compose
    docker-compose -f $COMPOSE_FILE build app
    
    log_success "Build completed using docker-compose"
}

# Push image to Docker Hub
push_image() {
    log_info "Pushing image to Docker Hub..."
    
    # Push the image
    if docker push $DOCKER_IMAGE; then
        log_success "Image pushed successfully"
    else
        log_error "Failed to push image. Please check your Docker Hub credentials:"
        log_error "1. Run: docker login"
        log_error "2. Verify your username and password"
        log_error "3. Make sure the repository exists on Docker Hub"
        exit 1
    fi
}

# Main function
main() {
    log_info "Starting $PROJECT_NAME build and push process..."
    
    # Check prerequisites
    if ! command -v docker &> /dev/null; then
        log_error "Docker is not installed"
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose is not installed"
        exit 1
    fi
    
    if [ ! -f "$ENV_FILE" ]; then
        log_error "Environment file $ENV_FILE not found"
        exit 1
    fi
    
    # Run build and push steps
    check_docker_login
    build_with_compose
    push_image
    
    log_success "$PROJECT_NAME build and push completed successfully!"
    echo ""
    echo "🐳 Image: $DOCKER_IMAGE"
    echo "📦 Size: $(docker image inspect $DOCKER_IMAGE --format='{{.Size}}' | numfmt --to=iec 2>/dev/null || echo 'Unknown')"
    echo ""
    echo "🚀 Next steps:"
    echo "1. Transfer deployment files to VM"
    echo "2. Run deployment on VM: sudo ./deploy.sh"
}

# Run main function
main "$@"