<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\GeneralNotification;
use App\Jobs\SendBatchNotificationsNew;
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
        // Same validation
        $validate = $this->validate($request, [
            'notification_title' => 'required',
            'notification_desc'  => 'required',
        ]);

        // Same image handling logic
        $file = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image')->store('notification', 'public');
        }

        // Exactly same notification array structure
        $notification = [
            'title'   => $request->notification_title,
            'message' => $request->notification_desc,
            'image'   => (isset($file)) ? url('/').Storage::url($file) : null, // Kept the isset() check exactly as original
        ];

        // Customer notifications with exact same logic but chunked processing
        if (isset($request->purple_customers) || isset($request->gold_customers)) {
            // Same query as original
            $query = User::role('user')->whereExists(function($query) {
                $query->from('device_tokens')
                    ->whereColumn('user_id', 'users.id');
            });

            // Process in chunks to prevent timeout
            $query->chunk(100, function($customers) use ($request, $notification) {
                foreach ($customers as $key => $customer) {
                    // Exactly same conditions as original
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
                    else {
                        $customer->notify(new GeneralNotification($notification));
                    }
                }
            });
        }

        // Merchant notifications with same logic but chunked
        if (isset($request->all_merchnats)) {
            User::role('merchant')
                ->chunk(100, function($merchants) use ($notification) {
                    foreach ($merchants as $key => $merchant) {
                        $merchant->notify(new GeneralNotification($notification));
                    }
                });
        }

        // Sales person notifications with same logic but chunked
        if (isset($request->all_sales_persons)) {
            User::role('sales_person')
                ->chunk(100, function($sales_persons) use ($notification) {
                    foreach ($sales_persons as $key => $sales_person) {
                        $sales_person->notify(new GeneralNotification($notification));
                    }
                });
        }

        // Same redirect and success message
        return redirect(route('notification.create'))
            ->with('success', 'Notification sent successfully.');
    }
}
