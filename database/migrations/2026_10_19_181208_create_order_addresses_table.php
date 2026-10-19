<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('type', ['shipping', 'billing']);

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();

            $table->string('street');
            $table->string('city');
            $table->string('postal_code');
            $table->string('country')->default('Slovensko');

            $table->string('company_name')->nullable();
            $table->string('ico')->nullable();
            $table->string('dic')->nullable();
            $table->string('ic_dph')->nullable();

            $table->timestamps();

            // 1 shipping + 1 billing max na objednávku
            $table->unique(['order_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
