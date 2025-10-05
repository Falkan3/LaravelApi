<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('first_name_normalized', 100)->nullable()->default(null);
            $table->string('last_name', 100);
            $table->string('last_name_normalized', 100)->nullable()->default(null);
            $table->string('email', 256);
            $table->string('phone_number', 20)->nullable()->default(null);
            $table->timestamps();

            $table->index('first_name');
            $table->fullText('first_name', 'ft_first_name');
            $table->index('first_name_normalized');
            $table->fullText('first_name_normalized', 'ft_first_name_normalized');
            $table->index('last_name');
            $table->fullText('last_name', 'ft_last_name');
            $table->index('last_name_normalized');
            $table->fullText('last_name_normalized', 'ft_last_name_normalized');
            $table->index('phone_number');
            $table->fullText('phone_number', 'ft_phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('employees');
    }
};
