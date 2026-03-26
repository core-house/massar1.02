<?php

declare(strict_types=1);

namespace Modules\Accounts\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Accounts\Http\Requests\IndexAccountRequest;
use Modules\Accounts\Models\AccHead;

class AccHeadApiController extends Controller
{
    /**
     * Display a listing of the accounts.
     *
     * @param IndexAccountRequest $request
     * @return JsonResponse
     */
    public function index(IndexAccountRequest $request): JsonResponse
    {
        $type = $request->getType();

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Account type is required'
            ], 400);
        }

        $accounts = AccHead::nonBasic()
            ->byType($type)
            ->search($request->getSearch())
            ->withBasicRelations()
            ->orderBy('code')
            ->paginate($request->getPerPage());

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }
}
