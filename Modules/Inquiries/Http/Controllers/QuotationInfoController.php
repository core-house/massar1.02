<?php

namespace Modules\Inquiries\Http\Controllers;

use App\Http\Controllers\Controller;

class QuotationInfoController extends Controller
{
    public function create()
    {
        return view('inquiries::quotation-info.create');
    }
}
