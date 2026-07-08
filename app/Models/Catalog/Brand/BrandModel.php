<?php

declare(strict_types=1);

namespace App\Models\Catalog\Brand;

use App\DataTransferObjects\Catalog\Brand\BrandDTO;
use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

class BrandModel extends BaseModel
{
    protected $table = 'cat_brand';

    protected $primaryKey = 'brand_id';

    protected static ?string $fieldSource = BrandDTO::class;

    public function getAllowedFilters(): array
    {
        return [
            'name',
            'slug',
            'description',
            'website',
            AllowedFilter::exact('is_active'),
            AllowedFilter::trashed(),
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
