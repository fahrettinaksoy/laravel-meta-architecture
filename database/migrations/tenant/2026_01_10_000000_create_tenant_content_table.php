<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CATEGORY_TABLE = 'cnt_category';
    private const CATEGORY_TRANSLATION_TABLE = 'cnt_category_translation';
    private const POST_TABLE = 'cnt_post';
    private const POST_TRANSLATION_TABLE = 'cnt_post_translation';
    private const POST_CATEGORY_TABLE = 'cnt_post_category';
    private const POST_IMAGE_TABLE = 'cnt_post_image';
    private const POST_VIDEO_TABLE = 'cnt_post_video';
    private const POST_RELATED_TABLE = 'cnt_post_related';
    private const POST_PRODUCT_TABLE = 'cnt_post_product';
    private const COMMENT_TABLE = 'cnt_comment';
    private const PAGE_TABLE = 'cnt_page';
    private const PAGE_TRANSLATION_TABLE = 'cnt_page_translation';
    private const PAGE_IMAGE_TABLE = 'cnt_page_image';
    private const PAGE_VIDEO_TABLE = 'cnt_page_video';

    public function up(): void
    {
        Schema::create(self::CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İçerik kategorileri (blog/haber kategori ağacı)');

            $table->bigIncrements('category_id')->comment('Kategori için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Üst kategori kimliği (kendine referans); null ise kök kategori');
            $table->string('code', 64)->unique()->comment('Kategori kodu/slug — benzersiz');
            $table->string('image', 500)->nullable()->comment('Kategori görseli dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('membership')->default(false)->comment('Yalnızca üyelere açık mı?');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (tanım tablosuna referans)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('parent_id');
            $table->index('status');

            $table->foreign('parent_id')->references('category_id')->on(self::CATEGORY_TABLE)->nullOnDelete();
        });

        Schema::create(self::CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kategori çevirileri (dile göre ad, açıklama ve meta bilgileri)');

            $table->bigIncrements('category_translation_id')->comment('Kategori çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('category_id')->comment('Bağlı olduğu kategori (cnt_category)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Kategori adı');
            $table->string('summary')->nullable()->comment('Kısa özet');
            $table->text('description')->nullable()->comment('Kategori açıklaması (HTML içerebilir)');
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

            $table->unique(['category_id', 'language_code'], 'uq_category_translation_lang');

            $table->foreign('category_id')->references('category_id')->on(self::CATEGORY_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::POST_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İçerik gönderileri (blog/haber yazıları)');

            $table->bigIncrements('post_id')->comment('Gönderi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Gönderi kodu/slug — benzersiz');
            $table->string('image', 500)->nullable()->comment('Kapak görseli dosya yolu');
            $table->unsignedBigInteger('viewed')->default(0)->comment('Görüntülenme sayısı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (tanım tablosuna referans)');
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

        Schema::create(self::POST_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Gönderi çevirileri (dile göre başlık, içerik ve meta bilgileri)');

            $table->bigIncrements('post_translation_id')->comment('Gönderi çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('post_id')->comment('Bağlı olduğu gönderi (cnt_post)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Gönderi başlığı');
            $table->string('summary')->nullable()->comment('Kısa özet/spot');
            $table->longText('description')->nullable()->comment('Gönderi içeriği (HTML)');
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

            $table->unique(['post_id', 'language_code'], 'uq_post_translation_lang');

            $table->foreign('post_id')->references('post_id')->on(self::POST_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::POST_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Gönderi-kategori eşleşmeleri (çoka-çok bağlantı tablosu)');

            $table->bigIncrements('post_category_id')->comment('Bağlantı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('post_id')->comment('Bağlı olduğu gönderi (cnt_post)');
            $table->unsignedBigInteger('category_id')->comment('Bağlı olduğu kategori (cnt_category)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['post_id', 'category_id'], 'uq_post_category_post_category');
            $table->index('category_id');

            $table->foreign('post_id')->references('post_id')->on(self::POST_TABLE)->cascadeOnDelete();
            $table->foreign('category_id')->references('category_id')->on(self::CATEGORY_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::POST_IMAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Gönderi galeri görselleri');

            $table->bigIncrements('post_image_id')->comment('Görsel için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('post_id')->comment('Bağlı olduğu gönderi (cnt_post)');
            $table->string('file', 500)->comment('Görsel dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('post_id')->references('post_id')->on(self::POST_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::POST_VIDEO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Gönderi videoları (gömülü kod, URL, dosya veya embed)');

            $table->bigIncrements('post_video_id')->comment('Video için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('post_id')->comment('Bağlı olduğu gönderi (cnt_post)');
            $table->enum('source', ['code', 'url', 'file', 'embed'])->default('url')->comment('Video kaynağı: code=gömülü kod, url=bağlantı, file=dosya, embed=iframe');
            $table->string('content')->comment('Kaynağa göre kod/URL/dosya yolu');
            $table->string('name')->nullable()->comment('Video başlığı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('post_id')->references('post_id')->on(self::POST_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::POST_RELATED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İlişkili gönderi bağlantıları (bir gönderiye önerilen diğer gönderiler)');

            $table->bigIncrements('post_related_id')->comment('İlişki için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('post_id')->comment('Kaynak gönderi (cnt_post)');
            $table->unsignedBigInteger('related_id')->comment('İlişkili gönderi (cnt_post)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['post_id', 'related_id'], 'uq_post_related_post_related');
            $table->index('related_id');

            $table->foreign('post_id')->references('post_id')->on(self::POST_TABLE)->cascadeOnDelete();
            $table->foreign('related_id')->references('post_id')->on(self::POST_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::POST_PRODUCT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Gönderi-ürün eşleşmeleri (içerikte tanıtılan ürünler)');

            $table->bigIncrements('post_product_id')->comment('Bağlantı için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('post_id')->comment('Bağlı olduğu gönderi (cnt_post)');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği (katalog tablosuna referans; FK katalog modülünde eklenir)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['post_id', 'product_id'], 'uq_post_product_post_product');
            $table->index('product_id');

            $table->foreign('post_id')->references('post_id')->on(self::POST_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::COMMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Gönderi yorumları ve değerlendirmeleri');

            $table->bigIncrements('comment_id')->comment('Yorum için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('post_id')->comment('Bağlı olduğu gönderi (cnt_post)');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Yorumu yapan üye kimliği (mbr_account; FK üyelik modülünde eklenir); null ise misafir');
            $table->string('code', 64)->nullable()->comment('Yorum takip kodu');
            $table->string('author', 150)->comment('Yorum yapanın görünen adı');
            $table->text('content')->comment('Yorum metni');
            $table->unsignedTinyInteger('rating')->default(0)->comment('Puan (0-5 arası)');
            $table->boolean('status')->default(false)->comment('Onay durumu: true=onaylı/yayında, false=onay bekliyor');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('account_id');
            $table->index('status');

            $table->foreign('post_id')->references('post_id')->on(self::POST_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Statik içerik sayfaları (kurumsal/yasal sayfalar)');

            $table->bigIncrements('page_id')->comment('Sayfa için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->boolean('membership')->default(false)->comment('Yalnızca üyelere açık mı?');
            $table->boolean('legal')->default(false)->comment('Yasal sayfa mı? (gizlilik, KVKK, sözleşme vb.)');
            $table->string('code', 64)->unique()->comment('Sayfa kodu/slug — benzersiz');
            $table->string('html')->nullable()->comment('Özel HTML şablon/gövde tanımı');
            $table->string('image', 500)->nullable()->comment('Sayfa görseli dosya yolu');
            $table->unsignedBigInteger('viewed')->default(0)->comment('Görüntülenme sayısı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->unsignedBigInteger('layout_id')->nullable()->comment('Sayfa şablonu/düzeni kimliği (tanım tablosuna referans)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::PAGE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sayfa çevirileri (dile göre başlık, içerik ve meta bilgileri)');

            $table->bigIncrements('page_translation_id')->comment('Sayfa çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('page_id')->comment('Bağlı olduğu sayfa (cnt_page)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Sayfa başlığı');
            $table->string('summary')->nullable()->comment('Kısa özet/spot');
            $table->longText('description')->nullable()->comment('Sayfa içeriği (HTML)');
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

            $table->unique(['page_id', 'language_code'], 'uq_page_translation_lang');

            $table->foreign('page_id')->references('page_id')->on(self::PAGE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PAGE_IMAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sayfa galeri görselleri');

            $table->bigIncrements('page_image_id')->comment('Görsel için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('page_id')->comment('Bağlı olduğu sayfa (cnt_page)');
            $table->string('file', 500)->comment('Görsel dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('page_id')->references('page_id')->on(self::PAGE_TABLE)->cascadeOnDelete();
        });

        Schema::create(self::PAGE_VIDEO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sayfa videoları (gömülü kod, URL, dosya veya embed)');

            $table->bigIncrements('page_video_id')->comment('Video için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('page_id')->comment('Bağlı olduğu sayfa (cnt_page)');
            $table->enum('source', ['code', 'url', 'file', 'embed'])->default('url')->comment('Video kaynağı: code=gömülü kod, url=bağlantı, file=dosya, embed=iframe');
            $table->string('content')->comment('Kaynağa göre kod/URL/dosya yolu');
            $table->string('name')->nullable()->comment('Video başlığı');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->foreign('page_id')->references('page_id')->on(self::PAGE_TABLE)->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::PAGE_VIDEO_TABLE);
        Schema::dropIfExists(self::PAGE_IMAGE_TABLE);
        Schema::dropIfExists(self::PAGE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PAGE_TABLE);
        Schema::dropIfExists(self::COMMENT_TABLE);
        Schema::dropIfExists(self::POST_PRODUCT_TABLE);
        Schema::dropIfExists(self::POST_RELATED_TABLE);
        Schema::dropIfExists(self::POST_VIDEO_TABLE);
        Schema::dropIfExists(self::POST_IMAGE_TABLE);
        Schema::dropIfExists(self::POST_CATEGORY_TABLE);
        Schema::dropIfExists(self::POST_TRANSLATION_TABLE);
        Schema::dropIfExists(self::POST_TABLE);
        Schema::dropIfExists(self::CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CATEGORY_TABLE);
    }
};
