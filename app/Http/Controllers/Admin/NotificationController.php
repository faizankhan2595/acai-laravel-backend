<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\GeneralNotification;
use App\Jobs\SendBatchNotifications;
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

        // Handle image upload
        $file = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image')->store('notification', 'public');
        }

        // Prepare notification data
        $notification = [
            'title'   => $request->notification_title,
            'message' => $request->notification_desc,
            'image'   => $file ? url('/').Storage::url($file) : null,
        ];

        // Handle customer notifications
        if (isset($request->purple_customers) || isset($request->gold_customers)) {
            $query = User::role('user')->whereExists(function($query) {
                $query->from('device_tokens')
                      ->whereColumn('user_id', 'users.id');
            });

            // Apply membership filters
            if (isset($request->purple_customers) && !isset($request->gold_customers)) {
                $query->whereHas('membership', function($q) {
                    $q->where('type', 1);
                });
            } elseif (!isset($request->purple_customers) && isset($request->gold_customers)) {
                $query->whereHas('membership', function($q) {
                    $q->where('type', 2);
                });
            }

            // Process in chunks
            $query->chunk(100, function($users) use ($notification) {
                dispatch(new SendNotifications($users->pluck('id')->toArray(), $notification));
            });
        }

        // Handle merchant notifications
        if (isset($request->all_merchnats)) {
            User::role('merchant')->chunk(100, function($users) use ($notification) {
                dispatch(new SendNotifications($users->pluck('id')->toArray(), $notification));
            });
        }

        // Handle sales person notifications
        if (isset($request->all_sales_persons)) {
            User::role('sales_person')->chunk(100, function($users) use ($notification) {
                dispatch(new SendNotifications($users->pluck('id')->toArray(), $notification));
            });
        }

        return redirect(route('notification.create'))
            ->with('success', 'Notifications are being sent in the background.');
    }
}
