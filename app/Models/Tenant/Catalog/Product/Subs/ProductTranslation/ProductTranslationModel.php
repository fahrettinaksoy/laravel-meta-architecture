<?php

declare(strict_types=1);

namespace App\Models\Tenant\Catalog\Product\Subs\ProductTranslation;

use App\DataTransferObjects\Tenant\Catalog\Product\Subs\ProductTranslation\ProductTranslationDTO;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslationModel extends TenantModel
{
    protected $table = 'cat_product_translation';

    protected $primaryKey = 'product_translation_id';

    protected static ?string $fieldSource = ProductTranslationDTO::class;

    protected array $allowedRelations = [
        'product',
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = 'language_code';

    protected $casts = [
        'language_code' => 'string',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}
