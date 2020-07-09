<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Drives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drives', function (Blueprint $table) {
            $table->id();
            $table->enum('choices', ['Google Drive', 'AWS S3']);
            $table->string('name');
            $table->text('credentials');
            $table->timestamps();
        });

        Schema::create('files', function (Blueprint $table){
            $table->id();
            $table->integer('drive_id');
            $table->string('name');
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
        Schema::dropIfExists('files');
        Schema::dropIfExists('drives');
    }
}
