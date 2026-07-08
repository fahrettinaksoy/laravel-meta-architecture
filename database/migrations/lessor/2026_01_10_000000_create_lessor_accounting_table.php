<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_lsr';

    private const CASH_TABLE = 'acct_cash';
    private const CASH_BANK_TABLE = 'acct_cash_bank';
    private const CASH_ACTIVITY_TABLE = 'acct_cash_activity';
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
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Kasa türü kimliği: nakit/banka (def_acct_cash_type tablosuna referans)');
            $table->string('code', 64)->unique()->comment('Kasa kodu — benzersiz');
            $table->string('name')->comment('Kasa adı (örn. Merkez Nakit Kasa)');
            $table->string('description')->nullable()->comment('Kasa açıklaması');
            $table->char('currency_code', 3)->default('TRY')->comment('Kasa para birimi (ISO 4217: TRY, USD, EUR)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('type_id');
            $table->index('status');
        });

        Schema::create(self::CASH_BANK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasaya bağlı banka hesabı ve IBAN bilgileri');

            $table->bigIncrements('cash_bank_id')->comment('Kasa banka hesabı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
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
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('bank_id');
            $table->index('iban');

            $table->foreign('cash_id')->references('cash_id')->on(self::CASH_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CASH_ACTIVITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa hareketleri: tahsilat, ödeme ve virman kayıtları');

            $table->bigIncrements('cash_activity_id')->comment('Kasa hareketi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('cash_id')->comment('Bağlı olduğu kasa (acct_cash)');
            $table->unsignedBigInteger('process_id')->nullable()->comment('Hareketi doğuran süreç kimliği (def_acct_cash_process tablosuna referans)');
            $table->unsignedBigInteger('module_group_id')->nullable()->comment('İlişkili modül grubu kimliği (polimorfik kaynak)');
            $table->unsignedBigInteger('module_type_id')->nullable()->comment('İlişkili modül türü kimliği (polimorfik kaynak)');
            $table->unsignedBigInteger('module_id')->nullable()->comment('İlişkili modül kayıt kimliği (polimorfik kaynak)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili cari hesap kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('branch_id')->nullable()->comment('Şube kimliği (def_com_branch tablosuna referans)');
            $table->decimal('amount', 19, 2)->default(0)->comment('Hareket tutarı: pozitif=giriş, negatif=çıkış');
            $table->string('code', 64)->nullable()->comment('Hareket/belge kodu');
            $table->string('name')->nullable()->comment('Hareket başlığı');
            $table->text('description')->nullable()->comment('Hareket açıklaması');
            $table->longText('content')->nullable()->comment('Hareket detay içeriği (JSON/serileştirilmiş veri)');
            $table->timestamp('date_activity')->nullable()->comment('Hareketin muhasebe tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('branch_id');
            $table->index(['module_group_id', 'module_type_id', 'module_id'], 'idx_cash_activity_module');

            $table->foreign('cash_id')->references('cash_id')->on(self::CASH_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::EMPLOYEE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personel ana tablosu: çalışan özlük kayıtları');

            $table->bigIncrements('employee_id')->comment('Personel için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('department_id')->nullable()->comment('Departman kimliği (def_com_department tablosuna referans)');
            $table->string('code', 64)->nullable()->comment('Personel sicil kodu');
            $table->string('title', 100)->nullable()->comment('Görev/unvan (örn. Muhasebe Uzmanı)');
            $table->string('firstname', 100)->nullable()->comment('Personelin adı');
            $table->string('lastname', 100)->nullable()->comment('Personelin soyadı');
            $table->string('nationality', 100)->nullable()->comment('Uyruk');
            $table->string('citizenship_number', 11)->nullable()->comment('TC kimlik numarası (11 hane)');
            $table->string('health_insurance_number', 50)->nullable()->comment('SGK/sağlık sigorta numarası');
            $table->string('image', 500)->nullable()->comment('Profil görseli dosya yolu');
            $table->enum('gender', ['male', 'female'])->nullable()->comment('Cinsiyet: male=erkek, female=kadın; null ise belirtilmemiş');
            $table->date('start_date')->nullable()->comment('İşe başlama tarihi');
            $table->date('end_date')->nullable()->comment('İşten ayrılma tarihi');
            $table->string('end_reason')->nullable()->comment('İşten ayrılma nedeni');
            $table->date('birth_date')->nullable()->comment('Doğum tarihi');
            $table->unsignedBigInteger('default_contact_id')->nullable()->comment('Varsayılan iletişim kaydı (acct_employee_contact)');
            $table->unsignedBigInteger('default_bank_account_id')->nullable()->comment('Varsayılan banka hesabı (acct_employee_bank_account)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('department_id');
            $table->index('status');
        });

        Schema::create(self::EMPLOYEE_SALARY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personel maaş tanımları ve geçerlilik dönemleri');

            $table->bigIncrements('employee_salary_id')->comment('Maaş kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('employee_id')->comment('Bağlı olduğu personel (acct_employee)');
            $table->char('currency_code', 3)->default('TRY')->comment('Maaş para birimi (ISO 4217: TRY, USD, EUR)');
            $table->decimal('amount', 19, 2)->default(0)->comment('Maaş tutarı');
            $table->date('start_date')->nullable()->comment('Maaşın geçerli olduğu başlangıç tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('employee_id')->references('employee_id')->on(self::EMPLOYEE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::EMPLOYEE_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personelin adres/iletişim kartları');

            $table->bigIncrements('employee_contact_id')->comment('İletişim kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('employee_id')->comment('Bağlı olduğu personel (acct_employee)');
            $table->unsignedBigInteger('address_type_id')->nullable()->comment('Adres türü kimliği (def_loc_address_type tablosuna referans)');
            $table->string('code', 64)->nullable()->comment('Adres kartı kodu');
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

            $table->foreign('employee_id')->references('employee_id')->on(self::EMPLOYEE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::EMPLOYEE_BANK_ACCOUNT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personelin banka hesapları ve IBAN bilgileri (maaş ödemeleri)');

            $table->bigIncrements('employee_bank_account_id')->comment('Banka hesabı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('employee_id')->comment('Bağlı olduğu personel (acct_employee)');
            $table->unsignedBigInteger('bank_id')->nullable()->comment('Banka kimliği (def_gen_bank tablosuna referans)');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi kodu (ISO 4217: TRY, USD, EUR)');
            $table->string('code', 64)->nullable()->comment('Hesap kartı kodu');
            $table->string('type', 50)->nullable()->comment('Hesap türü (örn. vadesiz, vadeli)');
            $table->string('name', 100)->comment('Hesap kartı başlığı');
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

            $table->foreign('employee_id')->references('employee_id')->on(self::EMPLOYEE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::EMPLOYEE_DOCUMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personele ait belgeler (yasal ve özel evraklar)');

            $table->bigIncrements('employee_document_id')->comment('Belge kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('employee_id')->comment('Bağlı olduğu personel (acct_employee)');
            $table->enum('type', ['legal', 'special'])->default('legal')->comment('Belge türü: legal=yasal, special=özel');
            $table->string('code', 64)->nullable()->comment('Belge kodu');
            $table->string('name')->comment('Belge adı (örn. İş Sözleşmesi)');
            $table->string('description')->nullable()->comment('Belge açıklaması');
            $table->string('file', 500)->nullable()->comment('Belge dosya yolu');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');
            $table->timestamp('date_validity')->nullable()->comment('Belge geçerlilik bitiş tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('employee_id')->references('employee_id')->on(self::EMPLOYEE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CART_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sepet kalemleri: üyeye veya oturuma ait ürün sepeti');

            $table->bigIncrements('cart_id')->comment('Sepet kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->nullable()->comment('Sepet kalemi takip kodu');
            $table->string('session')->nullable()->comment('Oturum kimliği (misafir sepetleri için)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili üye hesabı kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->unsignedBigInteger('recurring_type_id')->nullable()->comment('Abonelik/tekrar türü kimliği (def_cat_recurring_type tablosuna referans)');
            $table->unsignedBigInteger('variant_stock_id')->nullable()->comment('Seçilen ürün varyant/stok kimliği (katalog tablosuna referans)');
            $table->unsignedInteger('quantity')->default(1)->comment('Sepetteki ürün miktarı');
            $table->longText('options')->nullable()->comment('Seçilen ürün seçenekleri (JSON)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('product_id');
            $table->index('session');
        });

        Schema::create(self::ORDER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş ana tablosu: müşteri siparişlerinin başlık kayıtları');

            $table->bigIncrements('order_id')->comment('Sipariş için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Sipariş numarası — benzersiz');
            $table->string('group_code', 64)->nullable()->comment('Sipariş grup kodu (parçalı siparişleri birleştirir)');
            $table->string('channel_code', 64)->nullable()->comment('Satış kanalı kodu (örn. web, pazaryeri)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili üye hesabı kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->string('account_name')->nullable()->comment('Sipariş anındaki müşteri adı/unvanı (anlık kopya)');
            $table->unsignedBigInteger('account_group_id')->nullable()->comment('Müşteri grup kimliği (def_mbr_account_group tablosuna referans)');
            $table->unsignedBigInteger('operation_id')->nullable()->comment('Sipariş operasyon kimliği (def_acct_order_operation tablosuna referans)');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Sipariş durum kimliği (def_acct_order_status tablosuna referans)');
            $table->char('language_code', 2)->default('tr')->comment('Sipariş dili (ISO 639-1: tr, en)');
            $table->char('currency_code', 3)->default('TRY')->comment('Sipariş para birimi (ISO 4217: TRY, USD, EUR)');
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
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('status_id');
            $table->index('group_code');
        });

        Schema::create(self::ORDER_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş kalemleri: siparişe ait ürün satırları');

            $table->bigIncrements('order_item_id')->comment('Sipariş kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->unsignedBigInteger('recurring_id')->nullable()->comment('İlişkili abonelik kaydı (acct_recurring)');
            $table->unsignedBigInteger('variant_stock_id')->nullable()->comment('Ürün varyant/stok kimliği (katalog tablosuna referans)');
            $table->string('product_code', 64)->nullable()->comment('Sipariş anındaki ürün kodu (anlık kopya)');
            $table->string('product_name')->comment('Sipariş anındaki ürün adı (anlık kopya)');
            $table->decimal('product_price', 19, 2)->default(0)->comment('Birim ürün fiyatı');
            $table->decimal('exchange_rate', 19, 4)->default(1)->comment('Sipariş anındaki döviz kuru');
            $table->char('currency_code', 3)->default('TRY')->comment('Kalem para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedInteger('quantity')->default(1)->comment('Ürün miktarı');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('Birim kimliği: adet/kg/lt (def_loc_unit tablosuna referans)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('Vergi sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->string('discount_type', 20)->nullable()->comment('İndirim türü: percentage/amount');
            $table->decimal('discount_value', 19, 2)->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->string('excise_duty_type', 20)->nullable()->comment('ÖTV türü: percentage/amount');
            $table->decimal('excise_duty_rate', 8, 2)->default(0)->comment('ÖTV oranı/tutarı');
            $table->decimal('communications_tax_rate', 8, 2)->default(0)->comment('ÖİV (özel iletişim vergisi) oranı');
            $table->string('delivery_method', 100)->nullable()->comment('Teslim yöntemi kodu');
            $table->string('shipping_method', 100)->nullable()->comment('Kargo yöntemi kodu');
            $table->longText('options')->nullable()->comment('Seçilen ürün seçenekleri (JSON)');
            $table->text('variants')->nullable()->comment('Ürün varyant bilgileri (JSON)');
            $table->text('groupeds')->nullable()->comment('Gruplanan ürün bilgileri (JSON)');
            $table->text('bundles')->nullable()->comment('Paket/bundle ürün bilgileri (JSON)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('product_id');

            $table->foreign('order_id')->references('order_id')->on(self::ORDER_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ORDER_FINANCIAL_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş mali satırları: ara toplam, kargo, indirim, vergi kalemleri');

            $table->bigIncrements('order_financial_id')->comment('Mali satır için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->string('title')->comment('Satır başlığı (örn. Ara Toplam, Kargo)');
            $table->string('code', 64)->nullable()->comment('Satır kodu (örn. sub_total, shipping)');
            $table->string('type', 50)->nullable()->comment('Satır türü/sınıfı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('removable')->default(false)->comment('Satır kaldırılabilir mi?');
            $table->decimal('value', 19, 2)->default(0)->comment('Satır sayısal değeri/oranı');
            $table->decimal('amount', 19, 2)->default(0)->comment('Hesaplanan tutar');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('order_id')->references('order_id')->on(self::ORDER_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ORDER_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş iletişim/adres kayıtları (fatura ve sevkiyat adresleri)');

            $table->bigIncrements('order_contact_id')->comment('Sipariş adresi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
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
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('country_id');
            $table->index('city_id');
            $table->index('district_id');

            $table->foreign('order_id')->references('order_id')->on(self::ORDER_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ORDER_ACTIVITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş hareket geçmişi: durum değişiklikleri ve işlem günlüğü');

            $table->bigIncrements('order_activity_id')->comment('Sipariş hareketi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->unsignedBigInteger('operation_id')->nullable()->comment('Operasyon kimliği (def_acct_order_operation tablosuna referans)');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Yeni durum kimliği (def_acct_order_status tablosuna referans)');
            $table->boolean('notification')->default(false)->comment('Müşteriye bildirim gönderildi mi?');
            $table->string('comment')->nullable()->comment('Hareket notu');
            $table->longText('payment_operation')->nullable()->comment('Ödeme işlem detayları (JSON)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status_id');

            $table->foreign('order_id')->references('order_id')->on(self::ORDER_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ORDER_SHIPMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş sevkiyatları: her sevk için kargo/takip bilgisi');

            $table->bigIncrements('order_shipment_id')->comment('Sevkiyat için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->string('code', 64)->nullable()->comment('Sevkiyat takip kodu');
            $table->unsignedBigInteger('cargo_id')->nullable()->comment('Kargo firması kimliği (def_gen_cargo tablosuna referans)');
            $table->string('tracking_code', 100)->nullable()->comment('Kargo takip numarası');
            $table->timestamp('date_shipment')->nullable()->comment('Sevkiyat tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('cargo_id');

            $table->foreign('order_id')->references('order_id')->on(self::ORDER_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ORDER_SHIPMENT_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sevkiyat kalemleri: bir sevkiyatta gönderilen sipariş satırları');

            $table->bigIncrements('order_shipment_item_id')->comment('Sevkiyat kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_id')->comment('Bağlı olduğu sipariş (acct_order)');
            $table->unsignedBigInteger('order_shipment_id')->comment('Bağlı olduğu sevkiyat (acct_order_shipment)');
            $table->unsignedBigInteger('order_item_id')->comment('Gönderilen sipariş kalemi (acct_order_item)');
            $table->unsignedInteger('quantity')->default(1)->comment('Bu sevkiyatta gönderilen miktar');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('order_shipment_id')->references('order_shipment_id')->on(self::ORDER_SHIPMENT_TABLE)->cascadeOnDelete();
            $table->foreign('order_item_id')->references('order_item_id')->on(self::ORDER_ITEM_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::INVOICE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura ana tablosu: satış/alış faturalarının başlık kayıtları');

            $table->bigIncrements('invoice_id')->comment('Fatura için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Fatura türü kimliği: satış/alış/iade (def_acct_invoice_type tablosuna referans)');
            $table->string('group_code', 64)->nullable()->comment('Fatura grup kodu');
            $table->char('currency_code', 3)->default('TRY')->comment('Fatura para birimi (ISO 4217: TRY, USD, EUR)');
            $table->char('language_code', 2)->default('tr')->comment('Fatura dili (ISO 639-1: tr, en)');
            $table->string('code', 64)->unique()->comment('Fatura numarası — benzersiz');
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
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('type_id');
            $table->index('account_id');
            $table->index('order_id');

            $table->foreign('order_id')->references('order_id')->on(self::ORDER_TABLE)->nullOnDelete();
        });

        Schema::create(self::INVOICE_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura kalemleri: faturaya ait ürün/hizmet satırları');

            $table->bigIncrements('invoice_item_id')->comment('Fatura kalemi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('invoice_id')->comment('Bağlı olduğu fatura (acct_invoice)');
            $table->unsignedBigInteger('product_id')->nullable()->comment('Ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->string('product_name')->comment('Fatura anındaki ürün/hizmet adı (anlık kopya)');
            $table->decimal('product_price', 19, 2)->default(0)->comment('Birim fiyat');
            $table->decimal('exchange_rate', 19, 4)->default(1)->comment('Fatura anındaki döviz kuru');
            $table->char('currency_code', 3)->default('TRY')->comment('Kalem para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedInteger('quantity')->default(1)->comment('Miktar');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('Birim kimliği: adet/kg/lt (def_loc_unit tablosuna referans)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('Vergi sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->string('discount_type', 20)->nullable()->comment('İndirim türü: percentage/amount');
            $table->decimal('discount_value', 19, 2)->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->string('excise_duty_type', 20)->nullable()->comment('ÖTV türü: percentage/amount');
            $table->decimal('excise_duty_rate', 8, 2)->default(0)->comment('ÖTV oranı/tutarı');
            $table->decimal('communications_tax_rate', 8, 2)->default(0)->comment('ÖİV (özel iletişim vergisi) oranı');
            $table->string('delivery_method', 100)->nullable()->comment('Teslim yöntemi kodu');
            $table->string('shipping_method', 100)->nullable()->comment('Kargo yöntemi kodu');
            $table->string('variant')->nullable()->comment('Ürün varyant bilgisi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('product_id');

            $table->foreign('invoice_id')->references('invoice_id')->on(self::INVOICE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::INVOICE_FINANCIAL_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura mali satırları: ara toplam, indirim, vergi ve genel toplam kalemleri');

            $table->bigIncrements('invoice_financial_id')->comment('Mali satır için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('invoice_id')->comment('Bağlı olduğu fatura (acct_invoice)');
            $table->string('title')->comment('Satır başlığı (örn. KDV %20, Genel Toplam)');
            $table->string('code', 64)->nullable()->comment('Satır kodu (örn. tax, total)');
            $table->string('type', 50)->nullable()->comment('Satır türü/sınıfı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->decimal('amount', 19, 2)->default(0)->comment('Hesaplanan tutar');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('invoice_id')->references('invoice_id')->on(self::INVOICE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::INVOICE_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura iletişim/adres kayıtları (fatura ve sevkiyat adresleri)');

            $table->bigIncrements('invoice_contact_id')->comment('Fatura adresi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
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
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('country_id');
            $table->index('city_id');
            $table->index('district_id');

            $table->foreign('invoice_id')->references('invoice_id')->on(self::INVOICE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::REFUND_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İade talepleri: sipariş kalemleri için iade/değişim kayıtları');

            $table->bigIncrements('refund_id')->comment('İade için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('İade takip kodu — benzersiz');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili üye hesabı kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('order_id')->nullable()->comment('İade edilen sipariş kimliği (acct_order)');
            $table->unsignedBigInteger('product_id')->nullable()->comment('İade edilen ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->unsignedBigInteger('status_id')->nullable()->comment('İade durum kimliği (def_acct_refund_status tablosuna referans)');
            $table->unsignedBigInteger('reason_id')->nullable()->comment('İade neden kimliği (def_acct_refund_reason tablosuna referans)');
            $table->unsignedBigInteger('action_id')->nullable()->comment('İade işlem türü kimliği: iade/değişim (def_acct_refund_action tablosuna referans)');
            $table->boolean('opened')->default(false)->comment('İade talebi açık mı?');
            $table->string('product_name')->nullable()->comment('İade anındaki ürün adı (anlık kopya)');
            $table->decimal('product_price', 19, 2)->default(0)->comment('Birim ürün fiyatı');
            $table->decimal('exchange_rate', 19, 4)->default(1)->comment('İade anındaki döviz kuru');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedInteger('quantity')->default(1)->comment('İade edilen miktar');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('Birim kimliği (def_loc_unit tablosuna referans)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('Vergi sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->string('discount_type', 20)->nullable()->comment('İndirim türü: percentage/amount');
            $table->decimal('discount_value', 19, 2)->default(0)->comment('İndirim değeri (oran ya da tutar)');
            $table->longText('comment')->nullable()->comment('İade açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('order_id');
            $table->index('product_id');
            $table->index('status_id');
        });

        Schema::create(self::RECURRING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Abonelik/tekrarlı sipariş tanımları');

            $table->bigIncrements('recurring_id')->comment('Abonelik için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Abonelik takip kodu — benzersiz');
            $table->unsignedBigInteger('recurring_type_id')->nullable()->comment('Abonelik periyot türü kimliği: haftalık/aylık (def_cat_recurring_type tablosuna referans)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('İlişkili üye hesabı kimliği (mbr_account; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('product_id')->comment('Abone olunan ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');
            $table->unsignedInteger('quantity')->default(1)->comment('Her periyottaki ürün miktarı');
            $table->decimal('product_price', 19, 2)->default(0)->comment('Birim ürün fiyatı');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');
            $table->unsignedBigInteger('tax_class_id')->nullable()->comment('Vergi sınıfı kimliği (def_loc_tax_class tablosuna referans)');
            $table->datetime('date_start')->nullable()->comment('Abonelik başlangıç tarihi');
            $table->datetime('date_end')->nullable()->comment('Abonelik bitiş tarihi; null ise süresiz');
            $table->string('user_agent')->nullable()->comment('Oluşturma anındaki tarayıcı/istemci bilgisi');
            $table->string('accept_language', 100)->nullable()->comment('İstemcinin kabul ettiği diller (HTTP Accept-Language)');
            $table->string('ip_created', 45)->nullable()->comment('Oluşturulma IP adresi (IPv6 uyumlu)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('product_id');
            $table->index('status');
        });

        Schema::create(self::DEMAND_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün talep/teklif kayıtları (fiyat sorma ve numune talepleri)');

            $table->bigIncrements('demand_id')->comment('Talep için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Talep takip kodu — benzersiz');
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
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('product_id');
        });

        Schema::table(self::EMPLOYEE_TABLE, function (Blueprint $table) {
            $table->foreign('default_contact_id')->references('employee_contact_id')->on(self::EMPLOYEE_CONTACT_TABLE)->nullOnDelete();
            $table->foreign('default_bank_account_id')->references('employee_bank_account_id')->on(self::EMPLOYEE_BANK_ACCOUNT_TABLE)->nullOnDelete();
        });

        Schema::table(self::ORDER_TABLE, function (Blueprint $table) {
            $table->foreign('payment_contact_id')->references('order_contact_id')->on(self::ORDER_CONTACT_TABLE)->nullOnDelete();
            $table->foreign('shipping_contact_id')->references('order_contact_id')->on(self::ORDER_CONTACT_TABLE)->nullOnDelete();
            $table->foreign('recurring_id')->references('recurring_id')->on(self::RECURRING_TABLE)->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(self::ORDER_TABLE, function (Blueprint $table) {
            $table->dropForeign(['payment_contact_id']);
            $table->dropForeign(['shipping_contact_id']);
            $table->dropForeign(['recurring_id']);
        });

        Schema::table(self::EMPLOYEE_TABLE, function (Blueprint $table) {
            $table->dropForeign(['default_contact_id']);
            $table->dropForeign(['default_bank_account_id']);
        });

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
        Schema::dropIfExists(self::CASH_ACTIVITY_TABLE);
        Schema::dropIfExists(self::CASH_BANK_TABLE);
        Schema::dropIfExists(self::CASH_TABLE);
    }
};
