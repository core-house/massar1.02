<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\NoteDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupsAndItemsController extends Controller
{
    /**
     * GET /api/groups
     * Returns all groups (note_id = 1 in notes table).
     */
    public function groups(): JsonResponse
    {
        $groups = NoteDetails::where('note_id', 1)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }

    /**
     * GET /api/categories
     * Returns all categories (note_id = 2 in notes table).
     */
    public function categories(): JsonResponse
    {
        $categories = NoteDetails::where('note_id', 2)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * GET /api/items
     * Returns paginated items with optional filters:
     *   - search (string)
     *   - group_id (int)
     *   - category_id (int)
     *   - per_page (int, default 50, max 200)
     */
    public function items(Request $request): JsonResponse
    {
        $search     = trim((string) $request->input('search', ''));
        $groupId    = $request->integer('group_id') ?: null;
        $categoryId = $request->integer('category_id') ?: null;
        $perPage    = min((int) $request->input('per_page', 50), 200);

        $query = Item::query()
            ->select(['id', 'name', 'code', 'average_cost', 'is_active'])
            ->with([
                'units:id,name',
                'barcodes:id,item_id,barcode',
            ])
            ->where('isdeleted', 0)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%")
                          ->orWhereHas('barcodes', fn ($b) => $b->where('barcode', 'like', "%{$search}%"));
                });
            })
            ->when($groupId, function ($q) use ($groupId) {
                $q->whereHas('notes', function ($n) use ($groupId) {
                    $n->where('note_id', 1)
                      ->where('note_detail_name', function ($sub) use ($groupId) {
                          $sub->select('name')->from('note_details')->where('id', $groupId);
                      });
                });
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('notes', function ($n) use ($categoryId) {
                    $n->where('note_id', 2)
                      ->where('note_detail_name', function ($sub) use ($categoryId) {
                          $sub->select('name')->from('note_details')->where('id', $categoryId);
                      });
                });
            });

        $items = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $items->map(fn ($item) => [
                'id'           => $item->id,
                'name'         => $item->name,
                'code'         => $item->code,
                'average_cost' => $item->average_cost,
                'is_active'    => $item->is_active,
                'barcode'      => $item->barcodes->first()?->barcode,
                'units'        => $item->units->map(fn ($u) => [
                    'id'    => $u->id,
                    'name'  => $u->name,
                    'u_val' => $u->pivot->u_val ?? 1,
                ]),
            ]),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
            ],
        ]);
    }
}
