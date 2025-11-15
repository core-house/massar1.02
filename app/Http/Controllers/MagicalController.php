<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magical;

class MagicalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $magicals = Magical::all();
        return view("magicals.index", compact("magicals"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("magicals.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            'magic_name' => 'required|string|max:255',
            'magic_link' => 'required|string|max:255|unique:magicals,magic_link',
            'info' => 'string|max:255',
            'is_journal' => 'nullable',
            'type' => 'required|array',
            'option' => 'array',
            'name' => 'required|array',
            'value' => 'required|array',
            'placeholder' => 'required|array',
        ], [
            'magic_name.required' => __('validation.custom.magic_name.required'),
            'magic_name.max' => __('validation.custom.magic_name.max'),
            'magic_link.required' => __('validation.custom.magic_link.required'),
            'magic_link.max' => __('validation.custom.magic_link.max'),
            'magic_link.unique' => __('validation.custom.magic_link.unique'),
            'info.max' => __('validation.custom.info.max'),
            'type.required' => __('validation.custom.type.required'),
            'name.required' => __('validation.custom.name.required'),
            'value.required' => __('validation.custom.value.required'),
            'placeholder.required' => __('validation.custom.placeholder.required'),
        ]);

        $magical = \App\Models\Magical::create([
            'magic_name' => $request->magic_name,
            'magic_link' => $request->magic_link,
            'info' => $request->info,
            'is_journal' => $request->is_journal ? 1 : 0,
        ]);

        foreach ($request->type as $i => $type) {
            \App\Models\MagicaDet::create([
                'magical_id' => $magical->id,
                'type' => $type,
                'option' => $request->option[$i] ?? '',
                'name' => $request->name[$i] ?? '',
                'value' => $request->value[$i] ?? '',
                'placeholder' => $request->placeholder[$i] ?? '',
                'class' => '', // يمكنك تعديلها لاحقًا حسب الحاجة
            ]);
        }
        try {
            // All logic above already executed
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
        return redirect()->route('magicals.index')->with('success', 'Magical created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view("magicals.show", compact("magical"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view("magicals.edit", compact("magical"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'magic_name' => 'required|string|max:255',
            'magic_link' => 'required|string|max:255',
            'is_journal' => 'required|boolean',
        ]);
        Magical::find($id)->update($request->all());
        return redirect()->route('magicals.index')->with('success', 'Magical updated successfully');
    }

    /**
     * Remove the specified resource from storage.  
     */
    public function destroy(string $id)
    {
        Magical::find($id)->delete();
        return redirect()->route('magicals.index')->with('success', 'Magical deleted successfully');
    }
}
