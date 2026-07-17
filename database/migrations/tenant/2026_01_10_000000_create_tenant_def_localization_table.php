<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const LANGUAGE_TABLE = 'def_loc_language';
    private const CURRENCY_TABLE = 'def_loc_currency';
    private const COUNTRY_TABLE = 'def_loc_country';
    private const CITY_TABLE = 'def_loc_city';
    private const DISTRICT_TABLE = 'def_loc_district';
    private const GEO_TABLE = 'def_loc_geo';
    private const GEO_ZONE_TABLE = 'def_loc_geo_zone';
    private const LENGTH_CLASS_TABLE = 'def_loc_length_class';
    private const LENGTH_CLASS_TRANSLATION_TABLE = 'def_loc_length_class_translation';
    private const WEIGHT_CLASS_TABLE = 'def_loc_weight_class';
    private const WEIGHT_CLASS_TRANSLATION_TABLE = 'def_loc_weight_class_translation';
    private const UNIT_TABLE = 'def_loc_unit';
    private const UNIT_TRANSLATION_TABLE = 'def_loc_unit_translation';
    private const TAX_CLASS_TABLE = 'def_loc_tax_class';
    private const TAX_CLASS_TRANSLATION_TABLE = 'def_loc_tax_class_translation';
    private const TAX_CLASS_ACCOUNT_TYPE_TABLE = 'def_loc_tax_class_account_type';
    private const TAX_RATE_TABLE = 'def_loc_tax_rate';
    private const TAX_RATE_ACCOUNT_GROUP_TABLE = 'def_loc_tax_rate_account_group';
    private const ADDRESS_TYPE_TABLE = 'def_loc_address_type';
    private const ADDRESS_TYPE_TRANSLATION_TABLE = 'def_loc_address_type_translation';

    public function up(): void
    {
        Schema::create(self::LANGUAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Diller (lokalizasyon tanım tablosu)');

            $table->bigIncrements('language_id')->comment('Dil için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 5)->unique()->comment('Dil kodu (ISO 639-1: tr, en, en-gb) — benzersiz; çeviri tablolarındaki language_code buna referans verir');
            $table->string('name', 100)->comment('Dil adı (kendi dilinde, örn. Türkçe, English)');
            $table->string('locale', 100)->nullable()->comment('Locale tanımı (örn. tr_TR.UTF-8)');
            $table->string('image', 500)->nullable()->comment('Bayrak görseli dosya yolu');
            $table->string('directory', 100)->nullable()->comment('Çeviri dosyalarının bulunduğu dizin adı');
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr')->comment('Yazım yönü: ltr=soldan sağa, rtl=sağdan sola');
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

        Schema::create(self::CURRENCY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Para birimleri ve kur bilgileri (lokalizasyon tanım tablosu)');

            $table->bigIncrements('currency_id')->comment('Para birimi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->char('code', 3)->unique()->comment('Para birimi kodu (ISO 4217: TRY, USD, EUR) — benzersiz; tablolardaki currency_code buna referans verir');
            $table->string('name', 100)->comment('Para birimi adı (örn. Türk Lirası)');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('symbol_left', 20)->nullable()->comment('Tutarın solunda gösterilecek sembol (örn. $)');
            $table->string('symbol_right', 20)->nullable()->comment('Tutarın sağında gösterilecek sembol (örn. ₺)');
            $table->unsignedTinyInteger('decimal_places')->default(2)->comment('Ondalık basamak sayısı');
            $table->char('decimal_separator', 1)->default(',')->comment('Ondalık ayracı');
            $table->char('thousand_separator', 1)->default('.')->comment('Binlik ayracı');
            $table->decimal('value', 15, 8)->default(1)->comment('Ana para birimine göre kur çarpanı; ana birimde 1');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::COUNTRY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ülkeler (lokalizasyon tanım tablosu)');

            $table->bigIncrements('country_id')->comment('Ülke için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('name', 150)->comment('Ülke adı (yerel dilde, örn. Türkiye)');
            $table->string('title', 150)->nullable()->comment('Uluslararası/resmi ad (örn. Republic of Türkiye)');
            $table->char('iso_code_2', 2)->unique()->comment('ISO 3166-1 alpha-2 kodu (TR) — benzersiz');
            $table->char('iso_code_3', 3)->unique()->comment('ISO 3166-1 alpha-3 kodu (TUR) — benzersiz');
            $table->text('address_format')->nullable()->comment('Adres yazım şablonu (yer tutucularla)');
            $table->boolean('postcode_required')->default(false)->comment('Adreslerde posta kodu zorunlu mu?');
            $table->char('currency_code', 3)->nullable()->comment('Varsayılan para birimi kodu (def_loc_currency.code)');
            $table->string('language_code', 5)->nullable()->comment('Varsayılan dil kodu (def_loc_language.code)');
            $table->string('time_zone', 50)->nullable()->comment('Saat dilimi (örn. Europe/Istanbul)');
            $table->string('phone_code', 10)->nullable()->comment('Uluslararası telefon kodu (örn. +90)');
            $table->string('domain_extension', 10)->nullable()->comment('Ülke alan adı uzantısı (örn. .tr)');
            $table->string('date_format', 20)->nullable()->comment('Tarih gösterim biçimi (örn. d.m.Y)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::CITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İller/şehirler (lokalizasyon tanım tablosu)');

            $table->bigIncrements('city_id')->comment('İl için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('country_id')->comment('Bağlı olduğu ülke (def_loc_country)');
            $table->string('name', 150)->comment('İl adı');
            $table->string('iso_code_2', 10)->nullable()->comment('ISO 3166-2 il kodu (örn. TR-34)');
            $table->string('iso_code_3', 10)->nullable()->comment('Alternatif il kodu');
            $table->string('traffic_code', 10)->nullable()->comment('Plaka kodu (örn. 34)');
            $table->string('phone_code', 10)->nullable()->comment('Telefon alan kodu (örn. 212)');
            $table->text('svg_path')->nullable()->comment('Harita gösterimi için SVG çizim yolu');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
            $table->index('country_id');
        });

        Schema::create(self::DISTRICT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İlçeler (lokalizasyon tanım tablosu)');

            $table->bigIncrements('district_id')->comment('İlçe için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('city_id')->comment('Bağlı olduğu il (def_loc_city); ülkeye il üzerinden ulaşılır');
            $table->string('name', 150)->comment('İlçe adı');
            $table->string('postcode', 10)->nullable()->comment('Posta kodu');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
            $table->index('city_id');
        });

        Schema::create(self::GEO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Coğrafi bölge grupları: vergi/kargo kuralları için bölge tanımları (lokalizasyon tanım tablosu)');

            $table->bigIncrements('geo_id')->comment('Coğrafi bölge için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Bölge kodu — benzersiz');
            $table->string('name', 150)->comment('Bölge adı (örn. Yurt İçi, AB Ülkeleri)');
            $table->string('description')->nullable()->comment('Bölge açıklaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::GEO_ZONE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Coğrafi bölge kapsamları (bölgeye dahil ülke/il/ilçe satırları)');

            $table->bigIncrements('geo_zone_id')->comment('Bölge kapsamı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('geo_id')->comment('Bağlı olduğu coğrafi bölge (def_loc_geo)');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Kapsanan ülke (def_loc_country); null ise tüm ülkeler');
            $table->unsignedBigInteger('city_id')->nullable()->comment('Kapsanan il (def_loc_city); null ise ülkenin tamamı');
            $table->unsignedBigInteger('district_id')->nullable()->comment('Kapsanan ilçe (def_loc_district); null ise ilin tamamı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('country_id');
            $table->index('city_id');
            $table->index('district_id');
            $table->index('geo_id');
        });

        Schema::create(self::LENGTH_CLASS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uzunluk birimleri: cm, m vb. (lokalizasyon tanım tablosu)');

            $table->bigIncrements('length_class_id')->comment('Uzunluk birimi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Birim kodu (örn. cm, m) — benzersiz');
            $table->decimal('value', 15, 8)->default(1)->comment('Temel birime dönüşüm çarpanı; temel birimde 1');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::LENGTH_CLASS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uzunluk birimi çevirileri (dile göre ad ve kısaltma)');

            $table->bigIncrements('length_class_translation_id')->comment('Birim çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('length_class_id')->comment('Bağlı olduğu uzunluk birimi (def_loc_length_class)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name', 100)->comment('Birim adı (örn. Santimetre)');
            $table->string('abbreviation', 20)->nullable()->comment('Kısaltma (örn. cm)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['length_class_id', 'language_code'], 'uq_length_class_translation_lang');
        });

        Schema::create(self::WEIGHT_CLASS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ağırlık birimleri: kg, g vb. (lokalizasyon tanım tablosu)');

            $table->bigIncrements('weight_class_id')->comment('Ağırlık birimi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Birim kodu (örn. kg, g) — benzersiz');
            $table->decimal('value', 15, 8)->default(1)->comment('Temel birime dönüşüm çarpanı; temel birimde 1');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::WEIGHT_CLASS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ağırlık birimi çevirileri (dile göre ad ve kısaltma)');

            $table->bigIncrements('weight_class_translation_id')->comment('Birim çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('weight_class_id')->comment('Bağlı olduğu ağırlık birimi (def_loc_weight_class)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name', 100)->comment('Birim adı (örn. Kilogram)');
            $table->string('abbreviation', 20)->nullable()->comment('Kısaltma (örn. kg)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['weight_class_id', 'language_code'], 'uq_weight_class_translation_lang');
        });

        Schema::create(self::UNIT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Satış birimleri: adet, kg, lt vb. (lokalizasyon tanım tablosu)');

            $table->bigIncrements('unit_id')->comment('Satış birimi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Birim kodu (örn. adet, kg) — benzersiz');
            $table->decimal('value', 15, 8)->default(1)->comment('Temel birime dönüşüm çarpanı; temel birimde 1');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::UNIT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Satış birimi çevirileri (dile göre ad ve kısaltma)');

            $table->bigIncrements('unit_translation_id')->comment('Birim çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('unit_id')->comment('Bağlı olduğu satış birimi (def_loc_unit)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name', 100)->comment('Birim adı (örn. Adet)');
            $table->string('abbreviation', 20)->nullable()->comment('Kısaltma (örn. ad)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['unit_id', 'language_code'], 'uq_unit_translation_lang');
        });

        Schema::create(self::TAX_CLASS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Vergi sınıfları: KDV oran grupları (lokalizasyon tanım tablosu)');

            $table->bigIncrements('tax_class_id')->comment('Vergi sınıfı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('geo_id')->nullable()->comment('Geçerli olduğu coğrafi bölge (def_loc_geo); null ise her yerde geçerli');
            $table->string('code', 64)->unique()->comment('Vergi sınıfı kodu — benzersiz');
            $table->string('type', 50)->nullable()->comment('Vergi türü (örn. KDV, ÖTV)');
            $table->decimal('rate', 8, 2)->default(0)->comment('Vergi oranı (%)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
            $table->index('geo_id');
        });

        Schema::create(self::TAX_CLASS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Vergi sınıfı çevirileri (dile göre ad, kısaltma ve açıklama)');

            $table->bigIncrements('tax_class_translation_id')->comment('Vergi sınıfı çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('tax_class_id')->comment('Bağlı olduğu vergi sınıfı (def_loc_tax_class)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name', 150)->comment('Vergi sınıfı adı (örn. KDV %20)');
            $table->string('abbreviation', 20)->nullable()->comment('Kısaltma (örn. KDV)');
            $table->string('description')->nullable()->comment('Vergi sınıfı açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['tax_class_id', 'language_code'], 'uq_tax_class_translation_lang');
        });

        Schema::create(self::TAX_CLASS_ACCOUNT_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Vergi sınıfı-üye türü eşleştirmeleri (vergi sınıfının geçerli olduğu üye türleri)');

            $table->bigIncrements('tax_class_account_type_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('tax_class_id')->comment('Bağlı olduğu vergi sınıfı (def_loc_tax_class)');
            $table->unsignedBigInteger('account_type_id')->comment('Üye türü kimliği (def_mbr_account_type tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['tax_class_id', 'account_type_id'], 'uq_tax_class_account_type');
            $table->index('account_type_id');
        });

        Schema::create(self::TAX_RATE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Vergi oranları: bölgeye bağlı oran/tutar kuralları (lokalizasyon tanım tablosu)');

            $table->bigIncrements('tax_rate_id')->comment('Vergi oranı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('geo_id')->nullable()->comment('Geçerli olduğu coğrafi bölge (def_loc_geo); null ise her yerde geçerli');
            $table->string('name', 150)->comment('Oran adı (örn. KDV %20 Yurt İçi)');
            $table->enum('type', ['percentage', 'fixed'])->default('percentage')->comment('Hesaplama türü: percentage=yüzde, fixed=sabit tutar');
            $table->decimal('rate', 15, 4)->default(0)->comment('Oran (%) veya sabit tutar (type alanına göre)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
            $table->index('geo_id');
        });

        Schema::create(self::TAX_RATE_ACCOUNT_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Vergi oranı-üye grubu eşleştirmeleri (oranın geçerli olduğu üye grupları)');

            $table->bigIncrements('tax_rate_account_group_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('tax_rate_id')->comment('Bağlı olduğu vergi oranı (def_loc_tax_rate)');
            $table->unsignedBigInteger('account_group_id')->comment('Üye grubu kimliği (def_mbr_account_group tablosuna referans)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['tax_rate_id', 'account_group_id'], 'uq_tax_rate_account_group');
            $table->index('account_group_id');
        });

        Schema::create(self::ADDRESS_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Adres türleri: fatura, sevkiyat vb. (lokalizasyon tanım tablosu)');

            $table->bigIncrements('address_type_id')->comment('Adres türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Adres türü kodu — benzersiz');
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

        Schema::create(self::ADDRESS_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Adres türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('address_type_translation_id')->comment('Adres türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('address_type_id')->comment('Bağlı olduğu adres türü (def_loc_address_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name', 100)->comment('Adres türü adı (örn. Fatura Adresi)');
            $table->string('description')->nullable()->comment('Adres türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['address_type_id', 'language_code'], 'uq_address_type_translation_lang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::ADDRESS_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ADDRESS_TYPE_TABLE);
        Schema::dropIfExists(self::TAX_RATE_ACCOUNT_GROUP_TABLE);
        Schema::dropIfExists(self::TAX_RATE_TABLE);
        Schema::dropIfExists(self::TAX_CLASS_ACCOUNT_TYPE_TABLE);
        Schema::dropIfExists(self::TAX_CLASS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::TAX_CLASS_TABLE);
        Schema::dropIfExists(self::UNIT_TRANSLATION_TABLE);
        Schema::dropIfExists(self::UNIT_TABLE);
        Schema::dropIfExists(self::WEIGHT_CLASS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::WEIGHT_CLASS_TABLE);
        Schema::dropIfExists(self::LENGTH_CLASS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::LENGTH_CLASS_TABLE);
        Schema::dropIfExists(self::GEO_ZONE_TABLE);
        Schema::dropIfExists(self::GEO_TABLE);
        Schema::dropIfExists(self::DISTRICT_TABLE);
        Schema::dropIfExists(self::CITY_TABLE);
        Schema::dropIfExists(self::COUNTRY_TABLE);
        Schema::dropIfExists(self::CURRENCY_TABLE);
        Schema::dropIfExists(self::LANGUAGE_TABLE);
    }
};
