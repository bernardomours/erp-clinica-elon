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
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tax_id')->unique();
            $table->string('slug')->unique();
            $table->enum('signature_status', ['active', 'expired', 'blocked'])->default('active');
            $table->date('expires_at')->nullable();
            $table->string('zip_code', 8);
            $table->string('street');
            $table->string('neighborhood');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('city');
            $table->string('state', 2);         
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
