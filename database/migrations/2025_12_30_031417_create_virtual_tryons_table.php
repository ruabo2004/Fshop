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
        Schema::create('virtual_tryons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Image paths
            $table->string('user_image');           // Path to user's photo
            $table->string('garment_image');        // Path to garment photo
            $table->string('result_image')->nullable(); // Path to result image
            
            // Fitroom.ai specific
            $table->string('fitroom_task_id')->nullable(); // Task ID from Fitroom
            $table->enum('clothing_type', ['upper', 'lower', 'full'])->default('upper');
            $table->string('quality', 20)->default('standard'); // 'standard' or 'hd'
            
            // Status tracking
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('processing_time')->nullable(); // Time in seconds
            $table->text('error_message')->nullable();
            
            // Credits tracking
            $table->decimal('credits_used', 8, 2)->default(0);
            
            // Sharing
            $table->boolean('is_public')->default(false);
            $table->string('share_token', 64)->unique()->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('fitroom_task_id');
            $table->index('share_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_tryons');
    }
};
