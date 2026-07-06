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

        Schema::connection('lessor')->create('dfntn_gnrl_branch', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('branch_id');
			$table->string('code', 255);
			$table->string('type', 255);
			$table->string('name', 255);
			$table->string('company', 255);
			$table->string('authorized', 255);
			$table->string('image', 255);
			$table->string('website', 255);
			$table->string('email', 255);
			$table->string('phone_number', 255);
			$table->string('fax_number', 255);
			$table->string('gsm_number', 255);
			$table->string('country_id', 255);
			$table->string('city_id', 255);
			$table->string('district_id', 255);
			$table->string('postcode', 255);
			$table->string('address_1', 255);
			$table->string('address_2', 255);
			$table->string('map_coordinate', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('prtnr_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_id');
			$table->integer('parent_id')->default(0);
			$table->integer('type_id')->default(0);
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('prtnr_category_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_translation_id');
			$table->integer('category_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->text('description');
			$table->string('keyword', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('prtnr_business', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('business_id');
			$table->string('code', 255);
			$table->string('type', 255);
			$table->string('name', 255);
			$table->string('company', 255);
			$table->string('authorized', 255);
			$table->string('image', 255);
			$table->string('website', 255);
			$table->string('email', 255);
			$table->string('phone_number', 255);
			$table->string('fax_number', 255);
			$table->string('gsm_number', 255);
			$table->string('country_id', 255);
			$table->string('city_id', 255);
			$table->string('district_id', 255);
			$table->string('postcode', 255);
			$table->string('address_1', 255);
			$table->string('address_2', 255);
			$table->string('map_coordinate', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('prtnr_business_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('business_translation_id');
			$table->integer('business_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->text('description');
			$table->text('about');
			$table->text('advantage');
			$table->text('application');
			$table->string('keyword', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('prtnr_business_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('business_category_id');
			$table->integer('business_id')->default(0);
			$table->integer('category_id')->default(0);
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
        Schema::dropIfExists('dfntn_gnrl_branch');

        Schema::dropIfExists('prtnr_category');
        Schema::dropIfExists('prtnr_category_translation');
        Schema::dropIfExists('prtnr_business');
        Schema::dropIfExists('prtnr_business_translation');
        Schema::dropIfExists('prtnr_business_category');
    }

};
