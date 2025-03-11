<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionReactionsTable extends Migration
{
    public function up()
    {
        Schema::create('inspection_reactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_inspection_id');
            $table->unsignedBigInteger('estate_configuration_id');
            $table->text('comment')->nullable();
            $table->json('analyse')->nullable();
            $table->string('photo', 255)->nullable();
            $table->enum('status', ['en cours', 'pdf généré', 'en facturation', 'facturé'])->default('en cours');
            $table->timestamps();

            $table->foreign('property_inspection_id')
                ->references('id')
                ->on('property_inspections')
                ->onDelete('cascade');

            $table->foreign('estate_configuration_id')
                ->references('id')
                ->on('estate_configurations')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspection_reactions');
    }
}