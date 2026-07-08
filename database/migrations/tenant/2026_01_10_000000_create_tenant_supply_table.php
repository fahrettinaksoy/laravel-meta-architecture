<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const MATCHING_TABLE = 'spl_matching';

    public function up(): void
    {
        Schema::create(self::MATCHING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tedarik eşleştirmeleri (tedarikçi kaynağındaki kayıt ↔ yerel modül kaydı)');

            $table->bigIncrements('matching_id')->comment('Eşleştirme için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('target_module', 100)->comment('Hedef modül türü (örn. product, category, brand)');
            $table->unsignedBigInteger('target_id')->nullable()->comment('Hedef kaydın kimliği (target_module ile birlikte polimorfik referans)');
            $table->string('target_code', 100)->nullable()->comment('Hedef kaydın kodu (anlık kopya)');
            $table->json('target_content')->nullable()->comment('Hedef kayda uygulanacak/uygulanan veri (JSON)');
            $table->string('source_provider', 100)->comment('Kaynak sağlayıcı kodu (örn. tedarikçi XML/API adı)');
            $table->string('source_id', 100)->nullable()->comment('Kaynak sistemdeki kayıt kimliği');
            $table->string('source_code', 100)->nullable()->comment('Kaynak sistemdeki kayıt kodu');
            $table->json('source_content')->nullable()->comment('Kaynaktan gelen ham veri (JSON)');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index(['target_module', 'target_id'], 'idx_matching_target');
            $table->index(['source_provider', 'source_id'], 'idx_matching_source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::MATCHING_TABLE);
    }
};
