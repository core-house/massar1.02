<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\Authorization\Models\Permission;
use Modules\Branches\Models\Branch;
use Modules\Progress\Models\ProjectProgress;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Users')->only(['index', 'show']);
        $this->middleware('can:create Users')->only(['create', 'store']);
        $this->middleware('can:edit Users')->only(['update', 'edit']);
        $this->middleware('can:delete Users')->only(['destroy']);
    }

    public function index()
    {
        $users = User::with('permissions')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        try {
            $hasOptionType = Schema::hasColumn('permissions', 'option_type');

            if ($hasOptionType) {
                $permissions = Permission::where('option_type', '1')
                    ->get()
                    ->groupBy('category');
                $selectivePermissions = Permission::where('option_type', '2')
                    ->get()
                    ->groupBy('category');
            } else {
                $permissions = Permission::whereNotNull('category')
                    ->get()
                    ->groupBy('category');
                $selectivePermissions = collect();
            }

            $branches = Branch::where('is_active', 1)->get();

            return view('users.create', compact('permissions', 'selectivePermissions', 'branches'));
        } catch (\Exception) {
            Alert::toast('حدث خطأ أثناء تحميل صفحة إنشاء المستخدم', 'error');

            return redirect()->route('users.index');
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create($request->validated());
            if ($request->filled('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
                $user->givePermissionTo($permissions);
            }
            if ($request->filled('branches')) {
                $user->branches()->sync($request->branches);
            }
            Alert::toast('تم إنشاء المستخدم بنجاح', 'success');

            return redirect()->route('users.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ أثناء إنشاء المستخدم: ', 'error');

            return redirect()->back()->withInput();
        }
    }

    public function show(User $user)
    {
        $user->load(['permissions', 'branches', 'roles']);
        $permissions = Permission::all()->groupBy('category');

        // جلب المشاريع المرتبطة بالمستخدم من خلال الموظف
        $userProjects = collect();
        if ($user->employee) {
            $userProjects = ProjectProgress::whereHas('employees', function ($query) use ($user) {
                $query->where('employees.id', $user->employee->id);
            })->with(['client', 'type'])->get();
        }

        return view('users.show', compact('user', 'permissions', 'userProjects'));
    }

    public function edit(User $user)
    {
        $hasOptionType = Schema::hasColumn('permissions', 'option_type');

        if ($hasOptionType) {
            $permissions = Permission::where('option_type', '1')
                ->whereNotNull('category')
                ->get()
                ->groupBy('category');

            $selectivePermissions = Permission::where('option_type', '2')
                ->get()
                ->groupBy('category');
        } else {
            $permissions = Permission::whereNotNull('category')
                ->get()
                ->groupBy('category');

            $selectivePermissions = collect();
        }

        $branches = Branch::where('is_active', 1)->get();

        // ✅ الطريقة الأولى (بدون eager loading)
        $userPermissions = $user->permissions->pluck('id')->toArray();
        $userBranches = $user->branches->pluck('id')->toArray();

        // أو الطريقة الثانية (مع query مباشر)
        // $userPermissions = $user->permissions()->pluck('id')->toArray();
        // $userBranches = $user->branches()->pluck('id')->toArray();

        return view('users.edit', compact(
            'user',
            'permissions',
            'userPermissions',
            'selectivePermissions',
            'branches',
            'userBranches'
        ));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|confirmed|min:6',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
            'branches' => 'nullable|array',
            'branches.*' => 'integer|exists:branches,id',
        ]);

        try {
            $data = $request->only('name', 'email');

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            // تزامن الصلاحيات - الاحتفاظ بالصلاحيات التي لا تندرج تحت النوعين 1 و 2
            $submittedIds = $request->input('permissions', []);
            $submittedPermissions = Permission::whereIn('id', $submittedIds)->get();

            $hasOptionType = Schema::hasColumn('permissions', 'option_type');

            if ($hasOptionType) {
                // نحدد الأنواع التي يديرها هذا النموذج (1: صلاحيات، 2: خيارات)
                $typesToUpdate = ['1', '2'];

                // إعادة تحميل المستخدم للحصول على الصلاحيات الحالية المحدثة
                $user->refresh();

                // جلب الصلاحيات الحالية للمستخدم (تتضمن option_type)
                $currentPermissions = $user->permissions()->get();

                // الاحتفاظ بالصلاحيات التي لا تندرج تحت الأنواع التي نقوم بتحديثها
                $permissionsToKeep = $currentPermissions->filter(function ($permission) use ($typesToUpdate) {
                    $optionType = $permission->option_type ?? null;
                    if ($optionType === null) {
                        return false;
                    }

                    return ! in_array((string) $optionType, $typesToUpdate);
                })->pluck('name')->toArray();

                // دمج الصلاحيات المحفوظة مع الصلاحيات الجديدة (التي تم إرسالها)
                $submittedPermissionNames = $submittedPermissions->pluck('name')->toArray();
                $allPermissions = array_unique(array_merge($permissionsToKeep, $submittedPermissionNames));

                $user->syncPermissions($allPermissions);
            } else {
                $user->syncPermissions($submittedPermissions->pluck('name')->toArray());
            }

            // تزامن الفروع
            if ($request->filled('branches')) {
                $user->branches()->sync($request->branches);
            } else {
                $user->branches()->sync([]);
            }

            Alert::toast('تم تحديث المستخدم بنجاح', 'success');

            return redirect()->route('users.index');
        } catch (\Exception $e) {
            Log::error('User update failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Alert::toast('حدث خطأ أثناء تحديث المستخدم: '.$e->getMessage(), 'error');

            return redirect()->back()->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            Alert::toast('تم حذف المستخدم بنجاح', 'success');

            return redirect()->route('users.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ أثناء حذف المستخدم: ', 'error');

            return redirect()->route('users.index');
        }
    }
}
