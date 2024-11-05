<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Imports\UsersImport;
use App\Notifications\TransactionNotification;
use App\Notifications\WelcomeUser;
use App\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Jobs\NotifyOnCompletedExport;
use App\Setting;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $users = User::role('user')->get();
    //     return view('admin.user.list', compact('users'));
    // }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::role('user');

            if (($request->has('dob_from') && $request->dob_from != '') && ($request->has('dob_to') && $request->dob_to != '')) {
                $from = explode('-', $request->dob_from);
                $to   = explode('-', $request->dob_to);

                $from_month = $from[1];
                $from_day   = $from[0];

                $to_month = $to[1];
                $to_day   = $to[0];
                $data     = $data->whereMonth("dob", '>=', $from_month)->whereMonth("dob", '<=', $to_month)->whereDay("dob", '>=', $from_day)->whereDay("dob", '<=', $to_day);
            }
            if ($request->has('reg_from_date') && $request->has('reg_to_date') && $request->reg_from_date != '' && $request->reg_to_date != '') {
                $data = $data->whereBetween('created_at', [$request->reg_from_date, $request->reg_to_date]);
            }

            if ($request->has('membership_from_date') && $request->has('membership_to_date') && $request->membership_from_date != '' && $request->membership_to_date != '') {
                $data = $data->whereBetween('gold_expiring_date', [$request->membership_from_date, $request->membership_to_date]);
            }

            if ($request->has('membership_tier') && $request->membership_tier != '') {
                $data = $data->where('membership_type', $request->membership_tier);
            }
            return DataTables::of($data)
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
                ->addColumn('action', function ($data) {
                    $button = '<a href=' . route("user.edit", $data->id) . ' class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>';
                    $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action=' . route("user.destroy", $data->id) . '  method="POST" class="inline-el" data-swal="1">
                        ' . method_field('DELETE') . '
                        ' . csrf_field() . '
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';
                    $button .= '&nbsp;&nbsp;<a href=' . route("point-transaction.list", $data->id) . ' class="on-default text-primary btn btn-xs btn-default"><i class="fa fa-list"></i> Transactions</a>';
                    $button .= '&nbsp;&nbsp;<button class="on-default text-primary btn btn-xs btn-default" data-transactionuser="' . $data->id . '"><i class="fa fa-money"></i> Do Transaction</button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.user.list');
    }

    public function goldUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::role('user')->goldUser();
            if (($request->has('dob_from') && $request->dob_from != '') && ($request->has('dob_to') && $request->dob_to != '')) {
                $from = explode('-', $request->dob_from);
                $to   = explode('-', $request->dob_to);

                $from_month = $from[1];
                $from_day   = $from[0];

                $to_month = $to[1];
                $to_day   = $to[0];
                $data     = $data->whereMonth("dob", '>=', $from_month)->whereMonth("dob", '<=', $to_month)->whereDay("dob", '>=', $from_day)->whereDay("dob", '<=', $to_day);
            }
            if ($request->has('reg_from_date') && $request->has('reg_to_date') && $request->reg_from_date != '' && $request->reg_to_date != '') {
                $data = $data->whereBetween('created_at', [$request->reg_from_date, $request->reg_to_date]);
            }

            if ($request->has('membership_from_date') && $request->has('membership_to_date') && $request->membership_from_date != '' && $request->membership_to_date != '') {
                $data = $data->whereBetween('gold_expiring_date', [$request->membership_from_date, $request->membership_to_date]);
            }
            return DataTables::of($data)
                ->editColumn('dob', function ($data) {
                    return [
                        'display'   => (!is_null($data->dob)) ? $data->dob->format('d M Y') : 'N/A',
                        'timestamp' => (!is_null($data->dob)) ? $data->dob->timestamp : (10 * 9000 * 100000),
                    ];
                })
                ->editColumn('gold_activation_date', function ($data) {
                    return [
                        'display'   => (!is_null($data->gold_activation_date)) ? $data->gold_activation_date->format('d M Y') : 'N/A',
                        'timestamp' => (!is_null($data->gold_activation_date)) ? $data->gold_activation_date->timestamp : (10 * 9000 * 100000),
                    ];
                })
                ->editColumn('gold_expiring_date', function ($data) {
                    return [
                        'display'   => (!is_null($data->gold_expiring_date)) ? $data->gold_expiring_date->format('d M Y') : 'N/A',
                        'timestamp' => (!is_null($data->gold_expiring_date)) ? $data->gold_expiring_date->timestamp : (10 * 9000 * 100000),
                    ];
                })
                ->addColumn('action', function ($data) {
                    $button = '<a href=' . route("user.edit", $data->id) . ' class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>';
                    $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action=' . route("user.destroy", $data->id) . '  method="POST" class="inline-el" data-swal="1">
                        ' . method_field('DELETE') . '
                        ' . csrf_field() . '
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';
                    $button .= '&nbsp;&nbsp;<a href=' . route("point-transaction.list", $data->id) . ' class="on-default text-primary btn btn-xs btn-default"><i class="fa fa-list"></i> Transactions</a>';
                    $button .= '&nbsp;&nbsp;<button class="on-default text-primary btn btn-xs btn-default" data-transactionuser="' . $data->id . '"><i class="fa fa-money"></i> Do Transaction</button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.user.gold');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserFormRequest $request)
    {
        $validated           = $request->validated();
        $validated['avatar'] = null;
        if ($request->hasfile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('user', 'public');
        }
        if ($validated['sort_order'] == '') {
            $validated['sort_order'] = (User::max('sort_order')) + 1;
        }
        $password                       = $validated['password'];
        $validated['password']          = bcrypt($password);
        $validated['dob']               = ($validated['dob']) ? $validated['dob'] : date('Y-m-d H:i:s');
        $validated['email_verified_at'] = Carbon::now();

        if ($validated['membership_type'] == 2) {
            $validated['gold_activation_date'] = Carbon::now();
            $validated['gold_expiring_date']   = Carbon::now()->addDays(365)->endOfDay();
        }

        $user = User::create($validated);
        $user->assignRole($validated['role']);
        $user->notify(new WelcomeUser($user->name, $user->email, $password));
        return redirect()->route('user.index')->with('success', 'User added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserFormRequest $request, User $user)
    {
        $validated = $request->validated();
        if ($request->hasfile('avatar')) {
            Storage::disk('public')->delete($user->avatar);
            $validated['avatar'] = $request->file('avatar')->store('user', 'public');
        } else {
            $validated['avatar'] = $validated['old_avatar'];
        }
        if ($validated['sort_order'] == '') {
            $validated['sort_order'] = (User::max('sort_order')) + 1;
        }
        $validated['dob'] = ($validated['dob']) ? $validated['dob'] : date('Y-m-d H:i:s');

        if ($validated['membership_type'] == 2 && $user->membership_type == 1) {
            $validated['gold_activation_date'] = Carbon::now();
            $validated['gold_expiring_date']   = Carbon::now()->addDays(365)->endOfDay();
        }
        if ($validated['membership_type'] == 1 && $user->membership_type == 2) {
            $validated['gold_expiring_date'] = Carbon::now();
        }
        $user->update($validated);
        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function trash()
    {
        $trashed = User::onlyTrashed()->role('user')->get();
        return view('admin.user.trash', compact('trashed'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrfail($id);
        $user->restore();
        return redirect(route('user.trash'))
            ->with('success', 'User restored successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect(route('user.index'))
            ->with('success', 'User moved to deleted list.');
    }

    /**
     * @param  Int $id
     * @return redirect()
     */
    public function forcedelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        DB::table('comments')->where('user_id', $user->id)->delete();
        DB::table('device_tokens')->where('user_id', $user->id)->delete();
        DB::table('generate_qr_codes')->where('scanned_by', $user->id)->delete();
        DB::table('generate_qr_codes')->where('generated_by', $user->id)->delete();
        DB::table('likes')->where('user_id', $user->id)->delete();


        DB::table('orders')->where('user_id', $user->id)->update(['user_id' => 25915]);
        DB::table('orders')->where('scanned_by', $user->id)->update(['scanned_by' => 25915]);

        DB::table('point_transactions')->where('user_id', $user->id)->delete();
        DB::table('reward_voucher_user')->where('user_id', $user->id)->delete();
        DB::table('reward_vouchers')->where('user_id', $user->id)->delete();

        Storage::disk('public')->delete($user->avatar);
        $user->points()->delete();
        $user->deviceTokens()->delete();
        $user->forceDelete();
        return redirect(route('user.trash'))
            ->with('success', 'User deleted successfully.');
    }

    /**
     * @param Request
     */
    public function changePassword(Request $request, User $user)
    {
        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
        ]);

        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(["message" => "Password changed successfully."], 200);

    }

    public function addpoints(Request $request)
    {
        $user = User::findOrfail($request->transaction_user);

        if ($request->transaction_type == 1) {
            if ($request->exp_date == '') {
                return response()->json(['success' => false, 'message' => "Expity Date missing"], 400);
            }
            $transaction = $user->creditPoints([
                'transaction_value' => (int) $request->transaction_value,
                'expiring_on'       => Carbon::createFromFormat('d-M-Y', $request->exp_date),
                'data'              => json_encode(['message' => $request->transaction_message, 'sub_heading' => 'By Project Acai Admin']),
            ]);
            $user->notify(new TransactionNotification($transaction));
            return response()->json(['status' => true, 'message' => 'Poins Credited successfully!'], 200);
        } else {
            if ($user->balance() < (int) $request->transaction_value) {
                return response()->json(['success' => false, 'message' => "User doesn't have enough points!"], 400);
            }
            $transaction = $user->debitPoints([
                'transaction_value' => (int) $request->transaction_value,
                'data'              => json_encode(['message' => $request->transaction_message, 'sub_heading' => 'By Project Acai Admin']),
            ]);
            $user->notify(new TransactionNotification($transaction));
            return response()->json(['success' => true, 'message' => 'Poins Debited successfully!'], 200);
        }
    }

    public function userSearch(Request $request)
    {
        $data  = [];
        $users = User::role('user')->where('name', 'like', '%' . $request->search . '%')->where('email', 'like', '%' . $request->search . '%')->paginate(8);
        foreach ($users as $key => $user) {
            $data['items'][] = [
                'id'   => $user->id,
                'text' => $user->name . ' (' . $user->email . ')',
            ];
        }

        $data['total_count'] = $users->total();
        return response()->json($data, 200);
    }

    public function importExcel(Request $request)
    {
        $this->validate($request, [
            'excel_file' => 'required|mimes:xlsx',
        ]);
        $import = new UsersImport((isset($request->sent_email)) ? 1 : 0);
        $import->import($request->file('excel_file'));
        $failures = $import->getErrors();

        if (count($failures) > 0) {
            return redirect()->route('user.index')->with('fail_errors', $failures);
        } else {
            return redirect()->route('user.index')->with('success', 'User imported successfully.');
        }
    }

    public function exportUsers()
    {
        // return Excel::download(new UsersExport, 'acai-users-'.date('d-M-Y').'.xlsx');
        $adminemail = Setting::where('key','contact_email')->pluck('value')->first();
        $filename = 'acai-users-' . date('d-M-Y') . '.xlsx';
        $dispatcher = (new UsersExport)->store($filename);
        $dispatcher->chain([
            new NotifyOnCompletedExport($filename,$adminemail),
        ]);
        return back()->withSuccess('Export started, You\'ll get the email on '.$adminemail.' with attached file.');
    }
}
