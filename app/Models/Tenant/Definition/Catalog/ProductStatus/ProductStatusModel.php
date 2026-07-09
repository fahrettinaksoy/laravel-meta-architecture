<?php

declare(strict_types=1);

namespace App\Models\Tenant\Definition\Catalog\ProductStatus;

use App\DataTransferObjects\Tenant\Definition\Catalog\ProductStatus\ProductStatusDTO;
use App\Models\Tenant\TenantModel;
use Spatie\QueryBuilder\AllowedFilter;

class ProductStatusModel extends TenantModel
{
    protected $table = 'def_cat_product_status';

    protected $primaryKey = 'product_status_id';

    protected static ?string $fieldSource = ProductStatusDTO::class;

    public function getAllowedFilters(): array
    {
        return [
            'code',
            AllowedFilter::exact('is_active'),
        ];
    }

    protected array $allowedRelations = [
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = 'sort_order';

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
