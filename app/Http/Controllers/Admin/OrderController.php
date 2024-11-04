<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use App\Order;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::with('user', 'voucher.merchant')->latest();
            return DataTables::of($data)
                ->editColumn('created_at', function ($data) {
                    return [
                        'display'   => e($data->created_at->format('d M Y')),
                        'timestamp' => $data->created_at->timestamp,
                    ];
                })
                ->editColumn('redeemed_on', function ($data) {
                    return [
                        'display'   => (!is_null($data->redeemed_on)) ?  e($data->redeemed_on->format('d M Y')) : 'N/A',
                        'timestamp' => (!is_null($data->redeemed_on)) ? $data->redeemed_on->timestamp : null,
                    ];
                })
                ->addColumn('action', function ($data) {
                    $redeemed_on = (!is_null($data->redeemed_on)) ?  e($data->redeemed_on->format('d M Y')) : NULL;
                    $scanned_by = (!is_null($data->scanned_by)) ? $data->scannedBy->name : NULL;
                    $qr_url = url('/').Storage::url($data->qr_path);
                    $button = '<a href="javascript:void(0)"
                                data-order="'.$data->id.'"
                                data-is_redeemed="'.$data->is_redeemed.'"
                                data-scanned_by="'.$scanned_by.'"
                                data-redeemed_on="'.$redeemed_on.'"
                                data-coupon_code="'.$data->coupon_code.'"
                                data-qr_code_path="'.$qr_url.'"
                                class="on-default text-success btn btn-xs btn-default"><i class="fa fa-eye"></i> View</a>';
                    $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action=' . route("order.destroy", $data->id) . '  method="POST" class="inline-el" data-swal="1">
                        ' . method_field('DELETE') . '
                        ' . csrf_field() . '
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';

                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.order.list');
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::onlyTrashed()->with('user', 'voucher.merchant')->get();
            return DataTables::of($data)
                ->editColumn('created_at', function ($data) {
                    return [
                        'display'   => e($data->created_at->format('d M Y')),
                        'timestamp' => $data->created_at->timestamp,
                    ];
                })
                ->editColumn('redeemed_on', function ($data) {
                    return [
                        'display'   => (!is_null($data->redeemed_on)) ?  e($data->redeemed_on->format('d M Y')) : 'N/A',
                        'timestamp' => (!is_null($data->redeemed_on)) ? $data->redeemed_on->timestamp : null,
                    ];
                })
                ->addColumn('action', function ($data) {
                    $redeemed_on = (!is_null($data->redeemed_on)) ?  e($data->redeemed_on->format('d M Y')) : NULL;
                    $scanned_by = (!is_null($data->scanned_by)) ? $data->scannedBy->name : NULL;
                    $qr_url = url('/').Storage::url($data->qr_path);
                    $button = '<a href="javascript:void(0)"
                                data-order="'.$data->id.'"
                                data-is_redeemed="'.$data->is_redeemed.'"
                                data-scanned_by="'.$scanned_by.'"
                                data-redeemed_on="'.$redeemed_on.'"
                                data-coupon_code="'.$data->coupon_code.'"
                                data-qr_code_path="'.$qr_url.'"
                                class="on-default text-success btn btn-xs btn-default"><i class="fa fa-eye"></i> View</a>';
                    $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to Permanently delete this?\')" action=' . route("order.forcedelete", $data->id) . '  method="POST" class="inline-el" data-swal="1">
                        ' . method_field('DELETE') . '
                        ' . csrf_field() . '
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Permanently Delete</button>
                    </form>';

                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.order.trash');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect(route('order.index'))
            ->with('success', 'Order moved to deleted list.');
    }

    public function forcedelete($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        Storage::disk('public')->delete($order->qr_path);
        $order->forceDelete();
        return redirect(route('order.trash'))
            ->with('success', 'Order deleted successfully.');
    }

    public function export()
        {
            return Excel::download(new OrderExport, 'orders.xlsx');
        }
}
