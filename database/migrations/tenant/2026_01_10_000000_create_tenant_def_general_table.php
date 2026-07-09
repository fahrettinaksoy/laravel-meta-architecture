<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const BANK_TABLE = 'def_gen_bank';
    private const CARGO_TABLE = 'def_gen_cargo';
    private const VIDEO_SOURCE_TABLE = 'def_gen_video_source';
    private const VIDEO_SOURCE_TRANSLATION_TABLE = 'def_gen_video_source_translation';
    private const DISCOUNT_TYPE_TABLE = 'def_gen_discount_type';
    private const DISCOUNT_TYPE_TRANSLATION_TABLE = 'def_gen_discount_type_translation';
    private const GENDER_TABLE = 'def_gen_gender';
    private const GENDER_TRANSLATION_TABLE = 'def_gen_gender_translation';
    private const DOCUMENT_TYPE_TABLE = 'def_gen_document_type';
    private const DOCUMENT_TYPE_TRANSLATION_TABLE = 'def_gen_document_type_translation';

    public function up(): void
    {
        Schema::create(self::GENDER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Cinsiyet tanımları: male, female (genel tanım tablosu; personel ve üyelik modüllerinde gender_id buraya referans verir)');

            $table->bigIncrements('gender_id')->comment('Cinsiyet için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_gender_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 32)->collation('ascii_general_ci')->unique('uq_gender_code')->comment('Sabit kod — benzersiz (uygulama katmanı bu koda göre eşleştirir)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
        });

        Schema::create(self::GENDER_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Cinsiyet çevirileri (dile göre görünen ad)');

            $table->bigIncrements('gender_translation_id')->comment('Cinsiyet çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_gender_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('gender_id')->comment('Bağlı olduğu cinsiyet (def_gen_gender)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Görünen ad');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['gender_id', 'language_code'], 'uq_gender_translation_lang');
            $table->index('gender_id', 'ix_gender_translation_gender_id');
        });

        Schema::create(self::DOCUMENT_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Belge türü tanımları: legal, special (genel tanım tablosu; muhasebe/personel modüllerinde document_type_id buraya referans verir)');

            $table->bigIncrements('document_type_id')->comment('Belge türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_document_type_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 32)->collation('ascii_general_ci')->unique('uq_document_type_code')->comment('Sabit kod — benzersiz (uygulama katmanı bu koda göre eşleştirir)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
        });

        Schema::create(self::DOCUMENT_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Belge türü çevirileri (dile göre görünen ad)');

            $table->bigIncrements('document_type_translation_id')->comment('Belge türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_document_type_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('document_type_id')->comment('Bağlı olduğu belge türü (def_gen_document_type)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Görünen ad');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['document_type_id', 'language_code'], 'uq_document_type_translation_lang');
            $table->index('document_type_id', 'ix_document_type_translation_type_id');
        });

        Schema::create(self::DISCOUNT_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İndirim türü tanımları: percentage, amount (genel tanım tablosu; katalog, kampanya ve pazarlama modüllerinde discount_type_id buraya referans verir)');

            $table->bigIncrements('discount_type_id')->comment('İndirim türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_discount_type_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 32)->collation('ascii_general_ci')->unique('uq_discount_type_code')->comment('Sabit kod — benzersiz (uygulama katmanı bu koda göre eşleştirir)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
        });

        Schema::create(self::DISCOUNT_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İndirim türü çevirileri (dile göre görünen ad)');

            $table->bigIncrements('discount_type_translation_id')->comment('İndirim türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_discount_type_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('discount_type_id')->comment('Bağlı olduğu indirim türü (def_gen_discount_type)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Görünen ad');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['discount_type_id', 'language_code'], 'uq_discount_type_translation_lang');
            $table->index('discount_type_id', 'ix_discount_type_translation_type_id');
        });
        Schema::create(self::VIDEO_SOURCE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Video kaynağı tanımları: code, url, file, embed (genel tanım tablosu; katalog, blog ve bilgi bankası modüllerinde kullanılır)');

            $table->bigIncrements('video_source_id')->comment('Video kaynağı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_video_source_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 32)->collation('ascii_general_ci')->unique('uq_video_source_code')->comment('Sabit kod — benzersiz (uygulama katmanı bu koda göre eşleştirir)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
        });

        Schema::create(self::VIDEO_SOURCE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Video kaynağı çevirileri (dile göre görünen ad)');

            $table->bigIncrements('video_source_translation_id')->comment('Video kaynağı çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_video_source_translation_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('video_source_id')->comment('Bağlı olduğu video kaynağı (def_gen_video_source)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Çeviri dil kodu (ISO 639-1 / BCP 47: tr, en, en-gb)');
            $table->string('name')->comment('Görünen ad');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->unique(['video_source_id', 'language_code'], 'uq_video_source_translation_lang');
            $table->index('video_source_id', 'ix_video_source_translation_source_id');
        });
        Schema::create(self::BANK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Banka tanımları (genel tanım tablosu; üyelik ve muhasebe modüllerinde kullanılır)');

            $table->bigIncrements('bank_id')->comment('Banka için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Banka kodu — benzersiz');
            $table->string('type', 50)->nullable()->comment('Banka türü (örn. kamu, özel, katılım)');
            $table->string('name', 150)->comment('Banka adı');
            $table->string('eft_code', 20)->nullable()->comment('EFT banka kodu');
            $table->string('swift_code', 20)->nullable()->comment('SWIFT/BIC kodu');
            $table->string('telex_code', 20)->nullable()->comment('Teleks kodu');
            $table->string('image', 500)->nullable()->comment('Banka logosu dosya yolu');
            $table->string('website')->nullable()->comment('Web sitesi adresi');
            $table->string('email', 254)->nullable()->comment('E-posta adresi');
            $table->string('call_center_number', 20)->nullable()->comment('Çağrı merkezi numarası');
            $table->string('phone_number', 20)->nullable()->comment('Telefon numarası');
            $table->string('fax_number', 20)->nullable()->comment('Faks numarası');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::CARGO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kargo firması tanımları (genel tanım tablosu; sevkiyat işlemlerinde kullanılır)');

            $table->bigIncrements('cargo_id')->comment('Kargo firması için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Kargo firması kodu — benzersiz');
            $table->string('name', 150)->comment('Kargo firması adı');
            $table->string('image', 500)->nullable()->comment('Firma logosu dosya yolu');
            $table->string('website')->nullable()->comment('Web sitesi adresi');
            $table->string('email', 254)->nullable()->comment('E-posta adresi');
            $table->string('call_center_number', 20)->nullable()->comment('Çağrı merkezi numarası');
            $table->string('phone_number', 20)->nullable()->comment('Telefon numarası');
            $table->string('fax_number', 20)->nullable()->comment('Faks numarası');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::DOCUMENT_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::DOCUMENT_TYPE_TABLE);
        Schema::dropIfExists(self::GENDER_TRANSLATION_TABLE);
        Schema::dropIfExists(self::GENDER_TABLE);
        Schema::dropIfExists(self::DISCOUNT_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::DISCOUNT_TYPE_TABLE);
        Schema::dropIfExists(self::VIDEO_SOURCE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::VIDEO_SOURCE_TABLE);
        Schema::dropIfExists(self::CARGO_TABLE);
        Schema::dropIfExists(self::BANK_TABLE);
    }
};
