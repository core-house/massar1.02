<?php

namespace Modules\Resources\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Resources\Models\Resource;
use Modules\Resources\Models\ResourceCategory;
use Modules\Resources\Models\ResourceStatus;
use Modules\Resources\Models\ResourceAssignment;

class ResourceDashboardController extends Controller
{
    public function index()
    {
        $totalResources = Resource::count();
        $activeResources = Resource::active()->count();
        
        $resourcesByCategory = Resource::selectRaw('resource_category_id, count(*) as count')
            ->with('category:id,name_ar')
            ->groupBy('resource_category_id')
            ->get();

        $resourcesByStatus = Resource::selectRaw('resource_status_id, count(*) as count')
            ->with('status:id,name_ar,color')
            ->groupBy('resource_status_id')
            ->get();

        $activeAssignments = ResourceAssignment::where('status', 'active')->count();
        $scheduledAssignments = ResourceAssignment::where('status', 'scheduled')->count();

        $upcomingMaintenance = Resource::whereNotNull('next_maintenance_date')
            ->whereDate('next_maintenance_date', '<=', now()->addDays(7))
            ->with(['category', 'type', 'status'])
            ->get();

        $recentAssignments = ResourceAssignment::with(['resource', 'project', 'assignedBy'])
            ->latest()
            ->limit(10)
            ->get();

        return view('resources::dashboard.index', compact(
            'totalResources',
            'activeResources',
            'resourcesByCategory',
            'resourcesByStatus',
            'activeAssignments',
            'scheduledAssignments',
            'upcomingMaintenance',
            'recentAssignments'
        ));
    }
}

