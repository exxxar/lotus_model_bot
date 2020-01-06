<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');

            $table->string('full_name')->default('');
            $table->string('height')->default('');
            $table->string('weight')->default('');
            $table->string('breast_volume')->default('');
            $table->string('sex')->default('');
            $table->string('waist')->default('');
            $table->string('hips')->default('');
            $table->string('model_school_education')->default('');
            $table->string('about')->default('');
            $table->string('hobby')->default('');
            $table->string('education')->default('');
            $table->string('wish_learn')->default('');


            //дополнительные вопросы
            $table->string('age')->default('');
            $table->string('clothing_size')->default('');
            $table->string('shoe_size')->default('');
            $table->string('eye_color')->default('');
            $table->string('hair_color')->default('');


            $table->string('city')->default('');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
