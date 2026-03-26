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

            Alert::toast(__('Settings updated successfully'), 'success');
            return redirect()->back();
        } catch (\Exception) {
            Alert::toast(__('An error occurred while updating settings'), 'error');
            return redirect()->back();
        }
    }
}
