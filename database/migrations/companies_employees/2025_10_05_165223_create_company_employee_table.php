<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('company_employee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index('company_id');
            $table->index('employee_id');
            $table->index(['company_id', 'employee_id']);
            $table->unique(['company_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('company_employee');
    }
};
