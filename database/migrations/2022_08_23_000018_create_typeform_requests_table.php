<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeformResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('typeform_responses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->index();
            $table->string('event_id');
            $table->string('typeform_signature');
            $table->string('fid');
            $table->string('rid');
            $table->string('value');
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
        Schema::dropIfExists('typeform_responses');
    }
}
