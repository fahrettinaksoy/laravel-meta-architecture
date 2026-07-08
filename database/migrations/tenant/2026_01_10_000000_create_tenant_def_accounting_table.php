<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const CASH_TYPE_TABLE = 'def_acct_cash_type';
    private const CASH_TYPE_TRANSLATION_TABLE = 'def_acct_cash_type_translation';
    private const CASH_PROCESS_TABLE = 'def_acct_cash_process';
    private const CASH_PROCESS_TRANSLATION_TABLE = 'def_acct_cash_process_translation';
    private const CASH_MODULE_GROUP_TABLE = 'def_acct_cash_module_group';
    private const CASH_MODULE_GROUP_TRANSLATION_TABLE = 'def_acct_cash_module_group_translation';
    private const CASH_MODULE_TYPE_TABLE = 'def_acct_cash_module_type';
    private const CASH_MODULE_TYPE_TRANSLATION_TABLE = 'def_acct_cash_module_type_translation';
    private const LOAN_TYPE_TABLE = 'def_acct_loan_type';
    private const LOAN_TYPE_TRANSLATION_TABLE = 'def_acct_loan_type_translation';
    private const ORDER_GROUP_TABLE = 'def_acct_order_group';
    private const ORDER_GROUP_TRANSLATION_TABLE = 'def_acct_order_group_translation';
    private const INVOICE_TYPE_TABLE = 'def_acct_invoice_type';
    private const INVOICE_TYPE_TRANSLATION_TABLE = 'def_acct_invoice_type_translation';
    private const INVOICE_GROUP_TABLE = 'def_acct_invoice_group';
    private const INVOICE_GROUP_TRANSLATION_TABLE = 'def_acct_invoice_group_translation';
    private const INVOICE_RELATION_TABLE = 'def_acct_invoice_relation';
    private const INVOICE_RELATION_TRANSLATION_TABLE = 'def_acct_invoice_relation_translation';
    private const CHEQUE_TYPE_TABLE = 'def_acct_cheque_type';
    private const CHEQUE_TYPE_TRANSLATION_TABLE = 'def_acct_cheque_type_translation';
    private const ORDER_OPERATION_TABLE = 'def_acct_order_operation';
    private const ORDER_OPERATION_TRANSLATION_TABLE = 'def_acct_order_operation_translation';
    private const ORDER_STATUS_TABLE = 'def_acct_order_status';
    private const ORDER_STATUS_TRANSLATION_TABLE = 'def_acct_order_status_translation';
    private const INVOICE_CATEGORY_TABLE = 'def_acct_invoice_category';
    private const INVOICE_CATEGORY_TRANSLATION_TABLE = 'def_acct_invoice_category_translation';
    private const REFUND_STATUS_TABLE = 'def_acct_refund_status';
    private const REFUND_STATUS_TRANSLATION_TABLE = 'def_acct_refund_status_translation';
    private const REFUND_ACTION_TABLE = 'def_acct_refund_action';
    private const REFUND_ACTION_TRANSLATION_TABLE = 'def_acct_refund_action_translation';
    private const REFUND_REASON_TABLE = 'def_acct_refund_reason';
    private const REFUND_REASON_TRANSLATION_TABLE = 'def_acct_refund_reason_translation';

    public function up(): void
    {
        Schema::create(self::ORDER_OPERATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş operasyonları (sipariş sürecinin ana aşamaları; muhasebe tanım tablosu)');

            $table->bigIncrements('order_operation_id')->comment('Sipariş operasyonu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Operasyon kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::ORDER_OPERATION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş operasyonu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('order_operation_translation_id')->comment('Operasyon çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_operation_id')->comment('Bağlı olduğu sipariş operasyonu (def_acct_order_operation)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Operasyon adı');
            $table->string('description')->nullable()->comment('Operasyon açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['order_operation_id', 'language_code'], 'uq_order_operation_translation_lang');

            $table->foreign('order_operation_id')->references('order_operation_id')->on(self::ORDER_OPERATION_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ORDER_STATUS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş durumları (operasyona bağlı alt durumlar; muhasebe tanım tablosu)');

            $table->bigIncrements('order_status_id')->comment('Sipariş durumu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_operation_id')->nullable()->comment('Bağlı olduğu sipariş operasyonu (def_acct_order_operation); null ise operasyondan bağımsız');
            $table->string('code', 64)->unique()->comment('Durum kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');

            $table->foreign('order_operation_id')->references('order_operation_id')->on(self::ORDER_OPERATION_TABLE)->nullOnDelete();
        });

        Schema::create(self::ORDER_STATUS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş durumu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('order_status_translation_id')->comment('Durum çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_status_id')->comment('Bağlı olduğu sipariş durumu (def_acct_order_status)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Durum adı');
            $table->string('description')->nullable()->comment('Durum açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['order_status_id', 'language_code'], 'uq_order_status_translation_lang');

            $table->foreign('order_status_id')->references('order_status_id')->on(self::ORDER_STATUS_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::INVOICE_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura kategorileri (kategori ağacı; muhasebe tanım tablosu)');

            $table->bigIncrements('invoice_category_id')->comment('Fatura kategorisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Üst kategori kimliği (kendine referans); null ise kök kategori');
            $table->string('code', 64)->unique()->comment('Kategori kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('parent_id');
            $table->index('status');

            $table->foreign('parent_id')->references('invoice_category_id')->on(self::INVOICE_CATEGORY_TABLE)->nullOnDelete();
        });

        Schema::create(self::INVOICE_CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura kategorisi çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('invoice_category_translation_id')->comment('Kategori çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('invoice_category_id')->comment('Bağlı olduğu fatura kategorisi (def_acct_invoice_category)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Kategori adı');
            $table->string('description')->nullable()->comment('Kategori açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['invoice_category_id', 'language_code'], 'uq_invoice_category_translation_lang');

            $table->foreign('invoice_category_id')->references('invoice_category_id')->on(self::INVOICE_CATEGORY_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::REFUND_STATUS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İade durumları (muhasebe tanım tablosu; eski adı: restitute_status)');

            $table->bigIncrements('refund_status_id')->comment('İade durumu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Durum kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::REFUND_STATUS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İade durumu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('refund_status_translation_id')->comment('İade durumu çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('refund_status_id')->comment('Bağlı olduğu iade durumu (def_acct_refund_status)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Durum adı');
            $table->string('description')->nullable()->comment('Durum açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['refund_status_id', 'language_code'], 'uq_refund_status_translation_lang');

            $table->foreign('refund_status_id')->references('refund_status_id')->on(self::REFUND_STATUS_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::REFUND_ACTION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İade işlem türleri: iade/değişim/onarım vb. (muhasebe tanım tablosu; eski adı: restitute_action)');

            $table->bigIncrements('refund_action_id')->comment('İade işlem türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('İşlem türü kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::REFUND_ACTION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İade işlem türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('refund_action_translation_id')->comment('İşlem türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('refund_action_id')->comment('Bağlı olduğu iade işlem türü (def_acct_refund_action)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('İşlem türü adı');
            $table->string('description')->nullable()->comment('İşlem türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['refund_action_id', 'language_code'], 'uq_refund_action_translation_lang');

            $table->foreign('refund_action_id')->references('refund_action_id')->on(self::REFUND_ACTION_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::REFUND_REASON_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İade nedenleri (muhasebe tanım tablosu; eski adı: restitute_reason)');

            $table->bigIncrements('refund_reason_id')->comment('İade nedeni için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Neden kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::REFUND_REASON_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İade nedeni çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('refund_reason_translation_id')->comment('İade nedeni çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('refund_reason_id')->comment('Bağlı olduğu iade nedeni (def_acct_refund_reason)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Neden adı');
            $table->string('description')->nullable()->comment('Neden açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['refund_reason_id', 'language_code'], 'uq_refund_reason_translation_lang');

            $table->foreign('refund_reason_id')->references('refund_reason_id')->on(self::REFUND_REASON_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CASH_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa türleri (muhasebe tanım tablosu; eski adı: safe_type)');

            $table->bigIncrements('cash_type_id')->comment('Kasa türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Kasa türü kodu — benzersiz');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::CASH_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('cash_type_translation_id')->comment('Kasa türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('cash_type_id')->comment('Bağlı olduğu kasa türü (def_acct_cash_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Kasa türü adı');
            $table->text('description')->nullable()->comment('Kasa türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['cash_type_id', 'language_code'], 'uq_cash_type_translation_lang');

            $table->foreign('cash_type_id')->references('cash_type_id')->on(self::CASH_TYPE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CASH_PROCESS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa işlem türleri: tahsilat/tediye vb. (muhasebe tanım tablosu; eski adı: safe_process)');

            $table->bigIncrements('cash_process_id')->comment('Kasa işlem türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('İşlem türü kodu — benzersiz');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::CASH_PROCESS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa işlem türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('cash_process_translation_id')->comment('İşlem türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('cash_process_id')->comment('Bağlı olduğu kasa işlem türü (def_acct_cash_process)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('İşlem türü adı');
            $table->text('description')->nullable()->comment('İşlem türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['cash_process_id', 'language_code'], 'uq_cash_process_translation_lang');

            $table->foreign('cash_process_id')->references('cash_process_id')->on(self::CASH_PROCESS_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CASH_MODULE_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa modül grupları (kasa hareket kaynaklarının gruplandırması; eski adı: safe_module_group)');

            $table->bigIncrements('cash_module_group_id')->comment('Modül grubu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Modül grubu kodu — benzersiz');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::CASH_MODULE_GROUP_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa modül grubu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('cash_module_group_translation_id')->comment('Modül grubu çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('cash_module_group_id')->comment('Bağlı olduğu modül grubu (def_acct_cash_module_group)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Modül grubu adı');
            $table->text('description')->nullable()->comment('Modül grubu açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['cash_module_group_id', 'language_code'], 'uq_cash_module_group_translation_lang');

            $table->foreign('cash_module_group_id')->references('cash_module_group_id')->on(self::CASH_MODULE_GROUP_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CASH_MODULE_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa modül türleri: kasa hareketini doğuran işlem kaynakları (eski adı: safe_module_type)');

            $table->bigIncrements('cash_module_type_id')->comment('Modül türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('cash_process_id')->nullable()->comment('Bağlı olduğu kasa işlem türü (def_acct_cash_process)');
            $table->unsignedBigInteger('cash_module_group_id')->nullable()->comment('Bağlı olduğu modül grubu (def_acct_cash_module_group)');
            $table->boolean('in_menu')->default(false)->comment('Menüde gösterilsin mi?');
            $table->string('request_id', 100)->nullable()->comment('İstek/işlem tanımlayıcı kodu');
            $table->string('module', 100)->nullable()->comment('Bağlı modül kodu (örn. order, invoice, cheque)');
            $table->string('route')->nullable()->comment('Yönlendirilecek rota adı');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');

            $table->foreign('cash_process_id')->references('cash_process_id')->on(self::CASH_PROCESS_TABLE)->nullOnDelete();
            $table->foreign('cash_module_group_id')->references('cash_module_group_id')->on(self::CASH_MODULE_GROUP_TABLE)->nullOnDelete();
        });

        Schema::create(self::CASH_MODULE_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa modül türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('cash_module_type_translation_id')->comment('Modül türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('cash_module_type_id')->comment('Bağlı olduğu modül türü (def_acct_cash_module_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Modül türü adı');
            $table->text('description')->nullable()->comment('Modül türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['cash_module_type_id', 'language_code'], 'uq_cash_module_type_translation_lang');

            $table->foreign('cash_module_type_id')->references('cash_module_type_id')->on(self::CASH_MODULE_TYPE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::LOAN_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kredi türleri (muhasebe tanım tablosu)');

            $table->bigIncrements('loan_type_id')->comment('Kredi türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Kredi türü kodu — benzersiz');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::LOAN_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kredi türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('loan_type_translation_id')->comment('Kredi türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('loan_type_id')->comment('Bağlı olduğu kredi türü (def_acct_loan_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Kredi türü adı');
            $table->text('description')->nullable()->comment('Kredi türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['loan_type_id', 'language_code'], 'uq_loan_type_translation_lang');

            $table->foreign('loan_type_id')->references('loan_type_id')->on(self::LOAN_TYPE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::ORDER_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş grupları (muhasebe tanım tablosu)');

            $table->bigIncrements('order_group_id')->comment('Sipariş grubu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Sipariş grubu kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::ORDER_GROUP_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş grubu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('order_group_translation_id')->comment('Sipariş grubu çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('order_group_id')->comment('Bağlı olduğu sipariş grubu (def_acct_order_group)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Sipariş grubu adı');
            $table->string('description')->nullable()->comment('Sipariş grubu açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['order_group_id', 'language_code'], 'uq_order_group_translation_lang');

            $table->foreign('order_group_id')->references('order_group_id')->on(self::ORDER_GROUP_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::INVOICE_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura türleri: satış/alış/iade vb. (muhasebe tanım tablosu)');

            $table->bigIncrements('invoice_type_id')->comment('Fatura türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Fatura türü kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::INVOICE_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('invoice_type_translation_id')->comment('Fatura türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('invoice_type_id')->comment('Bağlı olduğu fatura türü (def_acct_invoice_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Fatura türü adı');
            $table->string('description')->nullable()->comment('Fatura türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['invoice_type_id', 'language_code'], 'uq_invoice_type_translation_lang');

            $table->foreign('invoice_type_id')->references('invoice_type_id')->on(self::INVOICE_TYPE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::INVOICE_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura grupları (muhasebe tanım tablosu)');

            $table->bigIncrements('invoice_group_id')->comment('Fatura grubu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Fatura grubu kodu — benzersiz');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::INVOICE_GROUP_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura grubu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('invoice_group_translation_id')->comment('Fatura grubu çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('invoice_group_id')->comment('Bağlı olduğu fatura grubu (def_acct_invoice_group)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Fatura grubu adı');
            $table->string('description')->nullable()->comment('Fatura grubu açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['invoice_group_id', 'language_code'], 'uq_invoice_group_translation_lang');

            $table->foreign('invoice_group_id')->references('invoice_group_id')->on(self::INVOICE_GROUP_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::INVOICE_RELATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura ilişki türleri: faturanın ilişkilendirilebileceği kayıt türleri (muhasebe tanım tablosu)');

            $table->bigIncrements('invoice_relation_id')->comment('İlişki türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('İlişki türü kodu — benzersiz');
            $table->string('path')->nullable()->comment('İlgili kayda erişim için rota/bağlantı yolu');
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

        Schema::create(self::INVOICE_RELATION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura ilişki türü çevirileri (dile göre ad)');

            $table->bigIncrements('invoice_relation_translation_id')->comment('İlişki türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('invoice_relation_id')->comment('Bağlı olduğu ilişki türü (def_acct_invoice_relation)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('İlişki türü adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['invoice_relation_id', 'language_code'], 'uq_invoice_relation_translation_lang');

            $table->foreign('invoice_relation_id')->references('invoice_relation_id')->on(self::INVOICE_RELATION_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::CHEQUE_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Çek türleri (muhasebe tanım tablosu; eski adı: check_type)');

            $table->bigIncrements('cheque_type_id')->comment('Çek türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Çek türü kodu — benzersiz');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('color', 20)->nullable()->comment('Arayüz rengi (hex kodu, örn. #FF5733)');
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

        Schema::create(self::CHEQUE_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Çek türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('cheque_type_translation_id')->comment('Çek türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('cheque_type_id')->comment('Bağlı olduğu çek türü (def_acct_cheque_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Çek türü adı');
            $table->text('description')->nullable()->comment('Çek türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['cheque_type_id', 'language_code'], 'uq_cheque_type_translation_lang');

            $table->foreign('cheque_type_id')->references('cheque_type_id')->on(self::CHEQUE_TYPE_TABLE)->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::CHEQUE_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CHEQUE_TYPE_TABLE);
        Schema::dropIfExists(self::INVOICE_RELATION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::INVOICE_RELATION_TABLE);
        Schema::dropIfExists(self::INVOICE_GROUP_TRANSLATION_TABLE);
        Schema::dropIfExists(self::INVOICE_GROUP_TABLE);
        Schema::dropIfExists(self::INVOICE_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::INVOICE_TYPE_TABLE);
        Schema::dropIfExists(self::ORDER_GROUP_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ORDER_GROUP_TABLE);
        Schema::dropIfExists(self::LOAN_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::LOAN_TYPE_TABLE);
        Schema::dropIfExists(self::CASH_MODULE_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CASH_MODULE_TYPE_TABLE);
        Schema::dropIfExists(self::CASH_MODULE_GROUP_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CASH_MODULE_GROUP_TABLE);
        Schema::dropIfExists(self::CASH_PROCESS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CASH_PROCESS_TABLE);
        Schema::dropIfExists(self::CASH_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CASH_TYPE_TABLE);
        Schema::dropIfExists(self::REFUND_REASON_TRANSLATION_TABLE);
        Schema::dropIfExists(self::REFUND_REASON_TABLE);
        Schema::dropIfExists(self::REFUND_ACTION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::REFUND_ACTION_TABLE);
        Schema::dropIfExists(self::REFUND_STATUS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::REFUND_STATUS_TABLE);
        Schema::dropIfExists(self::INVOICE_CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::INVOICE_CATEGORY_TABLE);
        Schema::dropIfExists(self::ORDER_STATUS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ORDER_STATUS_TABLE);
        Schema::dropIfExists(self::ORDER_OPERATION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ORDER_OPERATION_TABLE);
    }
};
