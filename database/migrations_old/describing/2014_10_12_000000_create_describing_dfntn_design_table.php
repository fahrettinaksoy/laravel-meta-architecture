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

        Schema::connection('describing')->create('dfntn_dsgn_theme', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('theme_id');
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_theme_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('theme_translation_id');
			$table->integer('theme_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->string('description', 255);
			$table->string('keyword', 255);
			$table->string('tag', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_theme_template', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('template_widget_id');
			$table->integer('theme_id')->default(0);
			$table->string('widget_type', 255);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('thumb', 255);
			$table->string('path', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_device', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('device_id');
			$table->string('code', 255);
			$table->string('prefix', 255);
			$table->string('icon', 255);
			$table->string('dimension', 255);
			$table->string('landmark', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_device_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('device_translation_id');
			$table->integer('device_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_position', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('position_id');
			$table->string('type', 255);
			$table->string('code', 255);
			$table->string('relation', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_position_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('position_translation_id');
			$table->integer('position_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_position_template', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('position_template_id');
			$table->integer('position_id')->default(0);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('thumb', 255);
			$table->text('content');
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_block_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('color', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_block_category_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_translation_id');
			$table->integer('category_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_block_item', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('item_id');
			$table->integer('category_id')->default(0);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('thumb', 255);
			$table->text('content');
			$table->string('style', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_dsgn_block_item_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('item_translation_id');
			$table->integer('item_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
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

        Schema::dropIfExists('dfntn_dsgn_theme');
        Schema::dropIfExists('dfntn_dsgn_theme_translation');
        Schema::dropIfExists('dfntn_dsgn_theme_template');

        Schema::dropIfExists('dfntn_dsgn_device');
        Schema::dropIfExists('dfntn_dsgn_device_translation');

        Schema::dropIfExists('dfntn_dsgn_position');
        Schema::dropIfExists('dfntn_dsgn_position_translation');
        Schema::dropIfExists('dfntn_dsgn_position_template');

        Schema::dropIfExists('dfntn_dsgn_block_category');
        Schema::dropIfExists('dfntn_dsgn_block_category_translation');

        Schema::dropIfExists('dfntn_dsgn_block_item');
        Schema::dropIfExists('dfntn_dsgn_block_item_translation');

    }

};
