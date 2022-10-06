<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolTable extends Migration
{
    public function up()
    {
        Schema::create('school', function (Blueprint $table) {
            $table->id('id_School');
		    $table->string('Name');
		    $table->string('Adress');
		    $table->integer('Pupil_amount',5);
		    $table->integer('Teacher_amount',4);
        });
    }

    public function down()
    {
        Schema::dropIfExists('school');
    }
};
