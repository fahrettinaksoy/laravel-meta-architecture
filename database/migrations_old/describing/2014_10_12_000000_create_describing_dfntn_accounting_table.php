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

        Schema::connection('describing')->create('dfntn_accntng_safe_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_id');
			$table->string('color',255);
			$table->string('icon',255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_safe_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_translation_id');
			$table->integer('type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_safe_process', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('process_id');
			$table->string('color',255);
			$table->string('icon',255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_safe_process_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('process_translation_id');
			$table->integer('process_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_safe_module_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('module_type_id');
			$table->integer('process_id')->default(0);
			$table->integer('module_group_id')->default(0);
			$table->integer('menu_view')->default(0);
			$table->string('request_id',255);
			$table->string('icon',255);
			$table->string('color',255);
			$table->string('module',255);
			$table->string('route',255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_safe_module_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('module_type_translation_id');
			$table->integer('module_type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_safe_module_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('module_group_id');
			$table->string('icon',255);
			$table->string('color',255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_safe_module_group_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('module_group_translation_id');
			$table->integer('module_group_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_loan_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_id');
			$table->string('code',255);
			$table->string('color',255);
			$table->string('icon',255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_loan_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_translation_id');
			$table->integer('type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_order_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('group_id');
			$table->string('code', 255);
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_order_group_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('group_translation_id');
			$table->integer('group_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_invoice_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_id');
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_invoice_type_translation', function (Blueprint $table) {
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

        Schema::connection('describing')->create('dfntn_accntng_invoice_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('group_id');
			$table->string('code', 255);
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_invoice_group_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('group_translation_id');
			$table->integer('group_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_invoice_relation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('relation_id');
			$table->string('path',255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_invoice_relation_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('relation_translation_id');
			$table->integer('relation_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_check_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_id');
			$table->string('color',255);
			$table->string('icon',255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_accntng_check_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_translation_id');
			$table->integer('type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
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

        Schema::dropIfExists('dfntn_accntng_safe_type');
        Schema::dropIfExists('dfntn_accntng_safe_type_translation');
        Schema::dropIfExists('dfntn_accntng_safe_process');
        Schema::dropIfExists('dfntn_accntng_safe_process_translation');
        Schema::dropIfExists('dfntn_accntng_safe_module_type');
        Schema::dropIfExists('dfntn_accntng_safe_module_type_translation');
        Schema::dropIfExists('dfntn_accntng_safe_module_group');
        Schema::dropIfExists('dfntn_accntng_safe_module_group_translation');

        Schema::dropIfExists('dfntn_accntng_loan_type');
        Schema::dropIfExists('dfntn_accntng_loan_type_translation');

        Schema::dropIfExists('dfntn_accntng_order_group');
        Schema::dropIfExists('dfntn_accntng_order_group_translation');

        Schema::dropIfExists('dfntn_accntng_invoice_type');
        Schema::dropIfExists('dfntn_accntng_invoice_type_translation');
        Schema::dropIfExists('dfntn_accntng_invoice_group');
        Schema::dropIfExists('dfntn_accntng_invoice_group_translation');
        Schema::dropIfExists('dfntn_accntng_invoice_relation');
        Schema::dropIfExists('dfntn_accntng_invoice_relation_translation');

        Schema::dropIfExists('dfntn_accntng_check_type');
        Schema::dropIfExists('dfntn_accntng_check_type_translation');

    }

};
