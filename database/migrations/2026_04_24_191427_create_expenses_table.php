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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->integer('installments')->default(1);
            $table->decimal('installment_amount', 10, 2);
            $table->string('payment_plan');
            $table->enum('category', ["product_purchase","supplier_payment","operational_expense","others"]);
            $table->enum('status', ["pending","paid"])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
