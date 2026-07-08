<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const APPLICATION_TABLE = 'sys_application';
    private const SETTING_TABLE = 'sys_setting';
    private const COMPANY_TABLE = 'sys_company';
    private const METHOD_TABLE = 'sys_method';
    private const PLUGIN_TABLE = 'sys_plugin';
    private const WIDGET_TABLE = 'sys_widget';
    private const VARIABLE_TABLE = 'sys_variable';

    public function up(): void
    {
        Schema::create(self::APPLICATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulama/entegrasyon kayıtları (kurulu dış servis ve uygulama yapılandırmaları)');

            $table->bigIncrements('application_id')->comment('Uygulama kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('type', 50)->nullable()->comment('Uygulama türü/kategorisi (örn. sms, e-invoice, analytics)');
            $table->string('code', 100)->comment('Uygulama kodu (örn. netgsm, google-analytics)');
            $table->json('settings')->nullable()->comment('Uygulama yapılandırması: API anahtarları, seçenekler vb. (JSON)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['type', 'code'], 'uq_application_type_code');
            $table->index('status');
        });

        Schema::create(self::SETTING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sistem ayarları (grup + anahtar bazlı yapılandırma değerleri)');

            $table->bigIncrements('setting_id')->comment('Ayar için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('group', 100)->comment('Ayar grubu (örn. general, mail, seo)');
            $table->string('key', 150)->comment('Ayar anahtarı (grup içinde benzersiz)');
            $table->longText('value')->nullable()->comment('Ayar değeri');
            $table->boolean('is_serialized')->default(false)->comment('Değer serileştirilmiş (JSON/dizi) mi?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['group', 'key'], 'uq_setting_group_key');
        });

        Schema::create(self::COMPANY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Şirket bilgi kayıtları (grup + anahtar bazlı şirket profili değerleri)');

            $table->bigIncrements('company_id')->comment('Şirket bilgisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('group', 100)->comment('Bilgi grubu (örn. contact, legal, social)');
            $table->string('key', 150)->comment('Bilgi anahtarı (grup içinde benzersiz)');
            $table->longText('value')->nullable()->comment('Bilgi değeri');
            $table->boolean('is_visible')->default(false)->comment('Sitede/arayüzde gösterilsin mi?');
            $table->boolean('is_serialized')->default(false)->comment('Değer serileştirilmiş (JSON/dizi) mi?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['group', 'key'], 'uq_company_group_key');
        });

        Schema::create(self::METHOD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Yöntem tanımları: ödeme/kargo gibi yöntem uygulamaları ve ayarları');

            $table->bigIncrements('method_id')->comment('Yöntem için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('group', 50)->comment('Yöntem grubu kodu (def_ext_method_group.code; örn. payment, shipping)');
            $table->string('code', 100)->comment('Yöntem kodu (def_ext_method_type.code; örn. bank-transfer, credit-card)');
            $table->json('settings')->nullable()->comment('Yöntem yapılandırması (JSON)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['group', 'code'], 'uq_method_group_code');
            $table->index('status');
        });

        Schema::create(self::PLUGIN_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Eklenti kayıtları (kurulu eklentiler ve ayarları)');

            $table->bigIncrements('plugin_id')->comment('Eklenti için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('group', 50)->comment('Eklenti grubu kodu (def_ext_plugin_group.code)');
            $table->string('code', 100)->comment('Eklenti kodu (def_ext_plugin_type.code)');
            $table->json('settings')->nullable()->comment('Eklenti yapılandırması (JSON)');
            $table->integer('sort_order')->default(0)->comment('Çalışma/görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['group', 'code'], 'uq_plugin_group_code');
            $table->index('status');
        });

        Schema::create(self::WIDGET_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bileşenler (site_layout_widget bölgelerine yerleştirilen içerik bileşenleri)');

            $table->bigIncrements('widget_id')->comment('Bileşen için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('type', 100)->comment('Bileşen türü kodu (def_ext_widget_type.code; örn. slider, product-list, html)');
            $table->string('name', 150)->comment('Bileşen adı (iç kullanım)');
            $table->string('css')->nullable()->comment('Özel CSS sınıfı veya stil dosyası yolu');
            $table->string('html')->nullable()->comment('Özel HTML şablon tanımı');
            $table->json('settings')->nullable()->comment('Bileşen yapılandırması (JSON)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('type');
            $table->index('status');
        });

        Schema::create(self::VARIABLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sistem değişkenleri (basit anahtar-değer deposu)');

            $table->bigIncrements('variable_id')->comment('Değişken için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('key', 150)->unique()->comment('Değişken anahtarı — benzersiz');
            $table->longText('value')->nullable()->comment('Değişken değeri');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::VARIABLE_TABLE);
        Schema::dropIfExists(self::WIDGET_TABLE);
        Schema::dropIfExists(self::PLUGIN_TABLE);
        Schema::dropIfExists(self::METHOD_TABLE);
        Schema::dropIfExists(self::COMPANY_TABLE);
        Schema::dropIfExists(self::SETTING_TABLE);
        Schema::dropIfExists(self::APPLICATION_TABLE);
    }
};
