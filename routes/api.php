<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FIleUploadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('/v1')->group(function () {

    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('signin', [AuthController::class, 'signin']);

    Route::get('/auth/verify-email/{id}/{hash}', function ($id, $hash, Request $request) {
        // Find user by id
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        // Verify if the hash is correct
        if (!hash_equals((string)$hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link'], 403);
        }
        // Mark email as verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        // Redirect to frontend
        return redirect('http://localhost:3000/products/?email_verified=true');
    })->middleware('signed')->name('verification.verify');


    Route::post('/auth/resend-verification', function (Request $request) {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }
        $user->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link resent!']);
    });


    // Protected routes using Laravel Passport
    Route::middleware('auth:api', 'verified')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        // ADMIN ONLY ROUTES
        Route::group(['middleware' => ['role:admin']], function () {
            Route::post('users/{user}/make-admin', [UserController::class, 'makeAdmin']);
            Route::apiResource('users', UserController::class);
        });

        // USER AND ADMIN ROUTES (both can access)
        Route::group(['middleware' => ['role:user|admin']], function () {
            Route::get('is-admin', [UserController::class, 'isAdmin']);
            Route::get('user-products', [ProductController::class, 'userProducts']);
            Route::post('file-upload', [FIleUploadController::class, 'uploadFile']);
            Route::get('get-user-cart', [CartItemController::class, 'getUserCart']);
            Route::get('get-cart-products', [ProductController::class, 'getProductsFromCart']);
            Route::post('remove-all-cart-items', [CartItemController::class, 'destroyAll']);
            Route::apiResource('orders', OrderController::class);
            Route::apiResource('order-items', OrderItemsController::class);
            Route::apiResource('products', ProductController::class);
            Route::apiResource('cart-items', CartItemController::class);
            Route::apiResource('categories', CategoryController::class);
        });
    });
});
