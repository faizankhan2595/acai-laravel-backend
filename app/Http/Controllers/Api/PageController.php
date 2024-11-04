<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Page;
use App\Setting;
use App\FaqCategory;
use Illuminate\Http\Request;
class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pages()
    {
        $pages = [
            'privacy_policy'   => Page::where('slug', 'privacy-policy')->pluck('content')->first(),
            'terms_conditions' => Page::where('slug', 'terms-conditions')->pluck('content')->first(),
            'about_app'        => Page::where('slug', 'about-app')->pluck('content')->first(),
        ];

        $contact = [
            'email' => Setting::where('key', 'contact_email')->pluck('value')->first(),
            'website' => Setting::where('key', 'contact_site')->pluck('value')->first(),
        ];
        return response()->json(['pages' => $pages,'contact' => $contact],200);
    }

    public function faqs(Request $request)
    {
        if (!is_null($request->search) && !empty($request->search)) {
            $faqs = FaqCategory::with('faqs')->whereHas('faqs', function ($query) use ($request) {
                     $query->where('question', 'like', '%'.$request->search.'%');
                })->get();
        }else{
            $faqs = FaqCategory::with('faqs')->where('status',1)->get();
        }
        return response()->json(['data' => $faqs],200);
    }
}
