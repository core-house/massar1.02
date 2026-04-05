<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('key')->nullable()->after('name');
        });

        // Seed keys for existing categories based on known names
        $keys = [
            'الثوابت العامه'                => 'general',
            'الثوابت العامة'                => 'general',
            ' الفواتير'                     => 'invoices',
            'الفواتير'                      => 'invoices',
            'حساب الخصم المكتسب '           => 'accounts',
            'حساب الخصم المكتسب'            => 'accounts',
            'حساب فرق الجرد '               => 'inventory_diff',
            'حساب فرق الجرد'                => 'inventory_diff',
            'إعدادات تواريخ الصلاحية'       => 'expiry',
        ];

        foreach ($keys as $name => $key) {
            DB::table('categories')->where('name', $name)->update(['key' => $key]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('key');
        });
    }
};
