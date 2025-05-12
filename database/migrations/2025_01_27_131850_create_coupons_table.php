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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id()->index();
            $table->string('name');
            $table->string('coupon_code')->nullable();
            $table->string('duration');
            $table->string('currency');
            $table->integer('duration_in_months')->nullable();
            $table->string('percent_off');
            $table->integer('max_redemptions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('redeem_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
