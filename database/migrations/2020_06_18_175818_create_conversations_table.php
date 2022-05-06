<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', array('0', '1')); //0 - Individual 1 - Grupal
            $table->foreignId('user_id_1')->references('id')->on('users')->nullable();
            $table->foreignId('user_id_2')->references('id')->on('users')->nullable();
            $table->foreignId('group_id')->references('id')->on('groups')->nullable();
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
        Schema::dropIfExists('conversations');
    }
}
