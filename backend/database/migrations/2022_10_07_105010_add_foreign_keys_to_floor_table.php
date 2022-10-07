<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('floor', function (Blueprint $table) {
            $table->foreign(['fk_Schoolid_School'], 'School_Has_Floor')->references(['id_School'])->on('school');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('floor', function (Blueprint $table) {
            $table->dropForeign('School_Has_Floor');
        });
    }
};
