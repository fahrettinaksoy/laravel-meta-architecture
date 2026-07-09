<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Tenant\Definition\General\DiscountType;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use App\DataTransferObjects\BaseDTO;

/**
 * def_cat_discount_type tanım (lookup) tablosunun form/tablo alan tanımı.
 * Görünen ad (name) çeviri DTO'sunda tutulur.
 */
class DiscountTypeDTO extends BaseDTO
{
    public function __construct(
        #[FormField(type: 'number', sort_order: 1)]
        #[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
        #[ActionType(['index', 'show', 'destroy'])]
        public readonly ?int $discount_type_id = null,

        #[FormField(type: 'text', sort_order: 2, required: true)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $code = null,

        #[FormField(type: 'boolean', options: ['false' => 'passive', 'true' => 'active'], sort_order: 3)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_active = null,

        #[FormField(type: 'number', sort_order: 4)]
        #[TableColumn(['showing', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $sort_order = null,
    ) {}
}
