<?php
declare(strict_types=1);

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function index()
    {
        return view('pos::setup.index');
    }
}
