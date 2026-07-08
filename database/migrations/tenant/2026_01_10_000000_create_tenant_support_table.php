<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const TICKET_STATUS_TABLE = 'def_sup_ticket_status';
    private const FEEDBACK_STATUS_TABLE = 'def_sup_feedback_status';
    private const PRIORITY_TABLE = 'def_sup_priority';
    private const RELATION_TABLE = 'def_sup_relation';
    private const FAQ_GROUP_TABLE = 'sup_faq_group';
    private const FAQ_GROUP_TRANSLATION_TABLE = 'sup_faq_group_translation';
    private const FAQ_TABLE = 'sup_faq';
    private const FAQ_TRANSLATION_TABLE = 'sup_faq_translation';
    private const FAQ_FAQ_GROUP_TABLE = 'sup_faq_faq_group';
    private const KNOWLEDGE_CATEGORY_TABLE = 'sup_knowledge_category';
    private const KNOWLEDGE_CATEGORY_TRANSLATION_TABLE = 'sup_knowledge_category_translation';
    private const KNOWLEDGE_ARTICLE_TABLE = 'sup_knowledge_article';
    private const KNOWLEDGE_ARTICLE_TRANSLATION_TABLE = 'sup_knowledge_article_translation';
    private const KNOWLEDGE_ARTICLE_CATEGORY_TABLE = 'sup_knowledge_article_category';
    private const KNOWLEDGE_ARTICLE_IMAGE_TABLE = 'sup_knowledge_article_image';
    private const KNOWLEDGE_ARTICLE_VIDEO_TABLE = 'sup_knowledge_article_video';
    private const KNOWLEDGE_ARTICLE_RELATED_TABLE = 'sup_knowledge_article_related';
    private const FEEDBACK_TABLE = 'sup_feedback';
    private const TICKET_TABLE = 'sup_ticket';
    private const TICKET_MESSAGE_TABLE = 'sup_ticket_message';

    public function up(): void
    {
        Schema::create(self::FAQ_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('SSS grupları (sık sorulan soru başlıkları)');

            $table->bigIncrements('faq_group_id')->comment('SSS grubu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Grup kodu/slug — benzersiz');
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

        Schema::create(self::FAQ_GROUP_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('SSS grubu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('faq_group_translation_id')->comment('Grup çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('faq_group_id')->comment('Bağlı olduğu SSS grubu (sup_faq_group)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Grup adı');
            $table->string('description')->nullable()->comment('Grup açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['faq_group_id', 'language_code'], 'uq_faq_group_translation_lang');

            $table->foreign('faq_group_id')->references('faq_group_id')->on(self::FAQ_GROUP_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::FAQ_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sık sorulan sorular');

            $table->bigIncrements('faq_id')->comment('SSS için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Soru kodu/slug — benzersiz');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('membership')->default(false)->comment('Yalnızca üyelere açık mı?');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::FAQ_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('SSS çevirileri (dile göre soru ve cevap)');

            $table->bigIncrements('faq_translation_id')->comment('SSS çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('faq_id')->comment('Bağlı olduğu SSS (sup_faq)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('question')->comment('Soru metni');
            $table->text('answer')->nullable()->comment('Cevap metni (HTML içerebilir)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['faq_id', 'language_code'], 'uq_faq_translation_lang');

            $table->foreign('faq_id')->references('faq_id')->on(self::FAQ_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::FAQ_FAQ_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('SSS-grup eşleştirmeleri (soru birden fazla grupta yer alabilir)');

            $table->bigIncrements('faq_faq_group_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('faq_id')->comment('Bağlı olduğu SSS (sup_faq)');
            $table->unsignedBigInteger('faq_group_id')->comment('Bağlı olduğu SSS grubu (sup_faq_group)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['faq_id', 'faq_group_id'], 'uq_faq_faq_group');
            $table->index('faq_group_id');

            $table->foreign('faq_id')->references('faq_id')->on(self::FAQ_TABLE)->cascadeOnDelete();
            $table->foreign('faq_group_id')->references('faq_group_id')->on(self::FAQ_GROUP_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::KNOWLEDGE_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası kategorileri (kategori ağacı)');

            $table->bigIncrements('knowledge_category_id')->comment('Kategori için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Üst kategori kimliği (kendine referans); null ise kök kategori');
            $table->string('code', 64)->unique()->comment('Kategori kodu/slug — benzersiz');
            $table->string('image', 500)->nullable()->comment('Kategori görseli dosya yolu');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (site_layout tablosuna referans)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('membership')->default(false)->comment('Yalnızca üyelere açık mı?');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('parent_id');
            $table->index('status');

            $table->foreign('parent_id')->references('knowledge_category_id')->on(self::KNOWLEDGE_CATEGORY_TABLE)->nullOnDelete();
        });

        Schema::create(self::KNOWLEDGE_CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası kategorisi çevirileri (dile göre ad ve meta bilgileri)');

            $table->bigIncrements('knowledge_category_translation_id')->comment('Kategori çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('knowledge_category_id')->comment('Bağlı olduğu kategori (sup_knowledge_category)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Kategori adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->string('keyword')->nullable()->comment('Arama anahtar kelimeleri');
            $table->string('meta_title')->nullable()->comment('SEO meta başlık');
            $table->string('meta_description')->nullable()->comment('SEO meta açıklama');
            $table->string('meta_keyword')->nullable()->comment('SEO meta anahtar kelimeler');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['knowledge_category_id', 'language_code'], 'uq_knowledge_category_translation_lang');

            $table->foreign('knowledge_category_id')->references('knowledge_category_id')->on(self::KNOWLEDGE_CATEGORY_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::KNOWLEDGE_ARTICLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası makaleleri');

            $table->bigIncrements('knowledge_article_id')->comment('Makale için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Makale kodu/slug — benzersiz');
            $table->string('image', 500)->nullable()->comment('Kapak görseli dosya yolu');
            $table->unsignedBigInteger('viewed')->default(0)->comment('Görüntülenme sayısı');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (site_layout tablosuna referans)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('membership')->default(false)->comment('Yalnızca üyelere açık mı?');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::KNOWLEDGE_ARTICLE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Makale çevirileri (dile göre başlık, içerik ve meta bilgileri)');

            $table->bigIncrements('knowledge_article_translation_id')->comment('Makale çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('knowledge_article_id')->comment('Bağlı olduğu makale (sup_knowledge_article)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Makale başlığı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->longText('description')->nullable()->comment('Makale içeriği (HTML)');
            $table->string('tag')->nullable()->comment('Etiketler (virgülle ayrılmış)');
            $table->string('keyword')->nullable()->comment('Arama anahtar kelimeleri');
            $table->string('meta_title')->nullable()->comment('SEO meta başlık');
            $table->string('meta_description')->nullable()->comment('SEO meta açıklama');
            $table->string('meta_keyword')->nullable()->comment('SEO meta anahtar kelimeler');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['knowledge_article_id', 'language_code'], 'uq_knowledge_article_translation_lang');

            $table->foreign('knowledge_article_id')->references('knowledge_article_id')->on(self::KNOWLEDGE_ARTICLE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::KNOWLEDGE_ARTICLE_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Makale-kategori eşleştirmeleri');

            $table->bigIncrements('knowledge_article_category_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('knowledge_article_id')->comment('Bağlı olduğu makale (sup_knowledge_article)');
            $table->unsignedBigInteger('knowledge_category_id')->comment('Bağlı olduğu kategori (sup_knowledge_category)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['knowledge_article_id', 'knowledge_category_id'], 'uq_knowledge_article_category');
            $table->index('knowledge_category_id');

            $table->foreign('knowledge_article_id')->references('knowledge_article_id')->on(self::KNOWLEDGE_ARTICLE_TABLE)->cascadeOnDelete();
            $table->foreign('knowledge_category_id')->references('knowledge_category_id')->on(self::KNOWLEDGE_CATEGORY_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::KNOWLEDGE_ARTICLE_IMAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Makale görselleri (galeri)');

            $table->bigIncrements('knowledge_article_image_id')->comment('Makale görseli için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('knowledge_article_id')->comment('Bağlı olduğu makale (sup_knowledge_article)');
            $table->string('file', 500)->comment('Görsel dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('knowledge_article_id')->references('knowledge_article_id')->on(self::KNOWLEDGE_ARTICLE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::KNOWLEDGE_ARTICLE_VIDEO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Makale videoları');

            $table->bigIncrements('knowledge_article_video_id')->comment('Makale videosu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('knowledge_article_id')->comment('Bağlı olduğu makale (sup_knowledge_article)');
            $table->enum('source', ['code', 'url', 'file', 'embed'])->comment('Video kaynağı: code=platform kodu, url=bağlantı, file=dosya, embed=gömülü kod');
            $table->string('content', 500)->comment('Video içeriği (kaynağa göre kod, URL, dosya yolu veya embed)');
            $table->string('name', 150)->nullable()->comment('Video başlığı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('knowledge_article_id')->references('knowledge_article_id')->on(self::KNOWLEDGE_ARTICLE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::KNOWLEDGE_ARTICLE_RELATED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İlişkili makale eşleştirmeleri');

            $table->bigIncrements('knowledge_article_related_id')->comment('İlişkili makale kaydı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('knowledge_article_id')->comment('Bağlı olduğu makale (sup_knowledge_article)');
            $table->unsignedBigInteger('related_article_id')->comment('İlişkili makale (sup_knowledge_article)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['knowledge_article_id', 'related_article_id'], 'uq_knowledge_article_related');
            $table->index('related_article_id');

            $table->foreign('knowledge_article_id')->references('knowledge_article_id')->on(self::KNOWLEDGE_ARTICLE_TABLE)->cascadeOnDelete();
            $table->foreign('related_article_id')->references('knowledge_article_id')->on(self::KNOWLEDGE_ARTICLE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::FEEDBACK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Geri bildirimler (iletişim formu/öneri-şikayet kayıtları)');

            $table->bigIncrements('feedback_id')->comment('Geri bildirim için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Geri bildirim takip kodu — benzersiz');
            $table->string('name', 150)->comment('Gönderen adı');
            $table->string('email', 254)->nullable()->comment('Gönderen e-posta adresi');
            $table->string('subject')->comment('Konu başlığı');
            $table->text('message')->nullable()->comment('Geri bildirim metni');
            $table->unsignedBigInteger('assistant_id')->nullable()->comment('Atanan personel kimliği (sistem kullanıcı tablosuna referans)');
            $table->unsignedBigInteger('department_id')->nullable()->comment('İlgili departman (def_com_department tablosuna referans)');
            $table->unsignedBigInteger('priority_id')->nullable()->comment('Öncelik (def_sup_priority)');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Geri bildirim durumu (def_sup_feedback_status)');
            $table->unsignedTinyInteger('rating')->default(0)->comment('Memnuniyet puanı (1-5); 0 ise puanlanmamış');
            $table->boolean('is_read')->default(false)->comment('Kayıt okundu mu?');
            $table->string('ip_address', 45)->nullable()->comment('Kaydın oluşturulduğu IP adresi (IPv6 uyumlu)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('assistant_id');
            $table->index('department_id');

            $table->foreign('priority_id')->references('priority_id')->on(self::PRIORITY_TABLE)->nullOnDelete();
            $table->foreign('status_id')->references('feedback_status_id')->on(self::FEEDBACK_STATUS_TABLE)->nullOnDelete();
        });

        Schema::create(self::TICKET_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Destek talepleri (üye destek biletleri)');

            $table->bigIncrements('ticket_id')->comment('Destek talebi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Talep takip kodu — benzersiz');
            $table->string('subject')->comment('Konu başlığı');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Talebi açan üye (mbr_account tablosuna referans; FK üyelik modülünde eklenir)');
            $table->unsignedBigInteger('assistant_id')->nullable()->comment('Atanan personel kimliği (sistem kullanıcı tablosuna referans)');
            $table->unsignedBigInteger('department_id')->nullable()->comment('İlgili departman (def_com_department tablosuna referans)');
            $table->unsignedBigInteger('priority_id')->nullable()->comment('Öncelik (def_sup_priority)');
            $table->unsignedBigInteger('relation_id')->nullable()->comment('İlişki türü (def_sup_relation); talebin bağlandığı kayıt türü');
            $table->unsignedBigInteger('resource_id')->nullable()->comment('İlişkili kaydın kimliği (relation_id ile birlikte polimorfik referans)');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Talep durumu (def_sup_ticket_status)');
            $table->string('ip_address', 45)->nullable()->comment('Kaydın oluşturulduğu IP adresi (IPv6 uyumlu)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('assistant_id');
            $table->index('department_id');
            $table->index('status_id');

            $table->foreign('priority_id')->references('priority_id')->on(self::PRIORITY_TABLE)->nullOnDelete();
            $table->foreign('relation_id')->references('relation_id')->on(self::RELATION_TABLE)->nullOnDelete();
            $table->foreign('status_id')->references('ticket_status_id')->on(self::TICKET_STATUS_TABLE)->nullOnDelete();
        });

        Schema::create(self::TICKET_MESSAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Destek talebi yazışmaları (eski adı: ticket_meeting)');

            $table->bigIncrements('ticket_message_id')->comment('Yazışma için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('ticket_id')->comment('Bağlı olduğu destek talebi (sup_ticket)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Mesajı yazan üye (mbr_account tablosuna referans); null ise personel yazdı');
            $table->unsignedBigInteger('assistant_id')->nullable()->comment('Mesajı yazan personel kimliği (sistem kullanıcı tablosuna referans); null ise üye yazdı');
            $table->text('message')->comment('Mesaj metni');
            $table->boolean('is_read')->default(false)->comment('Mesaj karşı tarafça okundu mu?');
            $table->string('ip_address', 45)->nullable()->comment('Mesajın gönderildiği IP adresi (IPv6 uyumlu)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');

            $table->foreign('ticket_id')->references('ticket_id')->on(self::TICKET_TABLE)->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TICKET_MESSAGE_TABLE);
        Schema::dropIfExists(self::TICKET_TABLE);
        Schema::dropIfExists(self::FEEDBACK_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_RELATED_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_VIDEO_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_IMAGE_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_CATEGORY_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_CATEGORY_TABLE);
        Schema::dropIfExists(self::FAQ_FAQ_GROUP_TABLE);
        Schema::dropIfExists(self::FAQ_TRANSLATION_TABLE);
        Schema::dropIfExists(self::FAQ_TABLE);
        Schema::dropIfExists(self::FAQ_GROUP_TRANSLATION_TABLE);
        Schema::dropIfExists(self::FAQ_GROUP_TABLE);
    }
};
