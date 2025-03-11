<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyInspectionsTable extends Migration
{
    public function up()
    {
        Schema::create('property_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estate_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['Submitted', 'Approved', 'Rejected', 'In Progress', 'Completed', 'Closed'])->default('Submitted');
            $table->enum('who', ['particulier', 'agence', 'investisseur']);
            $table->longText('config');
            $table->date('date');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_inspections');
    }
} 