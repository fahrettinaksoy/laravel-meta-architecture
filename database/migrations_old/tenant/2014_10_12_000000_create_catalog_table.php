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

        Schema::create('ctlg_product', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_id');
			$table->string('code', 255);
			$table->integer('type_id')->default(0);
			$table->integer('variant_stock_id')->default(0);
			$table->integer('recurring_id')->default(0);
			$table->integer('condition_id')->default(0);
			$table->tinyInteger('adult')->default(0);
			$table->tinyInteger('domestic')->default(0);
			$table->string('origin', 255)->nullable();
			$table->integer('category_id')->default(0);
			$table->integer('brand_id')->default(0);
			$table->string('model', 255);
			$table->string('barcode', 255);
			$table->string('sku', 255);
			$table->string('upc', 255)->nullable();
			$table->string('ean', 255)->nullable();
			$table->string('jan', 255)->nullable();
			$table->string('isbn', 255)->nullable();
			$table->string('mpn', 255)->nullable();
			$table->string('oem', 255)->nullable();
			$table->string('image_cover', 255);
			$table->string('image_hover', 255);
			$table->string('video_cover', 255);
			$table->string('live_broadcast_url', 255);
			$table->integer('minimum')->default(0);
			$table->integer('maximum')->default(0);
			$table->integer('coefficient')->default(0);
			$table->integer('unit_id')->default(0);
			$table->integer('stockless_id')->default(0);
			$table->integer('subtract')->default(0);
			$table->decimal('buy_price', 19, 2);
			$table->string('buy_currency_code', 255);
			$table->integer('buy_tax_class_id')->default(0);
			$table->decimal('sell_price', 19, 2);
			$table->string('sell_currency_code', 255);
			$table->integer('sell_tax_class_id')->default(0);
			$table->integer('sell_point')->default(0);
			$table->decimal('discount_value', 19, 2);
			$table->string('discount_type', 255);
			$table->decimal('special_consumption_tax', 19, 2)->nullable();
			$table->decimal('special_communication_tax', 19, 2)->nullable();
			$table->integer('weight_class_id')->default(0);
			$table->integer('weight')->default(0);
			$table->integer('length_class_id')->default(0);
			$table->integer('length')->default(0);
			$table->integer('width')->default(0);
			$table->integer('height')->default(0);
			$table->string('desi', 255)->nullable();
			$table->integer('warranty_period')->default(0);
			$table->enum('warranty_type', ['day', 'week', 'month', 'year'])->nullable();
			$table->integer('shipping')->default(0);
			$table->integer('delivery_time')->default(0);
			$table->enum('delivery_type', ['day', 'week', 'month', 'year'])->nullable();
			$table->integer('cargo_time')->default(0);
            $table->enum('cargo_type', ['day', 'week', 'month', 'year'])->nullable();
			$table->decimal('cargo_price', 19, 2)->nullable();
			$table->string('cargo_currency_code', 255)->nullable();
			$table->integer('cargo_tax_class_id')->default(0);
			$table->enum('cargo_method', ['free','flat','weight','item','pickup','category','product'])->nullable();
			$table->tinyInteger('installment_status')->default(0);
			$table->integer('installment_rate')->default(0);
			$table->integer('point_reward')->default(0);
			$table->integer('viewed')->default(0);
			$table->integer('layout_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status_return')->default(0);
			$table->tinyInteger('status_membership')->default(0);
			$table->tinyInteger('status_usage')->default(0);
			$table->tinyInteger('status_publishing')->default(0);
			$table->timestamp('date_production', 0)->nullable();
			$table->timestamp('date_expiration', 0)->nullable();
			$table->timestamp('date_publishing_start', 0)->nullable();
			$table->timestamp('date_publishing_end', 0)->nullable();
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_account_price', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_account_price_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('account_type_id')->default(0);
			$table->decimal('price', 19, 2);
			$table->string('currency_code', 255);
			$table->integer('tax_class_id')->default(0);
			$table->integer('point_sales')->default(0);
			$table->integer('point_reward')->default(0);
			$table->decimal('discount_value', 19, 2);
			$table->string('discount_type', 255);
			$table->tinyInteger('login_show')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_translation_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->text('description');
			$table->string('tag', 255);
			$table->string('keyword', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_activity', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_activity_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('entry_id')->default(0);
			$table->integer('relation_id')->default(0);
			$table->integer('module_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->integer('product_variant_stock_id')->default(0);
			$table->integer('quantity')->default(0);
			$table->decimal('price', 19, 2);
			$table->string('currency_code', 255);
			$table->integer('tax_class_id')->default(0);
			$table->string('code', 255);
			$table->text('description');
			$table->integer('warehouse_id')->default(0);
			$table->integer('area_id')->default(0);
			$table->integer('shelf_id')->default(0);
			$table->tinyInteger('approval')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_reward', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_reward_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('account_group_id')->default(0);
			$table->integer('point')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_image', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_image_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
            $table->integer('product_variant_stock_id')->default(0);
            $table->string('file', 255);
            $table->string('description', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_video', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_video_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
            $table->enum('source', ['code', 'url', 'file', 'embed']);
            $table->string('content', 255);
            $table->string('name', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_related', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_related_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('related_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_post', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_post_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('post_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_accessory', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_accessory_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('accessory_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_download', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_download_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('download_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_faq', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_faq_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('faq_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_attribute', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_attribute_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('attribute_variable_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_attribute_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_attribute_translation_id');
			$table->index('product_attribute_id');
			$table->integer('product_attribute_id')->default(0);
			$table->string('language_code', 255);
			$table->string('text', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_field', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_field_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('field_type_id')->default(0);
			$table->string('image', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_field_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_field_translation_id');
			$table->index('product_field_id');
			$table->integer('product_field_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('content', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_filter_value', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_filter_value_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('filter_value_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_option', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_option_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('option_id')->default(0);
			$table->integer('required')->default(0);
			$table->integer('sort_order')->default(0);
			$table->string('value', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_option_value', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_option_value_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('product_option_id')->default(0);
			$table->integer('option_value_id')->default(0);
			$table->string('sku', 255);
			$table->integer('quantity')->default(0);
			$table->decimal('buy_price', 19, 2);
			$table->string('buy_price_prefix', 255);
			$table->decimal('sell_price', 19, 2);
			$table->string('sell_price_prefix', 255);
			$table->integer('sell_point')->default(0);
			$table->string('sell_point_prefix', 255);
			$table->integer('weight')->default(0);
			$table->string('weight_prefix', 255);
			$table->decimal('discount_value', 19, 2);
			$table->string('discount_type', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_variant', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_variant_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('variant_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_variant_stock', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_variant_stock_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->string('sku', 255);
			$table->integer('quantity')->default(0);
			$table->decimal('buy_price', 19, 2);
			$table->decimal('sell_price', 19, 2);
			$table->integer('sell_point')->default(0);
			$table->integer('weight')->default(0);
			$table->decimal('discount_value', 19, 2);
			$table->string('discount_type', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_variant_variable', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_variant_variable_id');
			$table->index('product_id');
			$table->integer('product_variant_stock_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->integer('variant_id')->default(0);
			$table->integer('variant_variable_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		/*
        Schema::create('ctlg_product_variant_variable', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_stock_variant_id');
			$table->index('product_id');
			$table->integer('product_variant_stock_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->integer('variant_id')->default(0);
			$table->integer('variant_variable_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		*/

        Schema::create('ctlg_product_grouped', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_grouped_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('grouped_id')->default(0);
			$table->integer('opening')->default(0);
			$table->integer('minimum')->default(0);
			$table->integer('maximum')->default(0);
			$table->integer('coefficient')->default(0);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_bundle', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_bundle_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('bundle_id')->default(0);
			$table->integer('quantity')->default(0);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_recurring', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_recurring_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('recurring_type_id')->default(0);
			$table->integer('account_type_id')->default(0);
			$table->integer('default')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_document', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_document_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->string('code', 255);
			$table->string('image', 255);
			$table->string('file', 255);
			$table->string('filename', 255);
			$table->string('mask', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_product_document_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('product_document_translation_id');
			$table->index('product_document_id');
			$table->integer('product_document_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_id');
			$table->integer('parent_id')->default(0);
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('layout_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->integer('membership')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_category_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_translation_id');
			$table->index('category_id');
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

        Schema::create('ctlg_category_brand', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_brand_id');
			$table->index('category_id');
			$table->integer('category_id')->default(0);
			$table->integer('brand_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_category_filter', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_filter_id');
			$table->index('category_id');
			$table->integer('category_id')->default(0);
			$table->integer('filter_value_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_brand', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('brand_id');
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('layout_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_brand_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('brand_translation_id');
			$table->index('brand_id');
			$table->integer('brand_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->text('description');
			$table->string('tag', 255);
			$table->string('keyword', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_download', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('download_id');
			$table->string('code', 255);
			$table->string('filename', 255);
			$table->string('mask', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_download_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('download_translation_id');
			$table->index('download_id');
			$table->integer('download_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_review', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('review_id');
			$table->index('product_id');
			$table->integer('product_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->string('code', 255);
			$table->string('author', 255);
			$table->text('content');
			$table->integer('rating')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('ctlg_warehouse', function (Blueprint $table) {
            $table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('warehouse_id');
			$table->integer('category_id')->default(0);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('authorized', 255);
			$table->string('country_id', 255);
			$table->string('city_id', 255);
			$table->string('district_id', 255);
			$table->string('postcode', 255);
			$table->string('address', 255);
			$table->string('description', 255);
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
        Schema::dropIfExists('ctlg_product');
        Schema::dropIfExists('ctlg_product_translation');
        Schema::dropIfExists('ctlg_product_activity');
        Schema::dropIfExists('ctlg_product_reward');
        Schema::dropIfExists('ctlg_product_image');
        Schema::dropIfExists('ctlg_product_video');
        Schema::dropIfExists('ctlg_product_related');
        Schema::dropIfExists('ctlg_product_post');
        Schema::dropIfExists('ctlg_product_accessory');
        Schema::dropIfExists('ctlg_product_download');
        Schema::dropIfExists('ctlg_product_faq');
        Schema::dropIfExists('ctlg_product_attribute');
        Schema::dropIfExists('ctlg_product_attribute_translation');
        Schema::dropIfExists('ctlg_product_field');
        Schema::dropIfExists('ctlg_product_field_translation');
        Schema::dropIfExists('ctlg_product_filter_value');
        Schema::dropIfExists('ctlg_product_option');
        Schema::dropIfExists('ctlg_product_option_value');
        Schema::dropIfExists('ctlg_product_variant');
        Schema::dropIfExists('ctlg_product_variant_variable');
        Schema::dropIfExists('ctlg_product_variant_stock');
        Schema::dropIfExists('ctlg_product_variant_variable');
        Schema::dropIfExists('ctlg_product_grouped');
        Schema::dropIfExists('ctlg_product_bundle');
        Schema::dropIfExists('ctlg_product_document');
        Schema::dropIfExists('ctlg_product_document_translation');

        Schema::dropIfExists('ctlg_category');
        Schema::dropIfExists('ctlg_category_translation');
        Schema::dropIfExists('ctlg_category_brand');
        Schema::dropIfExists('ctlg_category_filter');

        Schema::dropIfExists('ctlg_brand');
        Schema::dropIfExists('ctlg_brand_translation');

        Schema::dropIfExists('ctlg_download');
        Schema::dropIfExists('ctlg_download_translation');

        Schema::dropIfExists('ctlg_review');

        Schema::dropIfExists('ctlg_warehouse');
    }

};
