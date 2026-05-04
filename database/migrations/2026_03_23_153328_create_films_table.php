<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('release_date');
            $table->text('sinopsis');
            $table->integer('duration');
            $table->string('gendre');
            // Relación con la tabla directors
            $table->foreignId('director_id')
                  ->constrained('directors')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        
        Schema::dropIfExists('peliculas');
    }
};