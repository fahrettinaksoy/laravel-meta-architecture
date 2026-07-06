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

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_recurring_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('recurring_type_id');
			$table->string('code', 255);
			$table->integer('duration')->default(0);
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly']);
			$table->integer('cycle')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->decimal('trial_price', 19, 2);
			$table->integer('trial_duration')->default(0);
            $table->enum('trial_frequency', ['daily', 'weekly', 'monthly', 'yearly']);
			$table->integer('trial_cycle')->default(0);
			$table->tinyInteger('trial_status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_recurring_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('recurring_type_translation_id');
			$table->integer('recurring_type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_id');
			$table->string('icon', 255);
			$table->string('color', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_translation_id');
			$table->integer('type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_condition', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('condition_id');
			$table->string('sort_order', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_condition_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('condition_translation_id');
			$table->integer('condition_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_stockless', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('stockless_id');
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_stockless_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('stockless_translation_id');
			$table->integer('stockless_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_field_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('field_type_id');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_field_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('field_type_translation_id');
			$table->integer('field_type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_attribute_template', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_template_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_attribute_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_group_id');
			$table->string('code', 255);
			$table->integer('attribute_template_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_attribute_group_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_group_translation_id');
			$table->integer('attribute_group_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_attribute_variable', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_variable_id');
			$table->string('code', 255);
			$table->integer('attribute_group_id')->default(0);
			$table->string('type', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('dfntn_ctlg_prdct_attribute_variable_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_variable_translation_id');
			$table->integer('attribute_variable_id')->default(0);
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
        Schema::dropIfExists('dfntn_ctlg_prdct_recurring_type');
        Schema::dropIfExists('dfntn_ctlg_prdct_recurring_type_translation');

        Schema::dropIfExists('dfntn_ctlg_prdct_type');
        Schema::dropIfExists('dfntn_ctlg_prdct_type_translation');

        Schema::dropIfExists('dfntn_ctlg_prdct_condition');
        Schema::dropIfExists('dfntn_ctlg_prdct_condition_translation');

        Schema::dropIfExists('dfntn_ctlg_prdct_stockless');
        Schema::dropIfExists('dfntn_ctlg_prdct_stockless_translation');

        Schema::dropIfExists('dfntn_ctlg_prdct_field_type');
        Schema::dropIfExists('dfntn_ctlg_prdct_field_type_translation');
    }

};
