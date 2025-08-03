<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:عرض الموظفيين')->only(['index']);
        $this->middleware('can:إضافة الموظفيين')->only(['create', 'store']);
        $this->middleware('can:تعديل الموظفيين')->only(['edit', 'update']);
        $this->middleware('can:حذف الموظفيين')->only(['destroy']);
    }

    public function index()
    {
        return view('hr-management.employees.manage-employee');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
