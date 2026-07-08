<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const CAMPAIGN_TYPE_TABLE = 'def_mkt_campaign_type';
    private const GIFT_VOUCHER_THEME_TABLE = 'def_mkt_gift_voucher_theme';
    private const CAMPAIGN_TABLE = 'mkt_campaign';
    private const CAMPAIGN_TRANSLATION_TABLE = 'mkt_campaign_translation';
    private const CAMPAIGN_PRODUCT_TABLE = 'mkt_campaign_product';
    private const CAMPAIGN_HISTORY_TABLE = 'mkt_campaign_history';
    private const COUPON_TABLE = 'mkt_coupon';
    private const COUPON_HISTORY_TABLE = 'mkt_coupon_history';
    private const COUPON_PRODUCT_TABLE = 'mkt_coupon_product';
    private const COUPON_CATEGORY_TABLE = 'mkt_coupon_category';
    private const COUPON_BRAND_TABLE = 'mkt_coupon_brand';
    private const COUPON_ACCOUNT_GROUP_TABLE = 'mkt_coupon_account_group';
    private const GIFT_VOUCHER_TABLE = 'mkt_gift_voucher';

    public function up(): void
    {
        Schema::create(self::CAMPAIGN_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kampanyalar (pazarlama kampanyası ana tablosu)');

            $table->bigIncrements('campaign_id')->comment('Kampanya için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('campaign_type_id')->nullable()->comment('Kampanya türü (def_mkt_campaign_type)');
            $table->string('code', 64)->unique()->comment('Kampanya kodu/slug — benzersiz');
            $table->string('image', 500)->nullable()->comment('Kampanya görseli dosya yolu');
            $table->json('settings')->nullable()->comment('Kampanya kurgusuna özel ayarlar (JSON)');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (site_layout tablosuna referans)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');
            $table->timestamp('published_start_at')->nullable()->comment('Kampanya başlangıç tarihi; null ise hemen başlar');
            $table->timestamp('published_end_at')->nullable()->comment('Kampanya bitiş tarihi; null ise süresiz');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');

            $table->foreign('campaign_type_id')->references('campaign_type_id')->on(self::CAMPAIGN_TYPE_TABLE)->nullOnDelete();
        });

        Schema::create(self::CAMPAIGN_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kampanya çevirileri (dile göre ad, açıklama, koşullar ve meta bilgileri)');

            $table->bigIncrements('campaign_translation_id')->comment('Kampanya çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('campaign_id')->comment('Bağlı olduğu kampanya (mkt_campaign)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Kampanya adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->longText('description')->nullable()->comment('Kampanya açıklaması (HTML içerebilir)');
            $table->longText('condition')->nullable()->comment('Kampanya katılım koşulları (HTML içerebilir)');
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

            $table->unique(['campaign_id', 'language_code'], 'uq_campaign_translation_lang');

            $table->foreign('campaign_id')->references('campaign_id')->on(self::CAMPAIGN_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CAMPAIGN_PRODUCT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kampanya-ürün eşleştirmeleri (kampanyaya dahil ürünler)');

            $table->bigIncrements('campaign_product_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('campaign_id')->comment('Bağlı olduğu kampanya (mkt_campaign)');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği (cat_product tablosuna referans; FK katalog modülünde eklenir)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['campaign_id', 'product_id'], 'uq_campaign_product');
            $table->index('product_id');

            $table->foreign('campaign_id')->references('campaign_id')->on(self::CAMPAIGN_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CAMPAIGN_HISTORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kampanya kullanım geçmişi (siparişlerde uygulanan kampanya kayıtları)');

            $table->bigIncrements('campaign_history_id')->comment('Kullanım kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('campaign_id')->comment('Bağlı olduğu kampanya (mkt_campaign)');
            $table->unsignedBigInteger('order_id')->nullable()->comment('Uygulandığı sipariş kimliği (acct_order tablosuna referans; FK sipariş modülünde eklenir)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Faydalanan üye kimliği (mbr_account tablosuna referans; FK üyelik modülünde eklenir)');
            $table->decimal('amount', 19, 2)->default(0)->comment('Kampanyanın sağladığı indirim/fayda tutarı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('order_id');
            $table->index('account_id');

            $table->foreign('campaign_id')->references('campaign_id')->on(self::CAMPAIGN_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::COUPON_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İndirim kuponları');

            $table->bigIncrements('coupon_id')->comment('Kupon için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Kupon kodu — benzersiz; müşterinin sepette girdiği kod');
            $table->string('name', 150)->comment('Kupon adı (iç kullanım)');
            $table->enum('discount_type', ['percentage', 'amount'])->default('percentage')->comment('İndirim türü: percentage=yüzde, amount=tutar');
            $table->decimal('discount_value', 19, 2)->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->decimal('cart_total', 19, 2)->default(0)->comment('Kuponun geçerli olması için minimum sepet tutarı; 0 ise şart yok');
            $table->unsignedInteger('limit_total')->default(0)->comment('Toplam kullanım limiti; 0 ise sınırsız');
            $table->unsignedInteger('limit_per_account')->default(0)->comment('Üye başına kullanım limiti; 0 ise sınırsız');
            $table->timestamp('started_at')->nullable()->comment('Geçerlilik başlangıç tarihi; null ise hemen geçerli');
            $table->timestamp('ended_at')->nullable()->comment('Geçerlilik bitiş tarihi; null ise süresiz');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::COUPON_HISTORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kupon kullanım geçmişi (siparişlerde uygulanan kupon kayıtları)');

            $table->bigIncrements('coupon_history_id')->comment('Kullanım kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('coupon_id')->comment('Bağlı olduğu kupon (mkt_coupon)');
            $table->unsignedBigInteger('order_id')->nullable()->comment('Uygulandığı sipariş kimliği (acct_order tablosuna referans; FK sipariş modülünde eklenir)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Kullanan üye kimliği (mbr_account tablosuna referans; FK üyelik modülünde eklenir)');
            $table->decimal('amount', 19, 2)->default(0)->comment('Kuponun sağladığı indirim tutarı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('order_id');
            $table->index('account_id');

            $table->foreign('coupon_id')->references('coupon_id')->on(self::COUPON_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::COUPON_PRODUCT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kupon-ürün eşleştirmeleri (kuponun geçerli olduğu ürünler; boşsa tüm ürünler)');

            $table->bigIncrements('coupon_product_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('coupon_id')->comment('Bağlı olduğu kupon (mkt_coupon)');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği (cat_product tablosuna referans; FK katalog modülünde eklenir)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['coupon_id', 'product_id'], 'uq_coupon_product');
            $table->index('product_id');

            $table->foreign('coupon_id')->references('coupon_id')->on(self::COUPON_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::COUPON_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kupon-kategori eşleştirmeleri (kuponun geçerli olduğu kategoriler)');

            $table->bigIncrements('coupon_category_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('coupon_id')->comment('Bağlı olduğu kupon (mkt_coupon)');
            $table->unsignedBigInteger('category_id')->comment('Kategori kimliği (def_cat_category tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['coupon_id', 'category_id'], 'uq_coupon_category');
            $table->index('category_id');

            $table->foreign('coupon_id')->references('coupon_id')->on(self::COUPON_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::COUPON_BRAND_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kupon-marka eşleştirmeleri (kuponun geçerli olduğu markalar)');

            $table->bigIncrements('coupon_brand_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('coupon_id')->comment('Bağlı olduğu kupon (mkt_coupon)');
            $table->unsignedBigInteger('brand_id')->comment('Marka kimliği (def_cat_brand tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['coupon_id', 'brand_id'], 'uq_coupon_brand');
            $table->index('brand_id');

            $table->foreign('coupon_id')->references('coupon_id')->on(self::COUPON_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::COUPON_ACCOUNT_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kupon-üye grubu eşleştirmeleri (kuponun geçerli olduğu üye grupları)');

            $table->bigIncrements('coupon_account_group_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('coupon_id')->comment('Bağlı olduğu kupon (mkt_coupon)');
            $table->unsignedBigInteger('account_group_id')->comment('Üye grubu kimliği (def_mbr_account_group tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['coupon_id', 'account_group_id'], 'uq_coupon_account_group');
            $table->index('account_group_id');

            $table->foreign('coupon_id')->references('coupon_id')->on(self::COUPON_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::GIFT_VOUCHER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hediye çekleri (üyeler arası gönderilebilen bakiye çekleri)');

            $table->bigIncrements('gift_voucher_id')->comment('Hediye çeki için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('gift_voucher_theme_id')->nullable()->comment('Çeki teması (def_mkt_gift_voucher_theme)');
            $table->unsignedBigInteger('order_id')->nullable()->comment('Çekin satın alındığı sipariş kimliği (acct_order tablosuna referans; FK sipariş modülünde eklenir)');
            $table->string('code', 64)->unique()->comment('Çeki kodu — benzersiz; alıcının kullanacağı kod');
            $table->string('from_name', 150)->nullable()->comment('Gönderen adı');
            $table->string('from_email', 254)->nullable()->comment('Gönderen e-posta adresi');
            $table->string('to_name', 150)->comment('Alıcı adı');
            $table->string('to_email', 254)->comment('Alıcı e-posta adresi');
            $table->text('message')->nullable()->comment('Alıcıya iletilecek hediye mesajı');
            $table->decimal('amount', 19, 2)->default(0)->comment('Çeki tutarı');
            $table->boolean('is_used')->default(false)->comment('Çeki kullanıldı mı?');
            $table->timestamp('sent_at')->nullable()->comment('Alıcıya gönderim tarihi; null ise henüz gönderilmedi');
            $table->timestamp('used_at')->nullable()->comment('Kullanım tarihi; null ise kullanılmadı');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('order_id');
            $table->index('status');

            $table->foreign('gift_voucher_theme_id')->references('gift_voucher_theme_id')->on(self::GIFT_VOUCHER_THEME_TABLE)->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::GIFT_VOUCHER_TABLE);
        Schema::dropIfExists(self::COUPON_ACCOUNT_GROUP_TABLE);
        Schema::dropIfExists(self::COUPON_BRAND_TABLE);
        Schema::dropIfExists(self::COUPON_CATEGORY_TABLE);
        Schema::dropIfExists(self::COUPON_PRODUCT_TABLE);
        Schema::dropIfExists(self::COUPON_HISTORY_TABLE);
        Schema::dropIfExists(self::COUPON_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_HISTORY_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_PRODUCT_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_TABLE);
    }
};
