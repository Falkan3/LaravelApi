<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256);
            $table->string('name_normalized', 256);
            $table->string('tax_id', 60);
            $table->string('tax_id_country_code', 2)->nullable()->default(null);
            $table->string('tax_id_number', 60)->nullable()->default(null);
            $table->string('country_code', 3)->nullable()->default(null)->comment('ISO 3166-1-alpha-3 code'); // alternatively, use a relation to a countries table
            $table->string('city', 50);
            $table->string('city_normalized', 50)->nullable()->default(null);
            $table->string('address', 256);
            $table->string('address_normalized', 256)->nullable()->default(null);
            $table->string('post_code', 20);
            $table->string('post_code_normalized', 20)->nullable()->default(null);
            $table->timestamps();

            $table->index('name');
            $table->fullText('name', 'name');
            $table->index('name_normalized');
            $table->fullText('name_normalized', 'ft_name_normalized');
            $table->index('tax_id');
            $table->index('tax_id_country_code');
            $table->index('tax_id_number');
            $table->index('address');
            $table->fullText('address', 'ft_address');
            $table->index('address_normalized');
            $table->fullText('address_normalized', 'ft_address_normalized');
            $table->index('post_code');
            $table->index('post_code_normalized');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('companies');
    }
};
