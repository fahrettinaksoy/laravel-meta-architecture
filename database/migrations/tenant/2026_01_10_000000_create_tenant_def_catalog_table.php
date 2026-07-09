<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const BRAND_TABLE = 'def_cat_brand';
    private const BRAND_TRANSLATION_TABLE = 'def_cat_brand_translation';
    private const CATEGORY_TABLE = 'def_cat_category';
    private const CATEGORY_TRANSLATION_TABLE = 'def_cat_category_translation';
    private const CATEGORY_BRAND_TABLE = 'def_cat_category_brand';
    private const CATEGORY_FILTER_TABLE = 'def_cat_category_filter';

    private const PRODUCT_TYPE_TABLE = 'def_cat_product_type';
    private const PRODUCT_TYPE_TRANSLATION_TABLE = 'def_cat_product_type_translation';

    private const PRODUCT_CONDITION_TABLE = 'def_cat_product_condition';
    private const PRODUCT_CONDITION_TRANSLATION_TABLE = 'def_cat_product_condition_translation';

    private const PRODUCT_STOCKLESS_TABLE = 'def_cat_product_stockless';
    private const PRODUCT_STOCKLESS_TRANSLATION_TABLE = 'def_cat_product_stockless_translation';

    private const PRODUCT_RELATION_TABLE = 'def_cat_product_relation';
    private const PRODUCT_RELATION_TRANSLATION_TABLE = 'def_cat_product_relation_translation';

    private const PRODUCT_FIELD_TYPE_TABLE = 'def_cat_product_field_type';
    private const PRODUCT_FIELD_TYPE_TRANSLATION_TABLE = 'def_cat_product_field_type_translation';

    private const PRODUCT_VARIANT_TABLE = 'def_cat_product_variant';
    private const PRODUCT_VARIANT_TRANSLATION_TABLE = 'def_cat_product_variant_translation';
    private const PRODUCT_VARIANT_VARIABLE_TABLE = 'def_cat_product_variant_variable';
    private const PRODUCT_VARIANT_VARIABLE_TRANSLATION_TABLE = 'def_cat_product_variant_variable_translation';

    private const PRODUCT_OPTION_TABLE = 'def_cat_product_option';
    private const PRODUCT_OPTION_TRANSLATION_TABLE = 'def_cat_product_option_translation';
    private const PRODUCT_OPTION_VALUE_TABLE = 'def_cat_product_option_value';
    private const PRODUCT_OPTION_VALUE_TRANSLATION_TABLE = 'def_cat_product_option_value_translation';

    private const PRODUCT_FILTER_TABLE = 'def_cat_product_filter';
    private const PRODUCT_FILTER_TRANSLATION_TABLE = 'def_cat_product_filter_translation';
    private const PRODUCT_FILTER_VALUE_TABLE = 'def_cat_product_filter_value';
    private const PRODUCT_FILTER_VALUE_TRANSLATION_TABLE = 'def_cat_product_filter_value_translation';

    private const PRODUCT_ATTRIBUTE_TEMPLATE_TABLE = 'def_cat_product_attribute_template';
    private const PRODUCT_ATTRIBUTE_GROUP_TABLE = 'def_cat_product_attribute_group';
    private const PRODUCT_ATTRIBUTE_GROUP_TRANSLATION_TABLE = 'def_cat_product_attribute_group_translation';
    private const PRODUCT_ATTRIBUTE_VARIABLE_TABLE = 'def_cat_product_attribute_variable';
    private const PRODUCT_ATTRIBUTE_VARIABLE_TRANSLATION_TABLE = 'def_cat_product_attribute_variable_translation';

    private const PRODUCT_RECURRING_TYPE_TABLE = 'def_cat_product_recurring_type';
    private const PRODUCT_RECURRING_TYPE_TRANSLATION_TABLE = 'def_cat_product_recurring_type_translation';

    private const PRODUCT_STOCK_ENTRY_TABLE = 'def_cat_product_stock_entry';
    private const PRODUCT_STOCK_ENTRY_TRANSLATION_TABLE = 'def_cat_product_stock_entry_translation';

    private const PRODUCT_STATUS_TABLE = 'def_cat_product_status';
    private const PRODUCT_STATUS_TRANSLATION_TABLE = 'def_cat_product_status_translation';

    private const PRODUCT_REVIEW_STATUS_TABLE = 'def_cat_product_review_status';
    private const PRODUCT_REVIEW_STATUS_TRANSLATION_TABLE = 'def_cat_product_review_status_translation';

    public function up(): void
    {
        Schema::create(self::PRODUCT_STATUS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün durumu tanımları: draft, active, passive, archived, out_of_stock (cat_product.status_id buraya referans verir)');

            $table->bigIncrements('product_status_id')->comment('Ürün durumu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_product_status_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 32)->collation('ascii_general_ci')->unique('uq_product_status_code')->comment('Sabit kod — benzersiz (uygulama katmanı bu koda göre eşleştirir)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
        });

        Schema::create(self::PRODUCT_STATUS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün durumu çevirileri (dile göre görünen ad)');

            $table->bigIncrements('product_status_translation_id')->comment('Ürün durumu çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_product_status_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_status_id')->comment('Bağlı olduğu ürün durumu (def_cat_product_status)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Görünen ad');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_status_id', 'language_code'], 'uq_product_status_translation_lang');
            $table->index('product_status_id', 'ix_product_status_translation_status_id');
        });

        Schema::create(self::PRODUCT_REVIEW_STATUS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Değerlendirme durumu tanımları: pending, approved, rejected, spam (cat_review.status_id buraya referans verir)');

            $table->bigIncrements('review_status_id')->comment('Değerlendirme durumu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_review_status_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 32)->collation('ascii_general_ci')->unique('uq_review_status_code')->comment('Sabit kod — benzersiz (uygulama katmanı bu koda göre eşleştirir)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
        });

        Schema::create(self::PRODUCT_REVIEW_STATUS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Değerlendirme durumu çevirileri (dile göre görünen ad)');

            $table->bigIncrements('review_status_translation_id')->comment('Değerlendirme durumu çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_review_status_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('review_status_id')->comment('Bağlı olduğu değerlendirme durumu (def_cat_product_review_status)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Görünen ad');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['review_status_id', 'language_code'], 'uq_review_status_translation_lang');
            $table->index('review_status_id', 'ix_review_status_translation_status_id');
        });

        Schema::create(self::BRAND_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Marka kartları (katalog tanım tablosu)');

            $table->bigIncrements('brand_id')->comment('Marka için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_brand_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_brand_code')->comment('Marka kodu/slug — benzersiz');
            $table->string('image_path', 500)->nullable()->comment('Marka logosu dosya yolu');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (site_layout tablosuna referans)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('layout_id', 'ix_brand_layout_id');
        });

        Schema::create(self::BRAND_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Marka çevirileri (dile göre ad, açıklama ve meta bilgileri)');

            $table->bigIncrements('brand_translation_id')->comment('Marka çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_brand_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('brand_id')->comment('Bağlı olduğu marka (def_cat_brand)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Marka adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->text('description')->nullable()->comment('Marka açıklaması (HTML içerebilir)');
            $table->string('tag')->nullable()->comment('Etiketler (virgülle ayrılmış)');
            $table->string('keyword')->nullable()->comment('Arama anahtar kelimeleri');
            $table->string('meta_title')->nullable()->comment('SEO meta başlık');
            $table->string('meta_description')->nullable()->comment('SEO meta açıklama');
            $table->string('meta_keyword')->nullable()->comment('SEO meta anahtar kelimeler');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['brand_id', 'language_code'], 'uq_brand_translation_lang');
            $table->index('brand_id', 'ix_brand_translation_brand_id');
        });

        Schema::create(self::CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün kategorileri (kategori ağacı; katalog tanım tablosu)');

            $table->bigIncrements('category_id')->comment('Kategori için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_category_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Üst kategori kimliği (kendine referans); null ise kök kategori');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_category_code')->comment('Kategori kodu/slug — benzersiz');
            $table->string('image_path', 500)->nullable()->comment('Kategori görseli dosya yolu');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (site_layout tablosuna referans)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_members_only')->default(false)->comment('Yalnızca üyelere açık mı?');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('parent_id', 'ix_category_parent_id');
        });

        Schema::create(self::CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kategori çevirileri (dile göre ad, açıklama ve meta bilgileri)');

            $table->bigIncrements('category_translation_id')->comment('Kategori çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_category_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('category_id')->comment('Bağlı olduğu kategori (def_cat_category)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Kategori adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->text('description')->nullable()->comment('Kategori açıklaması (HTML içerebilir)');
            $table->string('keyword')->nullable()->comment('Arama anahtar kelimeleri');
            $table->string('meta_title')->nullable()->comment('SEO meta başlık');
            $table->string('meta_description')->nullable()->comment('SEO meta açıklama');
            $table->string('meta_keyword')->nullable()->comment('SEO meta anahtar kelimeler');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['category_id', 'language_code'], 'uq_category_translation_lang');
            $table->index('category_id', 'ix_category_translation_category_id');
        });

        Schema::create(self::CATEGORY_BRAND_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kategori-marka eşleştirmeleri (kategoride gösterilecek markalar)');

            $table->bigIncrements('category_brand_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('category_id')->comment('Bağlı olduğu kategori (def_cat_category)');
            $table->unsignedBigInteger('brand_id')->comment('Bağlı olduğu marka (def_cat_brand)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['category_id', 'brand_id'], 'uq_category_brand');
            $table->index('brand_id');

            $table->foreign('category_id')->references('category_id')->on(self::CATEGORY_TABLE)->cascadeOnDelete();
            $table->foreign('brand_id')->references('brand_id')->on(self::BRAND_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CATEGORY_FILTER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kategoride kullanılacak filtre değerleri');

            $table->bigIncrements('category_filter_id')->comment('Kategori filtresi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('category_id')->comment('Bağlı olduğu kategori (def_cat_category)');
            $table->unsignedBigInteger('filter_value_id')->comment('Filtre değeri kimliği (def_cat_product_filter_value tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['category_id', 'filter_value_id'], 'uq_category_filter_value');
            $table->index('filter_value_id');

            $table->foreign('category_id')->references('category_id')->on(self::CATEGORY_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün türleri: fiziksel/dijital/hizmet vb. (katalog tanım tablosu)');

            $table->bigIncrements('product_type_id')->comment('Ürün türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Ürün türü kodu — benzersiz');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('product_type_translation_id')->comment('Ürün türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_type_id')->comment('Bağlı olduğu ürün türü (def_cat_product_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Ürün türü adı');
            $table->string('description')->nullable()->comment('Ürün türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_type_id', 'language_code'], 'uq_product_type_translation_lang');

            $table->foreign('product_type_id')->references('product_type_id')->on(self::PRODUCT_TYPE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_CONDITION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün kondisyonları: sıfır/ikinci el/yenilenmiş vb. (katalog tanım tablosu)');

            $table->bigIncrements('product_condition_id')->comment('Ürün kondisyonu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Kondisyon kodu — benzersiz');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_CONDITION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün kondisyonu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('product_condition_translation_id')->comment('Kondisyon çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_condition_id')->comment('Bağlı olduğu ürün kondisyonu (def_cat_product_condition)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Kondisyon adı');
            $table->string('description')->nullable()->comment('Kondisyon açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_condition_id', 'language_code'], 'uq_product_condition_translation_lang');

            $table->foreign('product_condition_id')->references('product_condition_id')->on(self::PRODUCT_CONDITION_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_STOCKLESS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Stok bittiğinde davranış tanımları: satışa devam/bekleme/kapalı vb. (katalog tanım tablosu)');

            $table->bigIncrements('product_stockless_id')->comment('Stoksuz davranış tanımı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Davranış kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_STOCKLESS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Stoksuz davranış çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('product_stockless_translation_id')->comment('Davranış çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_stockless_id')->comment('Bağlı olduğu stoksuz davranış tanımı (def_cat_product_stockless)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Davranış adı');
            $table->string('description')->nullable()->comment('Davranış açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_stockless_id', 'language_code'], 'uq_product_stockless_translation_lang');

            $table->foreign('product_stockless_id')->references('product_stockless_id')->on(self::PRODUCT_STOCKLESS_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_RELATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün ilişki türleri (stok hareketi/belge ilişkilendirme tanımları)');

            $table->bigIncrements('product_relation_id')->comment('İlişki türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('İlişki türü kodu — benzersiz');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_RELATION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün ilişki türü çevirileri (dile göre ad)');

            $table->bigIncrements('product_relation_translation_id')->comment('İlişki türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_relation_id')->comment('Bağlı olduğu ilişki türü (def_cat_product_relation)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('İlişki türü adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_relation_id', 'language_code'], 'uq_product_relation_translation_lang');

            $table->foreign('product_relation_id')->references('product_relation_id')->on(self::PRODUCT_RELATION_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_FIELD_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün ek alan türleri (katalog tanım tablosu)');

            $table->bigIncrements('field_type_id')->comment('Ek alan türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Alan türü kodu — benzersiz');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_FIELD_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün ek alan türü çevirileri (dile göre ad)');

            $table->bigIncrements('field_type_translation_id')->comment('Alan türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('field_type_id')->comment('Bağlı olduğu alan türü (def_cat_product_field_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Alan türü adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['field_type_id', 'language_code'], 'uq_field_type_translation_lang');

            $table->foreign('field_type_id')->references('field_type_id')->on(self::PRODUCT_FIELD_TYPE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_VARIANT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Varyant tanımları: renk, beden vb. (katalog tanım tablosu)');

            $table->bigIncrements('variant_id')->comment('Varyant için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Varyant kodu — benzersiz');
            $table->string('type', 50)->nullable()->comment('Arayüz sunum türü (örn. select, image, color)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_VARIANT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Varyant çevirileri (dile göre ad)');

            $table->bigIncrements('variant_translation_id')->comment('Varyant çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('variant_id')->comment('Bağlı olduğu varyant (def_cat_product_variant)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Varyant adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['variant_id', 'language_code'], 'uq_variant_translation_lang');

            $table->foreign('variant_id')->references('variant_id')->on(self::PRODUCT_VARIANT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_VARIANT_VARIABLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Varyant değişkenleri: kırmızı, XL vb. (varyanta bağlı değerler)');

            $table->bigIncrements('variant_variable_id')->comment('Varyant değişkeni için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('variant_id')->comment('Bağlı olduğu varyant (def_cat_product_variant)');
            $table->string('image', 500)->nullable()->comment('Değişken görseli dosya yolu (örn. kumaş deseni)');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('variant_id')->references('variant_id')->on(self::PRODUCT_VARIANT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_VARIANT_VARIABLE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Varyant değişkeni çevirileri (dile göre ad)');

            $table->bigIncrements('variant_variable_translation_id')->comment('Değişken çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('variant_variable_id')->comment('Bağlı olduğu varyant değişkeni (def_cat_product_variant_variable)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Değişken adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['variant_variable_id', 'language_code'], 'uq_variant_variable_translation_lang');

            $table->foreign('variant_variable_id')->references('variant_variable_id')->on(self::PRODUCT_VARIANT_VARIABLE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_OPTION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Seçenek tanımları: sipariş sırasında seçilebilen opsiyonlar (katalog tanım tablosu)');

            $table->bigIncrements('option_id')->comment('Seçenek için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Seçenek kodu — benzersiz');
            $table->string('type', 50)->nullable()->comment('Girdi türü (örn. select, radio, checkbox, text, textarea, file, date, time)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_OPTION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Seçenek çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('option_translation_id')->comment('Seçenek çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('option_id')->comment('Bağlı olduğu seçenek (def_cat_product_option)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Seçenek adı');
            $table->string('description')->nullable()->comment('Seçenek açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['option_id', 'language_code'], 'uq_option_translation_lang');

            $table->foreign('option_id')->references('option_id')->on(self::PRODUCT_OPTION_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_OPTION_VALUE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Seçenek değerleri (seçeneğe bağlı önceden tanımlı değerler)');

            $table->bigIncrements('option_value_id')->comment('Seçenek değeri için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('option_id')->comment('Bağlı olduğu seçenek (def_cat_product_option)');
            $table->string('code', 64)->nullable()->comment('Değer kodu');
            $table->string('image', 500)->nullable()->comment('Değer görseli dosya yolu');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('option_id')->references('option_id')->on(self::PRODUCT_OPTION_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_OPTION_VALUE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Seçenek değeri çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('option_value_translation_id')->comment('Değer çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('option_value_id')->comment('Bağlı olduğu seçenek değeri (def_cat_product_option_value)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Değer adı');
            $table->string('description')->nullable()->comment('Değer açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['option_value_id', 'language_code'], 'uq_option_value_translation_lang');

            $table->foreign('option_value_id')->references('option_value_id')->on(self::PRODUCT_OPTION_VALUE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_FILTER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Filtre grupları: listeleme filtreleri (katalog tanım tablosu)');

            $table->bigIncrements('filter_id')->comment('Filtre grubu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Filtre grubu kodu — benzersiz');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_FILTER_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Filtre grubu çevirileri (dile göre ad)');

            $table->bigIncrements('filter_translation_id')->comment('Filtre çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('filter_id')->comment('Bağlı olduğu filtre grubu (def_cat_product_filter)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Filtre grubu adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['filter_id', 'language_code'], 'uq_filter_translation_lang');

            $table->foreign('filter_id')->references('filter_id')->on(self::PRODUCT_FILTER_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_FILTER_VALUE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Filtre değerleri (filtre grubuna bağlı seçenekler)');

            $table->bigIncrements('filter_value_id')->comment('Filtre değeri için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('filter_id')->comment('Bağlı olduğu filtre grubu (def_cat_product_filter)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('filter_id')->references('filter_id')->on(self::PRODUCT_FILTER_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_FILTER_VALUE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Filtre değeri çevirileri (dile göre ad)');

            $table->bigIncrements('filter_value_translation_id')->comment('Değer çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('filter_value_id')->comment('Bağlı olduğu filtre değeri (def_cat_product_filter_value)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Değer adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['filter_value_id', 'language_code'], 'uq_filter_value_translation_lang');

            $table->foreign('filter_value_id')->references('filter_value_id')->on(self::PRODUCT_FILTER_VALUE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_TEMPLATE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Özellik şablonları (özellik gruplarını bir arada tutan üst yapı)');

            $table->bigIncrements('attribute_template_id')->comment('Özellik şablonu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Şablon kodu — benzersiz');
            $table->string('name', 150)->comment('Şablon adı (iç kullanım, çevirisiz)');
            $table->string('description')->nullable()->comment('Şablon açıklaması');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Özellik grupları (örn. Ekran, İşlemci, Bellek)');

            $table->bigIncrements('attribute_group_id')->comment('Özellik grubu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('attribute_template_id')->nullable()->comment('Bağlı olduğu özellik şablonu (def_cat_product_attribute_template); null ise bağımsız grup');
            $table->string('code', 64)->unique()->comment('Grup kodu — benzersiz');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');

            $table->foreign('attribute_template_id')->references('attribute_template_id')->on(self::PRODUCT_ATTRIBUTE_TEMPLATE_TABLE)->nullOnDelete();
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_GROUP_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Özellik grubu çevirileri (dile göre ad)');

            $table->bigIncrements('attribute_group_translation_id')->comment('Grup çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('attribute_group_id')->comment('Bağlı olduğu özellik grubu (def_cat_product_attribute_group)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Grup adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['attribute_group_id', 'language_code'], 'uq_attribute_group_translation_lang');

            $table->foreign('attribute_group_id')->references('attribute_group_id')->on(self::PRODUCT_ATTRIBUTE_GROUP_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_VARIABLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Özellik değişkenleri: gruba bağlı özellikler (örn. ekran boyutu, çözünürlük)');

            $table->bigIncrements('attribute_variable_id')->comment('Özellik değişkeni için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('attribute_group_id')->nullable()->comment('Bağlı olduğu özellik grubu (def_cat_product_attribute_group); null ise bağımsız özellik');
            $table->string('code', 64)->unique()->comment('Özellik kodu — benzersiz');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');

            $table->foreign('attribute_group_id')->references('attribute_group_id')->on(self::PRODUCT_ATTRIBUTE_GROUP_TABLE)->nullOnDelete();
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_VARIABLE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Özellik değişkeni çevirileri (dile göre ad)');

            $table->bigIncrements('attribute_variable_translation_id')->comment('Değişken çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('attribute_variable_id')->comment('Bağlı olduğu özellik değişkeni (def_cat_product_attribute_variable)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Özellik adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['attribute_variable_id', 'language_code'], 'uq_attribute_variable_translation_lang');

            $table->foreign('attribute_variable_id')->references('attribute_variable_id')->on(self::PRODUCT_ATTRIBUTE_VARIABLE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_RECURRING_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tekrarlayan ödeme/abonelik planı tanımları (katalog tanım tablosu)');

            $table->bigIncrements('recurring_type_id')->comment('Tekrarlama planı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Plan kodu — benzersiz');
            $table->unsignedSmallInteger('duration')->default(0)->comment('Bir faturalama periyodunun uzunluğu (frequency birimi cinsinden)');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly'])->comment('Faturalama sıklığı: daily=günlük, weekly=haftalık, monthly=aylık, yearly=yıllık');
            $table->unsignedSmallInteger('cycle')->default(0)->comment('Toplam tekrar sayısı; 0 ise süresiz');
            $table->boolean('has_trial')->default(false)->comment('Deneme dönemi var mı?');
            $table->decimal('trial_price', 19, 2)->default(0)->comment('Deneme dönemi fiyatı');
            $table->unsignedSmallInteger('trial_duration')->default(0)->comment('Deneme periyodunun uzunluğu (trial_frequency birimi cinsinden)');
            $table->enum('trial_frequency', ['daily', 'weekly', 'monthly', 'yearly'])->nullable()->comment('Deneme faturalama sıklığı; null ise deneme yok');
            $table->unsignedSmallInteger('trial_cycle')->default(0)->comment('Deneme dönemi tekrar sayısı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_RECURRING_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tekrarlama planı çevirileri (dile göre ad, özet ve açıklama)');

            $table->bigIncrements('recurring_type_translation_id')->comment('Plan çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('recurring_type_id')->comment('Bağlı olduğu tekrarlama planı (def_cat_product_recurring_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Plan adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->string('description')->nullable()->comment('Plan açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['recurring_type_id', 'language_code'], 'uq_recurring_type_translation_lang');

            $table->foreign('recurring_type_id')->references('recurring_type_id')->on(self::PRODUCT_RECURRING_TYPE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_STOCK_ENTRY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Stok hareket türleri: alış, satış, sayım, fire vb. (katalog tanım tablosu)');

            $table->bigIncrements('stock_entry_id')->comment('Stok hareket türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Hareket türü kodu — benzersiz');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PRODUCT_STOCK_ENTRY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Stok hareket türü çevirileri (dile göre ad)');

            $table->bigIncrements('stock_entry_translation_id')->comment('Hareket türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('stock_entry_id')->comment('Bağlı olduğu stok hareket türü (def_cat_product_stock_entry)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Hareket türü adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['stock_entry_id', 'language_code'], 'uq_stock_entry_translation_lang');

            $table->foreign('stock_entry_id')->references('stock_entry_id')->on(self::PRODUCT_STOCK_ENTRY_TABLE)->cascadeOnDelete();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists(self::PRODUCT_REVIEW_STATUS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_REVIEW_STATUS_TABLE);
        Schema::dropIfExists(self::PRODUCT_STATUS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_STATUS_TABLE);
        Schema::dropIfExists(self::PRODUCT_STOCK_ENTRY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_STOCK_ENTRY_TABLE);
        Schema::dropIfExists(self::PRODUCT_RECURRING_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_RECURRING_TYPE_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_VARIABLE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_VARIABLE_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_GROUP_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_GROUP_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_TEMPLATE_TABLE);
        Schema::dropIfExists(self::PRODUCT_FILTER_VALUE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_FILTER_VALUE_TABLE);
        Schema::dropIfExists(self::PRODUCT_FILTER_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_FILTER_TABLE);
        Schema::dropIfExists(self::PRODUCT_OPTION_VALUE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_OPTION_VALUE_TABLE);
        Schema::dropIfExists(self::PRODUCT_OPTION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_OPTION_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_VARIABLE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_VARIABLE_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_TABLE);
        Schema::dropIfExists(self::PRODUCT_FIELD_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_FIELD_TYPE_TABLE);
        Schema::dropIfExists(self::PRODUCT_RELATION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_RELATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_STOCKLESS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_STOCKLESS_TABLE);
        Schema::dropIfExists(self::PRODUCT_CONDITION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_CONDITION_TABLE);
        Schema::dropIfExists(self::PRODUCT_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_TYPE_TABLE);
        Schema::dropIfExists(self::CATEGORY_FILTER_TABLE);
        Schema::dropIfExists(self::CATEGORY_BRAND_TABLE);
        Schema::dropIfExists(self::CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CATEGORY_TABLE);
        Schema::dropIfExists(self::BRAND_TRANSLATION_TABLE);
        Schema::dropIfExists(self::BRAND_TABLE);
    }
};
