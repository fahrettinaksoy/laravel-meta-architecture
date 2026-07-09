<?php

declare(strict_types=1);

namespace App\Models\Tenant\Definition\General\VideoSource;

use App\DataTransferObjects\Tenant\Definition\General\VideoSource\VideoSourceDTO;
use App\Models\Tenant\TenantModel;
use Spatie\QueryBuilder\AllowedFilter;

class VideoSourceModel extends TenantModel
{
    protected $table = 'def_gen_video_source';

    protected $primaryKey = 'video_source_id';

    protected static ?string $fieldSource = VideoSourceDTO::class;

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
