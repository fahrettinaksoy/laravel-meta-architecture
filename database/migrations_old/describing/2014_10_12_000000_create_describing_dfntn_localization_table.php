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

        Schema::connection('describing')->create('dfntn_lclztn_language', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('language_id');
			$table->string('name', 255);
			$table->string('code', 255);
			$table->string('locale', 255);
			$table->string('image', 255);
			$table->string('directory', 255);
			$table->string('direction', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_lclztn_currency', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('currency_id');
			$table->string('name', 255);
			$table->string('code', 255);
			$table->string('icon', 255);
			$table->string('symbol_left', 255);
			$table->string('symbol_right', 255);
			$table->string('decimal_place', 255);
			$table->string('decimal_point', 255);
			$table->string('thousand_point', 255);
			$table->double('value', 15, 8);
			$table->tinyInteger('status')->default(0);
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

        Schema::dropIfExists('dfntn_lclztn_language');
        Schema::dropIfExists('dfntn_lclztn_currency');

    }

};
