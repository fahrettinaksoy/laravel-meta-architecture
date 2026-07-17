<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const LAYOUT_TABLE = 'site_layout';
    private const LAYOUT_WIDGET_TABLE = 'site_layout_widget';
    private const BANNER_TABLE = 'site_banner';
    private const BANNER_ITEM_TABLE = 'site_banner_item';
    private const MENU_TABLE = 'site_menu';
    private const MENU_ITEM_TABLE = 'site_menu_item';
    private const MENU_ITEM_TRANSLATION_TABLE = 'site_menu_item_translation';
    private const URL_TABLE = 'site_url';
    private const URL_TRANSLATION_TABLE = 'site_url_translation';
    private const FORM_TABLE = 'site_form';
    private const FORM_TRANSLATION_TABLE = 'site_form_translation';
    private const FORM_SUBMISSION_TABLE = 'site_form_submission';

    public function up(): void
    {
        Schema::create(self::LAYOUT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sayfa şablonları/düzenleri (modüllerdeki layout_id alanlarının hedefi)');

            $table->bigIncrements('layout_id')->comment('Şablon için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Şablon kodu — benzersiz');
            $table->string('name', 150)->comment('Şablon adı');
            $table->text('summary')->nullable()->comment('Şablon açıklaması');
            $table->json('positions')->nullable()->comment('Şablondaki bileşen bölgeleri (JSON)');
            $table->string('path')->nullable()->comment('Şablon dosya/görünüm yolu');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::LAYOUT_WIDGET_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Şablon bölgelerine yerleştirilen bileşenler');

            $table->bigIncrements('layout_widget_id')->comment('Bölge bileşeni için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('layout_id')->comment('Bağlı olduğu şablon (site_layout)');
            $table->string('position', 100)->comment('Şablondaki bölge adı (örn. header, sidebar, footer)');
            $table->json('widgets')->nullable()->comment('Bölgedeki bileşen listesi ve ayarları (JSON)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['layout_id', 'position'], 'uq_layout_widget_position');
        });

        Schema::create(self::BANNER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Banner/slider grupları');

            $table->bigIncrements('banner_id')->comment('Banner grubu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Banner kodu — benzersiz');
            $table->string('name', 150)->comment('Banner adı (iç kullanım)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::BANNER_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Banner görselleri/slaytları (dile göre içerik)');

            $table->bigIncrements('banner_item_id')->comment('Banner slaytı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('banner_id')->comment('Bağlı olduğu banner grubu (site_banner)');
            $table->char('language_code', 2)->default('tr')->comment('Slaytın gösterileceği dil kodu (ISO 639-1: tr, en)');
            $table->string('image', 500)->nullable()->comment('Slayt görseli dosya yolu');
            $table->string('title')->nullable()->comment('Birincil başlık');
            $table->string('title2')->nullable()->comment('İkincil başlık');
            $table->string('title3')->nullable()->comment('Üçüncül başlık');
            $table->text('summary')->nullable()->comment('Slayt açıklama metni');
            $table->string('button', 100)->nullable()->comment('Birinci düğme metni');
            $table->string('button2', 100)->nullable()->comment('İkinci düğme metni');
            $table->string('button3', 100)->nullable()->comment('Üçüncü düğme metni');
            $table->string('link', 500)->nullable()->comment('Birinci düğme/slayt bağlantısı');
            $table->string('link2', 500)->nullable()->comment('İkinci düğme bağlantısı');
            $table->string('link3', 500)->nullable()->comment('Üçüncü düğme bağlantısı');
            $table->json('display')->nullable()->comment('Görüntüleme ayarları: animasyon, süre, hizalama vb. (JSON)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index(['banner_id', 'language_code'], 'idx_banner_item_banner_lang');
        });

        Schema::create(self::MENU_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Site menüleri (üst menü, alt menü vb.)');

            $table->bigIncrements('menu_id')->comment('Menü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Menü kodu — benzersiz');
            $table->string('type', 50)->nullable()->comment('Menü konumu/türü (örn. header, footer, mobile)');
            $table->string('name', 150)->comment('Menü adı (iç kullanım)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::MENU_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Menü öğeleri (menü ağacı)');

            $table->bigIncrements('menu_item_id')->comment('Menü öğesi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('menu_id')->comment('Bağlı olduğu menü (site_menu)');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Üst menü öğesi (kendine referans); null ise kök öğe');
            $table->string('type', 50)->nullable()->comment('Bağlantı türü (örn. module, route, external)');
            $table->unsignedBigInteger('module_id')->nullable()->comment('Bağlandığı modül kaydının kimliği (type=module ise)');
            $table->string('module_action')->nullable()->comment('Bağlandığı modül aksiyonu (type=module ise)');
            $table->string('query')->nullable()->comment('Bağlantıya eklenecek sorgu parametreleri');
            $table->string('route')->nullable()->comment('Rota adı veya URL (type=route/external ise)');
            $table->string('target', 20)->nullable()->comment('Bağlantı hedefi (örn. _self, _blank)');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
            $table->string('image', 500)->nullable()->comment('Menü öğesi görseli dosya yolu');
            $table->string('html_id', 100)->nullable()->comment('HTML id niteliği');
            $table->string('html_class', 150)->nullable()->comment('HTML class niteliği');
            $table->string('html_style')->nullable()->comment('Satır içi CSS stili');
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
            $table->index('menu_id');
        });

        Schema::create(self::MENU_ITEM_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Menü öğesi çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('menu_item_translation_id')->comment('Öğe çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('menu_item_id')->comment('Bağlı olduğu menü öğesi (site_menu_item)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Öğe adı (menüde görünen metin)');
            $table->string('summary')->nullable()->comment('Kısa açıklama (alt metin/tooltip)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['menu_item_id', 'language_code'], 'uq_menu_item_translation_lang');
        });

        Schema::create(self::URL_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('SEO URL kayıtları (modül kayıtlarına giden arama motoru dostu adresler)');

            $table->bigIncrements('url_id')->comment('URL kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('URL kaydı kodu — benzersiz');
            $table->boolean('is_locked')->default(false)->comment('Sistem kaydı mı? true ise silinemez/değiştirilemez');
            $table->boolean('in_menu')->default(false)->comment('Menü oluşturucuda seçilebilir mi?');
            $table->boolean('is_linkable')->default(false)->comment('İçerik içi otomatik bağlantı üretiminde kullanılsın mı?');
            $table->string('module_type', 100)->nullable()->comment('Hedef modül türü (örn. product, category, page)');
            $table->unsignedBigInteger('module_id')->nullable()->comment('Hedef modül kaydının kimliği');
            $table->string('module_query')->nullable()->comment('Hedefe eklenecek sorgu parametreleri');
            $table->string('module_pattern')->nullable()->comment('URL desen şablonu');
            $table->string('module_controller')->nullable()->comment('Hedef denetleyici (controller) sınıfı');
            $table->string('module_action')->nullable()->comment('Hedef denetleyici aksiyonu');
            $table->boolean('membership')->default(false)->comment('Yalnızca üyelere açık mı?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index(['module_type', 'module_id'], 'idx_url_module');
        });

        Schema::create(self::URL_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('SEO URL çevirileri (dile göre adres anahtarı)');

            $table->bigIncrements('url_translation_id')->comment('URL çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('url_id')->comment('Bağlı olduğu URL kaydı (site_url)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('keyword')->comment('Adres anahtarı/slug (örn. hakkimizda)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['url_id', 'language_code'], 'uq_url_translation_lang');
            $table->unique(['language_code', 'keyword'], 'uq_url_translation_keyword');
        });

        Schema::create(self::FORM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Site formları (form oluşturucu ile tanımlanan formlar)');

            $table->bigIncrements('form_id')->comment('Form için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Form kodu — benzersiz');
            $table->string('name', 150)->comment('Form adı (iç kullanım)');
            $table->string('html')->nullable()->comment('Özel HTML şablon tanımı');
            $table->json('send_settings')->nullable()->comment('Gönderim ayarları: bildirim alıcıları, e-posta şablonu vb. (JSON)');
            $table->json('fields')->nullable()->comment('Form alan tanımları (JSON; alan türleri def_frm_form_element tablosuna referans verir)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::FORM_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Form çevirileri (dile göre başlık, açıklama ve düğme metni)');

            $table->bigIncrements('form_translation_id')->comment('Form çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('form_id')->comment('Bağlı olduğu form (site_form)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('title')->comment('Form başlığı');
            $table->text('description')->nullable()->comment('Form açıklaması (HTML içerebilir)');
            $table->string('button', 100)->nullable()->comment('Gönder düğmesi metni');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['form_id', 'language_code'], 'uq_form_translation_lang');
        });

        Schema::create(self::FORM_SUBMISSION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Form gönderileri: ziyaretçilerin doldurduğu form yanıtları (eski adı: form_incoming)');

            $table->bigIncrements('form_submission_id')->comment('Form gönderisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('form_id')->comment('Bağlı olduğu form (site_form)');
            $table->json('content')->nullable()->comment('Gönderilen alan değerleri (JSON)');
            $table->boolean('is_read')->default(false)->comment('Gönderi okundu mu?');
            $table->string('ip_address', 45)->nullable()->comment('Gönderinin yapıldığı IP adresi (IPv6 uyumlu)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('form_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::FORM_SUBMISSION_TABLE);
        Schema::dropIfExists(self::FORM_TRANSLATION_TABLE);
        Schema::dropIfExists(self::FORM_TABLE);
        Schema::dropIfExists(self::URL_TRANSLATION_TABLE);
        Schema::dropIfExists(self::URL_TABLE);
        Schema::dropIfExists(self::MENU_ITEM_TRANSLATION_TABLE);
        Schema::dropIfExists(self::MENU_ITEM_TABLE);
        Schema::dropIfExists(self::MENU_TABLE);
        Schema::dropIfExists(self::BANNER_ITEM_TABLE);
        Schema::dropIfExists(self::BANNER_TABLE);
        Schema::dropIfExists(self::LAYOUT_WIDGET_TABLE);
        Schema::dropIfExists(self::LAYOUT_TABLE);
    }
};
