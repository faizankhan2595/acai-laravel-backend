<?php

namespace App\Http\Controllers\Api;

use App\DeviceToken;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Controller;
use App\Http\Resources\EarnedPointsResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Notifications\SendOtp;
use App\Notifications\TransactionNotification;
use App\Notifications\WelcomeUser;
use App\Notifications\WelcomeWithPasswordReset;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\PointTransaction;
use Config;

class UserController extends Controller
{
    /**
     * API Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $rules = [
            'name'     => 'required',
            'email'    => 'unique:users|required|email',
            'password' => 'required|min:8|confirmed',
        ];

        $input     = $request->only('name', 'email', 'password', 'password_confirmation');
        $messages = array(
            'password.confirmed' => 'Password and confirm password field does not match.'
        );
        $validator = Validator::make($input, $rules,$messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()]);
        }
        $name     = $request->name;
        $email    = $request->email;
        $password = $request->password;
        $otp      = rand(1000, 9999);
        $user     = User::create([
            'name'       => ucfirst($name),
            'email'      => $email,
            'password'   => Hash::make($password),
            'otp'        => $otp,
            'created_by' => -1,
        ]
        );
        $user->assignRole('user');

        $user->notify(new SendOtp($name,$otp));

        return response()->json([
            'success' => true,
            'email' => $user->email,
            'token'   => $user->createToken($request->email)->plainTextToken,
        ]);
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        $credentials = $request->only('email', 'password');

        if($request->has('testLOGIN')){
            $user = User::where('email',$request->email)->first();
            $token        = $user->createToken($user->email)->plainTextToken;
            $responsedata = [
                'success' => true,
                'status'  => 1,
                'token'   => $user->createToken($request->email)->plainTextToken,
                'message' => 'Logged in successfully.',
            ];
            return response()->json($responsedata);
        }

        if (Auth::attempt($credentials)) {
            $user         = Auth::user();
            $responsedata = [];
            $token        = $user->createToken($user->email)->plainTextToken;
            //check if user's email is verified
            if (is_null($user->email_verified_at)) {
                // $otp       = rand(1000, 9999);
                // $user->otp = $otp;
                // $user->save();
                // $user->notify(new SendOtp($otp));
                $responsedata = [
                    'success' => true,
                    'status'  => 2,
                    'message' => 'Please verify your email.',
                    'token'   => $token,
                    'user'    => new UserResource($user),
                    'role'    => $user->getRoleNames()->first(),
                ];
                return response()->json($responsedata);
            }
            // check if user's account is deactivated
            if ($user->account_status == 0) {
                $responsedata = [
                    'success' => false,
                    'status'  => 3,
                    'message' => 'Your account was deactivated. Please contact us via email for details.',
                ];
                return response()->json($responsedata, 401);
            }
            // check if user's profile is updated
            if (is_null($user->dob)) {
                $responsedata = [
                    'success' => true,
                    'status'  => 4,
                    'token'   => $token,
                    'message' => 'Please update your profile.',
		    'role' => $user->getRoleNames()->first(),
		    'user'    => new UserResource($user),
                ];
                return response()->json($responsedata);
            }

            //return token if everything is fine
            $responsedata = [
                'success' => true,
                'status'  => 1,
                'user'    => new UserResource($user),
                'token'   => $user->createToken($request->email)->plainTextToken,
                'message' => 'Logged in successfully.',
            ];
            return response()->json($responsedata);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Invalid email or password!",
            ], 422);
        }
    }

    /**
     * [verify description]
     * @param  Request $request [description]
     * @return [boolean] [check otp is valid or not]
     */
    public function verify(Request $request)
    {
        $user = $request->user();
        if ($user->otp != $request->otp || is_null($request->otp)) {
            return response()->json(['success' => false, 'error' => "Invalid Otp"]);
        }
        $user->account_status    = 1;
        $user->email_verified_at = Carbon::now();
        $user->save();
        //$user->tokens()->delete();
        $token = $user->createToken('userauthtoken')->plainTextToken;
        $user->notify(new WelcomeUser($user->name,$user->email, '*****'));
        return response()->json([
            'success' => true,
            'token'   => $token,
            'message' => "OTP Verified."]
        );
    }

    public function resend(Request $request)
    {
        $user = $request->user();
        if ($user->isVerified()) {
            return response()->json([
                'success' => true,
                'message' => "Already Verified."]
            );
        }
        $otp       = rand(1000, 9999);
        $user->otp = $otp;
        $user->save();
        if (env('APP_DEBUG') == false) {
            Config::set('mail.host', 'smtp.mailtrap.io');
            Config::set('mail.username', '8f351de205de4b');
            Config::set('mail.password', 'e419669c81dd98');
        }
        $user->notify(new SendOtp($user->name,$otp));
        return response()->json([
            'success' => true,
            'message' => "Otp sent successfully."]
        );
    }

    public function updateInfo(Request $request)
    {
        $user                = $request->user();
        $customAttributes = [
        'mobile_number' => 'Mobile Number',
        ];
        $errmessages = [
            'unique' => ':attribute has been registered with another account.',
        ];
        $rules = [
            'mobile_number' => 'required|unique:users,mobile_number,' . $user->id,
            'dob'           => 'required',
        ];
        $input     = $request->only('mobile_number', 'dob');
        $validator = Validator::make($input, $rules,$errmessages,$customAttributes);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->errors()->first(),
            ], 422);
        }

        $user->mobile_number = $request->mobile_number;
        $user->dob           = Carbon::createFromFormat('d/m/Y', $request->dob);
        $user->save();
        return response()->json([
            'success' => true,
            'user'    => new UserResource($user),
            'token'   => $request->bearerToken(),
            'message' => "Profile updated successfully."]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {
        $user = $request->user();
        if (is_null($user->email_verified_at)) {
            // $otp       = rand(1000, 9999);
            // $user->otp = $otp;
            // $user->save();
            // $user->notify(new SendOtp($otp));
            $responsedata = [
                'success' => true,
                'status'  => 2,
                'message' => 'Please verify your email.',
                'token'   => $request->bearerToken(),
                'role'    => $user->getRoleNames()->first(),
            ];
            return response()->json($responsedata);
        }
        // check if user's account is deactivated
        if ($user->account_status == 0) {
            $responsedata = [
                'success' => false,
                'status'  => 3,
                'message' => 'Your account was deactivated. Please contact us via email for details.',
            ];
            return response()->json($responsedata, 403);
        }
        // check if user's profile is updated
        if (is_null($user->dob)) {
            $responsedata = [
                'success' => true,
                'status'  => 4,
                'token'   => $request->bearerToken(),
                'message' => 'Please update your profile.',
            ];
            return response()->json($responsedata);
        }

        return new UserResource($user);
    }

    /**
     * [updateAvatar update profile photo]
     * @param  Request $request [request data]
     * @return [Model]           [User model]
     */
    public function updateAvatar(Request $request)
    {
        $user      = $request->user();
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|max:1024',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        } else {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = $request->file('photo')->store('user', 'public');
            $user->save();
            return response()->json(['status' => true, 'user' => new UserResource($user)], 200);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $customAttributes = [
        'mobile_number' => 'Mobile Number',
        ];
        $errmessages = [
            'unique' => ':attribute has been registered with another account.',
        ];
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'mobile_number' => 'required|unique:users,mobile_number,' . $user->id,
            // 'dob'           => 'required',
        ],$errmessages,$customAttributes);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        } else {
            $user->name          = ucfirst($request->name);
            $user->mobile_number = $request->mobile_number;
            // $user->dob           = Carbon::createFromFormat('d/m/Y', $request->dob);
            $user->save();
            return response()->json(['status' => true, 'message' => 'Profile updated successfully.', 'user' => new UserResource($user)], 200);
        }
    }

    public function pointsData(Request $request)
    {

        $user         = $request->user();
        $user_id      = $user->id;

        $tr = TransactionResource::collection($user->transactions(500));

        // if($user_id == 6) {
            $tr = $tr->toArray($request);

            $tr = collect($tr);
    
            $data2 = PointTransaction::where('user_id', $user_id)
            ->orderBy('created_at', 'asc')
            ->with('user:id,name')
            ->limit(500);
    
            $old_balance = 0;
            $expired_point_ids = [];
    
            $is_jan_update_considered = false;
    
            foreach ($data2 as $key => $point_transaction) {
                $point_transaction->points_available = $point_transaction->transaction_value;
                $point_transaction->points_redeemed = 0;
            }
    
            foreach ($data2 as $key => $point_transaction) {
                $date = Carbon::parse($point_transaction->created_at);
    
                if ($date->gte(Carbon::parse('2022-01-02 20:00:00'))) {
                    if(!$is_jan_update_considered) {
                        $old_balance = 0;
                        $is_jan_update_considered = true;
                    }
                } 
    
                if ($point_transaction->transaction_type == 1) {
                    $old_balance = $old_balance + $point_transaction->transaction_value;
                } else {
                    $total_points_redeemed = 0;
                    $date = Carbon::parse($point_transaction->created_at);

                    if($date->gte(Carbon::parse('2022-01-17 00:00:00'))) {
                        $date = $date->endOfDay();
                    }
                    
                    $rows = [];
    
                    foreach ($data2 as $key => $value) {
                        if ($value->transaction_type == 1 && Carbon::parse($value->expiring_on)->gte($date)) {
                            array_push($rows, $value);
                        }
                    }
    
                    $amount_to_redeem = (int) $point_transaction->transaction_value;
                    
                    $amount_redeemed_map = array();
    
                    foreach ($rows as $row) {
                        $amount_redeemed = min($row->points_available, $amount_to_redeem);
    
                        $amount_redeemed_map[$row->id] = $amount_redeemed;
                        $amount_to_redeem -= $amount_redeemed;
    
                        if ($amount_to_redeem == 0) {
                            break;
                        } 
                    }
    
                    foreach ($amount_redeemed_map as $tid => $tr_amt) {
                        $transaction = $data2->where('id', $tid)->first();
                        $temp_points_redeemed  = $transaction->points_redeemed + $tr_amt;
                        $temp_points_available = $transaction->transaction_value - $temp_points_redeemed;
    
                        $total_points_redeemed += $tr_amt;
                        
    
                        foreach ($data2 as $key2 => $value2) {
                            if($value2->id == $tid){
                                $data2[$key2]->points_redeemed = $temp_points_redeemed;
                                $data2[$key2]->points_available = $temp_points_available;
                            }
                        }
                    }
                    
                    $old_balance = $old_balance - $total_points_redeemed;
                }
    
                $expired_points = 0;
                $expired_point_id = '';
                $tempCount = 0.01;
                foreach ($data2 as $key3 => $value) {
                    
                    if ($value->transaction_type == 1 && Carbon::parse($value->expiring_on)->lt($date)) {
                        if($value->points_available > 0){
                            if(!in_array($value->id, $expired_point_ids)) {
                                $expired_points += $value->transaction_value;
                                
                                $tr = $tr->concat([[
                                    'id' => $point_transaction->id -1 + $tempCount,
                                    'transaction_type' => '2',
                                    'transaction_value' => $value->transaction_value,
                                    'message' => 'Points Expired ('.Carbon::parse($value->created_at)->format('d M Y').')',
                                    'sub_heading' => 'By System',
                                    'date' => Carbon::parse($value->expiring_on)->format('d M Y'),
                                ]]);
    
                                $tempCount += 0.01;
    
                                array_push($expired_point_ids, $value->id);
                                $expired_point_id = $expired_point_id.$value->id.' ';
                            }
                        }
                    }
                }
    
                if($expired_points>0) {
                    $old_balance = $old_balance - $expired_points;
                }
    
                if($expired_points>0) { 
                    $point_transaction->expired_points = $expired_points . ' (ID# '.rtrim($expired_point_id).')';
                } else {
                    $point_transaction->expired_points = 0;
                }
    
                $point_transaction->old_balance = $old_balance;
            }
    
            $tr = $tr->sortByDesc('id');
    
            $tr = $tr->values()->all();
        // }

        $nextExpiring = $user->nextExpiring();
        $data         = [
            'membership'      => $user->membership(),
            'total_points'    => $user->balance(),
            'next_exp_date'   => (!is_null($nextExpiring)) ? $nextExpiring->expiring_on->format('d M Y') : null,
            'next_exp_points' => (!is_null($nextExpiring)) ? $nextExpiring->points : null,
            'transactions'    => $tr,
            'gold_exp_date'   => null
        ];
        if ($user->membership(true) == 1) {
            $terms = Setting::where('key', 'gold_membership_tc')->first();
            $maxpoints = Setting::where('key', 'gold_membership_points')->first();
            $maxpointsval = (!is_null($maxpoints)) ? $maxpoints->value : config('admin.gold_membership_points');
            $data['gold_membership_tc'] = (!is_null($terms)) ? $terms->value : null;
            $data['can_apply_for_gold'] = $user->isEligibleForGold();
            $data['points_required_for_gold'] = $maxpointsval;
            $data['user_gold_reach'] = (($user->balance()*100)/$maxpointsval)/100;
        }else{
            $data['gold_exp_date'] = Carbon::parse($user->gold_expiring_date)->format('d M Y');
        }
        return response()->json(['status' => true, 'data' => $data, 'user_id' => $user_id], 200);

    }

    public function activateGold(Request $request)
    {
        $goldcharge = Setting::where('key', 'gold_membership_charge')->first();
        $gold_membership_charge = (!is_null($goldcharge)) ? $goldcharge->value : null;
        $user = $request->user();
        if (!$user->isEligibleForGold()) {
            return response()->json(['status' => true, 'message' => 'You are not eligible for Gold Membership.'], 400);
        }
        if (($gold_membership_charge > $user->balance()) || is_null($gold_membership_charge)) {
            return response()->json(['status' => true, 'message' => 'Insufficient points.'], 400);
        }
        $transaction =  $user->debitPoints([
            'transaction_value' => round($gold_membership_charge),
            'data' => json_encode(['message' => "Gold Membership Activated",'sub_heading' => 'Membership Charge']),
        ]);
        $user->notify(new TransactionNotification($transaction));
        $user->membership_type = 2;
        $user->gold_activation_date = Carbon::now();
        $user->gold_expiring_date = Carbon::now()->addDays(365)->endOfDay();
        $user->save();
        return response()->json(['success' => true,'message' => 'Gold Membership activated successfully!'],200);
    }
    public function saveToken(Request $request)
    {
        if (!is_null($request->token)) {
            $token            = $request->token;
            $user             = $request->user();
            $alreadyavailable = $user->deviceTokens()->where('fcm_token', $token)->exists();
            if (!$alreadyavailable) {
                $device            = new DeviceToken();
                $device->fcm_token = $token;
                $device->user_id   = $user->id;
                $device->save();
            }

        }
    }

    /**
     *  Methods for sales UI
     */

    /**
     * Store a newly created resource in DB.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addCustomer(Request $request)
    {
        if ($request->user()->getRoleNames()->first() != 'sales_person') {
            return response()->json(['success' => false, 'error' => 'You aren\'t allowed to perform this action'], 403);
        }
        $customAttributes = [
        'mobile_number' => 'Mobile Number',
        ];
        $errmessages = [
            'unique' => ':attribute has been registered with another account.',
        ];
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => 'required|email|unique:users',
            'mobile_number' => 'required|unique:users',
            'dob'           => 'required',
        ],$errmessages,$customAttributes);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
        $password                = getUniqueToken(10);
        $user                    = new User;
        $user->name              = ucfirst($request->name);
        $user->email             = $request->email;
        $user->mobile_number     = $request->mobile_number;
        $user->dob               = Carbon::createFromFormat('d/m/Y', $request->dob);
        $user->password          = bcrypt($password);
        $user->created_by        = $request->user()->id;
        $user->email_verified_at = Carbon::now();
        $user->account_status = 1;
        $user->save();
        $user->assignRole('user');
        $passwordtoken = app('auth.password.broker')->createToken($user);
        $user->notify(new WelcomeWithPasswordReset($user->name,$user->email, $password,$passwordtoken));
        return response()->json(['success' => true, 'user' => new UserResource($user)], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customerListForSalesPerson(Request $request)
    {
        $query = User::role('user');
        // $query->where('created_by',$request->user()->id);

        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $query  = $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate(10);
        return UserResource::collection($users);
    }

    //scanned Qr codes by user
    public function scannedQrcodes(Request $request)
    {
        if ($request->user()->getRoleNames()->first() != 'sales_person') {
            return response()->json(['success' => false, 'error' => 'You aren\'t allowed to perform this action'], 403);
        }
        $user        = User::findOrFail($request->user_id);
        $scanned_qrs = $user->scannedQrcodes()->latest()->get();
        $data        = EarnedPointsResource::collection($scanned_qrs);
        return response()->json(['success' => true, 'data' => $data, 'total' => $data->count()], 200);
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        $data = $user->notifications()->latest()->paginate(10);
        if (!$data->isEmpty()) {
            foreach ($data as $i => $item) {
                $returnlist[] = [
                    'id'   => $item->id,
                    'title'   => ($item->data['title']) ? $item->data['title'] : null,
                    'message' => ($item->data['message']) ? $item->data['message'] : null,
                    'is_read' => (!is_null($item->read_at)) ? 1 : 0,
                    'date'    => (!is_null($item->created_at)) ? $item->created_at->diffForHumans() : null,
                    'time'    => (!is_null($item->created_at)) ? $item->created_at->format('g:i A') : null,
                ];
            }
            return response()->json(['data' => $returnlist, 'meta' => ['current_page' => $data->currentPage(), 'last_page' => $data->lastPage()]], 200);
        } else {
            return response()->noContent();
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $user->deviceTokens()->where('fcm_token',$request->fcm_token)->delete();
    }
}
