<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const ACCOUNT_TYPE_TABLE = 'def_mbr_account_type';
    private const ACCOUNT_TYPE_TRANSLATION_TABLE = 'def_mbr_account_type_translation';
    private const ACCOUNT_GROUP_TABLE = 'def_mbr_account_group';
    private const ACCOUNT_GROUP_TRANSLATION_TABLE = 'def_mbr_account_group_translation';
    private const ACCOUNT_AUTHORIZED_GROUP_TABLE = 'def_mbr_account_authorized_group';
    private const ACCOUNT_AUTHORIZED_GROUP_TRANSLATION_TABLE = 'def_mbr_account_authorized_group_translation';

    public function up(): void
    {
        Schema::create(self::ACCOUNT_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üye türleri (üyelik tanım tablosu; fiyat listesi ve abonelik planlarında kullanılır)');

            $table->bigIncrements('account_type_id')->comment('Üye türü için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Üye türü kodu — benzersiz');
            $table->enum('kind', ['individual', 'corporate'])->nullable()->comment('Geçerli olduğu hesap şekli: individual=bireysel, corporate=kurumsal; null ise her ikisi');
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

        Schema::create(self::ACCOUNT_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üye türü çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('account_type_translation_id')->comment('Üye türü çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_type_id')->comment('Bağlı olduğu üye türü (def_mbr_account_type)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Üye türü adı');
            $table->string('description')->nullable()->comment('Üye türü açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['account_type_id', 'language_code'], 'uq_account_type_translation_lang');
        });

        Schema::create(self::ACCOUNT_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üye grupları: fiyat listesi, kupon ve vergi kurallarında kullanılır (üyelik tanım tablosu; eski mimaride describing veritabanındaydı)');

            $table->bigIncrements('account_group_id')->comment('Üye grubu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Üye grubu kodu — benzersiz');
            $table->boolean('requires_approval')->default(false)->comment('Gruba katılım yönetici onayı gerektirir mi?');
            $table->string('icon', 100)->nullable()->comment('Arayüz ikonu (ikon sınıfı veya dosya yolu)');
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

        Schema::create(self::ACCOUNT_GROUP_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Üye grubu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('account_group_translation_id')->comment('Üye grubu çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_group_id')->comment('Bağlı olduğu üye grubu (def_mbr_account_group)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Üye grubu adı');
            $table->string('description')->nullable()->comment('Üye grubu açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['account_group_id', 'language_code'], 'uq_account_group_translation_lang');
        });

        Schema::create(self::ACCOUNT_AUTHORIZED_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Yetkili kişi grupları (kurumsal hesap yetkililerinin izin şablonları)');

            $table->bigIncrements('account_authorized_group_id')->comment('Yetkili grubu için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Yetkili grubu kodu — benzersiz');
            $table->json('permissions')->nullable()->comment('Grubun yetki kapsamları (JSON dizi)');
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

        Schema::create(self::ACCOUNT_AUTHORIZED_GROUP_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Yetkili grubu çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('account_authorized_group_translation_id')->comment('Yetkili grubu çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('account_authorized_group_id')->comment('Bağlı olduğu yetkili grubu (def_mbr_account_authorized_group)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Yetkili grubu adı');
            $table->string('description')->nullable()->comment('Yetkili grubu açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['account_authorized_group_id', 'language_code'], 'uq_account_authorized_group_translation_lang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::ACCOUNT_AUTHORIZED_GROUP_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ACCOUNT_AUTHORIZED_GROUP_TABLE);
        Schema::dropIfExists(self::ACCOUNT_GROUP_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ACCOUNT_GROUP_TABLE);
        Schema::dropIfExists(self::ACCOUNT_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ACCOUNT_TYPE_TABLE);
    }
};
