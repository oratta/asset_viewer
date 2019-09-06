<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_masters', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('parent_id');
            $table->smallInteger('section_id');
            $table->text('name');
            $table->boolean('has_child');
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
        Schema::dropIfExists('category_masters');
    }
}
