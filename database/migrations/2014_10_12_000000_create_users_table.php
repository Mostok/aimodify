<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->enum('level', ['user', 'admin'])->default('user');
            $table->string('email');
            // $table->float('balance', 10, 2)->default('0');
            $table->string('timezone')->nullable();
            $table->enum('smiles', ['true', 'false'])->default('true');
            $table->enum('links', ['true', 'false'])->default('false');
            $table->mediumText('black_list_words')->nullable();
            $table->string('avatar')->default('/assets/user.png');
            $table->string('token')->nullable();
            $table->string('type');
            $table->string('phone')->nullable();
            $table->text('about')->nullable();
            $table->text('youtube')->nullable();
            $table->text('twitch')->nullable();
            $table->string('nickname')->nullable();
            $table->string('subscribers')->nullable();
            $table->string('avonline')->nullable();
            $table->string('streamviews')->nullable();
            $table->string('videoviews')->nullable();
            $table->text('bizdesc')->nullable();
            $table->text('products_for_advertisment')->nullable();
            $table->text('connection1')->nullable();
            $table->text('connection2')->nullable();
            $table->text('connection3')->nullable();
            $table->text('typeadver')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
