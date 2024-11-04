<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Notifications\WelcomeUser;
use App\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $merchants = User::role('merchant')->get();
    //     return view('admin.merchant.list', compact('merchants'));
    // }
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $data = User::role('merchant')->latest()->get();
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
                        $button = '<a href='.route("merchant.edit",$data->id).' class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>';
                        $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action='.route("merchant.destroy",$data->id).'  method="POST" class="inline-el" data-swal="1">
                        '.method_field('DELETE').'
                        '.csrf_field().'
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';

                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('admin.merchant.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.merchant.add');
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
        $validated['password'] = bcrypt($password);
        $validated['dob']      = ($validated['dob']) ? $validated['dob'] : date('Y-m-d H:i:s');
        $validated['is_featured'] = (isset($validated['is_featured'])) ? 1 : 0;
        $validated['is_project_acai'] = (isset($validated['is_project_acai'])) ? 1 : 0;
        $validated['email_verified_at']      = Carbon::now();
        $merchant                  = User::create($validated);
        $merchant->assignRole($validated['role']);
        $merchant->notify(new WelcomeUser($merchant->name,$merchant->email, $password));
        return redirect()->route('merchant.index')->with('success', 'Merchant added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $merchant
     * @return \Illuminate\Http\Response
     */
    public function show(User $merchant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $merchant
     * @return \Illuminate\Http\Response
     */
    public function edit(User $merchant)
    {
        $center = $merchant->center;
        return view('admin.merchant.edit', compact('merchant','center'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $merchant
     * @return \Illuminate\Http\Response
     */
    public function update(UserFormRequest $request, User $merchant)
    {
        $validated = $request->validated();
        if ($request->hasfile('avatar')) {
            Storage::disk('public')->delete($merchant->avatar);
            $validated['avatar'] = $request->file('avatar')->store('user', 'public');
        } else {
            $validated['avatar'] = $validated['old_avatar'];
        }
        if ($validated['sort_order'] == '') {
            $validated['sort_order'] = (User::max('sort_order'))+1;
        }
        $validated['dob'] = ($validated['dob']) ? $validated['dob'] : date('Y-m-d H:i:s');
        $validated['is_featured'] = (isset($validated['is_featured'])) ? 1 : 0;
        $validated['is_project_acai'] = (isset($validated['is_project_acai'])) ? 1 : 0;
        $merchant->update($validated);
        return redirect()->route('merchant.index')->with('success', 'Merchant updated successfully.');
    }

    public function trash()
    {
        $trashed = User::onlyTrashed()->role('merchant')->get();
        return view('admin.merchant.trash', compact('trashed'));
    }

    public function restore($id)
    {
        $merchant = User::onlyTrashed()->findOrfail($id);
        $merchant->restore();
        return redirect(route('merchant.trash'))
            ->with('success', 'Merchant restored successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $merchant
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $merchant)
    {
        $merchant->delete();
        return redirect(route('merchant.index'))
            ->with('success', 'Merchant moved to deleted list.');
    }

    /**
     * @param  Int $id
     * @return redirect()
     */
    public function forcedelete($id)
    {
        $merchant = User::withTrashed()->findOrFail($id);
        Storage::disk('public')->delete($merchant->avatar);
        $merchant->forceDelete();
        return redirect(route('merchant.trash'))
            ->with('success', 'Merchant deleted successfully.');
    }

    /**
     * @param Request
     */
    public function changePassword(Request $request, User $merchant)
     {
        $this->validate($request,[
            'password' => 'required|confirmed|min:6'
        ]);

        $merchant->password = bcrypt($request->password);
        $merchant->save();
        return response()->json(["message" => "Password changed successfully."], 200);

     }

     public function savecenter(Request $request, User $merchant)
     {
        if ($merchant->center()->exists()) {
            $merchant->center()->update($request->except('_token'));
        }else{
            $merchant->center()->create($request->except('_token'));
        }
        return redirect(route('merchant.index'))
            ->with('success', 'Center saved successfully.');
     }
}
