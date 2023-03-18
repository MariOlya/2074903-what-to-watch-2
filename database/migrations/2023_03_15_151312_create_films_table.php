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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
            $table->string('imdb_id', 20)->unique();
            $table->foreignIdFor(\App\Models\File::class, 'poster_image_id')->nullable()->constrained('files');
            $table->foreignIdFor(\App\Models\File::class, 'preview_image_id')->nullable()->constrained('files');
            $table->foreignIdFor(\App\Models\File::class, 'background_image_id')->nullable()->constrained('files');
            $table->foreignIdFor(\App\Models\Color::class, 'background_color_id')->nullable()->constrained('colors');
            $table->string('name', 255)->nullable();
            $table->integer('released')->nullable();
            $table->text('description')->nullable();
            $table->foreignIdFor(\App\Models\Director::class)->nullable()->constrained();
            $table->integer('run_time')->nullable();
            $table->foreignIdFor(\App\Models\Link::class, 'video_link_id')->nullable()->constrained('links');
            $table->foreignIdFor(\App\Models\Link::class, 'preview_video_link_id')->nullable()->constrained('links');
            $table->float('rating')->nullable();
            $table->integer('vote_amount')->nullable();
            $table->foreignIdFor(\App\Models\FilmStatus::class, 'status_id')->constrained('film_statuses');
            $table->boolean('promo')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
