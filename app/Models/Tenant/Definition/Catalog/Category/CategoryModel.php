<?php

declare(strict_types=1);

namespace App\Models\Tenant\Definition\Catalog\Category;

use App\DataTransferObjects\Tenant\Definition\Catalog\Category\CategoryDTO;
use App\Models\Tenant\Catalog\Product\ProductModel;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

class CategoryModel extends TenantModel
{
    protected $table = 'def_cat_category';

    protected $primaryKey = 'category_id';

    protected static ?string $fieldSource = CategoryDTO::class;

    public function getAllowedFilters(): array
    {
        return [
            'code',
            AllowedFilter::exact('parent_id'),
            AllowedFilter::exact('is_active'),
            AllowedFilter::exact('is_members_only'),
        ];
    }

    protected array $allowedRelations = [
        'parent',
        'children',
        'products',
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = 'sort_order';

    protected $casts = [
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'is_members_only' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CategoryModel::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductModel::class, 'category_id');
    }
}
