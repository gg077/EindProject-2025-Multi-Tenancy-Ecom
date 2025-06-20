<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();

            // nieuwe kollomen
            $table->string('website_name')->nullable();
            $table->string('website_logo')->nullable();
            $table->text('website_description')->nullable();
            $table->string('vat_number')->nullable();
            $table->decimal('vat_percentage', 5, 2)->default(0);
            $table->text('stripe_publishable_key')->nullable();
            $table->text('stripe_secret_key')->nullable();
            $table->boolean('is_setup_completed')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->json('data')->nullable(); // vatnr, vat percentage
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
