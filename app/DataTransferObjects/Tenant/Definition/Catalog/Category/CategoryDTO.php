<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Tenant\Definition\Catalog\Category;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use App\DataTransferObjects\BaseDTO;

/**
 * def_cat_category ana tablosunun form/tablo alan tanımı.
 * Dile bağlı alanlar (name, summary, description, meta_*) çeviri DTO'sunda tutulur.
 */
class CategoryDTO extends BaseDTO
{
    public function __construct(
        #[FormField(type: 'number', sort_order: 1)]
        #[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
        #[ActionType(['index', 'show', 'destroy'])]
        public readonly ?int $category_id = null,

        #[FormField(type: 'select', sort_order: 2, relationship: ['model' => 'CategoryModel', 'label' => 'code'])]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $parent_id = null,

        #[FormField(type: 'text', sort_order: 3, required: true)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $code = null,

        #[FormField(type: 'image', sort_order: 4)]
        #[TableColumn(['showing'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $image_path = null,

        #[FormField(type: 'number', sort_order: 5)]
        #[TableColumn(['filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $layout_id = null,

        #[FormField(type: 'boolean', options: ['false' => 'no', 'true' => 'yes'], sort_order: 6)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_members_only = null,

        #[FormField(type: 'boolean', options: ['false' => 'passive', 'true' => 'active'], sort_order: 7)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_active = null,

        #[FormField(type: 'number', sort_order: 8)]
        #[TableColumn(['showing', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $sort_order = null,
    ) {}
}
