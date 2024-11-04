<?php

namespace App\Http\Controllers\Admin;

use App\Faq;
use App\FaqCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;

class FaqController extends Controller
{
    protected $faqcategories;

    public function __construct()
    {
        $this->faqcategories = FaqCategory::all();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $faqs = Faq::with('faqcategory')->get();
    //     return view('admin.faq.list', compact('faqs'));
    // }
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $data = Faq::with('faqcategory')->get();
            return DataTables::of($data)
                    ->addColumn('action', function($data){
                        $button = '<button data-question="'.$data->question.'" data-answer="'.$data->answer.'" class="on-default text-info btn btn-xs btn-default"><i class="fa fa-eye"></i> View</button>';
                        $button .= '&nbsp;&nbsp;<a href='.route("faq.edit",$data->id).' class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>';
                        $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action='.route("faq.destroy",$data->id).'  method="POST" class="inline-el" data-swal="1">
                        '.method_field('DELETE').'
                        '.csrf_field().'
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';

                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('admin.faq.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $faqcategories = $this->faqcategories;
        return view('admin.faq.add', compact('faqcategories'));
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
            'faq_category_id' => 'required',
            'question'       => 'required',
            'answer'         => 'required',
            'status'         => 'required',
        ]);

        $faq = new Faq;
        $faq->create($validatedData);
        return redirect(route('faq.index'))
            ->with('success', 'Faq added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function show(Faq $faq)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function edit(Faq $faq)
    {
        $faqcategories = $this->faqcategories;
        return view('admin.faq.edit',compact('faq','faqcategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Faq $faq)
    {
        $validatedData = $request->validate([
            'faq_category_id' => 'required',
            'question'       => 'required',
            'answer'         => 'required',
            'status'         => 'required',
        ]);

        $faq->update($validatedData);
        return redirect(route('faq.index'))
            ->with('success', 'Faq updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect(route('faq.index'))
            ->with('success', 'Faq deleted successfully.');
    }
}
