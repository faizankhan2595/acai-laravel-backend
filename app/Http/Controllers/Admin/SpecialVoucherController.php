<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialVoucherFormRequest;
use App\Notifications\GeneralNotification;
use App\PointTransaction;
use App\RewardVoucher;
use App\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SpecialVoucherAllUsers;

class SpecialVoucherController extends Controller
{
    protected $merchantslist;

    public function __construct()
    {
        $this->merchantslist = User::role('merchant')->where('is_project_acai', 1)->get();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vouchers = RewardVoucher::with('merchant')->where('is_special_voucher', 1)->latest()->get();
        return view('admin.special-voucher.list', compact('vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $merchants = $this->merchantslist;
        $validFor  = (new RewardVoucher())->validFor;
        return view('admin.special-voucher.add', compact('merchants', 'validFor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SpecialVoucherFormRequest $request)
    {
        $validated          = $request->validated();
        $validated['image'] = null;
        if ($request->hasfile('image')) {
            $validated['image'] = $request->file('image')->store('vouchers', 'public');
        }
        $validated['is_featured']        = (isset($validated['is_featured'])) ? 1 : 0;
        $validated['is_special_voucher'] = 1;
        $validated['expiring_on']        = Carbon::now()->addDays($validated['expiring_on']);
        $voucher                         = RewardVoucher::create($validated);
        return redirect()->route('special-voucher.assignedusers', $voucher)->with('success', 'Special Voucher added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RewardVoucher  $rewardVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit(RewardVoucher $special_voucher)
    {
        $merchants = $this->merchantslist;
        return view('admin.special-voucher.edit', compact('merchants', 'special_voucher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RewardVoucher  $rewardVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(SpecialVoucherFormRequest $request, RewardVoucher $special_voucher)
    {
        $validated = $request->validated();
        if ($request->hasfile('image')) {
            Storage::disk('public')->delete($special_voucher->image);
            $validated['image'] = $request->file('image')->store('vouchers', 'public');
        } else {
            $validated['image'] = $validated['old_image'];
        }
        $validated['is_featured']        = (isset($validated['is_featured'])) ? 1 : 0;
        $validated['is_special_voucher'] = 1;
        $validated['expiring_on']        = $special_voucher->created_at->addDays($validated['expiring_on']);
        $validated['terms']              = (isset($validated['terms'])) ? $validated['terms'] : null;
        $special_voucher->update($validated);
        return redirect()->route('special-voucher.index')->with('success', 'Special Voucher updated successfully.');
    }

    public function assignedUsers(Request $request, RewardVoucher $special_voucher)
    {
        ini_set('memory_limit', '16024M');
        // $assignedUsers = $special_voucher->specialVoucherUsers()->get();
        $assignedUsers = array();
        return view('admin.special-voucher.assignedusers', compact('assignedUsers'));
    }

    public function assignToUsers(Request $request)
    {
        $special_voucher = RewardVoucher::find($request->voucher_id);

        $batchSize = 50;

        ini_set('memory_limit', '16024M');
        ini_set('max_execution_time', 3000000); 
  
        User::whereIn('id', $request->customers)->chunk($batchSize, function ($users) use ($special_voucher) {
            $datatoinsert = [];
            foreach ($users as $user) {
                $datatoinsert[$user->id] = [
                    'valid_from'  => $special_voucher->created_at->startOfDay(),
                    'valid_till'  => $special_voucher->expiring_on->endOfDay(),
                    'redeemed_on' => null,
                ];

                $user->notify(new GeneralNotification([
                    "title"   => "Special Reward",
                    "message" => "A reward just for you has been added to your account. View it under “Special Rewards”",
                ]));
            }

            $special_voucher->specialVoucherUsers()->sync($datatoinsert, false);
        });

        // SpecialVoucherAllUsers::dispatch($special_voucher, $request->customers);
      
        return response()->json(['status' => 1, 'message' => 'customers added successfully!'], 200);
    }

    public function assignToAllUsers(Request $request)
    {
        $special_voucher = RewardVoucher::find($request->voucher_id);

        ini_set('memory_limit', '16024M');
        ini_set('max_execution_time', 3000); 

        error_log('assignToAllUsers');

        SpecialVoucherAllUsers::dispatchAfterResponse($special_voucher);

        error_log('assignToAllUsers2');

        return response()->json(['status' => 1, 'message' => 'customers added successfully!'], 200);
    }

    public function searchUsers(Request $request)
    {
        ini_set('memory_limit', '16024M');
        ini_set('max_execution_time', 3000); 

        // $special_voucher = RewardVoucher::find($request->voucher_id);

        // if($request->voucher_id == 143) {
        //     $already_users = DB::table('reward_voucher_user')->where('reward_voucher_id', 143)->pluck('user_id');
        //     //$data = User::role('user')->whereNotIn('users.id', $already_users);
        //     dd($already_users);
        // }

        $data = User::role('user')->with('points')->whereNotIn('users.id', function($query) use ($request) {
            $query->select('user_id')->from('reward_voucher_user')->where('reward_voucher_id', $request->voucher_id);
        });

        if (($request->has('dob_from') && $request->dob_from != '') && ($request->has('dob_to') && $request->dob_to != '')) {
            $from = explode('-', $request->dob_from);
            $to   = explode('-', $request->dob_to);

            $from_month = $from[1];
            $from_day   = $from[0];

            $to_month = $to[1];
            $to_day   = $to[0];
            $data     = $data->whereMonth("dob", '>=', $from_month)->whereMonth("dob", '<=', $to_month)->whereDay("dob", '>=', $from_day)->whereDay("dob", '<=', $to_day);
        }
        if ($request->has('from_date') && $request->has('to_date') && $request->from_date != '' && $request->to_date != '') {
            $data = $data->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }
        if ($request->has('membership_from_date') && $request->has('membership_to_date') && $request->membership_from_date != '' && $request->membership_to_date != '') {
            $data = $data->whereBetween('gold_expiring_date', [$request->membership_from_date, $request->membership_to_date]);
        }

        if ($request->has('membership_tier') && $request->membership_tier != '') {
            $data = $data->where('membership_type', $request->membership_tier);
        }

        if ($request->has('spending_from_date') && $request->has('spending_to_date') && $request->spending_from_date != '' && $request->spending_to_date != '') {
            $data->whereHas('points', function ($q) use ($request) {
                $data = $q->where('transaction_type', 1)->whereBetween('created_at', [$request->spending_from_date." 00:00:00", $request->spending_to_date." 23:59:59"]);
            });
        }

        // dd(str_replace_array('?', $data->getBindings(), $data->toSql()));
        // $data = $data->get();
        return DataTables::of($data)->addColumn('selectcheckbox', function ($data) {
            return '<div class="checkbox checkbox-primary">
                <input id="checkbox' . $data->id . '" type="checkbox" value="' . $data->id . '" class="selectcheckbox">
                <label for="checkbox' . $data->id . '">
                </label>
            </div>';
        })
            ->editColumn('dob', function ($data) {
                return [
                    'display'   => (!is_null($data->dob)) ? $data->dob->format('d M Y') : 'N/A',
                    'timestamp' => (!is_null($data->dob)) ? $data->dob->timestamp : (10 * 9000 * 100000),
                ];
            })
            ->editColumn('created_at', function ($data) {
                return [
                    'display'   => (!is_null($data->created_at)) ? $data->created_at->format('d M Y') : 'N/A',
                    'timestamp' => (!is_null($data->created_at)) ? $data->created_at->timestamp : (10 * 9000 * 100000),
                ];
            })
            ->editColumn('gold_expiring_date', function ($data) {
                return [
                    'display'   => (!is_null($data->gold_expiring_date)) ? $data->gold_expiring_date->format('d M Y') : 'N/A',
                    'timestamp' => (!is_null($data->gold_expiring_date)) ? $data->gold_expiring_date->timestamp : (10 * 9000 * 100000),
                ];
            })
            ->addColumn('total_points_earned', function ($data) use ($request) {
                if ($request->has('spending_from_date') && $request->has('spending_to_date') && $request->spending_from_date != '' && $request->spending_to_date != '') {
                    return $data->points->where('transaction_type', 1)->whereBetween('created_at', [$request->spending_from_date." 00:00:00", $request->spending_to_date." 23:59:59"])->sum('transaction_value');
                }
                return $data->points->where('transaction_type', 1)->sum('transaction_value');
            })
            ->orderColumn('total_points_earned', function ($query, $order) use ($request) {
                if ($request->has('spending_from_date') && $request->has('spending_to_date') && $request->spending_from_date != '' && $request->spending_to_date != '') {
                    $query->orderBy(PointTransaction::selectRaw('SUM(`transaction_value`)')->where('transaction_type', 1)
                        ->whereBetween('created_at', [$request->spending_from_date." 00:00:00", $request->spending_to_date." 23:59:59"])
                        ->whereColumn('point_transactions.user_id', 'users.id'), $order);
                }else{
                    $query->orderBy(PointTransaction::selectRaw('SUM(`transaction_value`)')->where('transaction_type', 1)->whereColumn('point_transactions.user_id', 'users.id'), $order);
                }
            })
            ->order(function ($query) use ($request) {
                if ($request->has('spending_sort') && !empty($request->spending_sort)) {
                    if ($request->has('spending_from_date') && $request->has('spending_to_date') && $request->spending_from_date != '' && $request->spending_to_date != '') {
                        $query->orderBy(PointTransaction::selectRaw('SUM(`transaction_value`)')->where('transaction_type', 1)
                            ->whereBetween('created_at', [$request->spending_from_date." 00:00:00", $request->spending_to_date." 23:59:59"])
                            ->whereColumn('point_transactions.user_id', 'users.id'), $request->spending_sort);
                    }else{

                    }
                    $query->orderBy(PointTransaction::selectRaw('SUM(`transaction_value`)')->where('transaction_type', 1)
                        ->whereColumn('point_transactions.user_id', 'users.id'), $request->spending_sort);
                }
            })
            ->rawColumns(['selectcheckbox'])
            ->make(true);
        return view('admin.special-voucher.assignedusers');
    }

    public function searchUserWhoAreAssignedVoucher(Request $request)
    {
        $special_voucher = RewardVoucher::find($request->voucher_id);
        $data            = $special_voucher->specialVoucherUsers();

        return DataTables::of($data)->addColumn('selectcheckbox', function ($data) {
            return '<div class="checkbox checkbox-primary">
                <input id="checkbox' . $data->id . '" type="checkbox" value="' . $data->id . '" class="selectcheckbox">
                <label for="checkbox' . $data->id . '">
                </label>
            </div>';
        })
            ->editColumn('dob', function ($data) {
                return [
                    'display'   => (!is_null($data->dob)) ? $data->dob->format('d M Y') : 'N/A',
                    'timestamp' => (!is_null($data->dob)) ? $data->dob->timestamp : (10 * 9000 * 100000),
                ];
            })
            ->editColumn('created_at', function ($data) {
                return [
                    'display'   => (!is_null($data->created_at)) ? $data->created_at->format('d M Y') : 'N/A',
                    'timestamp' => (!is_null($data->created_at)) ? $data->created_at->timestamp : (10 * 9000 * 100000),
                ];
            })
            ->editColumn('valid_from', function ($user) {
                return $user->valid_from ? Carbon::parse($user->valid_from)->format('d M Y') : 'N/A';
            })
            ->editColumn('valid_till', function ($user) {
                return $user->valid_till ? Carbon::parse($user->valid_till)->format('d M Y') : 'N/A';
            })
            ->editColumn('is_redeemed', function ($data) {
                return ($data->is_redeemed == 1) ? '<span class="label label-success">Redeemed <span class="badge badge-danger">' . $data->redemption_count . '</span></span>' : '<span class="label label-danger">Not Redeemed </span>';
            })
            ->editColumn('redeemed_on', function ($data) {
                return (!is_null($data->redeemed_on)) ? Carbon::parse($data->redeemed_on)->format('d M Y') : 'N/A';
            })
            // <td><button class="btn btn-xs btn-success" data-sp_voucher_pivot_id="{{ $assignedUser->pivot->id }}">Change/Edit</button></td>
            ->addColumn('action', function ($assignedUser) {
                return '<button class="btn btn-xs btn-success" data-sp_voucher_pivot_id="' . $assignedUser->id . '">Change/Edit</button>';
            })
            ->editColumn('gold_expiring_date', function ($data) {
                return [
                    'display'   => (!is_null($data->gold_expiring_date)) ? $data->gold_expiring_date->format('d M Y') : 'N/A',
                    'timestamp' => (!is_null($data->gold_expiring_date)) ? $data->gold_expiring_date->timestamp : (10 * 9000 * 100000),
                ];
            })
            ->addColumn('total_points_earned', function ($data) use ($request) {
                if ($request->has('spending_from_date') && $request->has('spending_to_date') && $request->spending_from_date != '' && $request->spending_to_date != '') {
                    return $data->points->where('transaction_type', 1)->whereBetween('created_at', [$request->spending_from_date." 00:00:00", $request->spending_to_date." 23:59:59"])->sum('transaction_value');
                }
                return $data->points->where('transaction_type', 1)->sum('transaction_value');
            })
            ->orderColumn('total_points_earned', function ($query, $order) use ($request ) {
                if ($request->has('spending_from_date') && $request->has('spending_to_date') && $request->spending_from_date != '' && $request->spending_to_date != '') {
                    $query->orderBy(PointTransaction::selectRaw('SUM(`transaction_value`)')->where('transaction_type', 1)
                        ->whereBetween('created_at', [$request->spending_from_date." 00:00:00", $request->spending_to_date." 23:59:59"])
                        ->whereColumn('point_transactions.user_id', 'users.id'), $order);
                }else{
                    $query->orderBy(PointTransaction::selectRaw('SUM(`transaction_value`)')->where('transaction_type', 1)->whereColumn('point_transactions.user_id', 'users.id'), $order);
                }
            })
            ->order(function ($query) use ($request) {
                if ($request->has('spending_sort') && !empty($request->spending_sort)) {
                    if ($request->has('spending_from_date') && $request->has('spending_to_date') && $request->spending_from_date != '' && $request->spending_to_date != '') {
                        $query->orderBy(PointTransaction::selectRaw('SUM(`transaction_value`)')->where('transaction_type', 1)
                            ->whereBetween('created_at', [$request->spending_from_date." 00:00:00", $request->spending_to_date." 23:59:59"])
                            ->whereColumn('point_transactions.user_id', 'users.id'), $request->spending_sort);
                    }else{

                    }
                    $query->orderBy(PointTransaction::selectRaw('SUM(`transaction_value`)')->where('transaction_type', 1)
                        ->whereColumn('point_transactions.user_id', 'users.id'), $request->spending_sort);
                }
            })
            ->rawColumns(['selectcheckbox','is_redeemed','action'])
            ->make(true);
        return view('admin.special-voucher.assignedusers');
    }

    public function updatePivot(Request $request)
    {
        $update = DB::table('reward_voucher_user')->where('id', $request->sp_voucher_pivot_id)->update([
            'valid_till'  => Carbon::createFromFormat('d-M-Y', $request->valid_till),
            'is_redeemed' => $request->is_redeemed,
        ]);
        return response()->json(['message' => "Changes saved successfully!", 'valid_till' => $request->valid_till, 'is_redeemed' => $request->is_redeemed]);
    }
    public function destroy($id)
    {
        $voucher = RewardVoucher::withTrashed()->findOrFail($id);
        Storage::delete($voucher->image);
        $voucher->forceDelete();
        return redirect(route('special-voucher.index'))
            ->with('success', 'Voucher deleted successfully.');
    }

}
