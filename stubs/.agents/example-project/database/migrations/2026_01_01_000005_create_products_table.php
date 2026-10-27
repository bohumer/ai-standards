<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('slug');

            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->string('slug_sk')->virtualAs("json_unquote(json_extract(`slug`, '$.sk'))")->unique();
                $table->string('slug_en')->virtualAs("json_unquote(json_extract(`slug`, '$.en'))")->unique();
            }

            $table->json('description')->nullable();
            $table->string('category')->nullable()->index();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
