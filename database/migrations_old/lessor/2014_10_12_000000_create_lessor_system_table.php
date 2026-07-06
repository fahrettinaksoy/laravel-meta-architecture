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

        Schema::connection('lessor')->create('systm_setting', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('setting_id');
			$table->string('option', 255);
			$table->string('slug', 255);
			$table->text('value');
			$table->tinyInteger('serialized')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('systm_company', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('definition_id');
			$table->string('option', 255);
			$table->string('slug', 255);
			$table->text('value');
			$table->tinyInteger('view')->default(0);
			$table->tinyInteger('serialized')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('systm_method', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('method_id');
			$table->string('method_group', 255);
			$table->string('method_type', 255);
			$table->text('setting');
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('systm_user_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('user_group_id');
            $table->string('code', 255);
            $table->string('name', 255);
            $table->string('description', 255);
			$table->text('permission');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('systm_user', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('user_id');
            $table->string('code', 255);
            $table->string('firstname', 255);
            $table->string('lastname', 255);
            $table->string('image', 255);
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
			$table->integer('user_group_id')->default(0);
            $table->string('password');
            $table->rememberToken();
            $table->string('ip_address');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('systm_user_token_reset', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('user_token_reset_id');
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::connection('lessor')->create('systm_user_token_access', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('user_token_access_id');
			$table->morphs('tokenable');
			$table->string('name', 255);
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('systm_widget', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('widget_id');
			$table->string('widget_type', 255);
			$table->string('name', 255);
			$table->string('css', 255);
			$table->string('html', 255);
			$table->text('setting');
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

        Schema::dropIfExists('systm_user_group');
        Schema::dropIfExists('systm_user');
        Schema::dropIfExists('systm_user_token_reset');
        Schema::dropIfExists('systm_user_token_access');
        Schema::dropIfExists('systm_setting');
        Schema::dropIfExists('systm_company');
        Schema::dropIfExists('systm_method');

    }
};
