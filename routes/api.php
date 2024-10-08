<?php

use App\Http\Controllers\Api\V1\AbonnementController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\FeedbackController;
use App\Http\Controllers\Api\V1\MasterclassController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProgramController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Middleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth', 'controller' => AuthController::class], function () {
        Route::post('/login', 'login');
        Route::post('/refresh', 'refresh');

        Route::group(['middleware' => 'auth:api'], function () {
            Route::post('/logout', 'logout');
        });
    });

    Route::group(['prefix' => 'payment', 'controller' => PaymentController::class], function () {
        Route::post('/status', 'setPaymentStatus')
            ->name('payment.status')
            ->middleware(Middleware\RestrictAcquiringIPsMiddleware::class);
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['prefix' => 'profile', 'controller' => ProfileController::class], function () {
            Route::get('/', 'showProfile');
            Route::post('/', 'updateProfile');

            Route::get('/assets', 'assets');

            Route::get('/abonnements/current', 'abonnementsCurrent');
            Route::get('/abonnements/history', 'abonnementsHistory');

            Route::get('/programs/current', 'programsCurrent');
            Route::get('/programs/history', 'programsHistory');

            Route::get('/transactions', 'transactions');

            Route::delete('/photo', 'deletePhoto');
        });

        Route::group(['prefix' => 'payment', 'controller' => PaymentController::class], function () {
            Route::post('/create', 'createPayment');
        });

        Route::group(['prefix' => 'enrollments', 'controller' => EnrollmentController::class], function () {
            Route::get('/current', 'indexCurrent');
            Route::get('/history', 'indexHistory');
            Route::get('/{id}', 'show');
            Route::post('/{id}/reschedule', 'reschedule');
        });

        Route::group(['prefix' => 'categories', 'controller' => CategoryController::class], function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
        });

        Route::group(['prefix' => 'masterclasses', 'controller' => MasterclassController::class], function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/{masterclass_id}/enroll', 'enroll');
        });

        Route::group(['prefix' => 'abonnements', 'controller' => AbonnementController::class], function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/{abonnement_id}/buy', 'buy');
        });

        Route::group(['prefix' => 'feedbacks', 'controller' => FeedbackController::class], function () {
            Route::get('/', 'indexMyFeedbacks');
            Route::post('/', 'store');
        });

        Route::group(['prefix' => 'services', 'controller' => ServiceController::class], function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/{service_id}/enroll', 'enroll');
        });

        Route::group(['prefix' => 'programs', 'controller' => ProgramController::class], function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/{program_id}/buy', 'buy');
        });

        Route::group(['prefix' => 'pages', 'controller' => PageController::class], function () {
            Route::get('/schedules', 'schedules');
            Route::get('/header', 'mainPageHeader');
            Route::get('/about-us', 'aboutUs');
            Route::get('/search', 'search');
            Route::post('/proposal', 'proposal');
        });
    });
});
