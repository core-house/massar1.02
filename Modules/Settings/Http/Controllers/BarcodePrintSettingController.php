<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Settings\Models\BarcodePrintSetting;
use Modules\Settings\Http\Requests\BarcodePrintSettingRequest;
use RealRashid\SweetAlert\Facades\Alert;

class BarcodePrintSettingController extends Controller
{
    /**
     * Apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:view Barcode Settings')->only(['edit']);
        $this->middleware('permission:edit Barcode Settings')->only(['update']);
    }

    /**
     * Show the form for editing barcode print settings
     */
    public function edit()
    {
        $settings = BarcodePrintSetting::where('is_default', true)->firstOrFail();
        return view('settings::barcode-setting.edit', compact('settings'));
    }

    /**
     * Update barcode print settings
     */
    public function update(BarcodePrintSettingRequest $request)
    {
        try {
            $settings = BarcodePrintSetting::where('is_default', true)->firstOrFail();

            $settings->update($request->validated());

            Alert::toast(__('settings::settings.settings_updated_successfully'), 'success');
            return redirect()->back();
        } catch (\Exception) {
            Alert::toast(__('settings::settings.error_updating_settings'), 'error');
            return redirect()->back();
        }
    }
}
