<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lesson', function (Blueprint $table) {

		$table->string('Lessons_name');
		$table->dateTimeTz('Lessons_starting_time');
        $table->dateTimeTz('Lessons_ending_time');
		$table->integer('id_Lesson',true);
        $table->integer('Lower_grade_limit');
        $table->integer('Upper_grade_limit');
		$table->integer('fk_Classroomid_Classroom');
        $table->integer('creator_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson');
    }
};