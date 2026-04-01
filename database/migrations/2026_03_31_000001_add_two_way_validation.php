<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->unsignedBigInteger('finder_id')->nullable()->after('claimer_id');
            $table->boolean('owner_confirmed')->default(false)->after('status');
            $table->boolean('finder_confirmed')->default(false)->after('owner_confirmed');
            $table->timestamp('confirmed_at')->nullable()->after('finder_confirmed');
            $table->string('flag_reason')->nullable()->after('confirmed_at');
            $table->foreign('finder_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('user_points', function (Blueprint $table) {
            $table->integer('month')->nullable()->after('total_earned');
            $table->integer('year')->nullable()->after('month');
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['finder_id']);
            $table->dropColumn(['finder_id', 'owner_confirmed', 'finder_confirmed', 'confirmed_at', 'flag_reason']);
        });

        Schema::table('user_points', function (Blueprint $table) {
            $table->dropColumn(['month', 'year']);
        });
    }
};
