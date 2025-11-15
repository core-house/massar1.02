<?php

namespace App\Http\Controllers;

use App\Models\EmployeesJob;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmployeesJobController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view jobs')->only(['index']);
        $this->middleware('can:create jobs')->only(['create', 'store']);
        $this->middleware('can:edit jobs')->only(['edit', 'update']);
        $this->middleware('can:delete jobs')->only(['destroy']);
        $this->middleware('can:print jobs')->only(['print']);
    }

    public function index()
    {
        return view('hr-management.jobs.manage-jobs');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeesJob $employeesJob)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeesJob $employeesJob)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeesJob $employeesJob)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeesJob $employeesJob)
    {
        //
    }
}
