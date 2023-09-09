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
        Schema::create('heroes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('ability')->default(0);
            $table->unsignedBigInteger('trainer_id')->nullable()->unsigned();

            // trainer_id is a foreign -> all related records will be deleted in case the trainer will be deleted in trainers table

            $table->foreign('trainer_id')->references('id')->on('trainers')->onDelete('cascade');

            $table->date('training_start_date')->default(now());
            $table->integer('suit_colors')->default(0);
            $table->decimal('starting_power', 10, 2)->default(1.00);
            $table->decimal('current_power', 10, 2)->default(1.00);

            $table->integer('number_of_trainings')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
