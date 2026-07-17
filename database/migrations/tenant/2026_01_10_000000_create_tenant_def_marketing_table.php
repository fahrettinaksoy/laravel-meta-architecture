<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const CAMPAIGN_TYPE_TABLE = 'def_mkt_campaign_type';
    private const CAMPAIGN_TYPE_TRANSLATION_TABLE = 'def_mkt_campaign_type_translation';
    private const GIFT_VOUCHER_THEME_TABLE = 'def_mkt_gift_voucher_theme';
    private const GIFT_VOUCHER_THEME_TRANSLATION_TABLE = 'def_mkt_gift_voucher_theme_translation';

    public function up(): void
    {
        Schema::create(self::CAMPAIGN_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kampanya türleri (pazarlama tanım tablosu)');

            $table->bigIncrements('campaign_type_id')->comment('Kampanya türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Kampanya türü kodu — benzersiz');
            $table->string('image', 500)->nullable()->comment('Kampanya türü görseli dosya yolu');
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

        Schema::create(self::CAMPAIGN_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kampanya türü çevirileri (dile göre ad, özet ve açıklama)');

            $table->bigIncrements('campaign_type_translation_id')->comment('Kampanya türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('campaign_type_id')->comment('Bağlı olduğu kampanya türü (def_mkt_campaign_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Kampanya türü adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->text('description')->nullable()->comment('Kampanya türü açıklaması (HTML içerebilir)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['campaign_type_id', 'language_code'], 'uq_campaign_type_translation_lang');
        });

        Schema::create(self::GIFT_VOUCHER_THEME_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hediye çeki temaları (pazarlama tanım tablosu)');

            $table->bigIncrements('gift_voucher_theme_id')->comment('Hediye çeki teması için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Tema kodu — benzersiz');
            $table->string('image', 500)->nullable()->comment('Tema görseli dosya yolu');
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

        Schema::create(self::GIFT_VOUCHER_THEME_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hediye çeki teması çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('gift_voucher_theme_translation_id')->comment('Tema çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('gift_voucher_theme_id')->comment('Bağlı olduğu hediye çeki teması (def_mkt_gift_voucher_theme)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Tema adı');
            $table->text('description')->nullable()->comment('Tema açıklaması (HTML içerebilir)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['gift_voucher_theme_id', 'language_code'], 'uq_gift_voucher_theme_translation_lang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::GIFT_VOUCHER_THEME_TRANSLATION_TABLE);
        Schema::dropIfExists(self::GIFT_VOUCHER_THEME_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_TYPE_TABLE);
    }
};
