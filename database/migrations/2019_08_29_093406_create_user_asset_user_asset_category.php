<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAssetUserAssetCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_asset_user_asset_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('user_asset_category_id')->reference('id')->on('user_asset_categories');
            $table->bigInteger('user_asset_id')->reference('id')->on('user_assets');
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
        Schema::dropIfExists('user_asset_user_asset_category');
    }
}
