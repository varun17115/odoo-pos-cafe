<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('attribute')->default('Size'); // e.g. "Size", "Pack"
            $table->string('name');                       // e.g. "6 inch", "8 inch"
            $table->string('unit')->nullable();           // e.g. KG, Piece
            $table->decimal('price', 10, 2)->nullable();  // extra/override price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
