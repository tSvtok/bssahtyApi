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
        Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->foreignId('question_id')->nullable()->constrained()->onDelete('set null');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('spot_id')->nullable()->constrained()->onDelete('set null');
    $table->dateTime('date');
    $table->integer('max_participants')->default(10);
    $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
