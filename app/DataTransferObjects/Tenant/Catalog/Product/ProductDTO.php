<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Tenant\Catalog\Product;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use App\DataTransferObjects\BaseDTO;

/**
 * cat_product ana tablosunun form/tablo alan tanımı.
 * Dile bağlı alanlar (name, summary, description, meta_*) ProductTranslationDTO'da tutulur.
 */
class ProductDTO extends BaseDTO
{
    public function __construct(
        #[FormField(type: 'number', sort_order: 1)]
        #[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
        #[ActionType(['index', 'show', 'destroy'])]
        public readonly ?int $product_id = null,

        #[FormField(type: 'text', sort_order: 2, required: true)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $code = null,

        #[FormField(type: 'text', sort_order: 3)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $model = null,

        #[FormField(type: 'text', sort_order: 4)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $sku = null,

        #[FormField(type: 'text', sort_order: 5)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $barcode = null,

        #[FormField(type: 'select', sort_order: 6, relationship: ['model' => 'CategoryModel', 'label' => 'name'])]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $category_id = null,

        #[FormField(type: 'select', sort_order: 7, relationship: ['model' => 'BrandModel', 'label' => 'name'])]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $brand_id = null,

        #[FormField(type: 'number', sort_order: 8)]
        #[TableColumn(['filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $type_id = null,

        #[FormField(type: 'number', sort_order: 9)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $status_id = null,

        #[FormField(type: 'number', sort_order: 10, required: true)]
        #[TableColumn(['showing', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?float $sell_price = null,

        #[FormField(type: 'select', sort_order: 11, options: ['TRY' => 'TRY', 'USD' => 'USD', 'EUR' => 'EUR'])]
        #[TableColumn(['showing'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $sell_currency_code = null,

        #[FormField(type: 'number', sort_order: 12)]
        #[TableColumn([])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?float $buy_price = null,

        #[FormField(type: 'number', sort_order: 13)]
        #[TableColumn(['showing'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?float $discount_value = null,

        #[FormField(type: 'number', sort_order: 14)]
        #[TableColumn([])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?int $discount_type_id = null,

        #[FormField(type: 'number', sort_order: 15)]
        #[TableColumn([])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?int $sell_point = null,

        #[FormField(type: 'number', sort_order: 16)]
        #[TableColumn([])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?float $min_order_quantity = null,

        #[FormField(type: 'boolean', options: ['false' => 'no', 'true' => 'yes'], sort_order: 17)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_stock_tracked = null,

        #[FormField(type: 'boolean', options: ['false' => 'no', 'true' => 'yes'], sort_order: 18)]
        #[TableColumn(['showing'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_returnable = null,

        #[FormField(type: 'boolean', options: ['false' => 'no', 'true' => 'yes'], sort_order: 19)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_members_only = null,

        #[FormField(type: 'boolean', options: ['false' => 'no', 'true' => 'yes'], sort_order: 20)]
        #[TableColumn([])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_adult = null,

        #[FormField(type: 'image', sort_order: 21)]
        #[TableColumn(['showing'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $image_cover = null,

        #[FormField(type: 'number', sort_order: 22)]
        #[TableColumn(['showing', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $sort_order = null,

        #[FormField(type: 'datetime', sort_order: 23)]
        #[TableColumn([])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?string $published_start_at = null,

        #[FormField(type: 'datetime', sort_order: 24)]
        #[TableColumn([])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?string $published_end_at = null,
    ) {}
}
