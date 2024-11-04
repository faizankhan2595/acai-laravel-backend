<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Blog;
use App\Comment;
use App\Http\Controllers\Controller;
use App\Order;
use App\PointTransaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'usercount'     => User::role('user')->count(),
            'merchantcount' => User::role('merchant')->count(),
            'salescount'    => User::role('sales_person')->count(),
            'blogcount'     => Blog::count(),
        ];
        $recentusers  = User::role('user')->latest()->take(5)->get();
        $comments     = Comment::with('blog', 'user')->latest()->take(10)->get();
        $topcustomers = DB::select('SELECT u.name,u.avatar,u.email,u.mobile_number,u.dob,u.created_at,pt.id,pt.user_id,GREATEST((SUM(pt.transaction_value) - (SELECT SUM(transaction_value) FROM point_transactions WHERE user_id=pt.user_id AND transaction_type = 2)),0) AS balance FROM point_transactions pt JOIN users u ON pt.user_id = u.id WHERE DATE(expiring_on) > DATE(NOW()) AND transaction_type = 1 IS NOT NULL GROUP BY user_id ORDER BY balance DESC LIMIT 10');
        $topmerchants = Order::selectRaw('scanned_by,SUM(`amount`) as totalamount,COUNT(id) as totalorder')->whereNotNull('scanned_by')->with('scannedBy')->groupBy('scanned_by')->orderBy('totalorder', 'DESC')->get();

        $inactivecustomers = PointTransaction::selectRaw('user_id,DATE(expiring_on) AS dateonly')
        ->whereNotNull('user_id')
        ->whereRaw('DATE(expiring_on) <= DATE_SUB(DATE(NOW()), INTERVAL 1 YEAR)')
        ->with('user')
        ->groupBy('user_id')
        ->orderBy('dateonly', 'DESC')
        ->take(10)
        ->get();

        return view('admin.dashboard', compact('data', 'recentusers', 'comments', 'topcustomers', 'topmerchants', 'inactivecustomers'));
    }

    public function signupTrend(Request $request)
    {
        $data = [
            'labels' => array(),
            'count'  => array(),
        ];
        $records = User::role('user')->selectRaw('COUNT(`id`) as totalamount,DATE(created_at) as dateonly')->whereBetween('created_at', [$request->from, $request->to])->groupBy('dateonly')->get();

        foreach ($records as $key => $record) {
            array_push($data['labels'], date('d M Y', strtotime($record->dateonly)));
            array_push($data['count'], $record->totalamount);
        }
        return response()->json($data, 200);
    }

    public function orderTrend(Request $request)
    {
        $data = [
            'labels' => array(),
            'count'  => array(),
        ];
        $records = Order::selectRaw('COUNT(`id`) as total,DATE(created_at) as dateonly')->whereBetween('created_at', [$request->from, $request->to])->groupBy('dateonly')->get();

        foreach ($records as $key => $record) {
            array_push($data['labels'], date('d M Y', strtotime($record->dateonly)));
            array_push($data['count'], $record->total);
        }
        return response()->json($data, 200);
    }

    public function salesTrend(Request $request)
    {
        $data = [
            'labels' => array(),
            'count'  => array(),
        ];
        $records = Order::selectRaw('SUM(`amount`) as total,DATE(created_at) as dateonly')->whereBetween('created_at', [$request->from, $request->to])->groupBy('dateonly')->get();

        foreach ($records as $key => $record) {
            array_push($data['labels'], date('d M Y', strtotime($record->dateonly)));
            array_push($data['count'], $record->total);
        }
        return response()->json($data, 200);
    }

    public function redemptionTrend(Request $request)
    {
        $data = [
            'labels' => array(),
            'data'  => [
                'Total' => []
            ],
        ];
        $records = Order::selectRaw('COUNT(`id`) as total,DATE(redeemed_on) as dateonly')->whereBetween('redeemed_on', [$request->from, $request->to])->groupBy('dateonly')->get();
        $topmerchants = Order::selectRaw('scanned_by,DATE(redeemed_on) as dateonly,COUNT(id) as totalorder')->whereNotNull('scanned_by')->with('scannedBy')->whereBetween('redeemed_on', [$request->from, $request->to])->groupBy(['scanned_by','dateonly'])->orderBy('totalorder', 'DESC')->take(5)->get();
        foreach ($records as $key => $record) {
            array_push($data['labels'], date('d M Y', strtotime($record->dateonly)));
            array_push($data['data']['Total'], $record->total);
        }
        foreach ($topmerchants as $key => $merchant) {
            if (!array_key_exists($merchant->scannedBy->name, $data['data'])) {
                $data['data'][$merchant->scannedBy->name] = [];
            }
            array_push($data['data'][$merchant->scannedBy->name], $merchant->totalorder);
        }
        return response()->json($data, 200);
    }

    /**
     * [password description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
        ]);
        $user           = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(["message" => "Password changed successfully."], 200);
    }
}
