<?php

declare(strict_types=1);

namespace Modules\POS\app\Services;

use App\Models\Barcode;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * خدمة الأصناف في POS
 * تتولى جلب وتحضير بيانات الأصناف للعرض والـ AJAX
 */
class POSItemService
{
    /**
     * جلب صورة الصنف
     */
    public function getItemImage(Item $item): string
    {
        $url = $item->getFirstMediaUrl('item-images', 'thumb')
            ?: $item->getFirstMediaUrl('item-thumbnail', 'thumb');

        return str_contains($url, 'no-image') ? '' : $url;
    }

    /**
     * جلب الأصناف مع بياناتها الكاملة للـ JavaScript
     *
     * @param  Collection<int, Item>  $items
     * @return Collection<int, array<string, mixed>>
     */
    public function buildItemsData(Collection $items, ?Collection $barcodes = null, ?Collection $itemCategories = null): Collection
    {
        if ($barcodes === null) {
            $itemIds = $items->pluck('id');
            $barcodes = Barcode::whereIn('item_id', $itemIds)
                ->where('isdeleted', 0)
                ->select('item_id', 'unit_id', 'barcode')
                ->get()
                ->groupBy('item_id');
        }

        return $items->map(function (Item $item) use ($barcodes, $itemCategories) {
            $itemBarcodes = $barcodes->get($item->id, collect());

            return [
                'id'                 => $item->id,
                'name'               => $item->name,
                'code'               => $item->code,
                'notes'              => $item->notes,
                'sale_price'         => $item->sale_price ?? 0,
                'cost_price'         => $item->cost_price ?? 0,
                'available_quantity' => $item->available_quantity ?? 0,
                'is_weight_scale'    => $item->is_weight_scale ?? false,
                'scale_plu_code'     => $item->scale_plu_code ?? null,
                'category_id'        => $itemCategories?->get($item->id)?->category_id ?? null,
                'image'              => $this->getItemImage($item),
                'barcodes'           => $itemBarcodes->map(fn ($b) => [
                    'barcode' => $b->barcode,
                    'unit_id' => $b->unit_id,
                ])->toArray(),
                'units'  => $item->units->map(fn ($u) => [
                    'id'    => $u->id,
                    'name'  => $u->name,
                    'value' => $u->pivot->u_val ?? 1,
                ])->toArray(),
                'prices' => $item->prices->map(fn ($p) => [
                    'id'    => $p->id,
                    'name'  => $p->name,
                    'value' => $p->pivot->price ?? 0,
                ])->toArray(),
            ];
        })->keyBy('id');
    }

    /**
     * جلب تصنيفات الأصناف من item_notes
     *
     * @param  \Illuminate\Support\Collection<int, int>  $itemIds
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function getItemCategories(Collection $itemIds): Collection
    {
        return DB::table('item_notes')
            ->join('note_details', function ($join) {
                $join->on('note_details.name', '=', 'item_notes.note_detail_name')
                    ->where('note_details.note_id', '=', 2);
            })
            ->whereIn('item_notes.item_id', $itemIds)
            ->select('item_notes.item_id', 'note_details.id as category_id')
            ->get()
            ->keyBy('item_id');
    }

    /**
     * جلب الباركودات لمجموعة أصناف
     *
     * @param  \Illuminate\Support\Collection<int, int>  $itemIds
     * @return \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, Barcode>>
     */
    public function getBarcodes(Collection $itemIds): Collection
    {
        return Barcode::whereIn('item_id', $itemIds)
            ->where('isdeleted', 0)
            ->select('item_id', 'unit_id', 'barcode')
            ->get()
            ->groupBy('item_id');
    }

    /**
     * جلب التصنيفات من note_details
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function getCategories(): Collection
    {
        return DB::table('note_details')
            ->join('notes', 'note_details.note_id', '=', 'notes.id')
            ->select('note_details.id', 'note_details.name', 'notes.name as parent_name')
            ->where('note_details.note_id', '=', 2)
            ->get();
    }
}
