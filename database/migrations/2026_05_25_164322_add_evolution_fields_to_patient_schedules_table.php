<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('patient_schedules', function (Blueprint $table) {
            $table->string('tooth_number')->nullable()->after('procedure_id'); // Ex: "11, 12, 45"
            $table->text('clinical_evolution')->nullable()->after('status'); // O texto da evolução
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_schedules', function (Blueprint $table) {
            //
        });
    }
};
