<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const CASH_TABLE = 'acct_cash';
    private const CASH_BANK_TABLE = 'acct_cash_bank';
    private const CASH_ACTIVITY_TABLE = 'acct_cash_activity';

    private const LOAN_TABLE = 'acct_loan';

    private const CHEQUE_TABLE = 'acct_cheque';

    private const PROMISSORY_TABLE = 'acct_promissory';

    private const EMPLOYEE_TABLE = 'acct_employee';
    private const EMPLOYEE_SALARY_TABLE = 'acct_employee_salary';
    private const EMPLOYEE_CONTACT_TABLE = 'acct_employee_contact';
    private const EMPLOYEE_BANK_ACCOUNT_TABLE = 'acct_employee_bank_account';
    private const EMPLOYEE_DOCUMENT_TABLE = 'acct_employee_document';

    private const CART_TABLE = 'acct_cart';

    private const ORDER_TABLE = 'acct_order';
    private const ORDER_ITEM_TABLE = 'acct_order_item';
    private const ORDER_FINANCIAL_TABLE = 'acct_order_financial';
    private const ORDER_CONTACT_TABLE = 'acct_order_contact';
    private const ORDER_ACTIVITY_TABLE = 'acct_order_activity';
    private const ORDER_SHIPMENT_TABLE = 'acct_order_shipment';
    private const ORDER_SHIPMENT_ITEM_TABLE = 'acct_order_shipment_item';

    private const INVOICE_TABLE = 'acct_invoice';
    private const INVOICE_ITEM_TABLE = 'acct_invoice_item';
    private const INVOICE_FINANCIAL_TABLE = 'acct_invoice_financial';
    private const INVOICE_CONTACT_TABLE = 'acct_invoice_contact';

    private const REFUND_TABLE = 'acct_refund';

    private const RECURRING_TABLE = 'acct_recurring';

    private const DEMAND_TABLE = 'acct_demand';

    public function up(): void
    {
        Schema::create(self::CASH_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa/hesap kartları: nakit kasa ve banka hesaplarının ana tanımı');

            $table->bigIncrements('cash_id')->comment('Kasa için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_cash_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Kasa türü kimliği: nakit/banka (def_acct_cash_type tablosuna referans)');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_cash_code')->comment('Kasa kodu — benzersiz');
            $table->string('name')->comment('Kasa adı (örn. Merkez Nakit Kasa)');
            $table->string('description')->nullable()->comment('Kasa açıklaması');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Kasa para birimi (ISO 4217: TRY, USD, EUR)');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('type_id', 'ix_cash_type_id');
            $table->index('is_active', 'ix_cash_is_active');
        });

        Schema::create(self::CASH_BANK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasaya bağlı banka hesabı ve IBAN bilgileri');

            $table->bigIncrements('cash_bank_id')->comment('Kasa banka hesabı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_cash_bank_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('cash_id')->comment('Bağlı olduğu kasa (acct_cash)');
            $table->unsignedBigInteger('bank_id')->nullable()->comment('Banka kimliği (def_gen_bank tablosuna referans)');
            $table->string('type', 50)->nullable()->comment('Hesap türü (örn. vadesiz, vadeli)');
            $table->string('name', 100)->comment('Hesap kartı başlığı (örn. İş Bankası TL Hesabı)');
            $table->string('owner', 150)->comment('Hesap sahibinin adı/unvanı');
            $table->string('account_number', 30)->nullable()->comment('Banka hesap numarası');
            $table->string('branch_code', 20)->nullable()->comment('Şube kodu');
            $table->string('iban', 34)->nullable()->comment('IBAN numarası (en fazla 34 karakter)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('bank_id', 'ix_cash_bank_bank_id');
            $table->index('iban', 'ix_cash_bank_iban');

            $table->index('cash_id', 'ix_cash_bank_cash_id');
        });

        Schema::create(self::CASH_ACTIVITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa hareketleri: tahsilat, ödeme ve virman kayıtları');

            $table->bigIncrements('cash_activity_id')->comment('Kasa hareketi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_cash_activity_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('cash_id')->comment('Bağlı olduğu kasa (acct_cash)');
            $table->unsignedBigInteger('process_id')->nullable()->comment('Hareketi doğuran süreç kimliği (def_acct_cash_process tablosuna referans)');
            $table->unsignedBigInteger('module_group_id')->nullable()->comment('İlişkili modül grubu kimliği (polimorfik kaynak)');
            $table->unsignedBigInteger('module_type_id')->nullable()->comment('İlişkili modül türü kimliği (polimorfik kaynak)');
            $table->unsignedBigInteger('module_id')->nullable()->comment('İlişkili modül kayıt kimliği (polimorfik kaynak)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili cari hesap kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('branch_id')->nullable()->comment('Şube kimliği (def_com_branch tablosuna referans)');
            $table->decimal('amount', 19, 2)->default(0)->comment('Hareket tutarı: pozitif=giriş, negatif=çıkış');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Hareket/belge kodu');
            $table->string('name')->nullable()->comment('Hareket başlığı');
            $table->text('description')->nullable()->comment('Hareket açıklaması');
            $table->longText('content')->nullable()->comment('Hareket detay içeriği (JSON/serileştirilmiş veri)');
            $table->dateTime('date_activity')->nullable()->comment('Hareketin muhasebe tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('account_id', 'ix_cash_activity_account_id');
            $table->index('branch_id', 'ix_cash_activity_branch_id');
            $table->index(['module_group_id', 'module_type_id', 'module_id'], 'idx_cash_activity_module');

            $table->index('cash_id', 'ix_cash_activity_cash_id');
            $table->index('process_id', 'ix_cash_activity_process_id');
        });

        Schema::create(self::LOAN_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kredi/borç kayıtları: banka kredileri ve taksitli borç takibi');

            $table->bigIncrements('loan_id')->comment('Kredi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_loan_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Kredi türü kimliği (def_acct_loan_type tablosuna referans)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili cari hesap kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('module_group_id')->nullable()->comment('İlişkili modül grubu kimliği (polimorfik kaynak)');
            $table->unsignedBigInteger('module_type_id')->nullable()->comment('İlişkili modül türü kimliği (polimorfik kaynak)');
            $table->unsignedBigInteger('module_id')->nullable()->comment('İlişkili modül kayıt kimliği (polimorfik kaynak)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Kredi/sözleşme kodu');
            $table->string('name')->comment('Kredi adı (örn. Taşıt Kredisi)');
            $table->string('description')->nullable()->comment('Kredi açıklaması');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Kredi para birimi (ISO 4217: TRY, USD, EUR)');
            $table->decimal('amount', 19, 2)->default(0)->comment('Kredi/taksit tutarı');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');
            $table->dateTime('date_due')->nullable()->comment('Vade/son ödeme tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('type_id', 'ix_loan_type_id');
            $table->index('account_id', 'ix_loan_account_id');
            $table->index('is_active', 'ix_loan_is_active');
            $table->index('module_group_id', 'ix_loan_module_group_id');
            $table->index('module_type_id', 'ix_loan_module_type_id');
            $table->index('module_id', 'ix_loan_module_id');
        });

        Schema::create(self::CHEQUE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Çek kayıtları: alınan ve verilen çeklerin takibi');

            $table->bigIncrements('cheque_id')->comment('Çek için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_cheque_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Çek türü kimliği: alınan/verilen çek (def_acct_cheque_type tablosuna referans)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili cari hesap kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('bank_id')->nullable()->comment('Çekin ait olduğu banka kimliği (def_gen_bank tablosuna referans)');
            $table->string('bank_account_number', 30)->nullable()->comment('İlgili banka hesap numarası');
            $table->string('cheque_number', 50)->nullable()->comment('Çek numarası');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Çek takip kodu');
            $table->string('drawer', 150)->nullable()->comment('Keşideci: çeki düzenleyen kişi/firma');
            $table->string('name')->nullable()->comment('Çek başlığı');
            $table->string('description')->nullable()->comment('Çek açıklaması');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->decimal('amount', 19, 2)->default(0)->comment('Çek tutarı');
            $table->date('date_delivery')->nullable()->comment('Keşide/teslim tarihi');
            $table->date('date_expiry')->nullable()->comment('Vade tarihi');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('type_id', 'ix_cheque_type_id');
            $table->index('account_id', 'ix_cheque_account_id');
            $table->index('bank_id', 'ix_cheque_bank_id');
        });

        Schema::create(self::PROMISSORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Senet kayıtları: alınan (alacak) ve verilen (borç) senetlerin takibi');

            $table->bigIncrements('promissory_id')->comment('Senet için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_promissory_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Senet türü kimliği: alacak/borç senedi (tanım tablosuna referans)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili cari hesap kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->string('note_number', 50)->nullable()->comment('Senet/bono numarası');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Senet takip kodu');
            $table->string('drawer', 150)->nullable()->comment('Keşideci/borçlu: senedi düzenleyen kişi/firma');
            $table->string('payee', 150)->nullable()->comment('Lehtar: senedin ödeneceği kişi/firma');
            $table->string('name')->nullable()->comment('Senet başlığı');
            $table->string('description')->nullable()->comment('Senet açıklaması');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->decimal('amount', 19, 2)->default(0)->comment('Senet tutarı');
            $table->string('issue_place', 150)->nullable()->comment('Düzenleme yeri');
            $table->date('date_issue')->nullable()->comment('Düzenleme (tanzim) tarihi');
            $table->date('date_expiry')->nullable()->comment('Vade tarihi');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('type_id', 'ix_promissory_type_id');
            $table->index('account_id', 'ix_promissory_account_id');
        });

        Schema::create(self::EMPLOYEE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personel ana tablosu: çalışan özlük kayıtları');

            $table->bigIncrements('employee_id')->comment('Personel için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_employee_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('department_id')->nullable()->comment('Departman kimliği (def_com_department tablosuna referans)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Personel sicil kodu');
            $table->string('title', 100)->nullable()->comment('Görev/unvan (örn. Muhasebe Uzmanı)');
            $table->string('firstname', 100)->nullable()->comment('Personelin adı');
            $table->string('lastname', 100)->nullable()->comment('Personelin soyadı');
            $table->string('nationality', 100)->nullable()->comment('Uyruk');
            $table->string('citizenship_number', 11)->nullable()->comment('TC kimlik numarası (11 hane)');
            $table->string('health_insurance_number', 50)->nullable()->comment('SGK/sağlık sigorta numarası');
            $table->string('image', 500)->nullable()->comment('Profil görseli dosya yolu');
            $table->unsignedBigInteger('gender_id')->nullable()->comment('Cinsiyet kimliği (def_gen_gender tablosuna referans); null ise belirtilmemiş');
            $table->date('start_date')->nullable()->comment('İşe başlama tarihi');
            $table->date('end_date')->nullable()->comment('İşten ayrılma tarihi');
            $table->string('end_reason')->nullable()->comment('İşten ayrılma nedeni');
            $table->date('birth_date')->nullable()->comment('Doğum tarihi');
            $table->unsignedBigInteger('default_contact_id')->nullable()->comment('Varsayılan iletişim kaydı (acct_employee_contact)');
            $table->unsignedBigInteger('default_bank_account_id')->nullable()->comment('Varsayılan banka hesabı (acct_employee_bank_account)');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('department_id', 'ix_employee_department_id');
            $table->index('is_active', 'ix_employee_is_active');
            $table->index('gender_id', 'ix_employee_gender_id');
            $table->index('default_contact_id', 'ix_employee_default_contact_id');
            $table->index('default_bank_account_id', 'ix_employee_default_bank_account_id');
        });

        Schema::create(self::EMPLOYEE_SALARY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personel maaş tanımları ve geçerlilik dönemleri');

            $table->bigIncrements('employee_salary_id')->comment('Maaş kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_employee_salary_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('employee_id')->comment('Bağlı olduğu personel (acct_employee)');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Maaş para birimi (ISO 4217: TRY, USD, EUR)');
            $table->decimal('amount', 19, 2)->default(0)->comment('Maaş tutarı');
            $table->date('start_date')->nullable()->comment('Maaşın geçerli olduğu başlangıç tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('employee_id', 'ix_employee_salary_employee_id');
        });

        Schema::create(self::EMPLOYEE_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personelin adres/iletişim kartları');

            $table->bigIncrements('employee_contact_id')->comment('İletişim kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_employee_contact_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('employee_id')->comment('Bağlı olduğu personel (acct_employee)');
            $table->unsignedBigInteger('address_type_id')->nullable()->comment('Adres türü kimliği (def_loc_address_type tablosuna referans)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Adres kartı kodu');
            $table->string('email_address', 254)->nullable()->comment('E-posta adresi');
            $table->string('website')->nullable()->comment('Web sitesi adresi');
            $table->string('phone_fixed_number', 20)->nullable()->comment('Sabit telefon numarası');
            $table->string('phone_mobile_number', 20)->nullable()->comment('Cep telefonu numarası');
            $table->string('address_1')->nullable()->comment('Adres satırı 1 (cadde, sokak, bina)');
            $table->string('address_2')->nullable()->comment('Adres satırı 2 (daire, kat vb. ek bilgi)');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği (def_loc_country tablosuna referans)');
            $table->unsignedBigInteger('city_id')->nullable()->comment('İl kimliği (def_loc_city tablosuna referans)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği (def_loc_district tablosuna referans)');
            $table->string('postcode', 10)->nullable()->comment('Posta kodu');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('country_id', 'ix_employee_contact_country_id');
            $table->index('city_id', 'ix_employee_contact_city_id');
            $table->index('district_id', 'ix_employee_contact_district_id');

            $table->index('employee_id', 'ix_employee_contact_employee_id');
            $table->index('address_type_id', 'ix_employee_contact_address_type_id');
        });

        Schema::create(self::EMPLOYEE_BANK_ACCOUNT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personelin banka hesapları ve IBAN bilgileri (maaş ödemeleri)');

            $table->bigIncrements('employee_bank_account_id')->comment('Banka hesabı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_employee_bank_account_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('employee_id')->comment('Bağlı olduğu personel (acct_employee)');
            $table->unsignedBigInteger('bank_id')->nullable()->comment('Banka kimliği (def_gen_bank tablosuna referans)');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Para birimi kodu (ISO 4217: TRY, USD, EUR)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Hesap kartı kodu');
            $table->string('type', 50)->nullable()->comment('Hesap türü (örn. vadesiz, vadeli)');
            $table->string('name', 100)->comment('Hesap kartı başlığı');
            $table->string('owner', 150)->comment('Hesap sahibinin adı/unvanı');
            $table->string('account_number', 30)->nullable()->comment('Banka hesap numarası');
            $table->string('branch_code', 20)->nullable()->comment('Şube kodu');
            $table->string('iban', 34)->nullable()->comment('IBAN numarası (en fazla 34 karakter)');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('bank_id', 'ix_employee_bank_account_bank_id');
            $table->index('iban', 'ix_employee_bank_account_iban');

            $table->index('employee_id', 'ix_employee_bank_account_employee_id');
        });

        Schema::create(self::EMPLOYEE_DOCUMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personele ait belgeler (yasal ve özel evraklar)');

            $table->bigIncrements('employee_document_id')->comment('Belge kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_employee_document_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('employee_id')->comment('Bağlı olduğu personel (acct_employee)');
            $table->unsignedBigInteger('document_type_id')->nullable()->comment('Belge türü kimliği (def_gen_document_type tablosuna referans)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Belge kodu');
            $table->string('name')->comment('Belge adı (örn. İş Sözleşmesi)');
            $table->string('description')->nullable()->comment('Belge açıklaması');
            $table->string('file', 500)->nullable()->comment('Belge dosya yolu');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');
            $table->dateTime('date_validity')->nullable()->comment('Belge geçerlilik bitiş tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('employee_id', 'ix_employee_document_employee_id');
            $table->index('document_type_id', 'ix_employee_document_document_type_id');
        });

        Schema::create(self::CART_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sepet kalemleri: üyeye veya oturuma ait ürün sepeti');

            $table->bigIncrements('cart_id')->comment('Sepet kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_cart_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Sepet kalemi takip kodu');
            $table->string('session')->nullable()->comment('Oturum kimliği (misafir sepetleri için)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili üye hesabı kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->unsignedBigInteger('recurring_type_id')->nullable()->comment('Abonelik/tekrar türü kimliği (def_cat_recurring_type tablosuna referans)');
            $table->unsignedBigInteger('variant_stock_id')->nullable()->comment('Seçilen ürün varyant/stok kimliği (katalog tablosuna referans)');
            $table->decimal('quantity', 15, 4)->unsigned()->default(1)->comment('Sepetteki ürün miktarı');
            $table->longText('options')->nullable()->comment('Seçilen ürün seçenekleri (JSON)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('account_id', 'ix_cart_account_id');
            $table->index('product_id', 'ix_cart_product_id');
            $table->index('session', 'ix_cart_session');
            $table->index('recurring_type_id', 'ix_cart_recurring_type_id');
            $table->index('variant_stock_id', 'ix_cart_variant_stock_id');
        });

        Schema::create(self::ORDER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş ana tablosu: müşteri siparişlerinin başlık kayıtları');

            $table->bigIncrements('order_id')->comment('Sipariş için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_order_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_order_code')->comment('Sipariş numarası — benzersiz');
            $table->string('group_code', 64)->nullable()->comment('Sipariş grup kodu (parçalı siparişleri birleştirir)');
            $table->string('channel_code', 64)->nullable()->comment('Satış kanalı kodu (örn. web, pazaryeri)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili üye hesabı kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->string('account_name')->nullable()->comment('Sipariş anındaki müşteri adı/unvanı (anlık kopya)');
            $table->unsignedBigInteger('account_group_id')->nullable()->comment('Müşteri grup kimliği (def_mbr_account_group tablosuna referans)');
            $table->unsignedBigInteger('operation_id')->nullable()->comment('Sipariş operasyon kimliği (def_acct_order_operation tablosuna referans)');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Sipariş durum kimliği (def_acct_order_status tablosuna referans)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Sipariş dili (ISO 639-1: tr, en)');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Sipariş para birimi (ISO 4217: TRY, USD, EUR)');
            $table->string('payment_method', 100)->nullable()->comment('Ödeme yöntemi kodu');
            $table->unsignedBigInteger('payment_contact_id')->nullable()->comment('Fatura iletişim kaydı (acct_order_contact)');
            $table->string('payment_origin', 100)->nullable()->comment('Ödeme kaynağı/sağlayıcısı');
            $table->string('shipping_method', 100)->nullable()->comment('Kargo/teslimat yöntemi kodu');
            $table->unsignedBigInteger('shipping_contact_id')->nullable()->comment('Sevkiyat iletişim kaydı (acct_order_contact)');
            $table->string('coupon_code', 64)->nullable()->comment('Uygulanan kupon kodu');
            $table->string('voucher_code', 64)->nullable()->comment('Uygulanan hediye çeki kodu');
            $table->string('reward_point', 64)->nullable()->comment('Kullanılan/kazanılan ödül puanı');
            $table->longText('comment')->nullable()->comment('Sipariş notu/açıklaması');
            $table->unsignedBigInteger('recurring_id')->nullable()->comment('Kaynak abonelik kaydı (acct_recurring)');
            $table->string('user_agent')->nullable()->comment('Sipariş anındaki tarayıcı/istemci bilgisi');
            $table->string('accept_language', 100)->nullable()->comment('İstemcinin kabul ettiği diller (HTTP Accept-Language)');
            $table->string('ip_modified', 45)->nullable()->comment('Son güncelleme IP adresi (IPv6 uyumlu)');
            $table->string('ip_created', 45)->nullable()->comment('Oluşturulma IP adresi (IPv6 uyumlu)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('account_id', 'ix_order_account_id');
            $table->index('status_id', 'ix_order_status_id');
            $table->index('group_code', 'ix_order_group_code');
            $table->index('account_group_id', 'ix_order_account_group_id');
            $table->index('operation_id', 'ix_order_operation_id');
            $table->index('payment_contact_id', 'ix_order_payment_contact_id');
            $table->index('shipping_contact_id', 'ix_order_shipping_contact_id');
            $table->index('recurring_id', 'ix_order_recurring_id');
        });

        Schema::create(self::ORDER_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş kalemleri: siparişe ait ürün satırları');

            $table->bigIncrements('order_item_id')->comment('Sipariş kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_order_item_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->unsignedBigInteger('recurring_id')->nullable()->comment('İlişkili abonelik kaydı (acct_recurring)');
            $table->unsignedBigInteger('variant_stock_id')->nullable()->comment('Ürün varyant/stok kimliği (katalog tablosuna referans)');
            $table->string('product_code', 64)->nullable()->comment('Sipariş anındaki ürün kodu (anlık kopya)');
            $table->string('product_name')->comment('Sipariş anındaki ürün adı (anlık kopya)');
            $table->decimal('product_price', 19, 2)->unsigned()->default(0)->comment('Birim ürün fiyatı');
            $table->decimal('exchange_rate', 19, 4)->unsigned()->default(1)->comment('Sipariş anındaki döviz kuru');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Kalem para birimi (ISO 4217: TRY, USD, EUR)');
            $table->decimal('quantity', 15, 4)->unsigned()->default(1)->comment('Ürün miktarı');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('Birim kimliği: adet/kg/lt (def_loc_unit tablosuna referans)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('Vergi sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->string('discount_type', 20)->nullable()->comment('İndirim türü: percentage/amount');
            $table->decimal('discount_value', 19, 2)->unsigned()->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->string('excise_duty_type', 20)->nullable()->comment('ÖTV türü: percentage/amount');
            $table->decimal('excise_duty_rate', 8, 2)->unsigned()->default(0)->comment('ÖTV oranı/tutarı');
            $table->decimal('communications_tax_rate', 8, 2)->unsigned()->default(0)->comment('ÖİV (özel iletişim vergisi) oranı');
            $table->string('delivery_method', 100)->nullable()->comment('Teslim yöntemi kodu');
            $table->string('shipping_method', 100)->nullable()->comment('Kargo yöntemi kodu');
            $table->longText('options')->nullable()->comment('Seçilen ürün seçenekleri (JSON)');
            $table->text('variants')->nullable()->comment('Ürün varyant bilgileri (JSON)');
            $table->text('groupeds')->nullable()->comment('Gruplanan ürün bilgileri (JSON)');
            $table->text('bundles')->nullable()->comment('Paket/bundle ürün bilgileri (JSON)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_order_item_product_id');

            $table->index('order_id', 'ix_order_item_order_id');
            $table->index('recurring_id', 'ix_order_item_recurring_id');
            $table->index('variant_stock_id', 'ix_order_item_variant_stock_id');
            $table->index('unit_id', 'ix_order_item_unit_id');
            $table->index('tax_class_id', 'ix_order_item_tax_class_id');
        });

        Schema::create(self::ORDER_FINANCIAL_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş mali satırları: ara toplam, kargo, indirim, vergi kalemleri');

            $table->bigIncrements('order_financial_id')->comment('Mali satır için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_order_financial_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->string('title')->comment('Satır başlığı (örn. Ara Toplam, Kargo)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Satır kodu (örn. sub_total, shipping)');
            $table->string('type', 50)->nullable()->comment('Satır türü/sınıfı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('removable')->default(false)->comment('Satır kaldırılabilir mi?');
            $table->decimal('value', 19, 2)->default(0)->comment('Satır sayısal değeri/oranı');
            $table->decimal('amount', 19, 2)->default(0)->comment('Hesaplanan tutar');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('order_id', 'ix_order_financial_order_id');
        });

        Schema::create(self::ORDER_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş iletişim/adres kayıtları (fatura ve sevkiyat adresleri)');

            $table->bigIncrements('order_contact_id')->comment('Sipariş adresi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_order_contact_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->unsignedBigInteger('taxpayer_type_id')->nullable()->comment('Mükellef türü kimliği (tanım tablosuna referans)');
            $table->string('operation_type', 50)->nullable()->comment('Adres işlem türü: invoice/shipping');
            $table->unsignedBigInteger('address_type_id')->nullable()->comment('Adres türü kimliği (def_loc_address_type tablosuna referans)');
            $table->string('title', 100)->nullable()->comment('Adres kartı başlığı');
            $table->string('firstname', 100)->nullable()->comment('Ad');
            $table->string('lastname', 100)->nullable()->comment('Soyad');
            $table->string('citizenship_number', 11)->nullable()->comment('TC kimlik numarası (11 hane)');
            $table->string('company')->nullable()->comment('Firma unvanı (kurumsal fatura adresi için)');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi adı');
            $table->string('tax_number', 11)->nullable()->comment('Vergi kimlik numarası');
            $table->string('email', 254)->nullable()->comment('E-posta adresi');
            $table->string('telephone', 20)->nullable()->comment('Telefon numarası');
            $table->string('address_1')->nullable()->comment('Adres satırı 1 (cadde, sokak, bina)');
            $table->string('address_2')->nullable()->comment('Adres satırı 2 (daire, kat vb. ek bilgi)');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği (def_loc_country tablosuna referans)');
            $table->unsignedBigInteger('city_id')->nullable()->comment('İl kimliği (def_loc_city tablosuna referans)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği (def_loc_district tablosuna referans)');
            $table->string('postcode', 10)->nullable()->comment('Posta kodu');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('country_id', 'ix_order_contact_country_id');
            $table->index('city_id', 'ix_order_contact_city_id');
            $table->index('district_id', 'ix_order_contact_district_id');

            $table->index('order_id', 'ix_order_contact_order_id');
            $table->index('taxpayer_type_id', 'ix_order_contact_taxpayer_type_id');
            $table->index('address_type_id', 'ix_order_contact_address_type_id');
        });

        Schema::create(self::ORDER_ACTIVITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş hareket geçmişi: durum değişiklikleri ve işlem günlüğü');

            $table->bigIncrements('order_activity_id')->comment('Sipariş hareketi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_order_activity_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->unsignedBigInteger('operation_id')->nullable()->comment('Operasyon kimliği (def_acct_order_operation tablosuna referans)');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Yeni durum kimliği (def_acct_order_status tablosuna referans)');
            $table->boolean('notification')->default(false)->comment('Müşteriye bildirim gönderildi mi?');
            $table->string('comment')->nullable()->comment('Hareket notu');
            $table->longText('payment_operation')->nullable()->comment('Ödeme işlem detayları (JSON)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('status_id', 'ix_order_activity_status_id');

            $table->index('order_id', 'ix_order_activity_order_id');
            $table->index('operation_id', 'ix_order_activity_operation_id');
        });

        Schema::create(self::ORDER_SHIPMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş sevkiyatları: her sevk için kargo/takip bilgisi');

            $table->bigIncrements('order_shipment_id')->comment('Sevkiyat için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_order_shipment_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Sevkiyat takip kodu');
            $table->unsignedBigInteger('cargo_id')->nullable()->comment('Kargo firması kimliği (def_gen_cargo tablosuna referans)');
            $table->string('tracking_code', 100)->nullable()->comment('Kargo takip numarası');
            $table->dateTime('date_shipment')->nullable()->comment('Sevkiyat tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('cargo_id', 'ix_order_shipment_cargo_id');

            $table->index('order_id', 'ix_order_shipment_order_id');
        });

        Schema::create(self::ORDER_SHIPMENT_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sevkiyat kalemleri: bir sevkiyatta gönderilen sipariş satırları');

            $table->bigIncrements('order_shipment_item_id')->comment('Sevkiyat kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_order_shipment_item_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->unsignedBigInteger('order_shipment_id')->comment('Bağlı olduğu sevkiyat (acct_order_shipment)');
            $table->unsignedBigInteger('order_item_id')->comment('Gönderilen sipariş kalemi (acct_order_item)');
            $table->decimal('quantity', 15, 4)->unsigned()->default(1)->comment('Bu sevkiyatta gönderilen miktar');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('order_id', 'ix_order_shipment_item_order_id');
            $table->index('order_shipment_id', 'ix_order_shipment_item_order_shipment_id');
            $table->index('order_item_id', 'ix_order_shipment_item_order_item_id');
        });

        Schema::create(self::INVOICE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura ana tablosu: satış/alış faturalarının başlık kayıtları');

            $table->bigIncrements('invoice_id')->comment('Fatura için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_invoice_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Fatura türü kimliği: satış/alış/iade (def_acct_invoice_type tablosuna referans)');
            $table->string('group_code', 64)->nullable()->comment('Fatura grup kodu');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Fatura para birimi (ISO 4217: TRY, USD, EUR)');
            $table->string('language_code', 5)->collation('ascii_general_ci')->default('tr')->comment('Fatura dili (ISO 639-1: tr, en)');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_invoice_code')->comment('Fatura numarası — benzersiz');
            $table->string('title')->nullable()->comment('Fatura başlığı');
            $table->unsignedBigInteger('category_id')->nullable()->comment('Fatura kategorisi kimliği (def_acct_invoice_category tablosuna referans)');
            $table->string('tag')->nullable()->comment('Fatura etiketleri (virgülle ayrılmış)');
            $table->longText('description')->nullable()->comment('Fatura açıklaması');
            $table->string('entry_date_time')->nullable()->comment('e-Fatura/e-Arşiv düzenleme tarih-saati');
            $table->string('entry_serial', 50)->nullable()->comment('Fatura seri kodu');
            $table->string('entry_queue', 50)->nullable()->comment('Fatura sıra numarası');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili cari hesap kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->string('account_name')->nullable()->comment('Fatura anındaki cari adı/unvanı (anlık kopya)');
            $table->unsignedBigInteger('account_group_id')->nullable()->comment('Cari grup kimliği (def_mbr_account_group tablosuna referans)');
            $table->unsignedBigInteger('account_contact_id')->nullable()->comment('Cari iletişim kaydı kimliği');
            $table->boolean('account_abroad')->default(false)->comment('Yurt dışı cari mi?');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi adı');
            $table->string('tax_number', 11)->nullable()->comment('Vergi kimlik numarası');
            $table->datetime('payment_due_date')->nullable()->comment('Fatura ödeme vade tarihi');
            $table->boolean('waybill_status')->default(false)->comment('İrsaliye düzenlendi mi?');
            $table->datetime('waybill_date')->nullable()->comment('İrsaliye tarihi');
            $table->string('waybill_number', 50)->nullable()->comment('İrsaliye numarası');
            $table->unsignedBigInteger('order_id')->nullable()->comment('Kaynak sipariş kimliği (acct_order)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('type_id', 'ix_invoice_type_id');
            $table->index('account_id', 'ix_invoice_account_id');
            $table->index('order_id', 'ix_invoice_order_id');

            $table->index('category_id', 'ix_invoice_category_id');
            $table->index('account_group_id', 'ix_invoice_account_group_id');
            $table->index('account_contact_id', 'ix_invoice_account_contact_id');
        });

        Schema::create(self::INVOICE_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura kalemleri: faturaya ait ürün/hizmet satırları');

            $table->bigIncrements('invoice_item_id')->comment('Fatura kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_invoice_item_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('invoice_id')->comment('Bağlı olduğu fatura (acct_invoice)');
            $table->unsignedBigInteger('product_id')->nullable()->comment('Ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->string('product_name')->comment('Fatura anındaki ürün/hizmet adı (anlık kopya)');
            $table->decimal('product_price', 19, 2)->unsigned()->default(0)->comment('Birim fiyat');
            $table->decimal('exchange_rate', 19, 4)->unsigned()->default(1)->comment('Fatura anındaki döviz kuru');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Kalem para birimi (ISO 4217: TRY, USD, EUR)');
            $table->decimal('quantity', 15, 4)->unsigned()->default(1)->comment('Miktar');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('Birim kimliği: adet/kg/lt (def_loc_unit tablosuna referans)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('Vergi sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->string('discount_type', 20)->nullable()->comment('İndirim türü: percentage/amount');
            $table->decimal('discount_value', 19, 2)->unsigned()->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->string('excise_duty_type', 20)->nullable()->comment('ÖTV türü: percentage/amount');
            $table->decimal('excise_duty_rate', 8, 2)->unsigned()->default(0)->comment('ÖTV oranı/tutarı');
            $table->decimal('communications_tax_rate', 8, 2)->unsigned()->default(0)->comment('ÖİV (özel iletişim vergisi) oranı');
            $table->string('delivery_method', 100)->nullable()->comment('Teslim yöntemi kodu');
            $table->string('shipping_method', 100)->nullable()->comment('Kargo yöntemi kodu');
            $table->string('variant')->nullable()->comment('Ürün varyant bilgisi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('product_id', 'ix_invoice_item_product_id');

            $table->index('invoice_id', 'ix_invoice_item_invoice_id');
            $table->index('unit_id', 'ix_invoice_item_unit_id');
            $table->index('tax_class_id', 'ix_invoice_item_tax_class_id');
        });

        Schema::create(self::INVOICE_FINANCIAL_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura mali satırları: ara toplam, indirim, vergi ve genel toplam kalemleri');

            $table->bigIncrements('invoice_financial_id')->comment('Mali satır için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_invoice_financial_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('invoice_id')->comment('Bağlı olduğu fatura (acct_invoice)');
            $table->string('title')->comment('Satır başlığı (örn. KDV %20, Genel Toplam)');
            $table->string('code', 64)->collation('ascii_general_ci')->nullable()->comment('Satır kodu (örn. tax, total)');
            $table->string('type', 50)->nullable()->comment('Satır türü/sınıfı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->decimal('amount', 19, 2)->default(0)->comment('Hesaplanan tutar');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('invoice_id', 'ix_invoice_financial_invoice_id');
        });

        Schema::create(self::INVOICE_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura iletişim/adres kayıtları (fatura ve sevkiyat adresleri)');

            $table->bigIncrements('invoice_contact_id')->comment('Fatura adresi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_invoice_contact_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('invoice_id')->comment('Bağlı olduğu fatura (acct_invoice)');
            $table->string('operation_type', 50)->nullable()->comment('Adres işlem türü: invoice/shipping');
            $table->unsignedBigInteger('address_type_id')->nullable()->comment('Adres türü kimliği (def_loc_address_type tablosuna referans)');
            $table->string('title', 100)->nullable()->comment('Adres kartı başlığı');
            $table->string('firstname', 100)->nullable()->comment('Ad');
            $table->string('lastname', 100)->nullable()->comment('Soyad');
            $table->string('company')->nullable()->comment('Firma unvanı (kurumsal fatura adresi için)');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi adı');
            $table->string('tax_number', 11)->nullable()->comment('Vergi kimlik numarası');
            $table->string('email', 254)->nullable()->comment('E-posta adresi');
            $table->string('telephone', 20)->nullable()->comment('Telefon numarası');
            $table->string('address_1')->nullable()->comment('Adres satırı 1 (cadde, sokak, bina)');
            $table->string('address_2')->nullable()->comment('Adres satırı 2 (daire, kat vb. ek bilgi)');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği (def_loc_country tablosuna referans)');
            $table->unsignedBigInteger('city_id')->nullable()->comment('İl kimliği (def_loc_city tablosuna referans)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği (def_loc_district tablosuna referans)');
            $table->string('postcode', 10)->nullable()->comment('Posta kodu');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('country_id', 'ix_invoice_contact_country_id');
            $table->index('city_id', 'ix_invoice_contact_city_id');
            $table->index('district_id', 'ix_invoice_contact_district_id');

            $table->index('invoice_id', 'ix_invoice_contact_invoice_id');
            $table->index('address_type_id', 'ix_invoice_contact_address_type_id');
        });

        Schema::create(self::REFUND_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İade talepleri: sipariş kalemleri için iade/değişim kayıtları');

            $table->bigIncrements('refund_id')->comment('İade için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_refund_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_refund_code')->comment('İade takip kodu — benzersiz');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili üye hesabı kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('order_id')->nullable()->comment('İade edilen sipariş kimliği (acct_order)');
            $table->unsignedBigInteger('product_id')->nullable()->comment('İade edilen ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->unsignedBigInteger('status_id')->nullable()->comment('İade durum kimliği (def_acct_refund_status tablosuna referans)');
            $table->unsignedBigInteger('reason_id')->nullable()->comment('İade neden kimliği (def_acct_refund_reason tablosuna referans)');
            $table->unsignedBigInteger('action_id')->nullable()->comment('İade işlem türü kimliği: iade/değişim (def_acct_refund_action tablosuna referans)');
            $table->boolean('opened')->default(false)->comment('İade talebi açık mı?');
            $table->string('product_name')->nullable()->comment('İade anındaki ürün adı (anlık kopya)');
            $table->decimal('product_price', 19, 2)->unsigned()->default(0)->comment('Birim ürün fiyatı');
            $table->decimal('exchange_rate', 19, 4)->unsigned()->default(1)->comment('İade anındaki döviz kuru');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->decimal('quantity', 15, 4)->unsigned()->default(1)->comment('İade edilen miktar');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('Birim kimliği (def_loc_unit tablosuna referans)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('Vergi sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->string('discount_type', 20)->nullable()->comment('İndirim türü: percentage/amount');
            $table->decimal('discount_value', 19, 2)->unsigned()->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->longText('comment')->nullable()->comment('İade açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('account_id', 'ix_refund_account_id');
            $table->index('order_id', 'ix_refund_order_id');
            $table->index('product_id', 'ix_refund_product_id');
            $table->index('status_id', 'ix_refund_status_id');
            $table->index('reason_id', 'ix_refund_reason_id');
            $table->index('action_id', 'ix_refund_action_id');
            $table->index('unit_id', 'ix_refund_unit_id');
            $table->index('tax_class_id', 'ix_refund_tax_class_id');
        });

        Schema::create(self::RECURRING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Abonelik/tekrarlı sipariş tanımları');

            $table->bigIncrements('recurring_id')->comment('Abonelik için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_recurring_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_recurring_code')->comment('Abonelik takip kodu — benzersiz');
            $table->unsignedBigInteger('recurring_type_id')->nullable()->comment('Abonelik periyot türü kimliği: haftalık/aylık (def_cat_recurring_type tablosuna referans)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili üye hesabı kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('product_id')->comment('Abone olunan ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->decimal('quantity', 15, 4)->unsigned()->default(1)->comment('Her periyottaki ürün miktarı');
            $table->decimal('product_price', 19, 2)->unsigned()->default(0)->comment('Birim ürün fiyatı');
            $table->char('currency_code', 3)->collation('ascii_general_ci')->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('Vergi sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->datetime('date_start')->nullable()->comment('Abonelik başlangıç tarihi');
            $table->datetime('date_end')->nullable()->comment('Abonelik bitiş tarihi; null ise süresiz');
            $table->string('user_agent')->nullable()->comment('Oluşturma anındaki tarayıcı/istemci bilgisi');
            $table->string('accept_language', 100)->nullable()->comment('İstemcinin kabul ettiği diller (HTTP Accept-Language)');
            $table->string('ip_created', 45)->nullable()->comment('Oluşturulma IP adresi (IPv6 uyumlu)');
            $table->boolean('is_active')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('account_id', 'ix_recurring_account_id');
            $table->index('product_id', 'ix_recurring_product_id');
            $table->index('is_active', 'ix_recurring_is_active');
            $table->index('recurring_type_id', 'ix_recurring_recurring_type_id');
            $table->index('tax_class_id', 'ix_recurring_tax_class_id');
        });

        Schema::create(self::DEMAND_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün talep/teklif kayıtları (fiyat sorma ve numune talepleri)');

            $table->bigIncrements('demand_id')->comment('Talep için otomatik artan birincil anahtar');
            $table->uuid('uuid')->collation('ascii_general_ci')->unique('uq_demand_uuid')->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->collation('ascii_general_ci')->unique('uq_demand_code')->comment('Talep takip kodu — benzersiz');
            $table->unsignedBigInteger('product_id')->nullable()->comment('Talep edilen ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili üye hesabı kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->string('firstname', 100)->nullable()->comment('Talep edenin adı');
            $table->string('lastname', 100)->nullable()->comment('Talep edenin soyadı');
            $table->string('company')->nullable()->comment('Firma unvanı');
            $table->string('email', 254)->nullable()->comment('İletişim e-posta adresi');
            $table->string('telephone', 20)->nullable()->comment('İletişim telefon numarası');
            $table->longText('comment')->nullable()->comment('Talep açıklaması');
            $table->string('user_agent')->nullable()->comment('Oluşturma anındaki tarayıcı/istemci bilgisi');
            $table->string('accept_language', 100)->nullable()->comment('İstemcinin kabul ettiği diller (HTTP Accept-Language)');
            $table->string('ip_created', 45)->nullable()->comment('Oluşturulma IP adresi (IPv6 uyumlu)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');

            $table->index('account_id', 'ix_demand_account_id');
            $table->index('product_id', 'ix_demand_product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::DEMAND_TABLE);
        Schema::dropIfExists(self::RECURRING_TABLE);
        Schema::dropIfExists(self::REFUND_TABLE);
        Schema::dropIfExists(self::INVOICE_CONTACT_TABLE);
        Schema::dropIfExists(self::INVOICE_FINANCIAL_TABLE);
        Schema::dropIfExists(self::INVOICE_ITEM_TABLE);
        Schema::dropIfExists(self::INVOICE_TABLE);
        Schema::dropIfExists(self::ORDER_SHIPMENT_ITEM_TABLE);
        Schema::dropIfExists(self::ORDER_SHIPMENT_TABLE);
        Schema::dropIfExists(self::ORDER_ACTIVITY_TABLE);
        Schema::dropIfExists(self::ORDER_CONTACT_TABLE);
        Schema::dropIfExists(self::ORDER_FINANCIAL_TABLE);
        Schema::dropIfExists(self::ORDER_ITEM_TABLE);
        Schema::dropIfExists(self::ORDER_TABLE);
        Schema::dropIfExists(self::CART_TABLE);
        Schema::dropIfExists(self::EMPLOYEE_DOCUMENT_TABLE);
        Schema::dropIfExists(self::EMPLOYEE_BANK_ACCOUNT_TABLE);
        Schema::dropIfExists(self::EMPLOYEE_CONTACT_TABLE);
        Schema::dropIfExists(self::EMPLOYEE_SALARY_TABLE);
        Schema::dropIfExists(self::EMPLOYEE_TABLE);
        Schema::dropIfExists(self::PROMISSORY_TABLE);
        Schema::dropIfExists(self::CHEQUE_TABLE);
        Schema::dropIfExists(self::LOAN_TABLE);
        Schema::dropIfExists(self::CASH_ACTIVITY_TABLE);
        Schema::dropIfExists(self::CASH_BANK_TABLE);
        Schema::dropIfExists(self::CASH_TABLE);
    }
};
