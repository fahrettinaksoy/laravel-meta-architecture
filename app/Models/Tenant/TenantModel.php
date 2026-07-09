<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use App\Models\BaseModel;

/**
 * Tenant (kiracı) veritabanındaki tablolar için ortak base model.
 * BaseModel'in lessor bağlantısını (conn_lsr) tenant bağlantısıyla (conn_tnt) geçersiz kılar.
 *
 * SoftDeletes BaseModel'de opt-in'dir: deleted_at kolonu olan tenant modelleri
 * kendi sınıfında `use SoftDeletes;` ekler.
 */
abstract class TenantModel extends BaseModel
{
    protected $connection = 'conn_tnt';
}
