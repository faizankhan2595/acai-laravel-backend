<?php

namespace App\Http\Controllers\Admin;

use App\MailTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MailTemplateController extends Controller
{
    public function index()
    {
        $templates = MailTemplate::all();
        return view('admin.email.list',compact('templates'));
    }

    public function edit(Request $request)
    {
        $email = MailTemplate::findOrfail($request->email);
        return view('admin.email.edit',compact('email'));
    }

    public function update(Request $request,MailTemplate $email)
    {
        $email->subject = $request->subject;
        $email->content = $request->content;
        $email->save();
        return redirect(route('email.index'))
            ->with('success', 'Email Template updated successfully.');
    }
}
