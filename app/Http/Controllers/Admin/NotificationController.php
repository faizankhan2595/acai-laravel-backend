<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\GeneralNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function create()
    {
        return view('admin.notification.add');
    }

    public function store(Request $request)
    {
        $validate = $this->validate($request, [
            'notification_title' => 'required',
            'notification_desc'  => 'required',
        ]);
        if ($request->hasFile('image')) {
            $file = $request->file('image')->store('notification', 'public');
        }
        $notification = [
            'title'   => $request->notification_title,
            'message' => $request->notification_desc,
            'image'   => (isset($file)) ? url('/').Storage::url($file) : null,
        ];
        if (isset($request->purple_customers) || isset($request->gold_customers)) {
            $customers = User::role('user')->whereExists(function($query) {
                 $query->from('device_tokens')
                       ->whereColumn('user_id', 'users.id');
               })->get();
            foreach ($customers as $key => $customer) {
                if (isset($request->purple_customers) && !isset($request->gold_customers)) {
                    if ($customer->membership(true) == 1) {
                        $customer->notify(new GeneralNotification($notification));
                    }
                }
                else if (!isset($request->purple_customers) && isset($request->gold_customers)) {
                    if ($customer->membership(true) == 2) {
                        $customer->notify(new GeneralNotification($notification));
                    }
                }
                else{
                    $customer->notify(new GeneralNotification($notification));
                }
            }
        }

        if (isset($request->all_merchnats)) {
            $merchants = User::role('merchant')->get();
            foreach ($merchants as $key => $merchant) {
                $merchant->notify(new GeneralNotification($notification));
            }
        }

        if (isset($request->all_sales_persons)) {
            $sales_persons = User::role('sales_person')->get();
            foreach ($sales_persons as $key => $sales_person) {
                $sales_person->notify(new GeneralNotification($notification));
            }
        }

        return redirect(route('notification.create'))
            ->with('success', 'Notification sent successfully.');
    }
}
