<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Authorization\Models\Role;
use App\Http\Requests\StoreUserRequest;
use Modules\Authorization\Models\Permission;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:عرض العملاء')->only(['index']);
    //     $this->middleware('can:إضافة العملاء')->only(['create', 'store']);
    //     $this->middleware('can:تعديل العملاء')->only(['update', 'edit']);
    //     $this->middleware('can:حذف العملاء')->only(['destroy']);
    // }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id');
        $permissions = Permission::all()->groupBy('category');
        return view('users.create', compact('roles', 'permissions'));
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create($request->validated());
            if ($request->filled('permissions')) {
                $user->givePermissionTo($request->permissions);
            }
            Alert::toast('تم إنشاء المستخدم بنجاح', 'success');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            Alert::toast('حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0];
        });
        $userPermissions = $user->permissions->pluck('name')->toArray();

        return view('users.edit', compact('user', 'roles', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:6',
            'roles'    => 'required|array',
        ]);

        $data = $request->only('name', 'email');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        Alert::toast('تم حذف المستخدم بنجاح', 'success');
        return redirect()->route('users.index');
    }
}
