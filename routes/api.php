<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PrivacyTermController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\UserRequestsController;
use App\Http\Controllers\Api\DocumentTypeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

    Route::middleware('apilogs')->post('register', [AuthController::class,'register']);
    Route::middleware('apilogs')->post('otp/verify', [AuthController::class,'otp_verify']);
    Route::middleware('apilogs')->post('otp/send', [AuthController::class,'otp_send']);
    Route::middleware('apilogs')->get('call', [AuthController::class,'call']);
    Route::middleware('apilogs')->post('login', [AuthController::class,'login']);
    Route::middleware('apilogs')->post('update/password', [UserController::class,'forgot_password']);


    Route::group(['middleware' => ['auth:sanctum', 'verified', 'apilogs']], function () {
        Route::post('edit-profile',[UserController::class, 'edit_profile']);
        Route::post('change-location',[UserController::class, 'change_location']);
        Route::post('change-password',[UserController::class, 'change_password']);
        Route::post('change-token',[UserController::class, 'change_token']);
        Route::get('voice/accessToken', [UserController::class, 'voice_token']);
        
        Route::get('application/status',[UserController::class, 'application_status']);
        Route::post('change/status',[UserController::class, 'change_status']);

        Route::post('chat/image',[UserController::class, 'chat_image']);

        Route::get('provider/weekly/report',[OrderController::class, 'get_report']);
        Route::get('provider/orders',[OrderController::class, 'get_orders']);

        Route::get('nearby/providers', [UserController::class, 'nearby_provider']);
        Route::get('user/profile', [UserController::class, 'get_profile']);

        Route::get('get/provider',[UserController::class, 'get_providers']);
        Route::get('get/provider/{slug}', [UserController::class, 'providers']);
        Route::get('earning',[OrderController::class, 'get_earning']);

        Route::post('viewed/provider',[UserController::class, 'view_provider']);

        Route::get('check/banned',[UserController::class, 'check_banned']);

        Route::get('test-notification', [NotificationController::class,'test_notifications']);
        Route::get('notifications', [NotificationController::class,'notifications']);
        Route::get('read-notifications', [NotificationController::class,'read_notifications']);
        Route::get('logout', [AuthController::class,'logout']);

        Route::get('service',[ServiceController::class,'get_service']);
        Route::get('service/category',[ServiceController::class,'get_service_categories']);
        Route::post('service/provider/add',[ServiceController::class,'add_provider_services']);


        Route::get('get/order',[OrderController::class,'get_order_detail']);

        Route::post('fetch/provider/detail/customer',[ServiceController::class,'fetch_provider_detail_customer']);
        
        Route::post('add/customer/service',[UserController::class,'add_customer_service']);

        Route::get('get-user-requests',[UserRequestsController::class,'get_user_requests']);
        Route::post('customer/request/accept',[OrderController::class,'accept_decline_user_requests']);

        Route::post('add/order/rate',[OrderController::class,'add_user_order_rate']);

        Route::get('fetch/orders/customer',[OrderController::class,'fetch_orders_customer']);

        Route::post('order/work/complete',[OrderController::class,'order_work_complete']);

        Route::get('get-fetch-service-customer',[ServiceController::class,'get_fetch_service_customer']);
        
        Route::get('get-document-type',[DocumentTypeController::class,'get_document_type']);
        Route::post('add/user/document',[DocumentTypeController::class,'add_user_document']);


        Route::post('order/work/start',[OrderController::class,'order_work_start']);
        Route::post('confirm/Request/service/customer',[OrderController::class,'confirm_Request_service_customer']);
        Route::post('add/feedback',[FeedbackController::class,'add_feedback']);
        Route::post('cancel/order',[OrderController::class, 'cancel_order']);

        Route::post('add/user/address',[UserAddressController::class,'add_user_address']);
        Route::post('edit/user/address',[UserAddressController::class,'edit_user_address']);
        Route::post('delete/user/address',[UserAddressController::class,'delete_user_address']);
        Route::get('get/user/address',[UserAddressController::class,'get_user_address']);

        Route::post('accept/work',[OrderController::class,'accept_work']);
        Route::post('cancel-order',[OrderController::class,'cancel_order']);

        Route::post('order/start/provider',[OrderController::class,'order_start_time']);
        Route::post('add/payment ',[TransactionController::class,'add_payment']);

        Route::post('user/like',[UserController::class, 'user_like']);  
        
        Route::post('get/provider/services',[ServiceController::class,'get_provider_services']);

    });
   
    Route::get('send-mail', [NotificationController::class,'sendMail']);



   
