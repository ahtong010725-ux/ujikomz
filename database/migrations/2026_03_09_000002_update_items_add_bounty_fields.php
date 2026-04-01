<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            $table->string('brand_name')->nullable()->after('name');
            $table->string('item_type')->nullable()->after('item_name');
            $table->string('reward_offered')->nullable()->after('description');
        });

        Schema::table('found_items', function (Blueprint $table) {
            $table->string('brand_name')->nullable()->after('name');
            $table->string('item_type')->nullable()->after('item_name');
            $table->string('reward_offered')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            $table->dropColumn(['brand_name', 'item_type', 'reward_offered']);
        });

        Schema::table('found_items', function (Blueprint $table) {
            $table->dropColumn(['brand_name', 'item_type', 'reward_offered']);
        });
    }
};
