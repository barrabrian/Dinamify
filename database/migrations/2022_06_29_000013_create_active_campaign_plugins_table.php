<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveCampaignPluginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('active_campaign_plugins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->index();

            $table->string('name', 45);
            $table->string('api_key', 250);
            $table->string('api_url', 250);
            $table->integer('deliverable_link_field_id');
            $table->integer('deliverable_tag_id');

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
        Schema::dropIfExists('active_campaign_plugins');
    }
}
