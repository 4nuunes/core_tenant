<?php

use App\Models\{Organization, Ticket, User};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->string('file')->nullable();
            $table->string('status')->default('open');
            $table->string('type')->default('enhancement');
            $table->string('priority')->default('medium');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_responses', function (Blueprint $table) {

            $table->id();
            $table->foreignIdFor(Ticket::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete(); // Pode ser nulo se for um admin
            $table->string('image_path')->nullable();
            $table->string('file')->nullable();
            $table->text('message');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_responses');
    }
};
