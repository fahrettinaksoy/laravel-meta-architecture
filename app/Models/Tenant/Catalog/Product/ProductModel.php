<?php

declare(strict_types=1);

namespace App\Models\Tenant\Catalog\Product;

use App\DataTransferObjects\Tenant\Catalog\Product\ProductDTO;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Definition\Catalog\Brand\BrandModel;
use App\Models\Tenant\Definition\Catalog\Category\CategoryModel;
use App\Models\Tenant\Catalog\Product\Subs\ProductImage\ProductImageModel;
use App\Models\Tenant\Catalog\Product\Subs\ProductTranslation\ProductTranslationModel;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductModel extends TenantModel
{
    protected $table = 'cat_product';

    protected $primaryKey = 'product_id';

    protected static ?string $fieldSource = ProductDTO::class;

    public function getAllowedFilters(): array
    {
        return [
            'code',
            'sku',
            'barcode',
            'model',
            AllowedFilter::exact('category_id'),
            AllowedFilter::exact('brand_id'),
            AllowedFilter::exact('type_id'),
            AllowedFilter::exact('status_id'),
            AllowedFilter::exact('is_members_only'),
        ];
    }

    protected array $allowedRelations = [
        'category',
        'brand',
        'images',
        'translations',
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = '-created_at';

    protected $casts = [
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'cargo_price' => 'decimal:2',
        'weight' => 'decimal:4',
        'length' => 'decimal:4',
        'width' => 'decimal:4',
        'height' => 'decimal:4',
        'min_order_quantity' => 'decimal:4',
        'max_order_quantity' => 'decimal:4',
        'order_step' => 'decimal:4',
        'is_adult' => 'boolean',
        'is_domestic' => 'boolean',
        'is_stock_tracked' => 'boolean',
        'is_shipping_required' => 'boolean',
        'is_returnable' => 'boolean',
        'is_members_only' => 'boolean',
        'has_installment' => 'boolean',
        'produced_at' => 'datetime',
        'expires_at' => 'datetime',
        'published_start_at' => 'datetime',
        'published_end_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(BrandModel::class, 'brand_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImageModel::class, 'product_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslationModel::class, 'product_id');
    }
}
