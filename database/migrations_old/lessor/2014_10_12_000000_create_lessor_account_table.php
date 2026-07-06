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

        Schema::connection('lessor')->create('wbst_account', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_id');
			$table->integer('type_id')->default(0);
			$table->integer('group_id')->default(0);
			$table->string('code', 255);
			$table->integer('shape')->default(0);
			$table->string('language_code', 255);
			$table->string('image', 255);
			$table->string('name', 255);
			$table->string('tax_office', 255);
			$table->string('tax_number', 255);
			$table->string('trade_chamber', 255);
			$table->string('trade_number', 255);
			$table->string('mersis_number', 255);
			$table->string('kep_address', 255);
			$table->text('component');
			$table->tinyInteger('safe')->default(0);
			$table->tinyInteger('newsletter')->default(0);
			$table->integer('default_authorized_id')->default(0);
			$table->integer('default_contact_id')->default(0);
			$table->integer('default_bank_account_id')->default(0);
			$table->string('ip_address', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('wbst_account_access', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('access_id');
			$table->integer('account_id')->default(0);
			$table->integer('sector_id')->default(0);
			$table->string('code', 255);
			$table->string('debug', 255);
			$table->string('log_channel', 255);
			$table->string('log_deprecation', 255);
			$table->string('log_level', 255);
			$table->string('session_driver', 255);
			$table->string('session_lifetime', 255);
			$table->string('broadcast_driver', 255);
			$table->string('cache_driver', 255);
			$table->string('queue_connection', 255);
			$table->string('domain_protocol', 255);
			$table->string('domain_address', 255);
			$table->string('domain_extension', 255);
			$table->integer('domain_id')->default(0);
			$table->string('domain_guid', 255)->nullable();
			$table->integer('subdomain_status')->default(0);
			$table->string('subdomain_name', 255);
			$table->integer('host_shelter')->default(0);
			$table->string('host_address', 255);
			$table->string('host_port', 255);
			$table->string('host_username', 255);
			$table->string('host_password', 255);
			$table->integer('database_shelter')->default(0);
			$table->string('database_connection', 255);
			$table->integer('database_id')->default(0);
			$table->string('database_host', 255);
			$table->string('database_port', 255);
			$table->string('database_name', 255);
			$table->string('database_username', 255);
			$table->integer('database_username_id')->default(0);
			$table->string('database_password', 255);
			$table->integer('storage_shelter')->default(0);
			$table->string('storage_engine', 255);
			$table->integer('storage_id')->default(0);
			$table->string('storage_host', 255);
			$table->string('storage_username', 255);
			$table->string('storage_password', 255);
			$table->string('storage_url', 255);
			$table->string('storage_root', 255);
			$table->string('storage_folder', 255);
			$table->string('storage_path', 255);
			$table->string('storage_timeout', 255);

			$table->string('language_default', 255);
			$table->text('language_usable');
			$table->string('currency_default', 255);
			$table->text('currency_usable');
			$table->integer('theme_id')->default(0);

			$table->integer('partner_id')->default(0);
			$table->tinyInteger('reference')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('wbst_account_access_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_access_translation_id');
			$table->index('access_id');
			$table->integer('access_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->text('description');
			$table->string('tag', 255);
			$table->string('keyword', 255);
			$table->text('about');
			$table->text('issue');
			$table->text('solution');
			$table->text('comment');
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('wbst_account_authorized', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_authorized_id');
			$table->integer('account_id')->default(0);
			$table->integer('access_id')->default(0);
			$table->integer('authorized_group_id')->default(0);
			$table->string('firstname', 255);
			$table->string('lastname', 255);
			$table->string('citizenship_number', 255);
			$table->integer('gender')->default(0);
			$table->timestamp('birth_date', 0)->nullable();
			$table->string('title', 255);
			$table->string('description', 255);
			$table->string('phone_constant', 255);
			$table->string('phone_mobile', 255);
			$table->string('email', 255);
            $table->timestamp('sms_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verified_token');
			$table->string('password', 255);
            $table->rememberToken();
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

		/*
        Schema::connection('lessor')->create('wbst_account_authorized_password_reset', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
		*/

        Schema::connection('lessor')->create('wbst_account_authorized_token_reset', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_authorized_token_reset_id');
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::connection('lessor')->create('wbst_account_token_access', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_token_access_id');
			$table->morphs('tokenable');
			//$table->string('tokenable_type', 255)->unique();
			//$table->bigInteger('tokenable_id')->unique();
			$table->string('name', 255);
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);


            //$table->id();
            //$table->morphs('tokenable');
            //$table->string('name');
            //$table->string('token', 64)->unique();
            //$table->text('abilities')->nullable();
            //$table->timestamp('last_used_at')->nullable();
            //$table->timestamps();
        });

        Schema::connection('lessor')->create('wbst_account_contact', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_contact_id');
			$table->integer('account_id')->default(0);
			$table->integer('taxpayer_type_id')->default(0);
			$table->integer('address_type_id')->default(0);
			$table->tinyInteger('abroad')->default(0);
			$table->string('title', 255);
			$table->string('firstname', 255);
			$table->string('lastname', 255);
			$table->string('citizenship_number', 255);
			$table->string('company', 255);
			$table->string('tax_office', 255);
			$table->string('tax_number', 255);
			$table->string('email', 255);
			$table->string('telephone', 255);
			$table->string('address_1', 255);
			$table->string('address_2', 255);
			$table->integer('country_id')->default(0);
			$table->integer('city_id')->default(0);
			$table->integer('district_id')->default(0);
			$table->string('postcode', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('wbst_account_bank_account', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_bank_account_id');
			$table->integer('account_id')->default(0);
			$table->integer('bank_id')->default(0);
			$table->string('currency_code', 255);
			$table->string('type', 255);
			$table->string('name', 255);
			$table->string('owner', 255);
			$table->string('account_number', 255);
			$table->string('branch_code', 255);
			$table->string('iban_number', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('wbst_account_sms_verify', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_sms_verify_id');
			$table->integer('account_authorized_id')->default(0);
			$table->string('code', 255);
            $table->timestamp('expires_at')->nullable();
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

        Schema::dropIfExists('wbst_account');
        Schema::dropIfExists('wbst_account_authorized');
        Schema::dropIfExists('wbst_account_authorized_password_reset');
        Schema::dropIfExists('wbst_account_authorized_access_tokens');
        Schema::dropIfExists('wbst_account_access');
        Schema::dropIfExists('wbst_account_contact');
        Schema::dropIfExists('wbst_account_sms_verify');

    }
};
