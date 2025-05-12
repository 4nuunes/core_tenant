<?php

use App\Models\{Organization, Subscription};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('stripe_id')->unique();
            $table->string('stripe_status')->default('incomplete');
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->text('hosted_invoice_url')->nullable();
            $table->text('invoice_pdf')->nullable();
            $table->text('payment_intent')->nullable();
            $table->text('charge')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'stripe_status']);
        });

        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Subscription::class);
            $table->string('stripe_id')->unique();
            $table->string('stripe_product');
            $table->string('stripe_price');
            $table->integer('quantity')->nullable();
            $table->timestamps();

            $table->index(['subscription_id', 'stripe_price']);
        });

        Schema::create('subscription_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Subscription::class);
            $table->string('object')->default('refund');
            $table->string('refund_id')->nullable();
            $table->string('status')->nullable();
            $table->string('balance_transaction')->nullable();
            $table->string('reference')->nullable();
            $table->string('reference_status')->nullable();
            $table->integer('amount');
            $table->string('currency');
            $table->string('reason');
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'subscription_id']);
        });

        Schema::create('subscription_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->string('stripe_id')->unique();
            $table->string('reason')->default('other');
            $table->text('coments')->nullable();
            $table->string('rating')->default('other');
            $table->timestamps();

            $table->index(['organization_id', 'stripe_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscription_refunds');
        Schema::dropIfExists('subscription_cancellations');

    }
};
