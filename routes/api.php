<?php
use App\Notifications\GeneralNotification;
use App\User;
use App\GenerateQrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Notifications\ScanSuccessForSales;
use Illuminate\Support\Facades\Log;

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
Route::get('haha-notification', function () {
    DB::statement('UPDATE `users` SET gold_activation_date = email_verified_at AND  gold_expiring_date = DATE_ADD(DATE_FORMAT(email_verified_at, "%Y-%m-%d 23:59:59"), INTERVAL 1 YEAR)  WHERE id = 513 AND membership_type = 2 AND gold_activation_date IS NULL');
});

Route::get('haha-test-123', function () {
    // $user = User::where('email', 'faizankhan2595@gmail.com')->first();

    // $user->notify(new GeneralNotification([
    //     'title' => 'ew2rwer!',
    //     'message' => 'rwerwe'
    // ]));

    // Log::info('Notification sent successfully');

    $qrcode = GenerateQrCode::where("code", "CI13QFTJQY9FL9R")->first();
    $qrcode->generatedBy->notify(new ScanSuccessForSales($qrcode));

    return response()->json(['success' => true, 'message' => 'Notification sent']);
});


Route::post('register', 'Api\UserController@register');
Route::post('login', 'Api\UserController@authenticate');
Route::post('/password/email', 'Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('verify', 'Api\UserController@verify')->middleware('auth:sanctum');
Route::post('resend', 'Api\UserController@resend')->middleware('auth:sanctum');
Route::get('check', 'Api\UserController@check')->middleware('auth:sanctum');
// pages
Route::get('/pages', 'Api\PageController@pages');
Route::post('/faqs', 'Api\PageController@faqs');
Route::group(['namespace' => 'Api', 'middleware' => ['auth:sanctum', 'isVerified']], function () {
    //update profile
    Route::post('update-info', 'UserController@updateInfo');
    Route::post('activate-gold', 'UserController@activateGold');
    //save device tokens
    Route::post('user/device-token', 'UserController@saveToken');
    Route::post('user/logout', 'UserController@logout');

    //blog
    Route::get('blog/featured', 'BlogController@featured');
    Route::get('blog/latest', 'BlogController@latest');
    Route::get('blog/homepage', 'BlogController@homepage');
    Route::get('blog/categories', 'BlogController@categories');
    Route::post('blog/list', 'BlogController@list');
    Route::post('blog/detail', 'BlogController@blogDetail');
    Route::post('blog/like', 'BlogController@likeUnlike');
    Route::post('blog/comment', 'BlogController@saveComment');
    Route::post('blog/comments', 'BlogController@comments');

    // user
    Route::post('/updateavatar', 'UserController@updateAvatar');
    Route::post('user/update', 'UserController@updateProfile');
    Route::get('user/points', 'UserController@pointsData');
    Route::get('user/notifications', 'UserController@notifications');

    //reward|merchants
    Route::get('merchants', 'RewardController@merchants');
    Route::get('merchant/special-vouchers', 'RewardController@specialVouchers');
    Route::post('merchant/vouchers', 'RewardController@vouchers');
    Route::post('voucher/info', 'RewardController@info');

    // Order
    Route::post('/voucher/order', 'OrderController@create');
    Route::post('/orders/list', 'OrderController@list');
    Route::post('/orders/merchants', 'OrderController@orderMerchants');
    Route::post('/order/scan', 'OrderController@scanQrCode');

    //generate QR code
    Route::post('qrcode/generate', 'GenerateQrCodeController@generate');
    Route::post('qrcode/scan', 'GenerateQrCodeController@scan');

    /**
     * sales|merchant routes
     */

    //homepage
    Route::get('sales/home', 'SalesMerchantHomeController@salesHomepage');

    Route::post('add-customer', 'UserController@addCustomer');
    Route::post('customers', 'UserController@customerListForSalesPerson');
    //customer orders
    Route::post('user/scanned-codes', 'UserController@scannedQrcodes');

    //Sales|merchant Home
    Route::post('home-order-list', 'SalesMerchantHomeController@homePageOrderList');
    Route::post('orderlist-search', 'SalesMerchantHomeController@searchOrderList');

    //Sales|merchant reward transactions
    Route::post('rewardtransaction/list', 'SalesMerchantRewardTransactionController@rewardTransactions');

    /**
     * Merchant App Api
     */

    //add voucher
    Route::post('merchant/add-voucher', 'MerchantApiController@addVoucher');
    Route::post('merchant/edit-voucher', 'MerchantApiController@editVoucher');
    Route::get('merchant/vouchers', 'MerchantApiController@MerchantVouchers');
    Route::get('merchant/center', 'MerchantApiController@centerInfo');
    Route::post('merchant/center/update', 'MerchantApiController@centerUpdate');

});
