<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeformRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('typeform_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->index();
            $table->string('event_id');
            $table->string('typeform_signature');
            $table->string('form_id');
            $table->string('response_id');
            $table->string('value');
            $table->integer('ebook_id')->nullable()->index();
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
        Schema::dropIfExists('typeform_requests');
    }
}
