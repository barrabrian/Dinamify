<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->index();

            $table->string('aid', 250)->nullable()->index();
            $table->string('aref', 250)->nullable()->index();
            $table->string('answer_id', 250);
            $table->string('question_id', 250)->index();
            $table->string('question_ref', 250);

            $table->string('type', 25);
            $table->string('value', 250)->nullable();
            // $table->string('hidden', 250)->nullable();

            $table->integer('response_id')->nullable()->index();

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
        Schema::dropIfExists('answers');
    }
}
