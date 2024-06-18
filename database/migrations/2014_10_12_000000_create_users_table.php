<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_role');
            $table->foreignId('id_province');
            $table->foreignId('id_city');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('owner_name');
            $table->string('umkm_name')->nullable();
            $table->text('umkm_description')->nullable();
            $table->string('instagram')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('facebook')->nullable();
            $table->string('umkm_email')->nullable();
            $table->string('address')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('pinpoint')->nullable();
            $table->string('umkm_image');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();

            $table->foreign('id_role')->references('id')->on('user_role')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_province')->references('id')->on('user_province')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_city')->references('id')->on('user_city')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
