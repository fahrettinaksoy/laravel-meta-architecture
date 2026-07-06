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

        Schema::connection('lessor')->create('mrktng_campaign', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('campaign_id');
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('campaign_type_id')->default(0);
			$table->string('campaign_type_code', 255);
			$table->text('campaign_type_setting');
			$table->integer('layout_id')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

		Schema::connection('lessor')->create('mrktng_campaign_history', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('campaign_history_id');
			$table->integer('campaign_id')->default(0);
			$table->integer('order_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->decimal('amount', 19, 2);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('mrktng_campaign_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('campaign_translation_id');
			$table->integer('campaign_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->longText('description');
			$table->longText('condition');
			$table->string('keyword', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::connection('lessor')->create('mrktng_campaign_product', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('campaign_product_id');
			$table->integer('campaign_id')->default(0);
			$table->integer('product_id')->default(0);
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
        Schema::dropIfExists('mrktng_campaign');
        Schema::dropIfExists('mrktng_campaign_translation');
        Schema::dropIfExists('mrktng_campaign_product');

        Schema::dropIfExists('mrktng_coupon');
        Schema::dropIfExists('mrktng_coupon_history');
        Schema::dropIfExists('mrktng_coupon_product');
        Schema::dropIfExists('mrktng_coupon_category');
        Schema::dropIfExists('mrktng_coupon_brand');
        Schema::dropIfExists('mrktng_coupon_account_group');;

        Schema::dropIfExists('mrktng_gift_voucher');
    }

};
