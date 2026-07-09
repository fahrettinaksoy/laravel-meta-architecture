<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Tenant\Catalog\Product\Subs\ProductImage;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use App\DataTransferObjects\BaseDTO;

class ProductImageDTO extends BaseDTO
{
    public function __construct(
        #[FormField(type: 'number', sort_order: 1)]
        #[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
        #[ActionType(['index', 'show', 'destroy'])]
        public readonly ?int $product_image_id = null,

        #[FormField(type: 'select', sort_order: 2, relationship: ['model' => 'ProductModel', 'label' => 'name'])]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $product_id = null,

        #[FormField(type: 'select', sort_order: 3, relationship: ['model' => 'ProductVariantStockModel', 'label' => 'sku'])]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $product_variant_stock_id = null,

        #[FormField(type: 'image', sort_order: 4)]
        #[TableColumn(['showing'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $file_path = null,

        #[FormField(type: 'text', sort_order: 5)]
        #[TableColumn(['showing'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $alt_text = null,

        #[FormField(type: 'number', sort_order: 6)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $sort_order = null,
    ) {}
}
