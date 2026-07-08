<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const PAIRING_BRAND_TABLE = 'mpl_pairing_brand';
    private const PAIRING_CATEGORY_TABLE = 'mpl_pairing_category';
    private const PAIRING_PRODUCT_TABLE = 'mpl_pairing_product';
    private const PAIRING_WAREHOUSE_TABLE = 'mpl_pairing_warehouse';
    private const PAIRING_CARGO_TABLE = 'mpl_pairing_cargo';
    private const PAIRING_FILTER_TABLE = 'mpl_pairing_filter';

    public function up(): void
    {
        Schema::create(self::PAIRING_BRAND_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazaryeri marka eşleştirmeleri (yerel marka ↔ kanal markası)');

            $table->bigIncrements('pairing_brand_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('channel_id')->nullable()->comment('Pazaryeri kanalı kimliği (def_mpl_channel_type tablosuna referans)');
            $table->string('channel_code', 64)->nullable()->comment('Pazaryeri kanal kodu (örn. trendyol, hepsiburada)');
            $table->unsignedBigInteger('local_id')->nullable()->comment('Yerel marka kimliği (def_cat_brand tablosuna referans); eski adı: matching_id');
            $table->string('local_code', 64)->nullable()->comment('Yerel marka kodu (anlık kopya)');
            $table->unsignedBigInteger('remote_id')->nullable()->comment('Kanal tarafındaki marka kimliği; eski adı: matched_id');
            $table->string('remote_code', 100)->nullable()->comment('Kanal tarafındaki marka kodu');
            $table->string('remote_name', 255)->nullable()->comment('Kanal tarafındaki marka adı (anlık kopya)');
            $table->json('remote_attributes')->nullable()->comment('Kanalın sunduğu özellik listesi (JSON)');
            $table->json('remote_attribute_selection')->nullable()->comment('Seçilen özellik eşleştirmeleri (JSON)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('channel_id');
            $table->index('local_id');
        });

        Schema::create(self::PAIRING_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazaryeri kategori eşleştirmeleri (yerel kategori ↔ kanal kategorisi)');

            $table->bigIncrements('pairing_category_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('channel_id')->nullable()->comment('Pazaryeri kanalı kimliği (def_mpl_channel_type tablosuna referans)');
            $table->string('channel_code', 64)->nullable()->comment('Pazaryeri kanal kodu (örn. trendyol, hepsiburada)');
            $table->unsignedBigInteger('local_id')->nullable()->comment('Yerel kategori kimliği (def_cat_category tablosuna referans); eski adı: matching_id');
            $table->string('local_code', 64)->nullable()->comment('Yerel kategori kodu (anlık kopya)');
            $table->unsignedBigInteger('remote_id')->nullable()->comment('Kanal tarafındaki kategori kimliği; eski adı: matched_id');
            $table->string('remote_code', 100)->nullable()->comment('Kanal tarafındaki kategori kodu');
            $table->string('remote_name', 255)->nullable()->comment('Kanal tarafındaki kategori adı (anlık kopya)');
            $table->json('remote_attributes')->nullable()->comment('Kanal kategorisinin zorunlu/opsiyonel özellikleri (JSON)');
            $table->json('remote_attribute_selection')->nullable()->comment('Seçilen özellik eşleştirmeleri (JSON)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('channel_id');
            $table->index('local_id');
        });

        Schema::create(self::PAIRING_PRODUCT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazaryeri ürün eşleştirmeleri (yerel ürün ↔ kanal ilanı; fiyat/stok gönderimi)');

            $table->bigIncrements('pairing_product_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('channel_id')->nullable()->comment('Pazaryeri kanalı kimliği (def_mpl_channel_type tablosuna referans)');
            $table->string('channel_code', 64)->nullable()->comment('Pazaryeri kanal kodu (örn. trendyol, hepsiburada)');
            $table->unsignedBigInteger('local_id')->nullable()->comment('Yerel ürün kimliği (cat_product tablosuna referans); eski adı: matching_id');
            $table->string('local_code', 64)->nullable()->comment('Yerel ürün kodu (anlık kopya)');
            $table->unsignedBigInteger('remote_id')->nullable()->comment('Kanal tarafındaki ilan/ürün kimliği; eski adı: matched_id');
            $table->string('remote_code', 100)->nullable()->comment('Kanal tarafındaki ürün kodu (örn. barkod/stok kodu)');
            $table->string('remote_name', 255)->nullable()->comment('Kanal tarafındaki ürün adı (anlık kopya)');
            $table->string('remote_batch_id', 100)->nullable()->comment('Kanala gönderimde dönen parti/istek (batch) kimliği');
            $table->decimal('remote_price', 19, 2)->default(0)->comment('Kanala gönderilen satış fiyatı');
            $table->char('remote_currency_code', 3)->default('TRY')->comment('Kanal fiyatının para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('remote_tax_class_id')->nullable()->comment('Kanala gönderilen KDV sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->integer('remote_quantity')->default(0)->comment('Kanala gönderilen stok miktarı');
            $table->json('remote_attributes')->nullable()->comment('Kanala gönderilen özellik listesi (JSON)');
            $table->json('remote_attribute_selection')->nullable()->comment('Seçilen özellik eşleştirmeleri (JSON)');
            $table->json('remote_response')->nullable()->comment('Kanaldan dönen son yanıt (JSON)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('channel_id');
            $table->index('local_id');
            $table->index('remote_code');
        });

        Schema::create(self::PAIRING_WAREHOUSE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazaryeri depo eşleştirmeleri (yerel depo ↔ kanal deposu)');

            $table->bigIncrements('pairing_warehouse_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('channel_id')->nullable()->comment('Pazaryeri kanalı kimliği (def_mpl_channel_type tablosuna referans)');
            $table->string('channel_code', 64)->nullable()->comment('Pazaryeri kanal kodu (örn. trendyol, hepsiburada)');
            $table->unsignedBigInteger('local_id')->nullable()->comment('Yerel depo kimliği (cat_warehouse tablosuna referans); eski adı: matching_id');
            $table->string('local_code', 64)->nullable()->comment('Yerel depo kodu (anlık kopya)');
            $table->unsignedBigInteger('remote_id')->nullable()->comment('Kanal tarafındaki depo kimliği; eski adı: matched_id');
            $table->string('remote_code', 100)->nullable()->comment('Kanal tarafındaki depo kodu');
            $table->string('remote_name', 255)->nullable()->comment('Kanal tarafındaki depo adı (anlık kopya)');
            $table->json('remote_attributes')->nullable()->comment('Kanalın sunduğu özellik listesi (JSON)');
            $table->json('remote_attribute_selection')->nullable()->comment('Seçilen özellik eşleştirmeleri (JSON)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('channel_id');
            $table->index('local_id');
        });

        Schema::create(self::PAIRING_CARGO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazaryeri kargo eşleştirmeleri (yerel kargo firması ↔ kanal kargo seçeneği)');

            $table->bigIncrements('pairing_cargo_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('channel_id')->nullable()->comment('Pazaryeri kanalı kimliği (def_mpl_channel_type tablosuna referans)');
            $table->string('channel_code', 64)->nullable()->comment('Pazaryeri kanal kodu (örn. trendyol, hepsiburada)');
            $table->unsignedBigInteger('local_id')->nullable()->comment('Yerel kargo firması kimliği (def_gen_cargo tablosuna referans); eski adı: matching_id');
            $table->string('local_code', 64)->nullable()->comment('Yerel kargo firması kodu (anlık kopya)');
            $table->unsignedBigInteger('remote_id')->nullable()->comment('Kanal tarafındaki kargo seçeneği kimliği; eski adı: matched_id');
            $table->string('remote_code', 100)->nullable()->comment('Kanal tarafındaki kargo kodu');
            $table->string('remote_name', 255)->nullable()->comment('Kanal tarafındaki kargo adı (anlık kopya)');
            $table->json('remote_attributes')->nullable()->comment('Kanalın sunduğu özellik listesi (JSON)');
            $table->json('remote_attribute_selection')->nullable()->comment('Seçilen özellik eşleştirmeleri (JSON)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('channel_id');
            $table->index('local_id');
        });

        Schema::create(self::PAIRING_FILTER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazaryeri filtre eşleştirmeleri (yerel filtre ↔ kanal filtresi/özelliği)');

            $table->bigIncrements('pairing_filter_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('channel_id')->nullable()->comment('Pazaryeri kanalı kimliği (def_mpl_channel_type tablosuna referans)');
            $table->string('channel_code', 64)->nullable()->comment('Pazaryeri kanal kodu (örn. trendyol, hepsiburada)');
            $table->unsignedBigInteger('local_id')->nullable()->comment('Yerel filtre kimliği (def_cat_filter tablosuna referans); eski adı: matching_id');
            $table->string('local_code', 64)->nullable()->comment('Yerel filtre kodu (anlık kopya)');
            $table->unsignedBigInteger('remote_id')->nullable()->comment('Kanal tarafındaki filtre kimliği; eski adı: matched_id');
            $table->string('remote_code', 100)->nullable()->comment('Kanal tarafındaki filtre kodu');
            $table->string('remote_name', 255)->nullable()->comment('Kanal tarafındaki filtre adı (anlık kopya)');
            $table->json('remote_attributes')->nullable()->comment('Kanalın sunduğu özellik listesi (JSON)');
            $table->json('remote_attribute_selection')->nullable()->comment('Seçilen özellik eşleştirmeleri (JSON)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('channel_id');
            $table->index('local_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::PAIRING_FILTER_TABLE);
        Schema::dropIfExists(self::PAIRING_CARGO_TABLE);
        Schema::dropIfExists(self::PAIRING_WAREHOUSE_TABLE);
        Schema::dropIfExists(self::PAIRING_PRODUCT_TABLE);
        Schema::dropIfExists(self::PAIRING_CATEGORY_TABLE);
        Schema::dropIfExists(self::PAIRING_BRAND_TABLE);
    }
};
