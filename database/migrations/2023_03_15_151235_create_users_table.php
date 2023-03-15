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
            $table->timestamp('created_at')->useCurrent();
            $table->string('name', 100);
            $table->string('email', 50);
            $table->unique('email');
            $table->char('password', 255);
            $table->foreignIdFor(\App\Models\File::class, 'avatar_id')->nullable()->constrained('files')->cascadeOnDelete();
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
