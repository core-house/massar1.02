<?php

namespace Modules\CRM\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Modules\CRM\Enums\{TaskPriorityEnum, TaskStatusEnum};
use Modules\CRM\Http\Requests\TaskRequest;
use Modules\CRM\Models\{Task, TaskType};
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Tasks')->only(['index', 'show']);
        $this->middleware('can:create Tasks')->only(['create', 'store']);
        $this->middleware('can:edit Tasks')->only(['edit', 'update']);
        $this->middleware('can:delete Tasks')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Task::with(['client', 'user', 'taskType', 'media', 'creator']);

        // Check if user has permission to view all tasks
        if (!auth()->user()->can('allow_view_all_tasks')) {
            // User can only see tasks assigned to them or created by them
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('created_by', auth()->id());
            });
        }

        // Default: Show only today's tasks (by start_date)
        if (!$request->has('date_filter') || $request->date_filter === 'today') {
            $query->whereDate('start_date', today());
        } elseif ($request->date_filter === 'all') {
            // Show all tasks (no date filter)
        } elseif ($request->date_filter === 'week') {
            $query->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->date_filter === 'month') {
            $query->whereMonth('start_date', now()->month)
                  ->whereYear('start_date', now()->year);
        } elseif ($request->date_filter === 'overdue') {
            $query->where('due_date', '<', today())
                  ->whereNotIn('status', ['completed', 'cancelled']);
        } elseif ($request->date_filter === 'upcoming') {
            $query->where('start_date', '>', today());
        }

        // Filter by task type
        if ($request->filled('task_type_id')) {
            $query->where('task_type_id', $request->task_type_id);
        }

        // Filter by assigned user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by priority (multiple values)
        if ($request->filled('priority')) {
            $priorities = is_array($request->priority) ? $request->priority : [$request->priority];
            $query->whereIn('priority', $priorities);
        }

        // Filter by status (multiple values)
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('due_date', '<=', $request->end_date);
        }

        $tasks = $query->latest('start_date')->paginate(20)->withQueryString();

        // Get filter options
        $taskTypes = TaskType::pluck('title', 'id');
        $users = \App\Models\User::pluck('name', 'id');
        $priorities = \Modules\CRM\Enums\TaskPriorityEnum::cases();
        $statuses = \Modules\CRM\Enums\TaskStatusEnum::cases();

        return view('crm::tasks.index', compact('tasks', 'taskTypes', 'users', 'priorities', 'statuses'));
    }

    public function create()
    {
        $branches = userBranches();
        $taskTypes = TaskType::pluck('title', 'id');
        $users = User::pluck('name', 'id');
        $priorities = TaskPriorityEnum::cases();
        $statuses = TaskStatusEnum::cases();

        return view('crm::tasks.create',  get_defined_vars());
    }

    public function store(TaskRequest $request)
    {
        $data = $request->validated();

        // Check if sending to all users
        if ($request->has('send_to_all_users') && $request->send_to_all_users == '1') {
            // Remove send_to_all_users from data
            unset($data['send_to_all_users']);
            
            // Set user_id to first user if not set
            if (empty($data['user_id'])) {
                $data['user_id'] = User::first()->id ?? auth()->id();
            }

            // Create single task
            $task = Task::create($data);

            // Handle multiple attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $task->addMedia($file)->toMediaCollection('tasks');
                }
            }

            // Get all users
            $users = User::all();

            // Send notification to all users
            \Illuminate\Support\Facades\Notification::send($users, new \Modules\Notifications\Notifications\GeneralNotification(
                title: 'تم إنشاء مهمة جديدة',
                message: 'تم إنشاء مهمة جديدة "' . $task->title . '". تاريخ الاستحقاق: ' . ($task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : 'غير محدد'),
                url: route('tasks.index'),
                type: 'info',
                icon: 'las la-tasks'
            ));

            Alert::toast('تم إنشاء المهمة وإرسال إشعار لجميع المستخدمين', 'success');
        } else {
            // Remove send_to_all_users from data
            unset($data['send_to_all_users']);

            // Create single task for selected user
            $task = Task::create($data);

            // Handle multiple attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $task->addMedia($file)->toMediaCollection('tasks');
                }
            }

            Alert::toast('تم إنشاء المهمة بنجاح', 'success');
        }

        return redirect()->route('tasks.index');
    }
    public function show(Task $task)
    {
        $task->load(['client', 'user', 'taskType', 'media', 'creator']);
        return view('crm::tasks.show', compact('task'));
    }
    public function edit(Task $task)
    {
        $taskTypes = TaskType::pluck('title', 'id');
        $users = User::pluck('name', 'id');
        $priorities = TaskPriorityEnum::cases();
        $statuses = TaskStatusEnum::cases();

        return view('crm::tasks.edit', compact('task', 'taskTypes', 'users', 'priorities', 'statuses'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        try {
            $task->update($request->validated());

            // Handle multiple attachments
            if ($request->hasFile('attachments')) {
                // Optionally clear old attachments if needed
                // $task->clearMediaCollection('tasks');
                
                foreach ($request->file('attachments') as $file) {
                    $task->addMedia($file)->toMediaCollection('tasks');
                }
            }

            Alert::toast(__('crm::crm.task_updated_successfully'), 'success');
        } catch (\Exception) {
            Alert::toast(__('crm::crm.error_updating_task'), 'error');
        }

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        try {
            $task->delete(); // Soft delete
            Alert::toast(__('crm::crm.task_deleted_successfully'), 'success');
        } catch (\Exception) {
            Alert::toast(__('crm::crm.error_deleting_task'), 'error');
        }

        return redirect()->route('tasks.index');
    }

    /**
     * Display trashed (soft deleted) tasks
     */
    public function trashed(Request $request)
    {
        $query = Task::onlyTrashed()->with(['client', 'user', 'taskType', 'media', 'creator']);

        // Check if user has permission to view all tasks
        if (!auth()->user()->can('allow_view_all_tasks')) {
            // User can only see tasks assigned to them or created by them
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('created_by', auth()->id());
            });
        }

        // Filter by task type
        if ($request->filled('task_type_id')) {
            $query->where('task_type_id', $request->task_type_id);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('client_comment', 'like', "%{$request->search}%")
                  ->orWhere('user_comment', 'like', "%{$request->search}%");
            });
        }

        $tasks = $query->latest('deleted_at')->paginate(20)->withQueryString();

        // Get filter options
        $taskTypes = TaskType::pluck('title', 'id');
        $priorities = \Modules\CRM\Enums\TaskPriorityEnum::cases();
        $statuses = \Modules\CRM\Enums\TaskStatusEnum::cases();

        return view('crm::tasks.trashed', compact('tasks', 'taskTypes', 'priorities', 'statuses'));
    }

    /**
     * Restore a soft deleted task
     */
    public function restore($id)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($id);
            $task->restore();
            Alert::toast(__('crm::crm.task_restored_successfully'), 'success');
        } catch (\Exception) {
            Alert::toast(__('crm::crm.error_restoring_task'), 'error');
        }

        return redirect()->route('tasks.trashed');
    }

    /**
     * Permanently delete a task
     */
    public function forceDelete($id)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($id);
            $task->forceDelete();
            Alert::toast(__('crm::crm.task_permanently_deleted'), 'success');
        } catch (\Exception) {
            Alert::toast(__('crm::crm.error_permanently_deleting_task'), 'error');
        }

        return redirect()->route('tasks.trashed');
    }

    public function kanban(Request $request)
    {
        $query = Task::with(['client', 'user', 'taskType', 'creator']);

        // Check if user has permission to view all tasks
        if (!auth()->user()->can('allow_view_all_tasks')) {
            // User can only see tasks assigned to them or created by them
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('created_by', auth()->id());
            });
        }

        // Default: Show only today's tasks (by start_date)
        if (!$request->has('date_filter') || $request->date_filter === 'today') {
            $query->whereDate('start_date', today());
        } elseif ($request->date_filter === 'all') {
            // Show all tasks (no date filter)
        } elseif ($request->date_filter === 'week') {
            $query->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->date_filter === 'month') {
            $query->whereMonth('start_date', now()->month)
                  ->whereYear('start_date', now()->year);
        } elseif ($request->date_filter === 'overdue') {
            $query->where('due_date', '<', today())
                  ->whereNotIn('status', ['completed', 'cancelled']);
        } elseif ($request->date_filter === 'upcoming') {
            $query->where('start_date', '>', today());
        }

        // Filter by task type
        if ($request->filled('task_type_id')) {
            $query->where('task_type_id', $request->task_type_id);
        }

        // Filter by assigned user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by priority (multiple values)
        if ($request->filled('priority')) {
            $priorities = is_array($request->priority) ? $request->priority : [$request->priority];
            $query->whereIn('priority', $priorities);
        }

        // Filter by status (multiple values)
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('due_date', '<=', $request->end_date);
        }

        $tasks = $query->get();
        $statuses = TaskStatusEnum::cases();

        // Get filter options
        $taskTypes = TaskType::pluck('title', 'id');
        $users = \App\Models\User::pluck('name', 'id');
        $priorities = TaskPriorityEnum::cases();

        return view('crm::tasks.kanban', compact('tasks', 'statuses', 'taskTypes', 'users', 'priorities'));
    }

    public function timeline(Request $request)
    {
        $query = Task::with(['client', 'user', 'taskType', 'creator']);

        // Check if user has permission to view all tasks
        if (!auth()->user()->can('allow_view_all_tasks')) {
            // User can only see tasks assigned to them or created by them
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('created_by', auth()->id());
            });
        }

        // Default: Show only today's tasks (by start_date)
        if (!$request->has('date_filter') || $request->date_filter === 'today') {
            $query->whereDate('start_date', today());
        } elseif ($request->date_filter === 'all') {
            // Show all tasks (no date filter)
        } elseif ($request->date_filter === 'week') {
            $query->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->date_filter === 'month') {
            $query->whereMonth('start_date', now()->month)
                  ->whereYear('start_date', now()->year);
        } elseif ($request->date_filter === 'overdue') {
            $query->where('due_date', '<', today())
                  ->whereNotIn('status', ['completed', 'cancelled']);
        } elseif ($request->date_filter === 'upcoming') {
            $query->where('start_date', '>', today());
        }

        // Filter by task type
        if ($request->filled('task_type_id')) {
            $query->where('task_type_id', $request->task_type_id);
        }

        // Filter by assigned user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by priority (multiple values)
        if ($request->filled('priority')) {
            $priorities = is_array($request->priority) ? $request->priority : [$request->priority];
            $query->whereIn('priority', $priorities);
        }

        // Filter by status (multiple values)
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('due_date', '<=', $request->end_date);
        }

        // Order by start_date and time
        $tasks = $query->orderBy('start_date', 'asc')->orderBy('created_at', 'asc')->get();

        // Group tasks by date
        $tasksByDate = $tasks->groupBy(function($task) {
            return \Carbon\Carbon::parse($task->start_date)->format('Y-m-d');
        });

        // Get filter options
        $taskTypes = TaskType::pluck('title', 'id');
        $users = \App\Models\User::pluck('name', 'id');
        $priorities = TaskPriorityEnum::cases();
        $statuses = TaskStatusEnum::cases();

        return view('crm::tasks.timeline', compact('tasksByDate', 'taskTypes', 'users', 'priorities', 'statuses'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'status' => 'required',
        ]);

        $task = Task::findOrFail($request->task_id);
        $task->status = $request->status;
        $task->save();

        return response()->json(['success' => true, 'message' => __('crm::crm.task_status_updated_successfully')]);
    }
}
