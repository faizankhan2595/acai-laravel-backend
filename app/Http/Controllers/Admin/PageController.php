<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PageFormRequest;
use App\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DataTables;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $pages = Page::all();
    //     return view('admin.cms.list',compact('pages'));
    // }

    public function index(Request $request)
    {
        if($request->ajax())
        {
            $data = Page::all();
            return DataTables::of($data)
                    ->addColumn('action', function($data){
                        $button = '<a href='.route("page.edit",$data->id).' class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>';
                        $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action='.route("page.destroy",$data->id).'  method="POST" class="inline-el" data-swal="1">
                        '.method_field('DELETE').'
                        '.csrf_field().'
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';

                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('admin.cms.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cms.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageFormRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['title']);
        $page = new Page;
        $page->create($validated);
        return redirect(route('page.index'))
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function privacy(Request $request)
    {
        $data = Page::where('slug','privacy-policy')->first();
        return view('privacy',compact('data'));
    }

    public function terms(Request $request)
    {
        $data = Page::where('slug','terms-conditions')->first();
        return view('terms',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        return view('admin.cms.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(PageFormRequest $request, Page $page)
    {
        $validated = $request->validated();
        $page->update($validated);
        return redirect(route('page.index'))
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $page->delete();
                return redirect(route('page.index'))
                    ->with('success', 'Page deleted successfully.');
    }
}
