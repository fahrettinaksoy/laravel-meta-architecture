<?php

declare(strict_types=1);

namespace App\Models\Catalog\Category;

use App\DataTransferObjects\Catalog\Category\CategoryDTO;
use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

class CategoryModel extends BaseModel
{
    protected $table = 'cat_category';

    protected $primaryKey = 'category_id';

    protected static ?string $fieldSource = CategoryDTO::class;

    public function getAllowedFilters(): array
    {
        return [
            'name',
            'slug',
            'description',
            AllowedFilter::exact('parent_id'),
            AllowedFilter::exact('is_active'),
            AllowedFilter::trashed(),
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
