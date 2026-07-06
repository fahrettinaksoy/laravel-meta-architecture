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

        Schema::connection('describing')->create('dfntn_ctlg_channel_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('channel_type_id');
			$table->string('class', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_ctlg_channel_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('channel_type_translation_id');
			$table->integer('channel_type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_ctlg_channel_service', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('channel_service_id');
			$table->string('code', 255);
			$table->text('element');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_ctlg_channel_service_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('channel_service_translation_id');
			$table->integer('channel_service_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_ctlg_bank', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('bank_id');
			$table->string('code', 255);
			$table->string('type', 255);
			$table->string('name', 255);
			$table->string('eft_code', 255);
			$table->string('swift_code', 255);
			$table->string('telex_code', 255);
			$table->string('image', 255);
			$table->string('website', 255);
			$table->string('email', 255);
			$table->string('call_number', 255);
			$table->string('telephone_number', 255);
			$table->string('fax_number', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('describing')->create('dfntn_ctlg_cargo', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('cargo_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('image', 255);
			$table->string('website', 255);
			$table->string('email', 255);
			$table->string('call_number', 255);
			$table->string('fax_number', 255);
			$table->string('telephone_number', 255);
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

        Schema::dropIfExists('dfntn_ctlg_stck_entry');
        Schema::dropIfExists('dfntn_ctlg_stck_entry_translation');

        Schema::dropIfExists('dfntn_ctlg_channel_type');
        Schema::dropIfExists('dfntn_ctlg_channel_type_translation');
        Schema::dropIfExists('dfntn_ctlg_channel_service');
        Schema::dropIfExists('dfntn_ctlg_channel_service_translation');

        Schema::dropIfExists('dfntn_ctlg_bank');

        Schema::dropIfExists('dfntn_ctlg_cargo');

    }

};
