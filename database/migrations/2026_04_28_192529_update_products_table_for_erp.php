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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'batch', 'expiration_date', 'unit_cost']);
            $table->integer('current_stock')->default(0)->after('name');
            $table->string('unit', 10)->default('un')->after('current_stock');
            $table->integer('minimum_stock')->default(5)->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
