<?php

declare(strict_types=1);

namespace App\Models\Tenant\Definition\Catalog\Brand;

use App\DataTransferObjects\Tenant\Definition\Catalog\Brand\BrandDTO;
use App\Models\Tenant\Catalog\Product\ProductModel;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

class BrandModel extends TenantModel
{
    protected $table = 'def_cat_brand';

    protected $primaryKey = 'brand_id';

    protected static ?string $fieldSource = BrandDTO::class;

    public function getAllowedFilters(): array
    {
        return [
            'code',
            AllowedFilter::exact('layout_id'),
            AllowedFilter::exact('is_active'),
        ];
    }

    protected array $allowedRelations = [
        'products',
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = 'sort_order';

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(ProductModel::class, 'brand_id');
    }
}
