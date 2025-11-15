<?php

namespace App\Http\Controllers;

use App\Models\ContractType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class ContractTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view contract types')->only(['index']);
        $this->middleware('can:create contract types')->only(['create', 'store']);
        $this->middleware('can:edit contract types')->only(['update', 'edit']);
        $this->middleware('can:delete contract types')->only(['destroy']);
    }

    public function index()
    {
        return view('hr-management.contracts.types.manage-typs');
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
    public function show(ContractType $contractType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContractType $contractType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContractType $contractType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractType $contractType)
    {
        //
    }
}
