<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Config;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected $failedmsg = "User not registered.";
    public function __construct(Request $request)
    {

        $email = $request->email;
        $user = User::where("email",$email)->first();
        if (!is_null($user)) {
            if($user->getRoleNames()->first() != 'user'){
                $this->sendResetLinkFailedResponse($request,"Please contact Acai customer care to reset your password!");
            }
        }
    }
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response(['message'=> "Password reset link sent to your email."]);

    }


    // protected function sendResetLinkFailedResponse(Request $request, $response)
    // {
    //     return response(['message'=> "User not registered."], 422);

    // }


}
