<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const CHANNEL_TYPE_TABLE = 'def_mpl_channel_type';
    private const CHANNEL_TYPE_TRANSLATION_TABLE = 'def_mpl_channel_type_translation';
    private const CHANNEL_SERVICE_TABLE = 'def_mpl_channel_service';
    private const CHANNEL_SERVICE_TRANSLATION_TABLE = 'def_mpl_channel_service_translation';
    private const CHANNEL_CATEGORY_TABLE = 'def_mpl_channel_category';

    public function up(): void
    {
        Schema::create(self::CHANNEL_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Satış kanalı türleri: pazaryeri entegrasyonları (pazaryeri tanım tablosu; eski adı: dfntn_ctlg_channel_type)');

            $table->bigIncrements('channel_type_id')->comment('Kanal türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Kanal türü kodu (örn. trendyol, hepsiburada) — benzersiz');
            $table->string('class')->nullable()->comment('Entegrasyonu yürüten sınıf yolu (PHP class)');
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

        Schema::create(self::CHANNEL_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kanal türü çevirileri (dile göre ad)');

            $table->bigIncrements('channel_type_translation_id')->comment('Kanal türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('channel_type_id')->comment('Bağlı olduğu kanal türü (def_mpl_channel_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Kanal türü adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['channel_type_id', 'language_code'], 'uq_channel_type_translation_lang');

            $table->foreign('channel_type_id')->references('channel_type_id')->on(self::CHANNEL_TYPE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CHANNEL_SERVICE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kanal servisleri: kanal entegrasyonlarının sunduğu hizmetler (pazaryeri tanım tablosu)');

            $table->bigIncrements('channel_service_id')->comment('Kanal servisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Servis kodu — benzersiz');
            $table->json('elements')->nullable()->comment('Servis yapılandırma form elemanları (JSON)');
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

        Schema::create(self::CHANNEL_SERVICE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kanal servisi çevirileri (dile göre ad ve özet)');

            $table->bigIncrements('channel_service_translation_id')->comment('Servis çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('channel_service_id')->comment('Bağlı olduğu kanal servisi (def_mpl_channel_service)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Servis adı');
            $table->string('summary')->nullable()->comment('Kısa özet');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['channel_service_id', 'language_code'], 'uq_channel_service_translation_lang');

            $table->foreign('channel_service_id')->references('channel_service_id')->on(self::CHANNEL_SERVICE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CHANNEL_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kanal kategori ağacı: pazaryerinden indirilen kategori listesinin yerel kopyası (eski adı: dfntn_mrktplc_category)');

            $table->bigIncrements('channel_category_id')->comment('Kanal kategorisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('channel_type_id')->nullable()->comment('Bağlı olduğu kanal türü (def_mpl_channel_type); eski adı: channel_id');
            $table->string('channel_code', 64)->nullable()->comment('Pazaryeri kanal kodu (örn. trendyol, hepsiburada)');
            $table->string('remote_id', 100)->nullable()->comment('Kanal tarafındaki kategori kimliği; eski adı: category_id');
            $table->string('remote_code', 100)->nullable()->comment('Kanal tarafındaki kategori kodu');
            $table->string('remote_parent_id', 100)->nullable()->comment('Kanal tarafındaki üst kategori kimliği; null/boş ise kök kategori');
            $table->string('remote_parent_code', 100)->nullable()->comment('Kanal tarafındaki üst kategori kodu');
            $table->string('remote_name', 255)->nullable()->comment('Kanal tarafındaki kategori adı');
            $table->string('remote_path', 500)->nullable()->comment('Kategori ağaç yolu (örn. Elektronik > Telefon > Aksesuar)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index(['channel_type_id', 'remote_id'], 'idx_channel_category_remote');
            $table->index('remote_parent_id');

            $table->foreign('channel_type_id')->references('channel_type_id')->on(self::CHANNEL_TYPE_TABLE)->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::CHANNEL_CATEGORY_TABLE);
        Schema::dropIfExists(self::CHANNEL_SERVICE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CHANNEL_SERVICE_TABLE);
        Schema::dropIfExists(self::CHANNEL_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CHANNEL_TYPE_TABLE);
    }
};
