<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const WAREHOUSE_TABLE = 'cat_warehouse';
    private const BLOCK_TABLE = 'def_cat_wh_block';
    private const AREA_TABLE = 'def_cat_wh_area';
    private const SHELF_TABLE = 'def_cat_wh_shelf';

    public function up(): void
    {
        Schema::create(self::BLOCK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Depo blokları: deponun ana bölümleri (depo tanım tablosu)');

            $table->bigIncrements('block_id')->comment('Blok için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('warehouse_id')->comment('Bağlı olduğu depo (cat_warehouse)');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Blok türü kimliği (tanım tablosuna referans)');
            $table->string('code', 64)->unique()->comment('Blok kodu — benzersiz');
            $table->string('name', 150)->comment('Blok adı');
            $table->string('authorized_name', 150)->nullable()->comment('Blok sorumlusunun adı');
            $table->decimal('width', 15, 4)->default(0)->comment('Genişlik değeri');
            $table->decimal('length', 15, 4)->default(0)->comment('Uzunluk değeri');
            $table->decimal('height', 15, 4)->default(0)->comment('Yükseklik değeri');
            $table->string('description')->nullable()->comment('Blok hakkında kısa açıklama');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
            $table->index('warehouse_id');
        });

        Schema::create(self::AREA_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Depo alanları: blok içindeki bölgeler (depo tanım tablosu)');

            $table->bigIncrements('area_id')->comment('Alan için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('warehouse_id')->comment('Bağlı olduğu depo (cat_warehouse)');
            $table->unsignedBigInteger('block_id')->nullable()->comment('Bağlı olduğu blok (def_cat_wh_block); null ise doğrudan depoya bağlı');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Alan türü kimliği (tanım tablosuna referans)');
            $table->string('code', 64)->unique()->comment('Alan kodu — benzersiz');
            $table->string('name', 150)->comment('Alan adı');
            $table->string('authorized_name', 150)->nullable()->comment('Alan sorumlusunun adı');
            $table->decimal('width', 15, 4)->default(0)->comment('Genişlik değeri');
            $table->decimal('length', 15, 4)->default(0)->comment('Uzunluk değeri');
            $table->decimal('height', 15, 4)->default(0)->comment('Yükseklik değeri');
            $table->string('description')->nullable()->comment('Alan hakkında kısa açıklama');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
            $table->index('warehouse_id');
            $table->index('block_id');
        });

        Schema::create(self::SHELF_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Depo rafları: alan içindeki raf lokasyonları (depo tanım tablosu)');

            $table->bigIncrements('shelf_id')->comment('Raf için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('warehouse_id')->comment('Bağlı olduğu depo (cat_warehouse)');
            $table->unsignedBigInteger('area_id')->nullable()->comment('Bağlı olduğu alan (def_cat_wh_area); null ise doğrudan depoya bağlı');
            $table->unsignedBigInteger('type_id')->nullable()->comment('Raf türü kimliği (tanım tablosuna referans)');
            $table->string('code', 64)->unique()->comment('Raf kodu — benzersiz');
            $table->string('name', 150)->comment('Raf adı');
            $table->string('authorized_name', 150)->nullable()->comment('Raf sorumlusunun adı');
            $table->decimal('width', 15, 4)->default(0)->comment('Genişlik değeri');
            $table->decimal('length', 15, 4)->default(0)->comment('Uzunluk değeri');
            $table->decimal('height', 15, 4)->default(0)->comment('Yükseklik değeri');
            $table->string('description')->nullable()->comment('Raf hakkında kısa açıklama');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
            $table->index('warehouse_id');
            $table->index('area_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::SHELF_TABLE);
        Schema::dropIfExists(self::AREA_TABLE);
        Schema::dropIfExists(self::BLOCK_TABLE);
    }
};
