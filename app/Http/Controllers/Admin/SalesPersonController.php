<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Notifications\WelcomeSalesPerson;
use App\Notifications\WelcomeUser;
use App\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class SalesPersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $salespersons = User::role('sales_person')->get();
    //     return view('admin.salesperson.list', compact('salespersons'));
    // }
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $data = User::role('sales_person')->latest()->get();
            return DataTables::of($data)
                    ->editColumn('dob', function ($data) {
                        return [
                            'display' => (!is_null($data->dob)) ? $data->dob->format('d M Y') : 'N/A',
                            'timestamp' => (!is_null($data->dob)) ?  $data->dob->timestamp : (10*9000*100000)
                        ];
                    })
                    ->editColumn('created_at', function ($data) {
                        return [
                            'display' => (!is_null($data->created_at)) ? $data->created_at->format('d M Y') : 'N/A',
                            'timestamp' => (!is_null($data->created_at)) ?  $data->created_at->timestamp : (10*9000*100000)
                        ];
                    })
                    ->addColumn('action', function($data){
                        $button = '<a href='.route("salesperson.edit",$data->id).' class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>';
                        $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action='.route("salesperson.destroy",$data->id).'  method="POST" class="inline-el" data-swal="1">
                        '.method_field('DELETE').'
                        '.csrf_field().'
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';

                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('admin.salesperson.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.salesperson.add');
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
            $validated['sort_order'] = (User::max('sort_order'))+1;
        }
        $password = $validated['password'];
        $validated['password'] = bcrypt($validated['password']);
        $validated['dob']      = ($validated['dob']) ? $validated['dob'] : date('Y-m-d H:i:s');
        $validated['email_verified_at'] = Carbon::now();
        $salesperson              = User::create($validated);
        $salesperson->assignRole($validated['role']);
        $salesperson->notify(new WelcomeSalesPerson($salesperson->name,$salesperson->email, $password));
        return redirect()->route('salesperson.index')->with('success', 'Sales Person added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $salesperson
     * @return \Illuminate\Http\Response
     */
    public function show(User $salesperson)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $salesperson
     * @return \Illuminate\Http\Response
     */
    public function edit(User $salesperson)
    {
        return view('admin.salesperson.edit', compact('salesperson'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $salesperson
     * @return \Illuminate\Http\Response
     */
    public function update(UserFormRequest $request, User $salesperson)
    {
        $validated = $request->validated();
        if ($request->hasfile('avatar')) {
            Storage::disk('public')->delete($salesperson->avatar);
            $validated['avatar'] = $request->file('avatar')->store('user', 'public');
        } else {
            $validated['avatar'] = $validated['old_avatar'];
        }
        if ($validated['sort_order'] == '') {
            $validated['sort_order'] = (User::max('sort_order'))+1;
        }
        $validated['dob'] = ($validated['dob']) ? $validated['dob'] : date('Y-m-d H:i:s');
        $salesperson->update($validated);
        return redirect()->route('salesperson.index')->with('success', 'Sales Person updated successfully.');
    }

    public function trash()
    {
        $trashed = User::onlyTrashed()->role('sales_person')->get();
        return view('admin.salesperson.trash', compact('trashed'));
    }

    public function restore($id)
    {
        $salesperson = User::onlyTrashed()->findOrfail($id);
        $salesperson->restore();
        return redirect(route('salesperson.trash'))
            ->with('success', 'Sales Person restored successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $salesperson
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $salesperson)
    {
        $salesperson->delete();
        return redirect(route('salesperson.index'))
            ->with('success', 'Sales Person moved to deleted list.');
    }

    /**
     * @param  Int $id
     * @return redirect()
     */
    public function forcedelete($id)
    {
        $salesperson = User::withTrashed()->findOrFail($id);
        Storage::disk('public')->delete($salesperson->avatar);
        $salesperson->forceDelete();
        return redirect(route('salesperson.trash'))
            ->with('success', 'Sales Person deleted successfully.');
    }

    /**
     * @param Request
     */
    public function changePassword(Request $request, User $salesperson)
    {
        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
        ]);

        $salesperson->password = bcrypt($request->password);
        $salesperson->save();
        return response()->json(["message" => "Password changed successfully."], 200);

    }
}
