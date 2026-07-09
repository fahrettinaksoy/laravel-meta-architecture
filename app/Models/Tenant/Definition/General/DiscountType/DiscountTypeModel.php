<?php

declare(strict_types=1);

namespace App\Models\Tenant\Definition\General\DiscountType;

use App\DataTransferObjects\Tenant\Definition\General\DiscountType\DiscountTypeDTO;
use App\Models\Tenant\TenantModel;
use Spatie\QueryBuilder\AllowedFilter;

class DiscountTypeModel extends TenantModel
{
    protected $table = 'def_gen_discount_type';

    protected $primaryKey = 'discount_type_id';

    protected static ?string $fieldSource = DiscountTypeDTO::class;

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
