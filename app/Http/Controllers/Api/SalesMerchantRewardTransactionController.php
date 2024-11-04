<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SalesMerchantRewardTransactionResource;
use Illuminate\Http\Request;

class SalesMerchantRewardTransactionController extends Controller
{
    public function rewardTransactions(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->getRoleNames()->first(),['merchant','sales_person'])) {
            return response()->json(['success' => false, 'message' => 'You don\'t have permission to perform this action'], 403);
        }
        $rewardtransactions = $user->scannedVouchers()->latest()->paginate(10);
        return SalesMerchantRewardTransactionResource::collection($rewardtransactions);
    }
}
