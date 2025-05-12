<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotion_codes', function (Blueprint $table) {
            $table->id();
            $table->string('id_promotional_code')->nullable();
            $table->boolean('active')->default(true);
            $table->string('code');
            $table->string('id_cupom_code')->nullable();
            $table->string('duration');
            $table->integer('duration_in_months')->nullable();
            $table->string('percent_off');
            $table->integer('max_redemptions')->nullable();
            $table->string('redeem_by');
            $table->string('customer')->nullable();
            $table->boolean('valid')->default(true);
            $table->boolean('first_time_transaction')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_codes');
    }
};
