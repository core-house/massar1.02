<?php

namespace Modules\OfflinePOS\Services;

use App\Models\Item;
use App\Models\User;
use Modules\HR\Models\Employee;
use Modules\Accounts\Models\AccHead;
use Modules\Settings\Models\PublicSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Service لتجهيز البيانات الأولية للتنزيل
 * يجلب جميع البيانات المطلوبة للعمل offline
 */
class InitDataService
{
    /**
     * جلب جميع البيانات الأولية
     * 
     * @param int|null $branchId
     * @return array
     */
    public function getInitialData(?int $branchId = null): array
    {
        $startTime = microtime(true);

        $data = [
            'items' => $this->getItems($branchId),
            'customers' => $this->getCustomers(),
            'stores' => $this->getStores(),
            'employees' => $this->getEmployees(),
            'cash_boxes' => $this->getCashBoxes(),
            'user' => $this->getCurrentUser(),
            'settings' => $this->getSettings(),
            'categories' => $this->getCategories(),
            'price_types' => $this->getPriceTypes(),
        ];

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        $metadata = [
            'total_items' => count($data['items']),
            'total_customers' => count($data['customers']),
            'total_stores' => count($data['stores']),
            'total_employees' => count($data['employees']),
            'branch_id' => $branchId,
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'execution_time_ms' => $executionTime,
        ];

        return [
            'data' => $data,
            'metadata' => $metadata,
        ];
    }

    /**
     * جلب الأصناف مع كامل التفاصيل
     * 
     * @param int|null $branchId
     * @return array
     */
    protected function getItems(?int $branchId = null): array
    {
        return Cache::remember(
            "offline_pos.items.branch_{$branchId}",
            now()->addMinutes(30),
            function () use ($branchId) {
                $items = Item::with([
                    'units' => function ($query) {
                        $query->orderBy('u_val');
                    },
                    'prices',
                    'barcodes' => function ($query) {
                        $query->where('isdeleted', 0);
                    },
                    'notes',
                ])
                ->get();

                return $items->map(function ($item) use ($branchId) {
                    // جلب التصنيف الأول إن وجد
                    $firstCategory = $item->notes->first();
                    
                    return [
                        'id' => $item->id,
                        'code' => $item->code,
                        'name' => $item->name,
                        'description' => $item->info ?? null,
                        'type' => $item->type ?? 1,
                        'average_cost' => $item->average_cost ?? 0,
                        
                        // Barcodes
                        'barcodes' => $item->barcodes->pluck('barcode')->toArray(),
                        
                        // Category (من item_notes)
                        'category_id' => $firstCategory?->id ?? null,
                        'category_name' => $firstCategory?->pivot->note_detail_name ?? null,
                        
                        // Units (من item_units)
                        'units' => $item->units->map(function ($unit) {
                            return [
                                'id' => $unit->id,
                                'name' => $unit->name,
                                'code' => $unit->code,
                                'conversion_factor' => $unit->pivot->u_val ?? 1,
                                'cost' => $unit->pivot->cost ?? 0,
                            ];
                        })->toArray(),
                        
                        // Prices (من item_prices عبر علاقة prices)
                        'prices' => $item->prices->map(function ($priceList) {
                            return [
                                'price_type_id' => $priceList->id,
                                'price_type_name' => $priceList->name,
                                'unit_id' => $priceList->pivot->unit_id,
                                'price' => $priceList->pivot->price,
                                'discount' => $priceList->pivot->discount ?? 0,
                                'tax_rate' => $priceList->pivot->tax_rate ?? 0,
                            ];
                        })->toArray(),
                        
                        // Stock balances (per branch if specified)
                        'stock_balances' => $this->getItemStockBalances($item->id, $branchId),
                        
                        'last_synced' => now()->toISOString(),
                    ];
                })->toArray();
            }
        );
    }

    /**
     * جلب أرصدة صنف في المخازن
     * 
     * @param int $itemId
     * @param int|null $branchId
     * @return array
     */
    protected function getItemStockBalances(int $itemId, ?int $branchId = null): array
    {
        $query = DB::table('operation_items')
            ->join('acc_heads', 'operation_items.detail_store', '=', 'acc_heads.id')
            ->select(
                'acc_heads.id as store_id',
                'acc_heads.aname as store_name',
                DB::raw('SUM(operation_items.qty_in - operation_items.qty_out) as quantity')
            )
            ->where('operation_items.item_id', $itemId)
            ->where('operation_items.is_stock', 1)
            ->groupBy('acc_heads.id', 'acc_heads.aname')
            ->having('quantity', '>', 0);

        // TODO: Add branch filter when branch system is fully implemented
        // if ($branchId) {
        //     $query->where('acc_heads.branch_id', $branchId);
        // }

        return $query->get()->map(function ($balance) use ($branchId) {
            return [
                'store_id' => $balance->store_id,
                'store_name' => $balance->store_name,
                'branch_id' => $branchId, // Will be dynamic later
                'quantity' => (float) $balance->quantity,
            ];
        })->toArray();
    }

    /**
     * جلب العملاء
     * 
     * @return array
     */
    protected function getCustomers(): array
    {
        // Auto-detect tenant ID if tenancy is available
        $tenantId = (function_exists('tenant') && tenant()) ? tenant('id') : 'default';
        
        return Cache::remember(
            "offline_pos.customers." . $tenantId,
            now()->addMinutes(30),
            function () {
                return AccHead::where('code', 'like', '1103%')
                    ->where('isdeleted', 0)
                    ->where('is_basic', 0)
                    ->select('id', 'code', 'aname as name', 'phone', 'address')
                    ->get()
                    ->map(function ($customer) {
                        // جلب الرصيد
                        $balance = DB::table('journal_details')
                            ->where('account_id', $customer->id)
                            ->where('isdeleted', 0)
                            ->selectRaw('SUM(debit) - SUM(credit) as balance')
                            ->value('balance') ?? 0;

                        return [
                            'id' => $customer->id,
                            'code' => $customer->code,
                            'name' => $customer->name,
                            'phone' => $customer->phone,
                            'address' => $customer->address,
                            'balance' => $balance,
                            'last_synced' => now()->toISOString(),
                        ];
                    })->toArray();
            }
        );
    }

    /**
     * جلب المخازن
     * 
     * @return array
     */
    protected function getStores(): array
    {
        return AccHead::where('code', 'like', '1104%')
            ->where('isdeleted', 0)
            ->where('is_basic', 0)
            ->select('id', 'code', 'aname as name')
            ->get()
            ->map(function ($store) {
                return [
                    'id' => $store->id,
                    'code' => $store->code,
                    'name' => $store->name,
                    'is_active' => true,
                    'last_synced' => now()->toISOString(),
                ];
            })->toArray();
    }

    /**
     * جلب الموظفين
     * 
     * @return array
     */
    protected function getEmployees(): array
    {
        return Employee::where('status', 'مفعل')
            ->select('id', 'name', 'phone', 'email', 'position', 'branch_id', 'salary', 'finger_print_id')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'phone' => $employee->phone ?? null,
                    'email' => $employee->email ?? null,
                    'position' => $employee->position ?? null,
                    'branch_id' => $employee->branch_id ?? null,
                    'finger_print_id' => $employee->finger_print_id ?? null,
                    'last_synced' => now()->toISOString(),
                ];
            })->toArray();
    }

    /**
     * جلب الصناديق
     * 
     * @return array
     */
    protected function getCashBoxes(): array
    {
        return AccHead::where('is_fund', 1)
            ->where('isdeleted', 0)
            ->where('is_basic', 0)
            ->select('id', 'code', 'aname as name')
            ->get()
            ->map(function ($cashBox) {
                // جلب الرصيد الحالي
                $balance = DB::table('journal_details')
                    ->where('account_id', $cashBox->id)
                    ->where('isdeleted', 0)
                    ->selectRaw('SUM(debit) - SUM(credit) as balance')
                    ->value('balance') ?? 0;

                return [
                    'id' => $cashBox->id,
                    'code' => $cashBox->code,
                    'name' => $cashBox->name,
                    'balance' => $balance,
                    'last_synced' => now()->toISOString(),
                ];
            })->toArray();
    }

    /**
     * جلب بيانات المستخدم الحالي مع صلاحياته
     * 
     * @return array
     */
    protected function getCurrentUser(): array
    {
        /** @var User $user */
        $user = Auth::user();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'branch_id' => $user->branch_id ?? null,
            'permissions' => $user->getAllPermissions()
                ->pluck('name')
                ->toArray(),
            'roles' => $user->roles->pluck('name')->toArray(),
        ];
    }

    /**
     * جلب الإعدادات
     * 
     * @return array
     */
    protected function getSettings(): array
    {
        // Auto-detect tenant ID if tenancy is available
        $tenantId = (function_exists('tenant') && tenant()) ? tenant('id') : 'default';
        
        return Cache::remember(
            "offline_pos.settings." . $tenantId,
            now()->addHour(),
            function () {
                return PublicSetting::pluck('value', 'key')->toArray();
            }
        );
    }

    /**
     * جلب التصنيفات
     * 
     * @return array
     */
    protected function getCategories(): array
    {
        return DB::table('note_details')
            ->join('notes', 'note_details.note_id', '=', 'notes.id')
            ->select('note_details.id', 'note_details.name', 'notes.name as parent_name')
            ->where('note_details.note_id', '=', 2)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'parent_name' => $category->parent_name,
                ];
            })->toArray();
    }

    /**
     * جلب أنواع الأسعار
     * 
     * @return array
     */
    protected function getPriceTypes(): array
    {
        return DB::table('prices')
            ->where('is_deleted', 0)
            ->select('id', 'name')
            ->get()
            ->map(function ($price) {
                return [
                    'id' => $price->id,
                    'name' => $price->name,
                ];
            })
            ->toArray();
    }

    /**
     * التحقق من وجود تحديثات
     * 
     * @param int|null $branchId
     * @param string|null $lastSyncTimestamp
     * @return array
     */
    public function checkForUpdates(?int $branchId, ?string $lastSyncTimestamp): array
    {
        if (!$lastSyncTimestamp) {
            return [
                'has_updates' => true,
                'sections' => ['all'],
            ];
        }

        $updatedSections = [];
        $lastSync = \Carbon\Carbon::parse($lastSyncTimestamp);

        // التحقق من تحديثات الأصناف
        if (Item::where('updated_at', '>', $lastSync)->exists()) {
            $updatedSections[] = 'items';
        }

        // التحقق من تحديثات العملاء
        if (AccHead::where('code', 'like', '1103%')->where('updated_at', '>', $lastSync)->exists()) {
            $updatedSections[] = 'customers';
        }

        // يمكن إضافة المزيد من الفحوصات...

        return [
            'has_updates' => count($updatedSections) > 0,
            'sections' => $updatedSections,
        ];
    }

    /**
     * جلب بيانات قسم معين
     * 
     * @param string $section
     * @param int|null $branchId
     * @return array
     */
    public function getSectionData(string $section, ?int $branchId): array
    {
        return match($section) {
            'items' => $this->getItems($branchId),
            'customers' => $this->getCustomers(),
            'stores' => $this->getStores(),
            'employees' => $this->getEmployees(),
            'cash_boxes' => $this->getCashBoxes(),
            'categories' => $this->getCategories(),
            default => [],
        };
    }
}
