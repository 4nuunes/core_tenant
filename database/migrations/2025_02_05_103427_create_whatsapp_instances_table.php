<?php

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_instances', function (Blueprint $table) {
            $table->id()->index();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('number');
            $table->string('instance_id')->nullable();
            $table->string('profile_picture_url')->nullable();
            $table->string('hash')->nullable();
            $table->string('status')->nullable();
            $table->boolean('reject_call')->default(true);
            $table->string('msg_call')->nullable();
            $table->boolean('groups_ignore')->default(true);
            $table->boolean('always_online')->default(true);
            $table->boolean('read_messages')->default(true);
            $table->boolean('read_status')->default(true);
            $table->boolean('sync_full_history')->default(true);
            $table->string('count')->nullable();
            $table->string('pairing_code')->nullable();
            $table->binary('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_instances');
    }
};
