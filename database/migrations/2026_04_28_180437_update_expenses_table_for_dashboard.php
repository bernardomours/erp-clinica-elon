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
    Schema::table('expenses', function (Blueprint $table) {
        $table->string('description')->after('supplier_id')->nullable();
        $table->date('due_date')->after('description')->nullable();
        $table->date('payment_date')->after('due_date')->nullable();
        $table->dropColumn('category');
        $table->foreignId('financial_category_id')->nullable()->after('payment_plan')->constrained('financial_categories')->nullOnDelete();
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
