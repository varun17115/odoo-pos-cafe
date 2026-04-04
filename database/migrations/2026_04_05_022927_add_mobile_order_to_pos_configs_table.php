<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_configs', function (Blueprint $table) {
            $table->boolean('self_ordering')->default(false)->after('upi_id');
            $table->enum('self_ordering_type', ['online_ordering', 'qr_menu'])->nullable()->after('self_ordering');
            $table->string('self_ordering_token', 32)->nullable()->unique()->after('self_ordering_type');
            $table->string('bg_color', 7)->default('#111827')->after('self_ordering_token');
            $table->string('bg_image_1')->nullable()->after('bg_color');
            $table->string('bg_image_2')->nullable()->after('bg_image_1');
            $table->string('bg_image_3')->nullable()->after('bg_image_2');
        });
    }

    public function down(): void
    {
        Schema::table('pos_configs', function (Blueprint $table) {
            $table->dropColumn(['self_ordering', 'self_ordering_type', 'self_ordering_token', 'bg_color', 'bg_image_1', 'bg_image_2', 'bg_image_3']);
        });
    }
};
