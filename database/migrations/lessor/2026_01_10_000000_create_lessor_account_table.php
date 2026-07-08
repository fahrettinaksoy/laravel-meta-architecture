<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_lsr';

    private const ACCOUNT_TABLE = 'wbst_account';
    private const ACCOUNT_ACCESS_TABLE = 'wbst_account_access';
    private const ACCOUNT_ACCESS_TRANSLATION_TABLE = 'wbst_account_access_translation';
    private const ACCOUNT_AUTHORIZED_TABLE = 'wbst_account_authorized';
    private const ACCOUNT_AUTHORIZED_TOKEN_RESET_TABLE = 'wbst_account_authorized_token_reset';
    private const ACCOUNT_TOKEN_ACCESS_TABLE = 'wbst_account_token_access';
    private const ACCOUNT_SMS_VERIFY_TABLE = 'wbst_account_sms_verify';
    private const ACCOUNT_CONTACT_TABLE = 'wbst_account_contact';
    private const ACCOUNT_BANK_ACCOUNT_TABLE = 'wbst_account_bank_account';

    public function up(): void
    {
        Schema::create(self::ACCOUNT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kiracı ana tablosu: platform müşterisi hesap kartları');

            $table->bigIncrements('account_id')->comment('Kiracı hesabı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Hesap türü kimliği (tanım tablosuna referans)');
            $table->unsignedBigInteger('group_id')->nullable()->comment('Hesap grubu kimliği (tanım tablosuna referans)');
            $table->string('code', 32)->unique()->comment('Kiracı hesap kodu — benzersiz');
            $table->enum('kind', ['individual', 'corporate'])->default('corporate')->comment('Hesap şekli: individual=bireysel, corporate=kurumsal; eski adı: shape');
            $table->char('language_code', 2)->default('tr')->comment('Tercih edilen dil kodu (ISO 639-1: tr, en)');
            $table->string('image', 500)->nullable()->comment('Logo/profil görseli dosya yolu');
            $table->string('name')->comment('Kiracı adı veya firma unvanı');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi adı');
            $table->string('tax_number', 11)->nullable()->comment('Vergi kimlik numarası (kurumsal 10, TC kimlik 11 hane)');
            $table->string('trade_chamber', 100)->nullable()->comment('Kayıtlı olduğu ticaret odası');
            $table->string('trade_number', 50)->nullable()->comment('Ticaret sicil numarası');
            $table->string('mersis_number', 16)->nullable()->comment('MERSİS numarası (16 hane)');
            $table->string('kep_address', 254)->nullable()->comment('KEP (kayıtlı elektronik posta) adresi');
            $table->json('components')->nullable()->comment('Kiracıya açık modül/bileşen listesi (JSON); eski adı: component');
            $table->boolean('has_ledger')->default(false)->comment('Kiracı için cari hesap (borç-alacak) takibi yapılıyor mu? Eski adı: safe');
            $table->boolean('newsletter')->default(false)->comment('E-bülten aboneliği var mı?');
            $table->unsignedBigInteger('default_authorized_id')->nullable()->comment('Varsayılan yetkili kişi (wbst_account_authorized)');
            $table->unsignedBigInteger('default_contact_id')->nullable()->comment('Varsayılan adres/iletişim kaydı (wbst_account_contact)');
            $table->unsignedBigInteger('default_bank_account_id')->nullable()->comment('Varsayılan banka hesabı (wbst_account_bank_account)');
            $table->string('ip_address', 45)->nullable()->comment('Kaydın oluşturulduğu IP adresi (IPv6 uyumlu)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('type_id');
            $table->index('group_id');
            $table->index('status');
        });

        Schema::create(self::ACCOUNT_ACCESS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kiracı site erişim yapılandırmaları: alan adı, barındırma, veritabanı ve depolama ayarları');

            $table->bigIncrements('account_access_id')->comment('Erişim kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Bağlı olduğu kiracı hesabı (wbst_account)');
            $table->unsignedBigInteger('sector_id')->nullable()->comment('Sektör kimliği (tanım tablosuna referans)');
            $table->string('code', 64)->unique()->comment('Erişim kaydı kodu — benzersiz');
            $table->boolean('is_debug')->default(false)->comment('Hata ayıklama modu açık mı?');
            $table->string('log_channel', 50)->nullable()->comment('Günlük kanalı (örn. stack, daily)');
            $table->string('log_deprecation', 50)->nullable()->comment('Kullanımdan kalkma uyarılarının günlük kanalı');
            $table->string('log_level', 20)->nullable()->comment('Günlük seviyesi (örn. debug, info, error)');
            $table->string('session_driver', 50)->nullable()->comment('Oturum sürücüsü (örn. file, redis, database)');
            $table->unsignedInteger('session_lifetime')->default(120)->comment('Oturum ömrü (dakika)');
            $table->string('broadcast_driver', 50)->nullable()->comment('Yayın (broadcast) sürücüsü');
            $table->string('cache_driver', 50)->nullable()->comment('Önbellek sürücüsü (örn. file, redis)');
            $table->string('queue_connection', 50)->nullable()->comment('Kuyruk bağlantısı (örn. sync, redis, database)');
            $table->string('domain_protocol', 10)->nullable()->comment('Alan adı protokolü (http, https)');
            $table->string('domain_address')->nullable()->comment('Alan adı adresi (örn. magaza.example)');
            $table->string('domain_extension', 10)->nullable()->comment('Alan adı uzantısı (örn. .com, .com.tr)');
            $table->unsignedBigInteger('domain_id')->nullable()->comment('Alan adı kaydının kimliği (alan adı tablosuna referans)');
            $table->string('domain_guid', 100)->nullable()->comment('Alan adı sağlayıcısındaki benzersiz kimlik (GUID)');
            $table->boolean('use_subdomain')->default(false)->comment('Alt alan adı kullanılıyor mu?');
            $table->string('subdomain_name', 100)->nullable()->comment('Alt alan adı (örn. magaza)');
            $table->boolean('host_shelter')->default(false)->comment('Barındırma iç altyapıda mı? true=kendi altyapımız, false=harici');
            $table->string('host_address')->nullable()->comment('Barındırma sunucu adresi');
            $table->string('host_port', 10)->nullable()->comment('Barındırma sunucu portu');
            $table->string('host_username', 100)->nullable()->comment('Barındırma kullanıcı adı');
            $table->string('host_password')->nullable()->comment('Barındırma parolası (şifrelenmiş saklanmalı)');
            $table->boolean('database_shelter')->default(false)->comment('Veritabanı iç altyapıda mı? true=kendi altyapımız, false=harici');
            $table->string('database_connection', 50)->nullable()->comment('Veritabanı bağlantı türü (örn. mysql)');
            $table->unsignedBigInteger('database_id')->nullable()->comment('Veritabanı kaydının kimliği (altyapı tablosuna referans)');
            $table->string('database_host')->nullable()->comment('Veritabanı sunucu adresi');
            $table->string('database_port', 10)->nullable()->comment('Veritabanı portu');
            $table->string('database_name', 100)->nullable()->comment('Veritabanı adı');
            $table->string('database_username', 100)->nullable()->comment('Veritabanı kullanıcı adı');
            $table->unsignedBigInteger('database_username_id')->nullable()->comment('Veritabanı kullanıcı kaydının kimliği (altyapı tablosuna referans)');
            $table->string('database_password')->nullable()->comment('Veritabanı parolası (şifrelenmiş saklanmalı)');
            $table->boolean('storage_shelter')->default(false)->comment('Depolama iç altyapıda mı? true=kendi altyapımız, false=harici');
            $table->string('storage_engine', 50)->nullable()->comment('Depolama motoru (örn. local, s3, ftp)');
            $table->unsignedBigInteger('storage_id')->nullable()->comment('Depolama kaydının kimliği (altyapı tablosuna referans)');
            $table->string('storage_host')->nullable()->comment('Depolama sunucu adresi');
            $table->string('storage_username', 100)->nullable()->comment('Depolama kullanıcı adı');
            $table->string('storage_password')->nullable()->comment('Depolama parolası (şifrelenmiş saklanmalı)');
            $table->string('storage_url', 500)->nullable()->comment('Depolama erişim URL adresi');
            $table->string('storage_root')->nullable()->comment('Depolama kök dizini');
            $table->string('storage_folder')->nullable()->comment('Depolama klasör adı');
            $table->string('storage_path')->nullable()->comment('Depolama yolu');
            $table->unsignedInteger('storage_timeout')->nullable()->comment('Depolama bağlantı zaman aşımı (saniye)');
            $table->char('language_default', 5)->default('tr')->comment('Varsayılan dil kodu (ISO 639-1)');
            $table->json('language_usable')->nullable()->comment('Kullanılabilir dil kodları (JSON dizi)');
            $table->char('currency_default', 3)->default('TRY')->comment('Varsayılan para birimi kodu (ISO 4217)');
            $table->json('currency_usable')->nullable()->comment('Kullanılabilir para birimi kodları (JSON dizi)');
            $table->unsignedBigInteger('theme_id')->nullable()->comment('Kullanılan tema kimliği (tema tablosuna referans)');
            $table->unsignedBigInteger('partner_id')->nullable()->comment('İş ortağı kimliği (ortak tablosuna referans)');
            $table->boolean('is_reference')->default(false)->comment('Referans site olarak gösterilsin mi?');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('domain_address');
            $table->index('status');

            $table->foreign('account_id')->references('account_id')->on(self::ACCOUNT_TABLE)->nullOnDelete();
        });

        Schema::create(self::ACCOUNT_ACCESS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Site erişim kaydı çevirileri (dile göre tanıtım ve meta bilgileri)');

            $table->bigIncrements('account_access_translation_id')->comment('Erişim çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_access_id')->comment('Bağlı olduğu erişim kaydı (wbst_account_access)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Site adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->text('description')->nullable()->comment('Site açıklaması (HTML içerebilir)');
            $table->string('tag')->nullable()->comment('Etiketler (virgülle ayrılmış)');
            $table->string('keyword')->nullable()->comment('Arama anahtar kelimeleri');
            $table->text('about')->nullable()->comment('Hakkında metni');
            $table->text('issue')->nullable()->comment('Sorun/ihtiyaç tanımı');
            $table->text('solution')->nullable()->comment('Sunulan çözüm açıklaması');
            $table->text('comment')->nullable()->comment('İç notlar/yorumlar');
            $table->string('meta_title')->nullable()->comment('SEO meta başlık');
            $table->string('meta_description')->nullable()->comment('SEO meta açıklama');
            $table->string('meta_keyword')->nullable()->comment('SEO meta anahtar kelimeler');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['account_access_id', 'language_code'], 'uq_account_access_translation_lang');

            $table->foreign('account_access_id')->references('account_access_id')->on(self::ACCOUNT_ACCESS_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ACCOUNT_AUTHORIZED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kiracı hesabı yetkilileri (yönetim paneline giriş yapan kullanıcılar)');

            $table->bigIncrements('account_authorized_id')->comment('Yetkili kişi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Bağlı olduğu kiracı hesabı (wbst_account)');
            $table->unsignedBigInteger('account_access_id')->nullable()->comment('Bağlı olduğu site erişim kaydı (wbst_account_access); null ise tüm sitelerde yetkili');
            $table->unsignedBigInteger('authorized_group_id')->nullable()->comment('Yetkili grubu kimliği (yetkili grubu tanım tablosuna referans)');
            $table->string('firstname', 100)->comment('Yetkili kişinin adı');
            $table->string('lastname', 100)->comment('Yetkili kişinin soyadı');
            $table->string('citizenship_number', 11)->nullable()->comment('TC kimlik numarası (11 hane)');
            $table->enum('gender', ['male', 'female'])->nullable()->comment('Cinsiyet: male=erkek, female=kadın; null ise belirtilmemiş');
            $table->date('birth_date')->nullable()->comment('Doğum tarihi');
            $table->string('title', 100)->nullable()->comment('Görev/unvan');
            $table->string('description')->nullable()->comment('Yetkili hakkında kısa açıklama');
            $table->string('phone_fixed_number', 20)->nullable()->comment('Sabit telefon numarası');
            $table->string('phone_mobile_number', 20)->nullable()->comment('Cep telefonu numarası');
            $table->string('email_address', 254)->unique()->comment('E-posta adresi — benzersiz, giriş kimliği');
            $table->timestamp('sms_verified_at')->nullable()->comment('SMS doğrulama tarihi; null ise doğrulanmamış');
            $table->timestamp('email_verified_at')->nullable()->comment('E-posta doğrulama tarihi; null ise doğrulanmamış');
            $table->string('email_verified_token', 100)->nullable()->comment('E-posta doğrulama belirteci');
            $table->string('password')->nullable()->comment('Parola özeti (bcrypt/argon2)');
            $table->rememberToken()->comment('Beni hatırla oturum belirteci');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');

            $table->foreign('account_id')->references('account_id')->on(self::ACCOUNT_TABLE)->nullOnDelete();
            $table->foreign('account_access_id')->references('account_access_id')->on(self::ACCOUNT_ACCESS_TABLE)->nullOnDelete();
        });

        Schema::create(self::ACCOUNT_AUTHORIZED_TOKEN_RESET_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Yetkili şifre/token sıfırlama istekleri (geçici token kayıtları)');

            $table->bigIncrements('account_authorized_token_reset_id')->comment('Sıfırlama isteği için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('email_address', 254)->index()->comment('Sıfırlama istenen e-posta adresi');
            $table->string('token')->comment('Sıfırlama belirteci (hash olarak saklanmalı)');
            $table->timestamp('expires_at')->nullable()->comment('Belirtecin geçerlilik bitiş tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');
        });

        Schema::create(self::ACCOUNT_TOKEN_ACCESS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kiracı API erişim belirteçleri (Sanctum uyumlu kişisel erişim tokenları)');

            $table->bigIncrements('account_token_access_id')->comment('Erişim belirteci için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('tokenable_type')->comment('Belirteç sahibinin model sınıfı (polimorfik ilişki)');
            $table->unsignedBigInteger('tokenable_id')->comment('Belirteç sahibinin kayıt kimliği (polimorfik ilişki)');
            $table->string('name', 100)->comment('Belirteç adı (hangi cihaz/uygulama için üretildiği)');
            $table->string('token', 64)->unique()->comment('Belirteç özeti (SHA-256 hash) — benzersiz');
            $table->text('abilities')->nullable()->comment('Belirtecin yetki kapsamları (JSON dizi)');
            $table->timestamp('last_used_at')->nullable()->comment('Belirtecin son kullanım tarihi');
            $table->timestamp('expires_at')->nullable()->comment('Belirtecin geçerlilik bitiş tarihi; null ise süresiz');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index(['tokenable_type', 'tokenable_id'], 'idx_token_access_tokenable');
        });

        Schema::create(self::ACCOUNT_SMS_VERIFY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Yetkili SMS doğrulama kodları');

            $table->bigIncrements('account_sms_verify_id')->comment('SMS doğrulama kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_authorized_id')->comment('Bağlı olduğu yetkili (wbst_account_authorized)');
            $table->string('code', 10)->comment('Doğrulama kodu');
            $table->timestamp('expires_at')->nullable()->comment('Kodun geçerlilik bitiş tarihi');
            $table->timestamp('verified_at')->nullable()->comment('Doğrulamanın tamamlandığı tarih; null ise beklemede');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('account_authorized_id')->references('account_authorized_id')->on(self::ACCOUNT_AUTHORIZED_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ACCOUNT_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kiracının adres/iletişim kartları (fatura ve yazışma adresleri)');

            $table->bigIncrements('account_contact_id')->comment('Adres kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_id')->comment('Bağlı olduğu kiracı hesabı (wbst_account)');
            $table->unsignedBigInteger('taxpayer_type_id')->nullable()->comment('Mükellef türü kimliği (tanım tablosuna referans)');
            $table->unsignedBigInteger('address_type_id')->nullable()->comment('Adres türü kimliği: fatura/yazışma (tanım tablosuna referans)');
            $table->boolean('is_abroad')->default(false)->comment('Yurt dışı adresi mi? Eski adı: abroad');
            $table->string('title', 100)->comment('Adres kartı başlığı (örn. Merkez Ofis)');
            $table->string('firstname', 100)->nullable()->comment('Adres sahibinin adı');
            $table->string('lastname', 100)->nullable()->comment('Adres sahibinin soyadı');
            $table->string('citizenship_number', 11)->nullable()->comment('TC kimlik numarası (11 hane)');
            $table->string('company')->nullable()->comment('Firma unvanı (kurumsal fatura adresi için)');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi adı');
            $table->string('tax_number', 11)->nullable()->comment('Vergi kimlik numarası');
            $table->string('email_address', 254)->nullable()->comment('Adrese ait e-posta adresi');
            $table->string('phone_number', 20)->nullable()->comment('Adrese ait telefon numarası; eski adı: telephone');
            $table->string('address_1')->comment('Adres satırı 1 (cadde, sokak, bina)');
            $table->string('address_2')->nullable()->comment('Adres satırı 2 (daire, kat vb. ek bilgi)');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği (lokalizasyon tanım tablosuna referans)');
            $table->unsignedBigInteger('city_id')->nullable()->comment('İl kimliği (lokalizasyon tanım tablosuna referans)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği (lokalizasyon tanım tablosuna referans)');
            $table->string('postcode', 10)->nullable()->comment('Posta kodu');
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

            $table->foreign('account_id')->references('account_id')->on(self::ACCOUNT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ACCOUNT_BANK_ACCOUNT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kiracının banka hesapları ve IBAN bilgileri');

            $table->bigIncrements('account_bank_account_id')->comment('Banka hesabı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_id')->comment('Bağlı olduğu kiracı hesabı (wbst_account)');
            $table->unsignedBigInteger('bank_id')->nullable()->comment('Banka kimliği (banka tanım tablosuna referans)');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi kodu (ISO 4217: TRY, USD, EUR)');
            $table->string('type', 50)->nullable()->comment('Hesap türü (örn. vadesiz, vadeli)');
            $table->string('name', 100)->comment('Hesap kartı başlığı (örn. İş Bankası TL Hesabı)');
            $table->string('owner', 150)->comment('Hesap sahibinin adı/unvanı');
            $table->string('account_number', 30)->nullable()->comment('Banka hesap numarası');
            $table->string('branch_code', 20)->nullable()->comment('Şube kodu');
            $table->string('iban', 34)->nullable()->comment('IBAN numarası (en fazla 34 karakter); eski adı: iban_number');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('bank_id');
            $table->index('iban');

            $table->foreign('account_id')->references('account_id')->on(self::ACCOUNT_TABLE)->cascadeOnDelete();
        });

        Schema::table(self::ACCOUNT_TABLE, function (Blueprint $table) {
            $table->foreign('default_authorized_id')->references('account_authorized_id')->on(self::ACCOUNT_AUTHORIZED_TABLE)->nullOnDelete();
            $table->foreign('default_contact_id')->references('account_contact_id')->on(self::ACCOUNT_CONTACT_TABLE)->nullOnDelete();
            $table->foreign('default_bank_account_id')->references('account_bank_account_id')->on(self::ACCOUNT_BANK_ACCOUNT_TABLE)->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(self::ACCOUNT_TABLE, function (Blueprint $table) {
            $table->dropForeign(['default_authorized_id']);
            $table->dropForeign(['default_contact_id']);
            $table->dropForeign(['default_bank_account_id']);
        });

        Schema::dropIfExists(self::ACCOUNT_BANK_ACCOUNT_TABLE);
        Schema::dropIfExists(self::ACCOUNT_CONTACT_TABLE);
        Schema::dropIfExists(self::ACCOUNT_SMS_VERIFY_TABLE);
        Schema::dropIfExists(self::ACCOUNT_TOKEN_ACCESS_TABLE);
        Schema::dropIfExists(self::ACCOUNT_AUTHORIZED_TOKEN_RESET_TABLE);
        Schema::dropIfExists(self::ACCOUNT_AUTHORIZED_TABLE);
        Schema::dropIfExists(self::ACCOUNT_ACCESS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ACCOUNT_ACCESS_TABLE);
        Schema::dropIfExists(self::ACCOUNT_TABLE);
    }
};
