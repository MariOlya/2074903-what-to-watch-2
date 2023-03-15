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
            $table->id()->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->string('name');
            $table->string('email')->unique();
            $table->char('password');
            $table->foreignIdFor(\App\Models\File::class, 'avatar_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\UserRole::class)->constrained()->cascadeOnDelete();
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
