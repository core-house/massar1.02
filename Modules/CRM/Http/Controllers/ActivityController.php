<?php

namespace Modules\CRM\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Modules\CRM\Models\Activity;
use RealRashid\SweetAlert\Facades\Alert;
use Modules\CRM\Http\Requests\ActivityRequest;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view Activities')->only(['index', 'show']);
        $this->middleware('permission:create Activities')->only(['create', 'store']);
        $this->middleware('permission:edit Activities')->only(['edit', 'update']);
        $this->middleware('permission:delete Activities')->only(['destroy']);
    }

    public function index()
    {
        $activities = Activity::with(['client', 'assignedUser'])->latest()->paginate(20);
        return view('crm::activities.index', compact('activities'));
    }

    public function create()
    {
        $branches = userBranches();
        $users = User::pluck('name', 'id');
        return view('crm::activities.create', compact('users', 'branches'));
    }

    public function store(ActivityRequest $request)
    {
        try {
            Activity::create($request->validated());
            Alert::toast(__('crm::crm.activity_added_successfully'), 'success');
            return redirect()->route('activities.index');
        } catch (\Exception) {

            Alert::toast(__('crm::crm.error_processing_data'), 'error');
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        // $activity = Activity::findOrFail($id);
        // return view('crm::activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        $users = User::pluck('name', 'id');
        return view('crm::activities.edit', compact('activity', 'users'));
    }

    public function update(ActivityRequest $request, Activity $activity)
    {
        try {
            $activity->update($request->validated());
            Alert::toast(__('crm::crm.activity_updated_successfully'), 'success');
            return redirect()->route('activities.index');
        } catch (\Exception) {
            Alert::toast(__('crm::crm.error_processing_data'), 'error');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Activity $activity)
    {
        try {
            $activity->delete();
            Alert::toast(__('crm::crm.activity_deleted_successfully'), 'success');
        } catch (\Exception) {
            Alert::toast(__('crm::crm.error_deleting_activity'), 'error');
        }
        return redirect()->route('activities.index');
    }
}
