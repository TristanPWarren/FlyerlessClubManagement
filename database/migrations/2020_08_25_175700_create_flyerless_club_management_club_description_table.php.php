<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlyerlessClubManagementClubDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flyerless_club_management_club_description', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('club_id');
            $table->string('club_name');
            $table->text('description')->nullable();
            $table->string('form_link');
            $table->string('club_email');
            $table->string('club_facebook');
            $table->string('club_instagram');
            $table->string('club_website');
            $table->string('mime');
            $table->string('path_of_image');
            $table->integer('size');
            $table->unsignedInteger('uploaded_by');
            $table->unsignedInteger('module_instance_id');
            $table->unsignedInteger('activity_instance_id');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flyerless_club_management_club_description');
    }

}