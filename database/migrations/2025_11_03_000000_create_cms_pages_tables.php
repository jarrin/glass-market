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
        // Create pages table
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create page_sections table
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->string('section_key', 100); // e.g., 'hero_title', 'section_1_heading'
            $table->string('section_label', 255); // Human-readable label for admin
            $table->string('section_type', 50)->default('text'); // text, textarea, html
            $table->string('section_group', 100)->nullable(); // To group sections visually in admin
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->unique(['page_id', 'section_key']);
        });

        // Create page_content table
        Schema::create('page_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('page_sections')->onDelete('cascade');
            $table->text('content_value');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique('section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_content');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
    }
};
