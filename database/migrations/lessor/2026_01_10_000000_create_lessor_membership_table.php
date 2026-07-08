<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_lsr';

    private const ACCOUNT_TABLE = 'mbr_account';
    private const AUTHORIZED_TABLE = 'mbr_account_authorized';
    private const CONTACT_TABLE = 'mbr_account_contact';
    private const BANK_ACCOUNT_TABLE = 'mbr_account_bank_account';

    public function up(): void
    {
        Schema::create(self::ACCOUNT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üye ana tablosu: müşteri/cari kartları (bireysel ve kurumsal)');

            $table->bigIncrements('account_id')->comment('Hesap için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Hesap türü kimliği (def_mbr_account_type tablosuna referans; FK tanım modülünde eklenir)');
            $table->unsignedBigInteger('group_id')->nullable()->comment('Hesap grubu kimliği (def_mbr_account_group tablosuna referans; FK tanım modülünde eklenir)');
            $table->string('code', 32)->unique()->comment('Cari hesap kodu (örn. 120.01.0001) — benzersiz');
            $table->enum('kind', ['individual', 'corporate'])->default('corporate')->comment('Hesap şekli: individual=bireysel, corporate=kurumsal');
            $table->char('language_code', 2)->default('tr')->comment('Tercih edilen dil kodu (ISO 639-1: tr, en)');
            $table->string('image', 500)->nullable()->comment('Logo/profil görseli dosya yolu');
            $table->string('name')->comment('Üye adı veya firma unvanı');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi adı');
            $table->string('tax_number', 11)->nullable()->comment('Vergi kimlik numarası (kurumsal 10, TC kimlik 11 hane)');
            $table->string('trade_chamber', 100)->nullable()->comment('Kayıtlı olduğu ticaret odası');
            $table->string('trade_number', 50)->nullable()->comment('Ticaret sicil numarası');
            $table->string('mersis_number', 16)->nullable()->comment('MERSİS numarası (16 hane)');
            $table->string('kep_address', 254)->nullable()->comment('KEP (kayıtlı elektronik posta) adresi');
            $table->string('phone_number', 20)->nullable()->comment('Telefon numarası (E.164 formatı önerilir)');
            $table->timestamp('sms_verified_at')->nullable()->comment('SMS doğrulama tarihi; null ise doğrulanmamış');
            $table->string('email', 254)->unique()->comment('E-posta adresi — benzersiz, giriş kimliği');
            $table->timestamp('email_verified_at')->nullable()->comment('E-posta doğrulama tarihi; null ise doğrulanmamış');
            $table->string('password')->nullable()->comment('Parola özeti (bcrypt/argon2); giriş yapmayan üyelerde null');
            $table->rememberToken()->comment('Beni hatırla oturum belirteci');
            $table->boolean('has_ledger')->default(false)->comment('Üye için cari hesap (borç-alacak) takibi yapılıyor mu? Açık hesap çalışmanın ön koşulu');
            $table->decimal('credit_limit', 15, 2)->default(0)->comment('Açık hesap risk limiti; 0 ise açık hesap satış yapılamaz');
            $table->unsignedSmallInteger('payment_term_days')->default(0)->comment('Ödeme vadesi (gün); 0 ise peşin çalışılır');
            $table->char('currency_code', 3)->default('TRY')->comment('Cari hesap ve kredi limitinin para birimi (ISO 4217: TRY, USD, EUR)');
            $table->boolean('newsletter')->default(false)->comment('E-bülten aboneliği var mı?');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');
            $table->unsignedBigInteger('default_authorized_id')->nullable()->comment('Varsayılan yetkili kişi (mbr_account_authorized)');
            $table->unsignedBigInteger('default_contact_id')->nullable()->comment('Varsayılan adres/iletişim kaydı (mbr_account_contact)');
            $table->unsignedBigInteger('default_bank_account_id')->nullable()->comment('Varsayılan banka hesabı (mbr_account_bank_account)');
            $table->string('ip_address', 45)->nullable()->comment('Kaydın oluşturulduğu IP adresi (IPv6 uyumlu)');

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

        Schema::create(self::AUTHORIZED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üyeye bağlı yetkili kişiler (kurumsal hesaplarda imza/iletişim yetkilileri)');

            $table->bigIncrements('account_authorized_id')->comment('Yetkili kişi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_id')->comment('Bağlı olduğu üye hesabı (mbr_account)');
            $table->string('firstname', 100)->comment('Yetkili kişinin adı');
            $table->string('lastname', 100)->comment('Yetkili kişinin soyadı');
            $table->string('citizenship_number', 11)->nullable()->comment('TC kimlik numarası (11 hane)');
            $table->enum('gender', ['male', 'female'])->nullable()->comment('Cinsiyet: male=erkek, female=kadın; null ise belirtilmemiş');
            $table->date('birth_date')->nullable()->comment('Doğum tarihi');
            $table->string('title', 100)->nullable()->comment('Görev/unvan (örn. Satın Alma Müdürü)');
            $table->string('description')->nullable()->comment('Yetkili hakkında kısa açıklama');
            $table->string('phone_fixed_number', 20)->nullable()->comment('Sabit telefon numarası');
            $table->string('phone_mobile_number', 20)->nullable()->comment('Cep telefonu numarası');
            $table->string('email_address', 254)->nullable()->unique()->comment('E-posta adresi — benzersiz; giriş kimliği olarak kullanılabilir');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('account_id')->references('account_id')->on(self::ACCOUNT_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üyenin adres/iletişim kartları (fatura ve sevkiyat adresleri)');

            $table->bigIncrements('account_contact_id')->comment('Adres kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_id')->comment('Bağlı olduğu üye hesabı (mbr_account)');
            $table->unsignedBigInteger('taxpayer_type_id')->nullable()->comment('Mükellef türü kimliği (tanım tablosuna referans)');
            $table->unsignedBigInteger('address_type_id')->nullable()->comment('Adres türü kimliği: fatura/sevkiyat (def_loc_address_type tablosuna referans)');
            $table->boolean('is_abroad')->default(false)->comment('Yurt dışı adresi mi?');
            $table->string('title', 100)->comment('Adres kartı başlığı (örn. Merkez Ofis, Ev Adresi)');
            $table->string('firstname', 100)->nullable()->comment('Adres sahibinin adı');
            $table->string('lastname', 100)->nullable()->comment('Adres sahibinin soyadı');
            $table->string('citizenship_number', 11)->nullable()->comment('TC kimlik numarası (11 hane)');
            $table->string('company')->nullable()->comment('Firma unvanı (kurumsal fatura adresi için)');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi adı');
            $table->string('tax_number', 11)->nullable()->comment('Vergi kimlik numarası');
            $table->string('email_address', 254)->nullable()->comment('Adrese ait e-posta adresi');
            $table->string('phone_number', 20)->nullable()->comment('Adrese ait telefon numarası');
            $table->string('address_1')->comment('Adres satırı 1 (cadde, sokak, bina)');
            $table->string('address_2')->nullable()->comment('Adres satırı 2 (daire, kat vb. ek bilgi)');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği (def_loc_country tablosuna referans)');
            $table->unsignedBigInteger('city_id')->nullable()->comment('İl kimliği (def_loc_city tablosuna referans)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği (def_loc_district tablosuna referans)');
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

        Schema::create(self::BANK_ACCOUNT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üyenin banka hesapları ve IBAN bilgileri');

            $table->bigIncrements('account_bank_account_id')->comment('Banka hesabı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_id')->comment('Bağlı olduğu üye hesabı (mbr_account)');
            $table->unsignedBigInteger('bank_id')->nullable()->comment('Banka kimliği (def_gen_bank tablosuna referans)');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi kodu (ISO 4217: TRY, USD, EUR)');
            $table->string('type', 50)->nullable()->comment('Hesap türü (örn. vadesiz, vadeli)');
            $table->string('name', 100)->comment('Hesap kartı başlığı (örn. İş Bankası TL Hesabı)');
            $table->string('owner', 150)->comment('Hesap sahibinin adı/unvanı');
            $table->string('account_number', 30)->nullable()->comment('Banka hesap numarası');
            $table->string('branch_code', 20)->nullable()->comment('Şube kodu');
            $table->string('iban', 34)->nullable()->comment('IBAN numarası (en fazla 34 karakter)');
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
            $table->foreign('default_authorized_id')->references('account_authorized_id')->on(self::AUTHORIZED_TABLE)->nullOnDelete();
            $table->foreign('default_contact_id')->references('account_contact_id')->on(self::CONTACT_TABLE)->nullOnDelete();
            $table->foreign('default_bank_account_id')->references('account_bank_account_id')->on(self::BANK_ACCOUNT_TABLE)->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(self::ACCOUNT_TABLE, function (Blueprint $table) {
            $table->dropForeign(['default_authorized_id']);
            $table->dropForeign(['default_contact_id']);
            $table->dropForeign(['default_bank_account_id']);
        });

        Schema::dropIfExists(self::BANK_ACCOUNT_TABLE);
        Schema::dropIfExists(self::CONTACT_TABLE);
        Schema::dropIfExists(self::AUTHORIZED_TABLE);
        Schema::dropIfExists(self::ACCOUNT_TABLE);
    }
};
