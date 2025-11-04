<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->boolean('is_draft')->default(false)->after('status');
            $table->json('draft_data')->nullable()->after('is_draft');
            $table->timestamp('last_draft_saved_at')->nullable()->after('draft_data');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['is_draft', 'draft_data', 'last_draft_saved_at', 'created_by']);
        });
    }
};
