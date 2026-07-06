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

		Schema::connection('lessor')->create('wbst_form', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('form_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('html', 255);
			$table->text('send');
			$table->text('content');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

		Schema::connection('lessor')->create('wbst_form_incoming', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('form_incoming_id');
			$table->integer('form_id')->default(0);
			$table->text('content');
			$table->string('ip_address', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

		Schema::connection('lessor')->create('wbst_form_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('form_translation_id');
			$table->integer('form_id')->default(0);
			$table->string('language_code', 255);
			$table->string('title', 255);
			$table->text('description');
			$table->string('button', 255);
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

        Schema::dropIfExists('wbst_form');
        Schema::dropIfExists('wbst_form_incoming');
        Schema::dropIfExists('wbst_form_translation');

    }
};
