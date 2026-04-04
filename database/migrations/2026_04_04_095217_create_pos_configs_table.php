<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(false);

            // Payment methods
            $table->boolean('payment_cash')->default(true);
            $table->boolean('payment_card')->default(false);
            $table->boolean('payment_upi')->default(false);
            $table->string('upi_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_configs');
    }
};
