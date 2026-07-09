<?php

declare(strict_types=1);

namespace App\Models\Tenant\Definition\Catalog\ReviewStatus;

use App\DataTransferObjects\Tenant\Definition\Catalog\ReviewStatus\ReviewStatusDTO;
use App\Models\Tenant\TenantModel;
use Spatie\QueryBuilder\AllowedFilter;

class ReviewStatusModel extends TenantModel
{
    protected $table = 'def_cat_product_review_status';

    protected $primaryKey = 'review_status_id';

    protected static ?string $fieldSource = ReviewStatusDTO::class;

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
