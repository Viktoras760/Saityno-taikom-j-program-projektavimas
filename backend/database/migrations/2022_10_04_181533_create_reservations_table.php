<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationTable extends Migration
{
    public function up()
    {
        Schema::create('reservation', function (Blueprint $table) {

		$table->string('Lessons_name');
		$table->string('Reservation_period');
		$table->integer('id_Reservation',100);
        $table->integer('Lower_grade_limit',2);
        $table->integer('Upper_grade_limit',2);
		$table->integer('fk_Classroomid_Classroom',20);
		$table->integer('fk_Userid_User',50);
    

        });
    }

    public function down()
    {
        Schema::dropIfExists('reservation');
    }
};