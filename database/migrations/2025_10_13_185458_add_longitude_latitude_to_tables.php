<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('title');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        Schema::table('towns', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('title');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('distance_from_headquarters', 8, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('towns', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'distance_from_headquarters']);
        });
    }
};
