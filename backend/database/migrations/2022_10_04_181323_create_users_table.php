<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {

		$table->string('Name');
		$table->string('Surname');
		$table->biginteger('Personal_code');
		$table->string('Email')->nullable()->default('NULL');
        $table->integer('Grade')->nullable();
		$table->string('Password');
		$table->enum('Confirmation', ['Confirmed', 'Uncomfirmed'])->default('Uncomfirmed');
		$table->integer('id_User',true);
		$table->integer('fk_Schoolid_School')->nullable();
		$table->enum('Role',['Pupil','Teacher','School Administrator','System Administrator'])->default('Pupil');

        });
    }

    public function down()
    {
        Schema::dropIfExists('user');
    }
};
