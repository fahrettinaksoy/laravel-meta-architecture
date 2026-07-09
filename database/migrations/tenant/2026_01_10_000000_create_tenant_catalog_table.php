<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const DOWNLOAD_TABLE = 'cat_download';
    private const DOWNLOAD_TRANSLATION_TABLE = 'cat_download_translation';
    private const WAREHOUSE_TABLE = 'cat_warehouse';
    private const PRODUCT_TABLE = 'cat_product';
    private const PRODUCT_STAT_TABLE = 'cat_product_stat';
    private const PRODUCT_TRANSLATION_TABLE = 'cat_product_translation';
    private const PRODUCT_ACCOUNT_PRICE_TABLE = 'cat_product_account_price';
    private const PRODUCT_VARIANT_TABLE = 'cat_product_variant';
    private const PRODUCT_VARIANT_STOCK_TABLE = 'cat_product_variant_stock';
    private const PRODUCT_VARIANT_VARIABLE_TABLE = 'cat_product_variant_variable';
    private const PRODUCT_ACTIVITY_TABLE = 'cat_product_activity';
    private const PRODUCT_REWARD_TABLE = 'cat_product_reward';
    private const PRODUCT_IMAGE_TABLE = 'cat_product_image';
    private const PRODUCT_VIDEO_TABLE = 'cat_product_video';
    private const PRODUCT_RELATED_TABLE = 'cat_product_related';
    private const PRODUCT_ACCESSORY_TABLE = 'cat_product_accessory';
    private const PRODUCT_DOWNLOAD_TABLE = 'cat_product_download';
    private const PRODUCT_FAQ_TABLE = 'cat_product_faq';
    private const PRODUCT_ATTRIBUTE_TABLE = 'cat_product_attribute';
    private const PRODUCT_ATTRIBUTE_TRANSLATION_TABLE = 'cat_product_attribute_translation';
    private const PRODUCT_FIELD_TABLE = 'cat_product_field';
    private const PRODUCT_FIELD_TRANSLATION_TABLE = 'cat_product_field_translation';
    private const PRODUCT_FILTER_VALUE_TABLE = 'cat_product_filter_value';
    private const PRODUCT_OPTION_TABLE = 'cat_product_option';
    private const PRODUCT_OPTION_VALUE_TABLE = 'cat_product_option_value';
    private const PRODUCT_GROUPED_TABLE = 'cat_product_grouped';
    private const PRODUCT_BUNDLE_TABLE = 'cat_product_bundle';
    private const PRODUCT_RECURRING_TABLE = 'cat_product_recurring';
    private const PRODUCT_DOCUMENT_TABLE = 'cat_product_document';
    private const PRODUCT_DOCUMENT_TRANSLATION_TABLE = 'cat_product_document_translation';
    private const REVIEW_TABLE = 'cat_review';

    public function up(): void
    {
        Schema::create(self::DOWNLOAD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İndirilebilir dosyalar (dijital ürün içerikleri)');

            $table->bigIncrements('download_id')->comment('İndirilebilir dosya için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_download_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_download_code')->comment('Dosya kodu — benzersiz');
            $table->string('file_path', 255)->comment('Sunucudaki gerçek dosya yolu');
            $table->string('display_name', 255)->comment('İndirme sırasında kullanıcıya gösterilecek dosya adı');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
        });

        Schema::create(self::DOWNLOAD_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İndirilebilir dosya çevirileri (dile göre görünen ad)');

            $table->bigIncrements('download_translation_id')->comment('Dosya çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_download_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('download_id')->comment('Bağlı olduğu dosya (cat_download)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Dosyanın görünen adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['download_id', 'language_code'], 'uq_download_translation_lang');
        });

        Schema::create(self::WAREHOUSE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Depolar (stok hareketlerinin bağlandığı fiziksel lokasyonlar)');

            $table->bigIncrements('warehouse_id')->comment('Depo için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_warehouse_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Depo türü kimliği (tanım tablosuna referans)');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_warehouse_code')->comment('Depo kodu — benzersiz');
            $table->string('name', 150)->comment('Depo adı');
            $table->string('contact_person', 150)->nullable()->comment('Depo sorumlusunun adı');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği (def_loc_country tablosuna referans)');
            $table->unsignedBigInteger('city_id')->nullable()->comment('İl kimliği (def_loc_city tablosuna referans)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği (def_loc_district tablosuna referans)');
            $table->string('postcode', 10)->nullable()->comment('Posta kodu');
            $table->string('address')->nullable()->comment('Açık adres');
            $table->string('description')->nullable()->comment('Depo hakkında kısa açıklama');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('type_id', 'ix_warehouse_type_id');
            $table->index('country_id', 'ix_warehouse_country_id');
            $table->index('city_id', 'ix_warehouse_city_id');
            $table->index('district_id', 'ix_warehouse_district_id');
        });

        Schema::create(self::PRODUCT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün ana tablosu: katalog ürün kartları');

            $table->bigIncrements('product_id')->comment('Ürün için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_product_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Ürün türü kimliği: fiziksel/dijital/hizmet vb. (def_cat_product_type tablosuna referans)');
            $table->unsignedBigInteger('condition_id')->nullable()->comment('Ürün kondisyonu kimliği: sıfır/ikinci el vb. (def_cat_product_condition tablosuna referans)');
            $table->unsignedBigInteger('category_id')->nullable()->comment('Ana kategori kimliği (def_cat_category tablosuna referans)');
            $table->unsignedBigInteger('brand_id')->nullable()->comment('Marka kimliği (def_cat_brand tablosuna referans)');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('Satış birimi kimliği: adet/kg/lt vb. (def_loc_unit tablosuna referans)');
            $table->unsignedBigInteger('stockless_id')->nullable()->comment('Stok bittiğinde davranış kimliği (def_cat_product_stockless tablosuna referans)');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (site_layout tablosuna referans)');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Ürün durumu kimliği: taslak/aktif/pasif/arşiv/stok yok (def_cat_product_status tablosuna referans)');
            $table->unsignedBigInteger('default_variant_stock_id')->nullable()->comment('Varsayılan varyant stok kaydı (cat_product_variant_stock)');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_product_code')->comment('Ürün kodu/slug — benzersiz');
            $table->string('model', 100)->nullable()->comment('Model adı/kodu');
            $table->string('barcode', 64)->collation('ascii_general_ci')->nullable()->comment('Barkod numarası');
            $table->string('sku', 64)->collation('ascii_general_ci')->nullable()->comment('Stok tutma birimi (SKU)');
            $table->string('upc', 20)->collation('ascii_general_ci')->nullable()->comment('UPC kodu (Universal Product Code)');
            $table->string('ean', 20)->collation('ascii_general_ci')->nullable()->comment('EAN kodu (European Article Number)');
            $table->string('jan', 20)->collation('ascii_general_ci')->nullable()->comment('JAN kodu (Japanese Article Number)');
            $table->string('isbn', 20)->collation('ascii_general_ci')->nullable()->comment('ISBN numarası (kitaplar için)');
            $table->string('mpn', 64)->collation('ascii_general_ci')->nullable()->comment('MPN (üretici parça numarası)');
            $table->string('oem', 64)->collation('ascii_general_ci')->nullable()->comment('OEM referans numarası');
            $table->string('origin', 100)->nullable()->comment('Menşei/üretim yeri');
            $table->boolean('is_adult')->default(false)->comment('Yetişkin (18+) ürünü mü?');
            $table->boolean('is_domestic')->default(false)->comment('Yerli üretim mi?');
            $table->string('image_cover', 500)->nullable()->comment('Kapak görseli dosya yolu');
            $table->string('image_hover', 500)->nullable()->comment('Üzerine gelince gösterilecek ikinci görsel dosya yolu');
            $table->string('video_cover', 500)->nullable()->comment('Kapak videosu dosya yolu/URL');
            $table->string('live_broadcast_url', 500)->nullable()->comment('Canlı yayın URL adresi');
            $table->decimal('min_order_quantity', 15, 4)->unsigned()->default(1)->comment('Minimum sipariş miktarı');
            $table->decimal('max_order_quantity', 15, 4)->unsigned()->nullable()->comment('Maksimum sipariş miktarı; null ise sınırsız');
            $table->decimal('order_step', 15, 4)->unsigned()->default(1)->comment('Sipariş artış katsayısı (kaçar adet artabilir)');
            $table->boolean('is_stock_tracked')->default(true)->comment('Satışta stoktan düşülsün mü?');
            $table->decimal('buy_price', 19, 2)->unsigned()->default(0)->comment('Alış fiyatı');
            $table->char('buy_currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Alış para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('buy_tax_class_id')->nullable()->comment('Alış KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->decimal('sell_price', 19, 2)->unsigned()->default(0)->comment('Satış fiyatı');
            $table->char('sell_currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Satış para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('sell_tax_class_id')->nullable()->comment('Satış KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->unsignedInteger('sell_point')->nullable()->comment('Puan ile satın alma bedeli; null ise puanla satılmaz');
            $table->decimal('discount_value', 19, 2)->unsigned()->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->unsignedBigInteger('discount_type_id')->nullable()->comment('İndirim türü kimliği: yüzde/tutar (def_gen_discount_type tablosuna referans); null ise indirim yok');
            $table->decimal('excise_duty_rate', 8, 2)->unsigned()->default(0)->comment('ÖTV (özel tüketim vergisi) oranı/tutarı');
            $table->decimal('communications_tax_rate', 8, 2)->unsigned()->default(0)->comment('ÖİV (özel iletişim vergisi) oranı');
            $table->decimal('weight', 15, 4)->unsigned()->default(0)->comment('Ağırlık değeri');
            $table->unsignedBigInteger('weight_class_id')->nullable()->comment('Ağırlık birimi kimliği: kg/g vb. (def_loc_weight_class tablosuna referans)');
            $table->decimal('length', 15, 4)->unsigned()->default(0)->comment('Uzunluk değeri');
            $table->decimal('width', 15, 4)->unsigned()->default(0)->comment('Genişlik değeri');
            $table->decimal('height', 15, 4)->unsigned()->default(0)->comment('Yükseklik değeri');
            $table->unsignedBigInteger('length_class_id')->nullable()->comment('Uzunluk birimi kimliği: cm/m vb. (def_loc_length_class tablosuna referans)');
            $table->decimal('desi', 8, 2)->unsigned()->nullable()->comment('Kargo desi değeri');
            $table->unsignedSmallInteger('warranty_period')->nullable()->comment('Garanti süresi; null ise garanti yok');
            $table->unsignedBigInteger('warranty_time_unit_id')->nullable()->comment('Garanti süresi birimi kimliği: gün/hafta/ay/yıl (def_loc_unit tablosuna referans)');
            $table->boolean('is_shipping_required')->default(true)->comment('Kargoya verilecek fiziksel ürün mü?');
            $table->unsignedSmallInteger('delivery_time')->nullable()->comment('Tedarik/hazırlık süresi; null ise yok');
            $table->unsignedBigInteger('delivery_time_unit_id')->nullable()->comment('Tedarik süresi birimi kimliği: gün/hafta/ay/yıl (def_loc_unit tablosuna referans)');
            $table->unsignedSmallInteger('cargo_time')->nullable()->comment('Kargoya teslim süresi; null ise yok');
            $table->unsignedBigInteger('cargo_time_unit_id')->nullable()->comment('Kargo süresi birimi kimliği: gün/hafta/ay/yıl (def_loc_unit tablosuna referans)');
            $table->decimal('cargo_price', 19, 2)->unsigned()->nullable()->comment('Kargo ücreti; null ise genel kargo kuralı geçerli');
            $table->char('cargo_currency_code', 3)->collation('ascii_general_ci')->nullable()->comment('Kargo ücreti para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('cargo_tax_class_id')->nullable()->comment('Kargo KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->unsignedBigInteger('cargo_method_id')->nullable()->comment('Kargo yöntemi/firması kimliği (def_gen_cargo tablosuna referans)');
            $table->boolean('has_installment')->default(false)->comment('Taksitli satış yapılabilir mi?');
            $table->unsignedTinyInteger('installment_count')->nullable()->comment('İzin verilen maksimum taksit sayısı; null ise taksit yok');
            $table->unsignedInteger('reward_points')->default(0)->comment('Satın alımda kazandırılan ödül puanı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_returnable')->default(true)->comment('İade edilebilir mi?');
            $table->boolean('is_members_only')->default(false)->comment('Yalnızca üyelere açık mı?');
            $table->dateTime('produced_at')->nullable()->comment('Üretim tarihi');
            $table->dateTime('expires_at')->nullable()->comment('Son kullanma/tüketim tarihi');
            $table->dateTime('published_start_at')->nullable()->comment('Yayına başlama tarihi; null ise hemen yayında');
            $table->dateTime('published_end_at')->nullable()->comment('Yayından kalkma tarihi; null ise süresiz');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index(['status_id', 'published_start_at', 'published_end_at'], 'ix_product_visibility');
            $table->index(['category_id', 'status_id', 'sort_order'], 'ix_product_category_listing');
            $table->index(['brand_id', 'status_id'], 'ix_product_brand_listing');
            $table->index('type_id', 'ix_product_type_id');
            $table->index('condition_id', 'ix_product_condition_id');
            $table->index('unit_id', 'ix_product_unit_id');
            $table->index('buy_tax_class_id', 'ix_product_buy_tax_class_id');
            $table->index('sell_tax_class_id', 'ix_product_sell_tax_class_id');
            $table->index('discount_type_id', 'ix_product_discount_type_id');
            $table->index('cargo_method_id', 'ix_product_cargo_method_id');
            $table->index('barcode', 'ix_product_barcode');
            $table->index('sku', 'ix_product_sku');
            $table->index('default_variant_stock_id', 'ix_product_default_variant_stock_id');
        });

        Schema::create(self::PRODUCT_STAT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün istatistik/sayaç tablosu (1:1 agregat — ana tabloyu sıcak yazmadan korur)');

            $table->unsignedBigInteger('product_id')->primary()->comment('Bağlı olduğu ürün (cat_product) — 1:1 birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_product_stat_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('view_count')->default(0)->comment('Görüntülenme sayısı');
            $table->unsignedBigInteger('order_count')->default(0)->comment('Sipariş edilme sayısı');
            $table->unsignedBigInteger('cart_count')->default(0)->comment('Sepete eklenme sayısı');
            $table->unsignedBigInteger('wishlist_count')->default(0)->comment('Favoriye eklenme sayısı');
            $table->decimal('rating_avg', 3, 2)->unsigned()->default(0)->comment('Ortalama puan (0.00-5.00)');
            $table->unsignedInteger('rating_count')->default(0)->comment('Puanlanma sayısı');
            $table->unsignedInteger('review_count')->default(0)->comment('Onaylı değerlendirme sayısı');

            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('view_count', 'ix_product_stat_view_count');
            $table->index('rating_avg', 'ix_product_stat_rating_avg');
        });

        Schema::create(self::PRODUCT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün çevirileri (dile göre ad, açıklama ve meta bilgileri)');

            $table->bigIncrements('product_translation_id')->comment('Ürün çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_product_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Ürün adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->longText('description')->nullable()->comment('Ürün açıklaması (HTML içerebilir)');
            $table->string('tag')->nullable()->comment('Etiketler (virgülle ayrılmış)');
            $table->string('keyword')->nullable()->comment('Arama anahtar kelimeleri');
            $table->string('meta_title')->nullable()->comment('SEO meta başlık');
            $table->string('meta_description')->nullable()->comment('SEO meta açıklama');
            $table->string('meta_keyword')->nullable()->comment('SEO meta anahtar kelimeler');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'language_code'], 'uq_product_translation_lang');
            $table->fullText('name', 'ft_product_translation_name');
        });

        Schema::create(self::PRODUCT_ACCOUNT_PRICE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üye türüne/grubuna özel ürün fiyatları');

            $table->bigIncrements('product_account_price_id')->comment('Özel fiyat için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_account_price_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('account_type_id')->nullable()->comment('Üye türü kimliği (def_mbr_account_type tablosuna referans)');
            $table->decimal('price', 19, 2)->unsigned()->default(0)->comment('Bu üye türüne özel satış fiyatı');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->unsignedInteger('sell_point')->nullable()->comment('Puan ile satın alma bedeli; null ise puanla satılmaz');
            $table->unsignedInteger('reward_points')->default(0)->comment('Satın alımda kazandırılan ödül puanı');
            $table->decimal('discount_value', 19, 2)->unsigned()->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->unsignedBigInteger('discount_type_id')->nullable()->comment('İndirim türü kimliği: yüzde/tutar (def_gen_discount_type tablosuna referans); null ise indirim yok');
            $table->boolean('is_price_login_only')->default(false)->comment('Fiyat yalnızca giriş yapmış üyelere mi gösterilsin?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_account_price_product_id');
            $table->index('account_type_id', 'ix_account_price_account_type_id');
            $table->index('discount_type_id', 'ix_account_price_discount_type_id');
        });

        Schema::create(self::PRODUCT_VARIANT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üründe kullanılan varyant tanımları (örn. renk, beden)');

            $table->bigIncrements('product_variant_id')->comment('Ürün varyantı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_product_variant_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('variant_id')->comment('Varyant kimliği (def_cat_product_variant tablosuna referans)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'variant_id'], 'uq_product_variant');
            $table->index('variant_id', 'ix_product_variant_variant_id');
        });

        Schema::create(self::PRODUCT_VARIANT_STOCK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Varyant kombinasyonlarına ait stok ve fiyat kayıtları');

            $table->bigIncrements('product_variant_stock_id')->comment('Varyant stoğu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_variant_stock_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->string('sku', 64)->collation('ascii_general_ci')->nullable()->comment('Varyanta özel stok tutma birimi (SKU)');
            $table->decimal('quantity', 15, 4)->default(0)->comment('Stok miktarı (negatif olabilir: fazla satış)');
            $table->decimal('buy_price', 19, 2)->unsigned()->default(0)->comment('Varyant alış fiyatı');
            $table->decimal('sell_price', 19, 2)->unsigned()->default(0)->comment('Varyant satış fiyatı');
            $table->unsignedInteger('sell_point')->nullable()->comment('Puan ile satın alma bedeli; null ise puanla satılmaz');
            $table->decimal('weight', 15, 4)->unsigned()->default(0)->comment('Varyant ağırlık değeri');
            $table->decimal('discount_value', 19, 2)->unsigned()->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->unsignedBigInteger('discount_type_id')->nullable()->comment('İndirim türü kimliği: yüzde/tutar (def_gen_discount_type tablosuna referans); null ise indirim yok');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_variant_stock_product_id');
            $table->index('sku', 'ix_variant_stock_sku');
            $table->index('discount_type_id', 'ix_variant_stock_discount_type_id');
        });

        Schema::create(self::PRODUCT_VARIANT_VARIABLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Varyant stok kaydını oluşturan değişken kombinasyonları (örn. kırmızı + XL)');

            $table->bigIncrements('product_variant_variable_id')->comment('Varyant değişkeni için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_variant_variable_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('product_variant_stock_id')->comment('Bağlı olduğu varyant stok kaydı (cat_product_variant_stock)');
            $table->unsignedBigInteger('variant_id')->comment('Varyant kimliği (def_cat_product_variant tablosuna referans)');
            $table->unsignedBigInteger('variant_variable_id')->comment('Varyant değişkeni kimliği (def_cat_product_variant_variable tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_variant_variable_product_id');
            $table->index('product_variant_stock_id', 'ix_variant_variable_stock_id');
            $table->index('variant_id', 'ix_variant_variable_variant_id');
            $table->index('variant_variable_id', 'ix_variant_variable_variable_id');
        });

        Schema::create(self::PRODUCT_ACTIVITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün stok hareketleri (append-only ledger: düzeltme ters kayıtla yapılır, güncelleme/silme yok)');

            $table->bigIncrements('product_activity_id')->comment('Stok hareketi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_activity_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('product_variant_stock_id')->nullable()->comment('İlgili varyant stok kaydı (cat_product_variant_stock); null ise ana ürün');
            $table->unsignedBigInteger('module_id')->nullable()->comment('Hareketi doğuran modül kimliği (tanım tablosuna referans)');
            $table->unsignedBigInteger('entry_id')->nullable()->comment('Stok hareket türü kimliği: alış/satış/sayım vb. (def_cat_product_stock_entry tablosuna referans)');
            $table->unsignedBigInteger('relation_id')->nullable()->comment('Ürün ilişki türü kimliği (def_cat_product_relation tablosuna referans)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlgili cari hesap kimliği (üyelik tablosuna referans)');
            $table->unsignedBigInteger('warehouse_id')->nullable()->comment('Hareketin gerçekleştiği depo (cat_warehouse)');
            $table->unsignedBigInteger('area_id')->nullable()->comment('Depo alanı kimliği (def_cat_wh_area tablosuna referans)');
            $table->unsignedBigInteger('shelf_id')->nullable()->comment('Raf kimliği (def_cat_wh_shelf tablosuna referans)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Hareket belge numarası');
            $table->decimal('quantity', 15, 4)->default(0)->comment('Hareket miktarı: pozitif=giriş, negatif=çıkış');
            $table->decimal('price', 19, 2)->unsigned()->default(0)->comment('Birim fiyat');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->text('description')->nullable()->comment('Hareket açıklaması');
            $table->boolean('is_approved')->default(false)->comment('Hareket onaylandı mı?');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Hareketi onaylayan kullanıcı kimliği');
            $table->dateTime('approved_at')->nullable()->comment('Onay zamanı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Hareket anı (değiştirilemez)');

            $table->index(['product_id', 'created_at'], 'ix_activity_product_time');
            $table->index(['warehouse_id', 'created_at'], 'ix_activity_warehouse_time');
            $table->index(['product_variant_stock_id', 'created_at'], 'ix_activity_variant_time');
            $table->index('account_id', 'ix_activity_account_id');
            $table->index('module_id', 'ix_activity_module_id');
            $table->index('entry_id', 'ix_activity_entry_id');
        });

        Schema::create(self::PRODUCT_REWARD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üye grubuna göre üründen kazanılacak ödül puanları');

            $table->bigIncrements('product_reward_id')->comment('Ürün puanı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_reward_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('account_group_id')->nullable()->comment('Üye grubu kimliği (def_mbr_account_group tablosuna referans)');
            $table->unsignedInteger('points')->default(0)->comment('Kazandırılan ödül puanı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'account_group_id'], 'uq_product_reward_group');
            $table->index('account_group_id', 'ix_reward_account_group_id');
        });

        Schema::create(self::PRODUCT_IMAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün görselleri (galeri)');

            $table->bigIncrements('product_image_id')->comment('Ürün görseli için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_image_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('product_variant_stock_id')->nullable()->comment('İlgili varyant stok kaydı (cat_product_variant_stock); null ise ana ürün görseli');
            $table->string('file_path', 500)->comment('Görsel dosya yolu');
            $table->string('alt_text')->nullable()->comment('Görsel alternatif metni (erişilebilirlik/SEO)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_image_product_id');
            $table->index('product_variant_stock_id', 'ix_image_variant_stock_id');
        });

        Schema::create(self::PRODUCT_VIDEO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün videoları');

            $table->bigIncrements('product_video_id')->comment('Ürün videosu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_video_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('video_source_id')->comment('Video kaynağı kimliği: kod/url/dosya/embed (def_gen_video_source tablosuna referans)');
            $table->string('source_value', 500)->comment('Video içeriği (kaynağa göre kod, URL, dosya yolu veya embed)');
            $table->string('name', 150)->nullable()->comment('Video başlığı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_video_product_id');
            $table->index('video_source_id', 'ix_video_source_id');
        });

        Schema::create(self::PRODUCT_RELATED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İlişkili/benzer ürün eşleştirmeleri');

            $table->bigIncrements('product_related_id')->comment('İlişkili ürün kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_related_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('related_product_id')->comment('İlişkili ürün (cat_product)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'related_product_id'], 'uq_product_related');
            $table->index('related_product_id', 'ix_related_related_product_id');
        });

        Schema::create(self::PRODUCT_ACCESSORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün aksesuar eşleştirmeleri (birlikte satılan ürünler)');

            $table->bigIncrements('product_accessory_id')->comment('Aksesuar kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_accessory_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('accessory_product_id')->comment('Aksesuar olarak önerilen ürün (cat_product)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'accessory_product_id'], 'uq_product_accessory');
            $table->index('accessory_product_id', 'ix_accessory_accessory_product_id');
        });

        Schema::create(self::PRODUCT_DOWNLOAD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün-indirilebilir dosya eşleştirmeleri (dijital ürün içerikleri)');

            $table->bigIncrements('product_download_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_product_download_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('download_id')->comment('Bağlı olduğu indirilebilir dosya (cat_download)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'download_id'], 'uq_product_download');
            $table->index('download_id', 'ix_product_download_download_id');
        });

        Schema::create(self::PRODUCT_FAQ_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün-SSS eşleştirmeleri');

            $table->bigIncrements('product_faq_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_faq_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('faq_id')->comment('SSS kaydı kimliği (sup_faq tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'faq_id'], 'uq_product_faq');
            $table->index('faq_id', 'ix_faq_faq_id');
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün özellik değerleri (teknik özellik atamaları)');

            $table->bigIncrements('product_attribute_id')->comment('Ürün özelliği için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_attribute_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('attribute_variable_id')->comment('Özellik değişkeni kimliği (def_cat_product_attribute_variable tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_attribute_product_id');
            $table->index('attribute_variable_id', 'ix_attribute_variable_id');
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün özellik değeri çevirileri (dile göre özellik metni)');

            $table->bigIncrements('product_attribute_translation_id')->comment('Özellik çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_attribute_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_attribute_id')->comment('Bağlı olduğu ürün özelliği (cat_product_attribute)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('content')->comment('Özellik değeri metni');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_attribute_id', 'language_code'], 'uq_attribute_translation_lang');
        });

        Schema::create(self::PRODUCT_FIELD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürüne özel ek alanlar (serbest içerik blokları)');

            $table->bigIncrements('product_field_id')->comment('Ek alan için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_field_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('field_type_id')->nullable()->comment('Alan türü kimliği (def_cat_product_field_type tablosuna referans)');
            $table->string('image_path', 500)->nullable()->comment('Alan görseli dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_field_product_id');
            $table->index('field_type_id', 'ix_field_field_type_id');
        });

        Schema::create(self::PRODUCT_FIELD_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün ek alan çevirileri (dile göre ad ve içerik)');

            $table->bigIncrements('product_field_translation_id')->comment('Ek alan çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_field_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_field_id')->comment('Bağlı olduğu ek alan (cat_product_field)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Alan adı/başlığı');
            $table->text('content')->nullable()->comment('Alan içeriği');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_field_id', 'language_code'], 'uq_field_translation_lang');
        });

        Schema::create(self::PRODUCT_FILTER_VALUE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün-filtre değeri eşleştirmeleri (listeleme filtreleri)');

            $table->bigIncrements('product_filter_value_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_filter_value_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('filter_value_id')->comment('Filtre değeri kimliği (def_cat_product_filter_value tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'filter_value_id'], 'uq_product_filter_value');
            $table->index('filter_value_id', 'ix_filter_value_filter_value_id');
        });

        Schema::create(self::PRODUCT_OPTION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün seçenekleri (sipariş sırasında seçilebilen opsiyonlar)');

            $table->bigIncrements('product_option_id')->comment('Ürün seçeneği için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_option_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('option_id')->comment('Seçenek kimliği (def_cat_product_option tablosuna referans)');
            $table->boolean('is_required')->default(false)->comment('Sipariş için seçim zorunlu mu?');
            $table->string('value')->nullable()->comment('Metin/tarih türü seçenekler için varsayılan değer');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_option_product_id');
            $table->index('option_id', 'ix_option_option_id');
        });

        Schema::create(self::PRODUCT_OPTION_VALUE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün seçenek değerleri (seçeneğe bağlı fiyat/stok etkileri)');

            $table->bigIncrements('product_option_value_id')->comment('Seçenek değeri için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_option_value_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('product_option_id')->comment('Bağlı olduğu ürün seçeneği (cat_product_option)');
            $table->unsignedBigInteger('option_value_id')->comment('Seçenek değeri kimliği (def_cat_product_option_value tablosuna referans)');
            $table->string('sku', 64)->collation('ascii_general_ci')->nullable()->comment('Bu değere özel stok tutma birimi (SKU)');
            $table->decimal('quantity', 15, 4)->default(0)->comment('Stok miktarı');
            $table->decimal('buy_price', 19, 2)->unsigned()->default(0)->comment('Alış fiyatına etkisi');
            $table->enum('buy_price_prefix', ['+', '-'])->default('+')->comment('Alış fiyat etkisinin yönü: +=ekle, -=çıkar');
            $table->decimal('sell_price', 19, 2)->unsigned()->default(0)->comment('Satış fiyatına etkisi');
            $table->enum('sell_price_prefix', ['+', '-'])->default('+')->comment('Satış fiyat etkisinin yönü: +=ekle, -=çıkar');
            $table->unsignedInteger('sell_point')->default(0)->comment('Puan bedeline etkisi');
            $table->enum('sell_point_prefix', ['+', '-'])->default('+')->comment('Puan etkisinin yönü: +=ekle, -=çıkar');
            $table->decimal('weight', 15, 4)->unsigned()->default(0)->comment('Ağırlığa etkisi');
            $table->enum('weight_prefix', ['+', '-'])->default('+')->comment('Ağırlık etkisinin yönü: +=ekle, -=çıkar');
            $table->decimal('discount_value', 19, 2)->unsigned()->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->unsignedBigInteger('discount_type_id')->nullable()->comment('İndirim türü kimliği: yüzde/tutar (def_gen_discount_type tablosuna referans); null ise indirim yok');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_option_value_product_id');
            $table->index('product_option_id', 'ix_option_value_option_id');
            $table->index('option_value_id', 'ix_option_value_value_id');
            $table->index('discount_type_id', 'ix_option_value_discount_type_id');
        });

        Schema::create(self::PRODUCT_GROUPED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Gruplu ürün üyeleri (ana ürün altında ayrı ayrı satılan ürünler)');

            $table->bigIncrements('product_grouped_id')->comment('Grup üyesi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_grouped_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ana ürün (cat_product)');
            $table->unsignedBigInteger('grouped_product_id')->comment('Gruba dahil edilen ürün (cat_product)');
            $table->decimal('opening_quantity', 15, 4)->unsigned()->default(0)->comment('Varsayılan seçili miktar');
            $table->decimal('min_order_quantity', 15, 4)->unsigned()->nullable()->comment('Minimum sipariş miktarı; null ise sınır yok');
            $table->decimal('max_order_quantity', 15, 4)->unsigned()->nullable()->comment('Maksimum sipariş miktarı; null ise sınırsız');
            $table->decimal('order_step', 15, 4)->unsigned()->default(1)->comment('Sipariş artış katsayısı (kaçar adet artabilir)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'grouped_product_id'], 'uq_product_grouped');
            $table->index('grouped_product_id', 'ix_grouped_grouped_product_id');
        });

        Schema::create(self::PRODUCT_BUNDLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Paket ürün içeriği (tek fiyatla satılan ürün setleri)');

            $table->bigIncrements('product_bundle_id')->comment('Paket kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_bundle_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu paket ürünü (cat_product)');
            $table->unsignedBigInteger('bundle_product_id')->comment('Pakete dahil edilen ürün (cat_product)');
            $table->decimal('quantity', 15, 4)->unsigned()->default(1)->comment('Paketteki adet');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'bundle_product_id'], 'uq_product_bundle');
            $table->index('bundle_product_id', 'ix_bundle_bundle_product_id');
        });

        Schema::create(self::PRODUCT_RECURRING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün abonelik/tekrarlayan ödeme planları');

            $table->bigIncrements('product_recurring_id')->comment('Abonelik planı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_recurring_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('recurring_type_id')->comment('Tekrarlama planı kimliği (def_cat_product_recurring_type tablosuna referans)');
            $table->unsignedBigInteger('account_type_id')->nullable()->comment('Planın geçerli olduğu üye türü kimliği (def_mbr_account_type tablosuna referans); null ise tüm üyeler');
            $table->boolean('is_default')->default(false)->comment('Ürünün varsayılan abonelik planı mı?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_id', 'recurring_type_id', 'account_type_id'], 'uq_product_recurring');
            $table->index('recurring_type_id', 'ix_recurring_recurring_type_id');
            $table->index('account_type_id', 'ix_recurring_account_type_id');
        });

        Schema::create(self::PRODUCT_DOCUMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün dokümanları (kılavuz, sertifika vb. dosyalar)');

            $table->bigIncrements('product_document_id')->comment('Doküman için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_document_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Doküman kodu');
            $table->string('cover_image', 500)->nullable()->comment('Doküman kapak görseli dosya yolu');
            $table->string('file_path', 500)->comment('Doküman dosya yolu');
            $table->string('original_filename', 255)->nullable()->comment('Sunucudaki gerçek dosya adı');
            $table->string('display_name', 255)->nullable()->comment('İndirme sırasında kullanıcıya gösterilecek dosya adı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_document_product_id');
        });

        Schema::create(self::PRODUCT_DOCUMENT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün dokümanı çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('product_document_translation_id')->comment('Doküman çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_document_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_document_id')->comment('Bağlı olduğu doküman (cat_product_document)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Doküman adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->text('description')->nullable()->comment('Doküman açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['product_document_id', 'language_code'], 'uq_document_translation_lang');
        });

        Schema::create(self::REVIEW_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün değerlendirmeleri (yorum ve puanlar)');

            $table->bigIncrements('review_id')->comment('Değerlendirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_review_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Değerlendirmeyi yapan üye (üyelik tablosuna referans); null ise misafir');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Değerlendirme durumu kimliği: bekliyor/onaylı/reddedildi/spam (def_cat_product_review_status tablosuna referans)');
            $table->string('tracking_code', 32)->collation('ascii_general_ci')->unique('uq_review_tracking_code')->comment('Değerlendirme takip kodu — benzersiz');
            $table->string('author_name', 150)->comment('Değerlendirme sahibinin görünen adı');
            $table->text('content')->comment('Değerlendirme metni');
            $table->unsignedTinyInteger('rating')->nullable()->comment('Puan (1-5); null ise puanlanmamış');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index(['product_id', 'status_id', 'created_at'], 'ix_review_product_listing');
            $table->index('account_id', 'ix_review_account_id');
            $table->index('status_id', 'ix_review_status_id');
        });

        DB::connection('conn_tnt')->statement(
            'ALTER TABLE ' . self::REVIEW_TABLE . ' ADD CONSTRAINT chk_review_rating CHECK (rating IS NULL OR rating BETWEEN 1 AND 5)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(self::REVIEW_TABLE);
        Schema::dropIfExists(self::PRODUCT_DOCUMENT_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_DOCUMENT_TABLE);
        Schema::dropIfExists(self::PRODUCT_RECURRING_TABLE);
        Schema::dropIfExists(self::PRODUCT_BUNDLE_TABLE);
        Schema::dropIfExists(self::PRODUCT_GROUPED_TABLE);
        Schema::dropIfExists(self::PRODUCT_OPTION_VALUE_TABLE);
        Schema::dropIfExists(self::PRODUCT_OPTION_TABLE);
        Schema::dropIfExists(self::PRODUCT_FILTER_VALUE_TABLE);
        Schema::dropIfExists(self::PRODUCT_FIELD_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_FIELD_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_TABLE);
        Schema::dropIfExists(self::PRODUCT_FAQ_TABLE);
        Schema::dropIfExists(self::PRODUCT_DOWNLOAD_TABLE);
        Schema::dropIfExists(self::PRODUCT_ACCESSORY_TABLE);
        Schema::dropIfExists(self::PRODUCT_RELATED_TABLE);
        Schema::dropIfExists(self::PRODUCT_VIDEO_TABLE);
        Schema::dropIfExists(self::PRODUCT_IMAGE_TABLE);
        Schema::dropIfExists(self::PRODUCT_REWARD_TABLE);
        Schema::dropIfExists(self::PRODUCT_ACTIVITY_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_VARIABLE_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_STOCK_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_TABLE);
        Schema::dropIfExists(self::PRODUCT_ACCOUNT_PRICE_TABLE);
        Schema::dropIfExists(self::PRODUCT_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_STAT_TABLE);
        Schema::dropIfExists(self::PRODUCT_TABLE);
        Schema::dropIfExists(self::WAREHOUSE_TABLE);
        Schema::dropIfExists(self::DOWNLOAD_TRANSLATION_TABLE);
        Schema::dropIfExists(self::DOWNLOAD_TABLE);
    }
};
