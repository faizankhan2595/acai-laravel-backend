<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantCollection;
use App\Http\Resources\VoucherResource;
use App\RewardVoucher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RewardController extends Controller
{
    public function merchants(Request $request)
    {
        $list = User::role('merchant')->active()->orderBy('sort_order','ASC')->paginate(1000);
        return new MerchantCollection($list);
    }

    public function vouchers(Request $request)
    {
        $user_membership_type = $request->user()->membership(true);
        $merchant = User::findOrFail($request->id);
        $vouchers = $merchant->vouchers()->where('is_special_voucher',0)->whereIn('valid_for',[0,$user_membership_type])->active()->latest()->paginate(10000);
        return VoucherResource::collection($vouchers);
    }


    public function specialVouchers(Request $request)
    {
        $user = $request->user();
        $vouchers = $user->specialVouchers()->where('status',1)->wherePivot('is_redeemed',0)->wherePivot('valid_till','>=',Carbon::now()->endOfDay())->latest()->paginate(10);
        return VoucherResource::collection($vouchers);
    }
    public function info(Request $request)
    {
        $voucher = RewardVoucher::where('status', 1)
                                    ->where('id', $request->id)
                                    ->whereDate('expiring_on', '>=', Carbon::now())
                                    ->firstOrFail();
        return new VoucherResource($voucher);
    }
}
