<?php

use App\Models\{WhatsappInstance};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instance_typebots', function (Blueprint $table) {
            $table->id()->index();
            $table->string('id_typebot')->nullable()->index();
            $table->foreignIdFor(WhatsappInstance::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->string('url');
            $table->string('type_bot');
            $table->string('trigger_type');
            $table->string('trigger_operator')->nullable();
            $table->string('trigger_value')->nullable();
            $table->integer('expire');
            $table->string('keyword_finish');
            $table->integer('delay_message')->default(1000);
            $table->string('unknown_message');
            $table->boolean('listening_from_me')->default(false);
            $table->boolean('stop_bot_from_me')->default(false);
            $table->boolean('keep_open')->default(false);
            $table->integer('debounce_time')->default(10);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instance_typebots');
    }
};
