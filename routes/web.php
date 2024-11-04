<?php

use App\Notifications\GeneralNotification;
use App\Notifications\SendOtp;
use App\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::get('/hahainfo', function ()
{
    $notification = [
        'title'   => "Test",
        'message' => "Test Notifications",
        'image'   => null,
    ];
    //echo storage_path('app/secret/acai-3bb2e-firebase-adminsdk-acag1-8e9d55a547.json');
    // $user = User::find(1);
    // for($i = 0; $i<5;$i++){
    //     $user->notify(new GeneralNotification($notification));
    // }
});

Route::get('/success', function ()
{
    return view('admin.success');
});

Route::get('privacy-policy', 'Admin\PageController@privacy')->name('privacy');
Route::get('terms-conditions', 'Admin\PageController@terms')->name('terms');
Route::get('/', 'Admin\AdminController@index');

Auth::routes(['login' => false, 'register' => false]);
Route::match(['get','post'], 'login', function () {
    return redirect()->route('privacy');
});

// Route::match(['get','post'], 'password/reset', function () {
//     return redirect()->route('privacy');
// });

Route::get('/home', 'HomeController@index');
Route::get('/users/logout', 'Auth\LoginController@userLogout')->name('user.logout');

Route::prefix('admin')->group(function () {
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::get('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');

    // Password reset routes
    Route::post('/password/email', 'Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('/password/reset', 'Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('/password/reset', 'Auth\AdminResetPasswordController@reset');
    Route::get('/password/reset/{token}', 'Auth\AdminResetPasswordController@showResetForm')->name('admin.password.reset');
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin']], function () {
    Route::post('chart/signup-trend', 'AdminController@signupTrend')->name('signup-trend');
    Route::post('chart/order-trend', 'AdminController@orderTrend')->name('order-trend');
    Route::post('chart/sales-trend', 'AdminController@salesTrend')->name('sales-trend');
    Route::post('chart/rememption-trend', 'AdminController@redemptionTrend')->name('rememption-trend');


    Route::get('/', 'AdminController@index')->name('admin.dashboard');
    Route::post('/change-password', 'AdminController@changePassword')->name('admin.password');
    Route::resource('category', 'BlogCategoryController');

    //Blog Routes
    Route::get('blog/{image}/delete', 'BlogController@deleteimage')->name('image.destroy');
    Route::get('blog/trash', 'BlogController@trash')->name('blog.trash');
    Route::get('blog/{blog}/forcedelete', 'BlogController@forcedelete')->name('blog.forcedelete');
    Route::get('blog/{blog}/restore', 'BlogController@restore')->name('blog.restore');
    Route::get('blog/{blog}/comments','BlogController@comments')->name('blog.comments');
    Route::get('blog/{blog}/likes','BlogController@likes')->name('blog.likes');
    Route::post('blog/deletelike/{blog}/{user_id}','BlogController@deletelike')->name('blog.deletelike');
    Route::resource('blog', 'BlogController');

    //comment
    Route::get('comments/{blog?}','CommentController@index')->name('comment.index');
    Route::get('comment/export','CommentController@export')->name('comment.export');
    Route::patch('comment/{comment}','CommentController@approve')->name('comment.approve');
    Route::patch('comment/{comment}/deny','CommentController@deny')->name('comment.deny');
    Route::post('comment/deny-ajax','CommentController@denyAjax')->name('comment.disable');
    Route::delete('comment/{comment}','CommentController@destroy')->name('comment.destroy');



    //user/Customer routes
    Route::post('user/search', 'UserController@userSearch')->name('user.search');
    Route::get('user/trash', 'UserController@trash')->name('user.trash');
    Route::post('user/importexcel', 'UserController@importExcel')->name('user.importexcel');
    Route::get('user/exportexcel', 'UserController@exportUsers')->name('user.exportexcel');
    Route::get('user/{user}/forcedelete', 'UserController@forcedelete')->name('user.forcedelete');
    Route::get('user/{user}/restore', 'UserController@restore')->name('user.restore');
    Route::put('user/{user}/change-password', 'UserController@changePassword')->name('user.change');
    Route::post('user/do-transaction', 'UserController@addpoints')->name('user.dotransaction');
    Route::get('users/gold', 'UserController@goldUsers')->name('user.gold');
    Route::resource('user', 'UserController');


    //Merchant routes
    Route::get('merchant/trash', 'MerchantController@trash')->name('merchant.trash');
    Route::get('merchant/{merchant}/forcedelete', 'MerchantController@forcedelete')->name('merchant.forcedelete');
    Route::get('merchant/{merchant}/restore', 'MerchantController@restore')->name('merchant.restore');
    Route::post('merchant/{merchant}/savecenter', 'MerchantController@savecenter')->name('merchant.savecenter');
    Route::put('merchant/{merchant}/change-password', 'MerchantController@changePassword')->name('merchant.change');
    Route::resource('merchant', 'MerchantController');

    //Sales Person routes
    Route::get('salesperson/trash', 'SalesPersonController@trash')->name('salesperson.trash');
    Route::get('salesperson/{salesperson}/forcedelete', 'SalesPersonController@forcedelete')->name('salesperson.forcedelete');
    Route::get('salesperson/{salesperson}/restore', 'SalesPersonController@restore')->name('salesperson.restore');
    Route::put('salesperson/{salesperson}/change-password', 'SalesPersonController@changePassword')->name('salesperson.change');
    Route::resource('salesperson', 'SalesPersonController');

    //reward vouchers
    Route::get('voucher/trash', 'RewardVoucherController@trash')->name('voucher.trash');
    Route::get('voucher/{voucher}/forcedelete', 'RewardVoucherController@forcedelete')->name('voucher.forcedelete');
    Route::get('voucher/{voucher}/restore', 'RewardVoucherController@restore')->name('voucher.restore');
    Route::resource('voucher', 'RewardVoucherController');

    //special voucher
    Route::get('special-voucher/search-users', 'SpecialVoucherController@searchUsers')->name('special-voucher.searchusers');
    Route::get('special-voucher/searchUserWhoAreAssignedVoucher', 'SpecialVoucherController@searchUserWhoAreAssignedVoucher')->name('special-voucher.searchUserWhoAreAssignedVoucher');
    Route::post('special-voucher/update-pivot', 'SpecialVoucherController@updatePivot')->name('special-voucher.updatepivote');
    Route::post('special-voucher/assigntousers', 'SpecialVoucherController@assignToUsers')->name('special-voucher.assigntousers');
    Route::post('special-voucher/assignToAllUsers', 'SpecialVoucherController@assignToAllUsers')->name('special-voucher.assignToAllUsers');
    Route::get('special-voucher/{special_voucher}/assignedusers', 'SpecialVoucherController@assignedUsers')->name('special-voucher.assignedusers');
    Route::resource('special-voucher', 'SpecialVoucherController');

    //faq
    Route::resource('faq-category', 'FaqCategoryController');
    Route::resource('faq', 'FaqController');
    // CMS
    Route::resource('page', 'PageController');
    // Order
    Route::get('order/export', 'OrderController@export')->name('order.export');
    Route::get('order/trash', 'OrderController@trash')->name('order.trash');
    Route::delete('order/forcedelete/{id}', 'OrderController@forcedelete')->name('order.forcedelete');
    Route::resource('order', 'OrderController');

    //generated Qrcode
    Route::get('qrcode/export', 'GeneratedQrCodeController@export')->name('qrcode.export');
    Route::resource('qrcode', 'GeneratedQrCodeController');
    //Setting
    Route::get('setting', 'SettingController@show')->name('setting.show');
    Route::post('setting', 'SettingController@save')->name('setting.save');

    //Locations
    Route::get('locations', 'SettingController@locations')->name('locations.show');
    Route::post('locations', 'SettingController@saveLocations')->name('locations.save');

    // Point Transaction
    Route::get('point-transaction/{user?}','PointTransactionController@index')->name('point-transaction.list');
    Route::delete('point-transaction/{id}','PointTransactionController@destroy')->name('point-transaction.destroy');

    //notifications
    Route::resource('notification', 'NotificationController');

    //email templates
    Route::resource('email', 'MailTemplateController');
});
