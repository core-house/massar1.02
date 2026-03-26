<?php

declare(strict_types=1);

namespace Modules\Recruitment\Http\Controllers;

use Illuminate\Routing\Controller;

class ContractTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Contract Types')->only(['index']);
        $this->middleware('can:create Contract Types')->only(['create', 'store']);
        $this->middleware('can:edit Contract Types')->only(['update', 'edit']);
        $this->middleware('can:delete Contract Types')->only(['destroy']);
    }

    public function index()
    {
        return view('recruitment::contract-types.manage');
    }
}

