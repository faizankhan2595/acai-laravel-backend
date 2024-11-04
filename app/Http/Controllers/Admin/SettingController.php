<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * [show description]
     * @param  Request $request [description]
     * @return [view] [admin.setting]
     */
    public function show(Request $request)
    {
        $data    = [];
        $records = Setting::all();
        foreach ($records as $key => $record) {
            $data[$record->key] = $record->value;
        }
        return view('admin.setting', compact('data'));
    }

    public function locations(Request $request)
    {
        $data    = [];
        $records = DB::table('locations')->select('location_title', 'location_link','location_address')->get();

        return view('admin.locations', ['locations' => $records]);
    }

    public function save(Request $request)
    {
        $files  = $this->processFiles($request);
        $inputs = array_merge($request->except('_token'), $files);
        DB::table('settings')->truncate();
        foreach ($inputs as $i => $input) {
            $setting        = new Setting;
            $setting->key   = $i;
            $setting->value = $input;
            $setting->save();
        }
        return redirect()->route('setting.show')->with('success', 'Setting saved successfully.');
    }

    public function saveLocations(Request $request)
    {
        $inputs = $request->except('_token');
        DB::table('locations')->truncate();
        foreach ($inputs['location_title'] as $i => $input) {
            if (!is_null($inputs['location_title'][$i])) {
                DB::table('locations')->insert([
                    'location_title'   => $inputs['location_title'][$i],
                    'location_address' => $inputs['location_address'][$i],
                    'location_link'    => $inputs['location_link'][$i],
                ]);
            }

        }
        return redirect()->route('locations.show')->with('success', 'Locations saved successfully.');
    }

    protected function processFiles($request)
    {
        $returnary = [];
        $allowed   = new Collection(['pdf', 'jpg', 'jpeg', 'png']);
        if (count($request->files)) {
            foreach ($request->files as $key => $file) {
                $ext = $request->file($key)->extension();
                Storage::disk('public')->delete($request->input($key));
                if ($allowed->contains($ext)) {
                    $filename        = $request->file($key)->store('setting', 'public');
                    $returnary[$key] = $filename;
                }
            }
        }
        return $returnary;
    }
}
