<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VoucherResource;
use App\RewardVoucher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MerchantApiController extends Controller
{
    public function addVoucher(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->getRoleNames()->first(), ['merchant'])) {
            return response()->json(['success' => false, 'message' => 'You don\'t have permission to perform this action'], 403);
        }
        $data  = json_decode($request->input('info'), true);
        $rules = [
            'title'             => 'required',
            'discount_title'    => 'required',
            'voucher_type'      => 'required',
            'discount_subtitle' => 'required',
            'price'             => 'required',
            'expiring_on'       => 'required',
            'image'             => 'sometimes|mimes:jpeg,jpg,png|max:10000',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
        }
        $voucher = new RewardVoucher();
        if ($request->hasfile('image')) {
            $voucher->image = $request->file('image')->store('vouchers', 'public');
        }
        $terms = [];
        if (isset($data['terms'])) {
            foreach ($data['terms'] as $key => $value) {
                array_push($terms, $value['text']);
            }
        }
        $voucher->user_id           = $user->id;
        $voucher->title             = $data['title'];
        $voucher->price             = $data['price'];
        $voucher->discount_title    = $data['discount_title'];
        $voucher->discount_subtitle = $data['discount_subtitle'];
        $voucher->expiring_on       = Carbon::createFromFormat('d/m/Y', $data['expiring_on']);
        $voucher->notes             = $data['notes'];
        $voucher->terms             = (isset($terms)) ? $terms : null;
        $voucher->voucher_type      = $data['voucher_type'];
        $voucher->status            = 1;
        $voucher->is_featured       = 0;

        if ($voucher->save()) {
            return response()->json(['success' => true, 'message' => 'Voucher added successfully'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 400);
        }
    }

    public function editVoucher(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->getRoleNames()->first(), ['merchant'])) {
            return response()->json(['success' => false, 'message' => 'You don\'t have permission to perform this action'], 403);
        }
        $data  = json_decode($request->input('info'), true);
        $rules = [
            'title'             => 'required',
            'discount_title'    => 'required',
            'voucher_type'      => 'required',
            'discount_subtitle' => 'required',
            'price'             => 'required',
            'expiring_on'       => 'required',
            'image'             => 'sometimes|mimes:jpeg,jpg,png|max:10000',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
        }
        $voucher = RewardVoucher::find($data['voucher_id']);
        if ($request->hasfile('image')) {
            Storage::disk('public')->delete($voucher->image);
            $voucher->image = $request->file('image')->store('vouchers', 'public');
        }
        $terms = [];
        if (isset($data['terms'])) {
            foreach ($data['terms'] as $key => $value) {
                array_push($terms, $value['text']);
            }
        }
        $voucher->user_id           = $user->id;
        $voucher->title             = $data['title'];
        $voucher->price             = $data['price'];
        $voucher->discount_title    = $data['discount_title'];
        $voucher->discount_subtitle = $data['discount_subtitle'];
        $voucher->expiring_on       = Carbon::createFromFormat('d/m/Y', $data['expiring_on']);
        $voucher->notes             = $data['notes'];
        $voucher->terms             = (isset($terms)) ? $terms : null;
        $voucher->voucher_type      = $data['voucher_type'];
        $voucher->status            = 1;
        $voucher->is_featured       = 0;

        if ($voucher->save()) {
            return response()->json(['success' => true, 'message' => 'Voucher updated successfully', 'data' => new VoucherResource($voucher)], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 400);
        }
    }

    public function MerchantVouchers(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->getRoleNames()->first(), ['merchant'])) {
            return response()->json(['success' => false, 'message' => 'You don\'t have permission to perform this action'], 403);
        }

        $vouchers = $user->vouchers()->paginate(10000);
        return VoucherResource::collection($vouchers);
    }

    public function centerInfo(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->getRoleNames()->first(), ['merchant'])) {
            return response()->json(['success' => false, 'message' => 'You don\'t have permission to perform this action'], 403);
        }
        if ($user->center()->exists()) {
            return response()->json(['success' => true, 'data' => $user->center], 200);
        } else {
            return response()->json(['success' => true, 'data' => null], 200);
        }
    }

    public function centerUpdate(Request $request)
    {
        $merchant = $request->user();
        if (!in_array($merchant->getRoleNames()->first(), ['merchant'])) {
            return response()->json(['success' => false, 'message' => 'You don\'t have permission to perform this action'], 403);
        }
        if ($merchant->center()->exists()) {
            $merchant->center()->update($request->except('_token'));
            return response()->json(['success' => true, 'data' => $merchant->center], 200);
        } else {
            $merchant->center()->create($request->except('_token'));
            return response()->json(['success' => true, 'data' => $merchant->center], 200);
        }
    }
}
