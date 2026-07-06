<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('describing')->create('dfntn_mrktplc_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('marketplace_category_id');
			$table->integer('channel_id')->default(0);
			$table->string('channel_code', 255);
			$table->string('category_id', 255);
			$table->string('category_code', 255);
			$table->string('category_parent_id', 255);
			$table->string('category_parent_code', 255);
			$table->string('category_name', 255);
			$table->string('category_path', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('dfntn_mrktplc_category');

    }

};
