<?php

namespace App\Http\Controllers\Admin;

use App\Exports\QrcodeExport;
use App\GenerateQrCode;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GeneratedQrCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = GenerateQrCode::with('generatedBy', 'scannedBy')->latest();
            return DataTables::of($data)
                ->editColumn('created_at', function ($data) {
                    return [
                        'display'   => (!is_null($data->created_at)) ? e($data->created_at->format('d M Y')) : 'N/A',
                        'timestamp' => (!is_null($data->created_at)) ? $data->created_at->timestamp : Null,
                    ];
                })
                ->editColumn('scanned_on', function ($data) {
                    return [
                        'display'   => (!is_null($data->scanned_on)) ?  e($data->scanned_on->format('d M Y')) : 'N/A',
                        'timestamp' => (!is_null($data->scanned_on)) ? $data->scanned_on->timestamp : null,
                    ];
                })
                ->addColumn('action', function ($data) {
                    $scanned_on = (!is_null($data->scanned_on)) ?  e($data->scanned_on->format('d M Y')) : '';
                    $scanned_by = (!is_null($data->scanned_by)) ? $data->scannedBy->name : NULL;
                    $qr_url = url('/').Storage::url($data->qr_path);
                    $button = '<a href="javascript:void(0)"
                                data-reference_id="'.$data->reference_id.'"
                                data-is_scanned="'.$data->is_scanned.'"
                                data-scanned_by="'.$scanned_by.'"
                                data-scanned_on="'.$scanned_on.'"
                                data-code="'.$data->code.'"
                                data-qr_code_path="'.$qr_url.'"
                                class="on-default text-success btn btn-xs btn-default"><i class="fa fa-eye"></i> View</a>';
                    $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action=' . route("qrcode.destroy", $data) . '  method="POST" class="inline-el" data-swal="1">
                        ' . method_field('DELETE') . '
                        ' . csrf_field() . '
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';

                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.qrcode.list');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GenerateQrCode  $generateQrCode
     * @return \Illuminate\Http\Response
     */
    public function destroy(GenerateQrCode $qrcode)
    {
        $qrcode->delete();
        return redirect(route('qrcode.index'))
            ->with('success', 'QR code deleted successfully.');
    }

    public function export()
    {
        return Excel::download(new QrcodeExport, 'data.xlsx');
    }
}
