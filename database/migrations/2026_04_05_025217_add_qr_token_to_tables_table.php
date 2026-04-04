<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->string('qr_token', 12)->nullable()->unique()->after('status');
        });

        // Generate tokens for existing tables
        \App\Models\RestaurantTable::whereNull('qr_token')->each(function ($table) {
            $table->update(['qr_token' => Str::random(8)]);
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};
