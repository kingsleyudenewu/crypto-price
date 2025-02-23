<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crypto_pairs', function (Blueprint $table) {
            $table->id();
            $table->string('pair');
            $table->decimal('average_price', 18, 8);
            $table->decimal('price_change', 18, 8)->default(0);
            $table->timestamp('last_updated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_pairs');
    }
};
