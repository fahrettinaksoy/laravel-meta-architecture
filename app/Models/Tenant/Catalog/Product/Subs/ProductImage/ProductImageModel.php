<?php

declare(strict_types=1);

namespace App\Models\Tenant\Catalog\Product\Subs\ProductImage;

use App\DataTransferObjects\Tenant\Catalog\Product\Subs\ProductImage\ProductImageDTO;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImageModel extends TenantModel
{
    protected $table = 'cat_product_image';

    protected $primaryKey = 'product_image_id';

    protected static ?string $fieldSource = ProductImageDTO::class;

    protected array $allowedRelations = [
        'product',
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = 'sort_order';

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}
