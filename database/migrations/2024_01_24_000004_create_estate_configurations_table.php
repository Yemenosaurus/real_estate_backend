<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstateConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::create('estate_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estate_id')->constrained()->onDelete('cascade');
            $table->string('level');
            $table->string('room_type')->nullable();
            $table->integer('room_count')->default(1);
            $table->text('additional_info')->nullable();
            $table->json('details')->nullable();
            $table->json('pieces')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('estate_configurations');
    }
} 