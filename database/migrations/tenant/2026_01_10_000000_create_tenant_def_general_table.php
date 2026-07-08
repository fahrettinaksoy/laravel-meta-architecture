<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const BANK_TABLE = 'def_gen_bank';
    private const CARGO_TABLE = 'def_gen_cargo';

    public function up(): void
    {
        Schema::create(self::BANK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Banka tanımları (genel tanım tablosu; üyelik ve muhasebe modüllerinde kullanılır)');

            $table->bigIncrements('bank_id')->comment('Banka için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Banka kodu — benzersiz');
            $table->string('type', 50)->nullable()->comment('Banka türü (örn. kamu, özel, katılım)');
            $table->string('name', 150)->comment('Banka adı');
            $table->string('eft_code', 20)->nullable()->comment('EFT banka kodu');
            $table->string('swift_code', 20)->nullable()->comment('SWIFT/BIC kodu');
            $table->string('telex_code', 20)->nullable()->comment('Teleks kodu');
            $table->string('image', 500)->nullable()->comment('Banka logosu dosya yolu');
            $table->string('website')->nullable()->comment('Web sitesi adresi');
            $table->string('email', 254)->nullable()->comment('E-posta adresi');
            $table->string('call_center_number', 20)->nullable()->comment('Çağrı merkezi numarası');
            $table->string('phone_number', 20)->nullable()->comment('Telefon numarası');
            $table->string('fax_number', 20)->nullable()->comment('Faks numarası');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });

        Schema::create(self::CARGO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kargo firması tanımları (genel tanım tablosu; sevkiyat işlemlerinde kullanılır)');

            $table->bigIncrements('cargo_id')->comment('Kargo firması için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Kargo firması kodu — benzersiz');
            $table->string('name', 150)->comment('Kargo firması adı');
            $table->string('image', 500)->nullable()->comment('Firma logosu dosya yolu');
            $table->string('website')->nullable()->comment('Web sitesi adresi');
            $table->string('email', 254)->nullable()->comment('E-posta adresi');
            $table->string('call_center_number', 20)->nullable()->comment('Çağrı merkezi numarası');
            $table->string('phone_number', 20)->nullable()->comment('Telefon numarası');
            $table->string('fax_number', 20)->nullable()->comment('Faks numarası');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::CARGO_TABLE);
        Schema::dropIfExists(self::BANK_TABLE);
    }
};
