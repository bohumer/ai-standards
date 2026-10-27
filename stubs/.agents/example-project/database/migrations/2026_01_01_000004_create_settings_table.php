<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('flag');
            $table->string('key');
            $table->string('value');
            $table->json('label');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['flag', 'key', 'value']);
            $table->index(['flag', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
