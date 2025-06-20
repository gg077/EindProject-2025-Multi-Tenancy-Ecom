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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('download_link')->nullable();
            $table->decimal('price', 8, 2);
            $table->json('images')->nullable();

            // bijkomende kolommen
            $table->decimal('average_rating', 2, 1)->nullable();
            $table->unsignedInteger('completed_orders_count')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
