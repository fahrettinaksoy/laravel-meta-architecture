<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const DOWNLOAD_TABLE = 'cat_download';
    private const DOWNLOAD_TRANSLATION_TABLE = 'cat_download_translation';
    private const WAREHOUSE_TABLE = 'cat_warehouse';
    private const PRODUCT_TABLE = 'cat_product';
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
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Dosya kodu — benzersiz');
            $table->string('filename', 255)->comment('Sunucudaki gerçek dosya adı/yolu');
            $table->string('mask', 255)->comment('İndirme sırasında kullanıcıya gösterilecek dosya adı');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');
        });

        Schema::create(self::DOWNLOAD_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İndirilebilir dosya çevirileri (dile göre görünen ad)');

            $table->bigIncrements('download_translation_id')->comment('Dosya çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('download_id')->comment('Bağlı olduğu dosya (cat_download)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Dosyanın görünen adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['download_id', 'language_code'], 'uq_download_translation_lang');

            $table->foreign('download_id')->references('download_id')->on(self::DOWNLOAD_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::WAREHOUSE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Depolar (stok hareketlerinin bağlandığı fiziksel lokasyonlar)');

            $table->bigIncrements('warehouse_id')->comment('Depo için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Depo türü kimliği (tanım tablosuna referans)');
            $table->string('code', 64)->unique()->comment('Depo kodu — benzersiz');
            $table->string('name', 150)->comment('Depo adı');
            $table->string('authorized_name', 150)->nullable()->comment('Depo sorumlusunun adı');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği (def_loc_country tablosuna referans)');
            $table->unsignedBigInteger('city_id')->nullable()->comment('İl kimliği (def_loc_city tablosuna referans)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği (def_loc_district tablosuna referans)');
            $table->string('postcode', 10)->nullable()->comment('Posta kodu');
            $table->string('address')->nullable()->comment('Açık adres');
            $table->string('description')->nullable()->comment('Depo hakkında kısa açıklama');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('country_id');
            $table->index('city_id');
            $table->index('district_id');
        });

        Schema::create(self::PRODUCT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün ana tablosu: katalog ürün kartları');

            $table->bigIncrements('product_id')->comment('Ürün için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Ürün türü kimliği: fiziksel/dijital/hizmet vb. (def_cat_product_type tablosuna referans)');
            $table->unsignedBigInteger('condition_id')->nullable()->comment('Ürün kondisyonu kimliği: sıfır/ikinci el vb. (def_cat_product_condition tablosuna referans)');
            $table->unsignedBigInteger('category_id')->nullable()->comment('Ana kategori kimliği (def_cat_category tablosuna referans; FK tanım modülünde eklenir)');
            $table->unsignedBigInteger('brand_id')->nullable()->comment('Marka kimliği (def_cat_brand tablosuna referans; FK tanım modülünde eklenir)');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('Satış birimi kimliği: adet/kg/lt vb. (def_loc_unit tablosuna referans)');
            $table->unsignedBigInteger('stockless_id')->nullable()->comment('Stok bittiğinde davranış kimliği (def_cat_product_stockless tablosuna referans)');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (site_layout tablosuna referans)');
            $table->unsignedBigInteger('default_variant_stock_id')->nullable()->comment('Varsayılan varyant stok kaydı (cat_product_variant_stock)');
            $table->string('code', 64)->unique()->comment('Ürün kodu/slug — benzersiz');
            $table->string('model', 100)->nullable()->comment('Model adı/kodu');
            $table->string('barcode', 64)->nullable()->comment('Barkod numarası');
            $table->string('sku', 64)->nullable()->comment('Stok tutma birimi (SKU)');
            $table->string('upc', 20)->nullable()->comment('UPC kodu (Universal Product Code)');
            $table->string('ean', 20)->nullable()->comment('EAN kodu (European Article Number)');
            $table->string('jan', 20)->nullable()->comment('JAN kodu (Japanese Article Number)');
            $table->string('isbn', 20)->nullable()->comment('ISBN numarası (kitaplar için)');
            $table->string('mpn', 64)->nullable()->comment('MPN (üretici parça numarası)');
            $table->string('oem', 64)->nullable()->comment('OEM referans numarası');
            $table->string('origin', 100)->nullable()->comment('Menşei/üretim yeri');
            $table->boolean('is_adult')->default(false)->comment('Yetişkin (18+) ürünü mü?');
            $table->boolean('is_domestic')->default(false)->comment('Yerli üretim mi?');
            $table->string('image_cover', 500)->nullable()->comment('Kapak görseli dosya yolu');
            $table->string('image_hover', 500)->nullable()->comment('Üzerine gelince gösterilecek ikinci görsel dosya yolu');
            $table->string('video_cover', 500)->nullable()->comment('Kapak videosu dosya yolu/URL');
            $table->string('live_broadcast_url', 500)->nullable()->comment('Canlı yayın URL adresi');
            $table->unsignedInteger('min_order_quantity')->default(1)->comment('Minimum sipariş miktarı');
            $table->unsignedInteger('max_order_quantity')->default(0)->comment('Maksimum sipariş miktarı; 0 ise sınırsız');
            $table->unsignedInteger('order_step')->default(1)->comment('Sipariş artış katsayısı (kaçar adet artabilir)');
            $table->boolean('subtract_stock')->default(true)->comment('Satışta stoktan düşülsün mü?');
            $table->decimal('buy_price', 19, 2)->default(0)->comment('Alış fiyatı');
            $table->char('buy_currency_code', 3)->default('TRY')->comment('Alış para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('buy_tax_class_id')->nullable()->comment('Alış KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->decimal('sell_price', 19, 2)->default(0)->comment('Satış fiyatı');
            $table->char('sell_currency_code', 3)->default('TRY')->comment('Satış para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('sell_tax_class_id')->nullable()->comment('Satış KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->unsignedInteger('sell_point')->default(0)->comment('Puan ile satın alma bedeli; 0 ise puanla satılmaz');
            $table->decimal('discount_value', 19, 2)->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->enum('discount_type', ['percentage', 'amount'])->nullable()->comment('İndirim türü: percentage=yüzde, amount=tutar; null ise indirim yok');
            $table->decimal('excise_duty_rate', 8, 2)->default(0)->comment('ÖTV (özel tüketim vergisi) oranı/tutarı');
            $table->decimal('communications_tax_rate', 8, 2)->default(0)->comment('ÖİV (özel iletişim vergisi) oranı');
            $table->decimal('weight', 15, 4)->default(0)->comment('Ağırlık değeri');
            $table->unsignedBigInteger('weight_class_id')->nullable()->comment('Ağırlık birimi kimliği: kg/g vb. (def_loc_weight_class tablosuna referans)');
            $table->decimal('length', 15, 4)->default(0)->comment('Uzunluk değeri');
            $table->decimal('width', 15, 4)->default(0)->comment('Genişlik değeri');
            $table->decimal('height', 15, 4)->default(0)->comment('Yükseklik değeri');
            $table->unsignedBigInteger('length_class_id')->nullable()->comment('Uzunluk birimi kimliği: cm/m vb. (def_loc_length_class tablosuna referans)');
            $table->decimal('desi', 8, 2)->nullable()->comment('Kargo desi değeri');
            $table->unsignedSmallInteger('warranty_period')->default(0)->comment('Garanti süresi; 0 ise garanti yok');
            $table->enum('warranty_type', ['day', 'week', 'month', 'year'])->nullable()->comment('Garanti süresi birimi: day=gün, week=hafta, month=ay, year=yıl');
            $table->boolean('requires_shipping')->default(true)->comment('Kargoya verilecek fiziksel ürün mü?');
            $table->unsignedSmallInteger('delivery_time')->default(0)->comment('Tedarik/hazırlık süresi');
            $table->enum('delivery_type', ['day', 'week', 'month', 'year'])->nullable()->comment('Tedarik süresi birimi: day=gün, week=hafta, month=ay, year=yıl');
            $table->unsignedSmallInteger('cargo_time')->default(0)->comment('Kargoya teslim süresi');
            $table->enum('cargo_type', ['day', 'week', 'month', 'year'])->nullable()->comment('Kargo süresi birimi: day=gün, week=hafta, month=ay, year=yıl');
            $table->decimal('cargo_price', 19, 2)->nullable()->comment('Kargo ücreti; null ise genel kargo kuralı geçerli');
            $table->char('cargo_currency_code', 3)->nullable()->comment('Kargo ücreti para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('cargo_tax_class_id')->nullable()->comment('Kargo KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->enum('cargo_method', ['free', 'flat', 'weight', 'item', 'pickup', 'category', 'product'])->nullable()->comment('Kargo ücretlendirme yöntemi: free=ücretsiz, flat=sabit, weight=ağırlığa göre, item=adet başı, pickup=mağazadan teslim, category=kategori kuralı, product=ürüne özel');
            $table->boolean('has_installment')->default(false)->comment('Taksitli satış yapılabilir mi?');
            $table->unsignedTinyInteger('installment_count')->default(0)->comment('İzin verilen maksimum taksit sayısı');
            $table->unsignedInteger('reward_points')->default(0)->comment('Satın alımda kazandırılan ödül puanı');
            $table->unsignedBigInteger('viewed')->default(0)->comment('Görüntülenme sayısı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_returnable')->default(true)->comment('İade edilebilir mi?');
            $table->boolean('membership')->default(false)->comment('Yalnızca üyelere açık mı?');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');
            $table->timestamp('produced_at')->nullable()->comment('Üretim tarihi');
            $table->timestamp('expires_at')->nullable()->comment('Son kullanma/tüketim tarihi');
            $table->timestamp('published_start_at')->nullable()->comment('Yayına başlama tarihi; null ise hemen yayında');
            $table->timestamp('published_end_at')->nullable()->comment('Yayından kalkma tarihi; null ise süresiz');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('type_id');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('barcode');
            $table->index('sku');
            $table->index('status');
        });

        Schema::create(self::PRODUCT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün çevirileri (dile göre ad, açıklama ve meta bilgileri)');

            $table->bigIncrements('product_translation_id')->comment('Ürün çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
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
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'language_code'], 'uq_product_translation_lang');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_ACCOUNT_PRICE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üye türüne/grubuna özel ürün fiyatları');

            $table->bigIncrements('product_account_price_id')->comment('Özel fiyat için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('account_type_id')->nullable()->comment('Üye türü kimliği (def_mbr_account_type tablosuna referans)');
            $table->decimal('price', 19, 2)->default(0)->comment('Bu üye türüne özel satış fiyatı');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->unsignedInteger('sell_point')->default(0)->comment('Puan ile satın alma bedeli; 0 ise puanla satılmaz');
            $table->unsignedInteger('reward_points')->default(0)->comment('Satın alımda kazandırılan ödül puanı');
            $table->decimal('discount_value', 19, 2)->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->enum('discount_type', ['percentage', 'amount'])->nullable()->comment('İndirim türü: percentage=yüzde, amount=tutar; null ise indirim yok');
            $table->boolean('show_login_only')->default(false)->comment('Fiyat yalnızca giriş yapmış üyelere mi gösterilsin?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_type_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_VARIANT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üründe kullanılan varyant tanımları (örn. renk, beden)');

            $table->bigIncrements('product_variant_id')->comment('Ürün varyantı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('variant_id')->comment('Varyant kimliği (def_cat_variant tablosuna referans)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'variant_id'], 'uq_product_variant');
            $table->index('variant_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_VARIANT_STOCK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Varyant kombinasyonlarına ait stok ve fiyat kayıtları');

            $table->bigIncrements('product_variant_stock_id')->comment('Varyant stoğu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->string('sku', 64)->nullable()->comment('Varyanta özel stok tutma birimi (SKU)');
            $table->integer('quantity')->default(0)->comment('Stok miktarı');
            $table->decimal('buy_price', 19, 2)->default(0)->comment('Varyant alış fiyatı');
            $table->decimal('sell_price', 19, 2)->default(0)->comment('Varyant satış fiyatı');
            $table->unsignedInteger('sell_point')->default(0)->comment('Puan ile satın alma bedeli; 0 ise puanla satılmaz');
            $table->decimal('weight', 15, 4)->default(0)->comment('Varyant ağırlık değeri');
            $table->decimal('discount_value', 19, 2)->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->enum('discount_type', ['percentage', 'amount'])->nullable()->comment('İndirim türü: percentage=yüzde, amount=tutar; null ise indirim yok');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('sku');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_VARIANT_VARIABLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Varyant stok kaydını oluşturan değişken kombinasyonları (örn. kırmızı + XL)');

            $table->bigIncrements('product_variant_variable_id')->comment('Varyant değişkeni için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('product_variant_stock_id')->comment('Bağlı olduğu varyant stok kaydı (cat_product_variant_stock)');
            $table->unsignedBigInteger('variant_id')->comment('Varyant kimliği (def_cat_variant tablosuna referans)');
            $table->unsignedBigInteger('variant_variable_id')->comment('Varyant değişkeni kimliği (def_cat_variant_variable tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('variant_id');
            $table->index('variant_variable_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
            $table->foreign('product_variant_stock_id')->references('product_variant_stock_id')->on(self::PRODUCT_VARIANT_STOCK_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_ACTIVITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün stok hareketleri (giriş/çıkış kayıtları)');

            $table->bigIncrements('product_activity_id')->comment('Stok hareketi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('product_variant_stock_id')->nullable()->comment('İlgili varyant stok kaydı (cat_product_variant_stock); null ise ana ürün');
            $table->unsignedBigInteger('module_id')->nullable()->comment('Hareketi doğuran modül kimliği (tanım tablosuna referans)');
            $table->unsignedBigInteger('entry_id')->nullable()->comment('Stok hareket türü kimliği: alış/satış/sayım vb. (def_cat_stock_entry tablosuna referans)');
            $table->unsignedBigInteger('relation_id')->nullable()->comment('Ürün ilişki türü kimliği (def_cat_product_relation tablosuna referans)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlgili cari hesap kimliği (üyelik tablosuna referans; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('warehouse_id')->nullable()->comment('Hareketin gerçekleştiği depo (cat_warehouse)');
            $table->unsignedBigInteger('area_id')->nullable()->comment('Depo alanı kimliği (def_cat_wh_area tablosuna referans)');
            $table->unsignedBigInteger('shelf_id')->nullable()->comment('Raf kimliği (def_cat_wh_shelf tablosuna referans)');
            $table->string('code', 64)->nullable()->comment('Hareket belge numarası');
            $table->integer('quantity')->default(0)->comment('Hareket miktarı: pozitif=giriş, negatif=çıkış');
            $table->decimal('price', 19, 2)->default(0)->comment('Birim fiyat');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->text('description')->nullable()->comment('Hareket açıklaması');
            $table->boolean('is_approved')->default(false)->comment('Hareket onaylandı mı?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('module_id');
            $table->index('entry_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
            $table->foreign('product_variant_stock_id')->references('product_variant_stock_id')->on(self::PRODUCT_VARIANT_STOCK_TABLE)->nullOnDelete();
            $table->foreign('warehouse_id')->references('warehouse_id')->on(self::WAREHOUSE_TABLE)->nullOnDelete();
        });

        Schema::create(self::PRODUCT_REWARD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üye grubuna göre üründen kazanılacak ödül puanları');

            $table->bigIncrements('product_reward_id')->comment('Ürün puanı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('account_group_id')->nullable()->comment('Üye grubu kimliği (def_mbr_account_group tablosuna referans)');
            $table->unsignedInteger('points')->default(0)->comment('Kazandırılan ödül puanı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'account_group_id'], 'uq_product_reward_group');
            $table->index('account_group_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_IMAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün görselleri (galeri)');

            $table->bigIncrements('product_image_id')->comment('Ürün görseli için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('product_variant_stock_id')->nullable()->comment('İlgili varyant stok kaydı (cat_product_variant_stock); null ise ana ürün görseli');
            $table->string('file', 500)->comment('Görsel dosya yolu');
            $table->string('description')->nullable()->comment('Görsel açıklaması (alt metin)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
            $table->foreign('product_variant_stock_id')->references('product_variant_stock_id')->on(self::PRODUCT_VARIANT_STOCK_TABLE)->nullOnDelete();
        });

        Schema::create(self::PRODUCT_VIDEO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün videoları');

            $table->bigIncrements('product_video_id')->comment('Ürün videosu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->enum('source', ['code', 'url', 'file', 'embed'])->comment('Video kaynağı: code=platform kodu, url=bağlantı, file=dosya, embed=gömülü kod');
            $table->string('content', 500)->comment('Video içeriği (kaynağa göre kod, URL, dosya yolu veya embed)');
            $table->string('name', 150)->nullable()->comment('Video başlığı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_RELATED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İlişkili/benzer ürün eşleştirmeleri');

            $table->bigIncrements('product_related_id')->comment('İlişkili ürün kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('related_product_id')->comment('İlişkili ürün (cat_product)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'related_product_id'], 'uq_product_related');
            $table->index('related_product_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
            $table->foreign('related_product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_ACCESSORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün aksesuar eşleştirmeleri (birlikte satılan ürünler)');

            $table->bigIncrements('product_accessory_id')->comment('Aksesuar kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('accessory_product_id')->comment('Aksesuar olarak önerilen ürün (cat_product)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'accessory_product_id'], 'uq_product_accessory');
            $table->index('accessory_product_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
            $table->foreign('accessory_product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_DOWNLOAD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün-indirilebilir dosya eşleştirmeleri (dijital ürün içerikleri)');

            $table->bigIncrements('product_download_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('download_id')->comment('Bağlı olduğu indirilebilir dosya (cat_download)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'download_id'], 'uq_product_download');
            $table->index('download_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
            $table->foreign('download_id')->references('download_id')->on(self::DOWNLOAD_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_FAQ_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün-SSS eşleştirmeleri');

            $table->bigIncrements('product_faq_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('faq_id')->comment('SSS kaydı kimliği (sup_faq tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'faq_id'], 'uq_product_faq');
            $table->index('faq_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün özellik değerleri (teknik özellik atamaları)');

            $table->bigIncrements('product_attribute_id')->comment('Ürün özelliği için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('attribute_variable_id')->comment('Özellik değişkeni kimliği (def_cat_attribute_variable tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('attribute_variable_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün özellik değeri çevirileri (dile göre özellik metni)');

            $table->bigIncrements('product_attribute_translation_id')->comment('Özellik çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_attribute_id')->comment('Bağlı olduğu ürün özelliği (cat_product_attribute)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('content')->comment('Özellik değeri metni');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_attribute_id', 'language_code'], 'uq_product_attribute_translation_lang');

            $table->foreign('product_attribute_id')->references('product_attribute_id')->on(self::PRODUCT_ATTRIBUTE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_FIELD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürüne özel ek alanlar (serbest içerik blokları)');

            $table->bigIncrements('product_field_id')->comment('Ek alan için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('field_type_id')->nullable()->comment('Alan türü kimliği (def_cat_field_type tablosuna referans)');
            $table->string('image', 500)->nullable()->comment('Alan görseli dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('field_type_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_FIELD_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün ek alan çevirileri (dile göre ad ve içerik)');

            $table->bigIncrements('product_field_translation_id')->comment('Ek alan çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_field_id')->comment('Bağlı olduğu ek alan (cat_product_field)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Alan adı/başlığı');
            $table->text('content')->nullable()->comment('Alan içeriği');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_field_id', 'language_code'], 'uq_product_field_translation_lang');

            $table->foreign('product_field_id')->references('product_field_id')->on(self::PRODUCT_FIELD_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_FILTER_VALUE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün-filtre değeri eşleştirmeleri (listeleme filtreleri)');

            $table->bigIncrements('product_filter_value_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('filter_value_id')->comment('Filtre değeri kimliği (def_cat_filter_value tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'filter_value_id'], 'uq_product_filter_value');
            $table->index('filter_value_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_OPTION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün seçenekleri (sipariş sırasında seçilebilen opsiyonlar)');

            $table->bigIncrements('product_option_id')->comment('Ürün seçeneği için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('option_id')->comment('Seçenek kimliği (def_cat_option tablosuna referans)');
            $table->boolean('is_required')->default(false)->comment('Sipariş için seçim zorunlu mu?');
            $table->string('value')->nullable()->comment('Metin/tarih türü seçenekler için varsayılan değer');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('option_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_OPTION_VALUE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün seçenek değerleri (seçeneğe bağlı fiyat/stok etkileri)');

            $table->bigIncrements('product_option_value_id')->comment('Seçenek değeri için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('product_option_id')->comment('Bağlı olduğu ürün seçeneği (cat_product_option)');
            $table->unsignedBigInteger('option_value_id')->comment('Seçenek değeri kimliği (def_cat_option_value tablosuna referans)');
            $table->string('sku', 64)->nullable()->comment('Bu değere özel stok tutma birimi (SKU)');
            $table->integer('quantity')->default(0)->comment('Stok miktarı');
            $table->decimal('buy_price', 19, 2)->default(0)->comment('Alış fiyatına etkisi');
            $table->enum('buy_price_prefix', ['+', '-'])->default('+')->comment('Alış fiyat etkisinin yönü: +=ekle, -=çıkar');
            $table->decimal('sell_price', 19, 2)->default(0)->comment('Satış fiyatına etkisi');
            $table->enum('sell_price_prefix', ['+', '-'])->default('+')->comment('Satış fiyat etkisinin yönü: +=ekle, -=çıkar');
            $table->unsignedInteger('sell_point')->default(0)->comment('Puan bedeline etkisi');
            $table->enum('sell_point_prefix', ['+', '-'])->default('+')->comment('Puan etkisinin yönü: +=ekle, -=çıkar');
            $table->decimal('weight', 15, 4)->default(0)->comment('Ağırlığa etkisi');
            $table->enum('weight_prefix', ['+', '-'])->default('+')->comment('Ağırlık etkisinin yönü: +=ekle, -=çıkar');
            $table->decimal('discount_value', 19, 2)->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->enum('discount_type', ['percentage', 'amount'])->nullable()->comment('İndirim türü: percentage=yüzde, amount=tutar; null ise indirim yok');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('option_value_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
            $table->foreign('product_option_id')->references('product_option_id')->on(self::PRODUCT_OPTION_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_GROUPED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Gruplu ürün üyeleri (ana ürün altında ayrı ayrı satılan ürünler)');

            $table->bigIncrements('product_grouped_id')->comment('Grup üyesi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ana ürün (cat_product)');
            $table->unsignedBigInteger('grouped_product_id')->comment('Gruba dahil edilen ürün (cat_product)');
            $table->unsignedInteger('opening_quantity')->default(0)->comment('Varsayılan seçili miktar');
            $table->unsignedInteger('min_order_quantity')->default(0)->comment('Minimum sipariş miktarı; 0 ise sınır yok');
            $table->unsignedInteger('max_order_quantity')->default(0)->comment('Maksimum sipariş miktarı; 0 ise sınırsız');
            $table->unsignedInteger('order_step')->default(1)->comment('Sipariş artış katsayısı (kaçar adet artabilir)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'grouped_product_id'], 'uq_product_grouped');
            $table->index('grouped_product_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
            $table->foreign('grouped_product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_BUNDLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Paket ürün içeriği (tek fiyatla satılan ürün setleri)');

            $table->bigIncrements('product_bundle_id')->comment('Paket kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu paket ürünü (cat_product)');
            $table->unsignedBigInteger('bundle_product_id')->comment('Pakete dahil edilen ürün (cat_product)');
            $table->unsignedInteger('quantity')->default(1)->comment('Paketteki adet');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'bundle_product_id'], 'uq_product_bundle');
            $table->index('bundle_product_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
            $table->foreign('bundle_product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_RECURRING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün abonelik/tekrarlayan ödeme planları');

            $table->bigIncrements('product_recurring_id')->comment('Abonelik planı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('recurring_type_id')->comment('Tekrarlama planı kimliği (def_cat_recurring_type tablosuna referans)');
            $table->unsignedBigInteger('account_type_id')->nullable()->comment('Planın geçerli olduğu üye türü kimliği (def_mbr_account_type tablosuna referans); null ise tüm üyeler');
            $table->boolean('is_default')->default(false)->comment('Ürünün varsayılan abonelik planı mı?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_id', 'recurring_type_id', 'account_type_id'], 'uq_product_recurring');
            $table->index('recurring_type_id');
            $table->index('account_type_id');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_DOCUMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün dokümanları (kılavuz, sertifika vb. dosyalar)');

            $table->bigIncrements('product_document_id')->comment('Doküman için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->string('code', 64)->nullable()->comment('Doküman kodu');
            $table->string('image', 500)->nullable()->comment('Doküman kapak görseli dosya yolu');
            $table->string('file', 500)->comment('Doküman dosya yolu');
            $table->string('filename', 255)->nullable()->comment('Sunucudaki gerçek dosya adı');
            $table->string('mask', 255)->nullable()->comment('İndirme sırasında kullanıcıya gösterilecek dosya adı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PRODUCT_DOCUMENT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün dokümanı çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('product_document_translation_id')->comment('Doküman çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_document_id')->comment('Bağlı olduğu doküman (cat_product_document)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Doküman adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->text('description')->nullable()->comment('Doküman açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['product_document_id', 'language_code'], 'uq_product_document_translation_lang');

            $table->foreign('product_document_id')->references('product_document_id')->on(self::PRODUCT_DOCUMENT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::REVIEW_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün değerlendirmeleri (yorum ve puanlar)');

            $table->bigIncrements('review_id')->comment('Değerlendirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün (cat_product)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Değerlendirmeyi yapan üye (üyelik tablosuna referans; FK üyelik modülünde eklenir); null ise misafir');
            $table->string('code', 32)->unique()->comment('Değerlendirme takip kodu — benzersiz');
            $table->string('author', 150)->comment('Değerlendirme sahibinin görünen adı');
            $table->text('content')->comment('Değerlendirme metni');
            $table->unsignedTinyInteger('rating')->default(0)->comment('Puan (1-5)');
            $table->boolean('status')->default(false)->comment('Yayın durumu: true=onaylı/yayında, false=onay bekliyor');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('status');

            $table->foreign('product_id')->references('product_id')->on(self::PRODUCT_TABLE)->cascadeOnDelete();
        });

        Schema::table(self::PRODUCT_TABLE, function (Blueprint $table) {
            $table->foreign('default_variant_stock_id')->references('product_variant_stock_id')->on(self::PRODUCT_VARIANT_STOCK_TABLE)->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(self::PRODUCT_TABLE, function (Blueprint $table) {
            $table->dropForeign(['default_variant_stock_id']);
        });

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
        Schema::dropIfExists(self::PRODUCT_TABLE);
        Schema::dropIfExists(self::WAREHOUSE_TABLE);
        Schema::dropIfExists(self::DOWNLOAD_TRANSLATION_TABLE);
        Schema::dropIfExists(self::DOWNLOAD_TABLE);
    }
};
