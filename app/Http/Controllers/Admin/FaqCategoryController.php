<?php

namespace App\Http\Controllers\Admin;

use App\FaqCategory;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class FaqCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $categories = FaqCategory::all();
    //     return view('admin.faqcategory.list', compact('categories'));
    // }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = FaqCategory::all();
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '<a href=' . route("faq-category.edit", $data->id) . ' class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>';
                    $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action=' . route("faq-category.destroy", $data->id) . '  method="POST" class="inline-el" data-swal="1">
                        ' . method_field('DELETE') . '
                        ' . csrf_field() . '
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';

                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.faqcategory.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.faqcategory.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_name' => 'required|unique:faq_categories',
            'status'        => 'required',
        ]);

        $category = new FaqCategory;
        $category->create($validatedData);
        return redirect(route('faq-category.index'))
            ->with('success', 'Faq Category added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(FaqCategory $faq_category)
    {
        return view('admin.faqcategory.edit', compact('faq_category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FaqCategory $faq_category, Request $request)
    {
        $validatedData = $request->validate([
            'category_name' => ['required', Rule::unique('faq_categories', 'category_name')->ignore($faq_category)],
            'status'        => 'required',
        ]);
        $faq_category->update($validatedData);
        return redirect(route('faq-category.index'))
            ->with('success', 'Faq Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(FaqCategory $faq_category)
    {
        try {
            $faq_category->delete();
            return redirect(route('faq-category.index'))
                ->with('success', 'Faq Category deleted successfully.');
        } catch (\Illuminate\Database\QueryException $ex) {
            if ($ex->getCode() === '23000') {
                return Redirect::back()->withErrors(['Can\'t delete category because some Faqs already attached to this category.']);
            }
        }
    }
}
