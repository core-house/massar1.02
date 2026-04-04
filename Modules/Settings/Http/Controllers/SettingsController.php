<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Settings\Models\Category;
use Modules\Settings\Models\PublicSetting;
use RealRashid\SweetAlert\Facades\Alert;

class SettingsController extends Controller
{
    /**
     * Apply permissions middleware
     */
    // public function __construct()
    // {
    //     $this->middleware('permission:view General Settings')->only(['index']);
    //     $this->middleware('permission:edit General Settings')->only(['update']);
    // }

    /**
     * Display general settings page
     */
    public function index()
    {
        $categories = Category::with('publicSettings')->get();
        $publicSettings = PublicSetting::with('category')->get();
        return view('settings::settings.index', get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('settings::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('settings::edit');
    }


    public function update(Request $request)
    {
        try {
            $settings = $request->input('settings', []);
            $removeFiles = $request->input('remove_files', []);

            // Handle file removals
            foreach ($removeFiles as $key => $shouldRemove) {
                if ($shouldRemove) {
                    $setting = PublicSetting::where('key', $key)->first();
                    if ($setting && $setting->value) {
                        Storage::disk('public')->delete($setting->value);
                        $setting->update(['value' => '']);
                    }
                }
            }

            // Handle file uploads
            $uploadedFiles = $request->file('files', []);
            foreach ($uploadedFiles as $key => $file) {
                $request->validate([
                    "files.{$key}" => 'image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
                ]);

                $setting = PublicSetting::where('key', $key)->first();
                if (!$setting) {
                    continue;
                }

                // Delete old file if exists
                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }

                // Store new file
                $path = $file->store('company', 'public');
                $setting->update(['value' => $path]);

                // Remove from text settings so it doesn't get overwritten below
                unset($settings[$key]);
            }

            // Handle text/boolean settings
            if (!empty($settings)) {
                foreach ($settings as $key => $value) {
                    PublicSetting::where('key', $key)->update([
                        'value' => is_array($value) ? json_encode($value) : trim((string) ($value ?? ''))
                    ]);
                }
            }

            // Clear settings cache
            Cache::forget('public_settings');

            Alert::toast(__('settings::settings.settings_updated_successfully'), 'success');
            return redirect()->back();
        } catch (\Exception) {
            Alert::toast(__('settings::settings.error_updating_settings'), 'error');
            return redirect()->back();
        }
    }
}
