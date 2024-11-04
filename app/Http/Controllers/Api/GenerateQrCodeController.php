<?php

namespace App\Http\Controllers\Api;

use App\GenerateQrCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\GenerateQrCodeResource;
use App\Notifications\ScanSuccessForSales;
use App\Notifications\TransactionNotification;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use Illuminate\Support\Facades\DB;

class GenerateQrCodeController extends Controller
{
    public function generate(Request $request)
    {
        $generated_by  = $request->user();
        $generate_code = new GenerateQrCode();

        $validator = Validator::make($request->all(), [
            'amount' => 'required|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        if ($generated_by->getRoleNames()->first() != 'sales_person') {
            return response()->json(['success' => false, 'message' => "You are not allowed to perform this action!"], 400);
        }

        $code        = strtoupper(getUniqueToken(15));
        $qrCode      = new QrCode($code);
        $output      = new Output\Png();
        $output_file = 'qrcodes/qr-' . time() . '.png';
        $qrpath      = Storage::disk('public')->put($output_file, $output->output($qrCode, 200, [255, 255, 255], [0, 0, 0]));

        $generate_code->generated_by = $generated_by->id;
        $generate_code->amount       = $request->amount;
        $generate_code->qr_path      = $output_file;
        $generate_code->code         = $code;
        $generate_code->save();
        //generate generate_code id
        $generate_code->reference_id = 'AC' . sprintf('%s%07s%02s', now()->format('ymd'), $generated_by->id, $generate_code->id);
        $generate_code->save();

        return new GenerateQrCodeResource($generate_code);
    }

    public function scan(Request $request)
    {
        $scanned_by = $request->user();
        if (!in_array($scanned_by->getRoleNames()->first(), ['user'])) {
            return response()->json(['success' => false, 'message' => 'You don\'t have permission to perform this action'], 400);
        }
        $now    = Carbon::now();
        $qrcode = GenerateQrCode::where("code", $request->code)->first();

        /*
        check if qrcode exists with the code
         */
        if (is_null($qrcode)) {
            return response()->json(['success' => false, 'message' => 'Invalid QR Code'], 400);
        }

        //if already scanned
        if (!is_null($qrcode->scanned_on)) {
            $msg = 'QR Code already scanned on ' . $qrcode->scanned_on->format('d M Y');
            return response()->json(['success' => false, 'message' => $msg], 400);
        }

        //if QR code is older than 12 hours
        if ($qrcode->created_at->diffInHours($now) > 168) {
            return response()->json(['success' => false, 'message' => 'This QR Code is older.'], 400);
        }

        //get value in ponts
        if ($scanned_by->membership(true) == 1) { //1 means purple // true means numeric return required
            $valinpoints = Setting::where('key', 'purple_member_conversion')->first();
            $valinpoints = (!is_null($valinpoints)) ? floor($valinpoints->value*$qrcode->amount) : $qrcode->amount;
        } else {
            $valinpoints = Setting::where('key', 'gold_member_conversion')->first();
            $valinpoints = (!is_null($valinpoints)) ? floor($valinpoints->value*$qrcode->amount) : $qrcode->amount;
        }

        //everything is fine do transaction and fill scanned on
        $point_transactions_count = DB::table('point_transactions')
                                        ->where('data', 'LIKE', '%"qr_id":"'.$qrcode->id.'",%')
                                        ->count();

        if($point_transactions_count == 0) {
            $transaction = $scanned_by->creditPoints([
                'transaction_value' => $valinpoints,
                'data'              => json_encode(['qr_id' => $qrcode->id, 'message' => 'Points Added', 'sub_heading' => 'by '.$qrcode->generatedBy->name]),
            ]);
        }

        $qrcode->points         = $valinpoints;
        $qrcode->is_scanned     = 1;
        $qrcode->transaction_id = $transaction->id;
        $qrcode->scanned_by     = $scanned_by->id;
        $qrcode->scanned_on     = $now;
        $qrcode->save();

        $qrcode->generatedBy->notify(new ScanSuccessForSales($qrcode));
        $scanned_by->notify(new TransactionNotification($transaction));
        return response()->json(['data' => [
            'success'       => true,
            'points_earned' => $valinpoints,
            'expiry_date'   => $transaction->expiring_on->format('d M Y'),
            'balance'       => $scanned_by->balance(),
            'membership'    => $scanned_by->membership(),
        ]], 200);
    }
}
