<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SalesMerchantHomeresource;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class SalesMerchantHomeController extends Controller
{
    //TODO : Optimize to solve duplicate queries
    public function homePageOrderList(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->getRoleNames()->first(), ['merchant', 'sales_person'])) {
            return response()->json(['success' => false, 'message' => 'You don\'t have permission to perform this action'], 400);
        }

        $generatedQrcodes = $user->myGeneratedQrcodes()->whereNotNull('scanned_by')->with('scannedBy')
            ->when(($request->has('date_filter') && $request->date_filter != 0 && $request->fromDate == '' && $request->toDate == ''), function ($query) use ($request) {
                switch ($request->date_filter) {
                    case 1:
                        $query = $query->whereDate('scanned_on', Carbon::today());
                        break;
                    case 2:
                        $query = $query->where('scanned_on', '>', Carbon::now()->startOfWeek())
                            ->where('scanned_on', '<', Carbon::now()->endOfWeek());
                        break;
                    case 3:
                        $query = $query->whereMonth('scanned_on', Carbon::now()->month);
                        break;
                    case 4:
                        $query = $query->whereYear('scanned_on', Carbon::now()->year);
                        break;
                }
            })
            ->when(($request->fromDate != '' && $request->toDate != ''), function ($query) use ($request) {
                $from = Carbon::createFromFormat('d/m/Y', $request->fromDate)->format('Y-m-d 00:00:00');
                $to   = Carbon::createFromFormat('d/m/Y', $request->toDate)->format('Y-m-d 23:59:59');
                if ($from > $to) {
                    $oldfrom = $from;
                    $from    = $to;
                    $to      = $oldfrom;
                }
                $query = $query->whereBetween('scanned_on', [$from, $to]);
            })
            ->when(($request->sort_by != ''), function ($query) use ($request) {
                $query = $query->orderBy('scanned_on', $request->sort_by);
            })
            ->paginate(30);
        return SalesMerchantHomeresource::collection($generatedQrcodes);
    }

    public function searchOrderList(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->getRoleNames()->first(), ['sales_person'])) {
            return response()->json(['success' => false, 'message' => 'You don\'t have permission to perform this action'], 400);
        }

        $Qrcodes = $user->myGeneratedQrcodes()->whereNotNull('scanned_by')
            ->where('reference_id', 'LIKE', '%' . $request->search . '%')
            ->with('scannedBy')->paginate(10);

        // $Qrcodes = $user->myGeneratedQrcodes()->whereNotNull('scanned_by')
        //     ->where('reference_id', 'LIKE', '%' . $request->search . '%')
        //     ->orWhereHas('scannedBy', function( $query ) use ($request){
        //         $query->where('name', 'LIKE', '%' . $request->search . '%');
        //     })->with('scannedBy')->paginate(10);
        return SalesMerchantHomeresource::collection($Qrcodes);
    }


    public function salesHomepage()
    {
        return response()->json([
            'sales_home_data'         => $this->salesHomeData(),
        ], 200);
    }

    public function salesHomeData()
    {
        $banner     = Setting::where('key', 'sales_home_banner')->first();
        $heading    = Setting::where('key', 'sales_home_heading')->first();
        $subheading = Setting::where('key', 'sales_home_subheading')->first();

        $banner   = (!is_null($banner)) ? url('/') . Storage::url($banner->value) : url('/') . '/img/landing-img.png';
        $title    = (!is_null($heading)) ? $heading->value : 'WELCOME TO ONE NINE';
        $subtitle = (!is_null($subheading)) ? $subheading->value : 'Earn rewards for every order done on store and on app.';

        return [
            'banner'   => $banner,
            'title'    => $title,
            'subtitle' => $subtitle,
        ];
    }
}
