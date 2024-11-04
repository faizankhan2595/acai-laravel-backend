<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RewardVoucherFromRequest;
use App\RewardVoucher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RewardVoucherController extends Controller
{
    protected $merchantslist;

    public function __construct()
    {
        $this->merchantslist = User::role('merchant')->get();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vouchers = RewardVoucher::with('merchant')->where('is_special_voucher',0)->latest()->get();
        return view('admin.voucher.list', compact('vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $merchants = $this->merchantslist;
        $validFor = (new RewardVoucher())->validFor;
        return view('admin.voucher.add', compact('merchants','validFor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RewardVoucherFromRequest $request)
    {
        $validated          = $request->validated();
        $validated['image'] = null;
        if ($request->hasfile('image')) {
            $validated['image'] = $request->file('image')->store('vouchers', 'public');
        }
        $validated['is_featured'] = (isset($validated['is_featured'])) ? 1 : 0;
        $voucher = RewardVoucher::create($validated);
        return redirect()->route('voucher.index')->with('success', 'Voucher added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RewardVoucher  $rewardVoucher
     * @return \Illuminate\Http\Response
     */
    public function show(RewardVoucher $rewardVoucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RewardVoucher  $rewardVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit(RewardVoucher $voucher)
    {
        $merchants = $this->merchantslist;
        return view('admin.voucher.edit', compact('merchants', 'voucher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RewardVoucher  $rewardVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(RewardVoucherFromRequest $request, RewardVoucher $voucher)
    {
        $validated          = $request->validated();
        if ($request->hasfile('image')) {
            Storage::disk('public')->delete($voucher->image);
            $validated['image'] = $request->file('image')->store('vouchers', 'public');
        }
        else {
                $validated['image'] = $validated['old_image'];
            }
        $validated['is_featured'] = (isset($validated['is_featured'])) ? 1 : 0;
        $validated['terms'] = (isset($validated['terms'])) ? $validated['terms'] : NULL;
        $voucher->update($validated);
        return redirect()->route('voucher.index')->with('success', 'Voucher updated successfully.');
    }

    public function trash()
    {
        $trashed = RewardVoucher::onlyTrashed()->where('is_special_voucher',0)->get();
        return view('admin.voucher.trash', compact('trashed'));
    }
    public function restore($id)
    {
        $voucher = RewardVoucher::onlyTrashed()->findOrfail($id);
        $voucher->restore();
        return redirect(route('voucher.trash'))
            ->with('success', 'Voucher restored successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(RewardVoucher $voucher)
    {
        $voucher->delete();
        return redirect(route('voucher.index'))
            ->with('success', 'Voucher moved to trash.');
    }

    public function forcedelete($id)
    {
        $voucher = RewardVoucher::withTrashed()->findOrFail($id);
        Storage::delete($voucher->image);
        $voucher->forceDelete();
        return redirect(route('voucher.trash'))
            ->with('success', 'Voucher deleted successfully.');
    }
}
