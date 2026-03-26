<?php

declare(strict_types=1);

namespace Modules\UniversalSearch\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\UniversalSearch\Services\UniversalRouteSearch;

class UniversalSearchController extends Controller
{
    public function search(Request $request, UniversalRouteSearch $routeSearch): JsonResponse
    {
        $query = (string) $request->query('q', '');

        return response()->json($routeSearch->search($request->user(), $query));
    }
}

