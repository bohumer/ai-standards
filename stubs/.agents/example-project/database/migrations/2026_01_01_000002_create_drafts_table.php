<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('context_key')->default('');
            $table->json('payload');
            $table->timestamps();

            $table->unique(['user_id', 'model_type', 'model_id', 'context_key'], 'drafts_user_model_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drafts');
    }
};
