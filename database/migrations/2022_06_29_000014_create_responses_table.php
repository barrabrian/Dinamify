<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->index();
            $table->string('form_id')->index();

            $table->string('token', 250)->nullable();
            $table->string('landing_id', 250)->nullable();
            $table->string('rid', 250)->nullable();

            $table->string('hidden_fields', 250)->nullable();

            $table->string('submitted_at', 250)->nullable();

            $table->integer('score')->nullable();

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
        Schema::dropIfExists('responses');
    }
}
