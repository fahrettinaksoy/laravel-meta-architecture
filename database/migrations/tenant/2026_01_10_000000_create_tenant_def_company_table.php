<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const BRANCH_TABLE = 'def_com_branch';
    private const DEPARTMENT_TABLE = 'def_com_department';
    private const DEPARTMENT_TRANSLATION_TABLE = 'def_com_department_translation';

    public function up(): void
    {
        Schema::create(self::BRANCH_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Şirket şubeleri: merkez, şube, mağaza vb. lokasyonlar (şirket tanım tablosu)');

            $table->bigIncrements('branch_id')->comment('Şube için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->string('code', 64)->unique()->comment('Şube kodu — benzersiz');
            $table->string('type', 50)->nullable()->comment('Şube türü (örn. merkez, şube, mağaza, ofis)');
            $table->string('name', 150)->comment('Şube adı');
            $table->string('company_name')->nullable()->comment('Şubenin bağlı olduğu şirket unvanı');
            $table->string('authorized_name', 150)->nullable()->comment('Şube yetkilisinin adı');
            $table->string('image', 500)->nullable()->comment('Şube görseli/logosu dosya yolu');
            $table->string('website')->nullable()->comment('Web sitesi adresi');
            $table->string('email', 254)->nullable()->comment('E-posta adresi');
            $table->string('phone_number', 20)->nullable()->comment('Sabit telefon numarası');
            $table->string('fax_number', 20)->nullable()->comment('Faks numarası');
            $table->string('gsm_number', 20)->nullable()->comment('Cep telefonu numarası');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği (def_loc_country tablosuna referans)');
            $table->unsignedBigInteger('city_id')->nullable()->comment('İl kimliği (def_loc_city tablosuna referans)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği (def_loc_district tablosuna referans)');
            $table->string('postcode', 10)->nullable()->comment('Posta kodu');
            $table->string('address_1')->nullable()->comment('Adres satırı 1 (cadde, sokak, bina)');
            $table->string('address_2')->nullable()->comment('Adres satırı 2 (daire, kat vb. ek bilgi)');
            $table->string('map_coordinate', 100)->nullable()->comment('Harita koordinatı (enlem,boylam)');
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
            $table->index('status');
        });

        Schema::create(self::DEPARTMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Şirket departmanları: satış, destek, muhasebe vb. (şirket tanım tablosu)');

            $table->bigIncrements('department_id')->comment('Departman için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('branch_id')->nullable()->comment('Bağlı olduğu şube (def_com_branch); null ise tüm şubelerde geçerli');
            $table->string('code', 64)->unique()->comment('Departman kodu — benzersiz');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('status')->default(true)->comment('Kayıt durumu: true=aktif, false=pasif');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->index('status');

            $table->foreign('branch_id')->references('branch_id')->on(self::BRANCH_TABLE)->nullOnDelete();
        });

        Schema::create(self::DEPARTMENT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Departman çevirileri (dile göre ad ve açıklama)');

            $table->bigIncrements('department_translation_id')->comment('Departman çevirisi için otomatik artan birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID kimliği');
            $table->unsignedBigInteger('department_id')->comment('Bağlı olduğu departman (def_com_department)');
            $table->char('language_code', 2)->default('tr')->comment('Çeviri dil kodu (ISO 639-1: tr, en)');
            $table->string('name')->comment('Departman adı');
            $table->string('description')->nullable()->comment('Departman açıklaması');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı son güncelleyen kullanıcı kimliği');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('Kaydı silen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma tarihi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme tarihi');
            $table->timestamp('deleted_at')->nullable()->comment('Yumuşak silme tarihi: null ise kayıt aktif');

            $table->unique(['department_id', 'language_code'], 'uq_department_translation_lang');

            $table->foreign('department_id')->references('department_id')->on(self::DEPARTMENT_TABLE)->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::DEPARTMENT_TRANSLATION_TABLE);
        Schema::dropIfExists(self::DEPARTMENT_TABLE);
        Schema::dropIfExists(self::BRANCH_TABLE);
    }
};
