<?php

namespace App\Exports;

use App\Order;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromArray, WithHeadings
{

    public function headings(): array
    {
        return ["Order Id", "Customer Name", "Voucher Title", "Order Amount","Coupon Code", "Qr Code", "Scanned By", "Redeemed On", "Created At"];
    }

    public function array(): array
    {
        $orders = Order::all();
        $data = [];
        foreach ($orders as $key => $order) {
            $data[] = [
            'order_id' => $order->order_id,
            'customer_name' => $order->user->name,
            'voucher_name' => $order->voucher->title,
            'coupon_code' => $order->coupon_code,
            'amount' => $order->amount." Points",
            'qr_code' => url('/').Storage::url($order->qr_path),
            'scanned_by' => (!is_null($order->scanned_by)) ? $order->scannedBy->name : 'NA',
            'redeemed_on' => (!is_null($order->redeemed_on)) ? $order->redeemed_on->format('d M Y') : 'NA',
            'created_at' => (!is_null($order->created_at)) ? $order->created_at->format('d M Y') : 'NA',
        ];
        }
        return $data;
    }
}
