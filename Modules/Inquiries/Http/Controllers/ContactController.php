<?php

namespace Modules\Inquiries\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Inquiries\Models\Contact;
use RealRashid\SweetAlert\Facades\Alert;
use Modules\Inquiries\Models\InquirieRole;
use Modules\Inquiries\Http\Requests\ContactRequest;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view Contacts')->only(['index']);
        $this->middleware('permission:create Contacts')->only(['create', 'store']);
        $this->middleware('permission:edit Contacts')->only(['edit', 'update']);
        $this->middleware('permission:delete Contacts')->only(['destroy']);
    }

    public function index()
    {
        $contacts = Contact::with(['role', 'parent', 'roles'])->get();
        return view('inquiries::contacts.index', compact('contacts'));
    }

    public function create()
    {
        $roles = InquirieRole::all();
        $parents = Contact::where('type', 'company')->get();
        return view('inquiries::contacts.create', compact('roles', 'parents'));
    }

    public function store(ContactRequest $request)
    {
        $contact = Contact::create($request->validated());

        // Sync roles if provided
        if ($request->has('roles')) {
            $contact->roles()->sync($request->roles);
        }

        Alert::toast(__('Item created successfully'), 'success');
        return redirect()->route('contacts.index');
    }

    public function show($id)
    {
        // return view('inquiries::contacts.show');
    }

    public function edit($id)
    {
        $contact = Contact::with('roles')->findOrFail($id);
        $roles = InquirieRole::all();
        $parents = Contact::where('type', 'company')->where('id', '!=', $id)->get();
        return view('inquiries::contacts.edit', compact('contact', 'roles', 'parents'));
    }

    public function update(ContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());

        // Sync roles if provided
        if ($request->has('roles')) {
            $contact->roles()->sync($request->roles);
        }

        Alert::toast(__('Item updated successfully'), 'success');
        return redirect()->route('contacts.index');
    }

    public function destroy(Contact $contact)
    {
        try {
            $contact->delete();
            Alert::toast(__('Item deleted successfully'), 'success');
        } catch (\Exception $e) {
            Alert::toast(__('An error occurred while deleting the item'), 'error');
        }

        return redirect()->route('contacts.index');
    }
}
