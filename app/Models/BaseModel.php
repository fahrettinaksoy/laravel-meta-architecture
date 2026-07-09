<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use App\Observers\BaseModelObserver;
use App\Traits\HasFieldMetadata;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class BaseModel extends Model
{
    use HasFactory;
    use HasFieldMetadata;

    // SoftDeletes artık opt-in'dir: deleted_at kolonu olan modeller kendi sınıfında
    // `use SoftDeletes;` ekler (örn. def_cat_* tanım modelleri). deleted_at içermeyen
    // tablolar (katalog cat_* ve append-only ledger) trait kullanmaz.

    protected $connection = 'conn_lsr';

    protected static ?string $fieldSource = null;

    protected $fillable = [];

    protected array $allowedFiltering = [];

    protected array $allowedSorting = [];

    protected array $allowedShowing = [];

    protected array $allowedRelations = [];

    protected array $defaultRelations = [];

    protected string $defaultSorting = '-created_at';

    public $keyType = 'int';

    public $incrementing = true;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected static function booted(): void
    {
        static::observe(BaseModelObserver::class);
    }

    public function getRouteKeyName(): string
    {
        return $this->getKeyName();
    }

    /**
     * Filters exposed to spatie/laravel-query-builder (?filter[...]).
     * Models may override to return AllowedFilter instances.
     */
    public function getAllowedFilters(): array
    {
        return $this->allowedFiltering;
    }

    /**
     * Sorts exposed to spatie/laravel-query-builder (?sort=...).
     */
    public function getAllowedSorts(): array
    {
        return $this->allowedSorting;
    }

    /**
     * Includes exposed to spatie/laravel-query-builder (?include=...).
     */
    public function getAllowedIncludes(): array
    {
        return $this->allowedRelations;
    }

    /**
     * Sparse fieldset whitelist for spatie/laravel-query-builder (?fields=...).
     */
    public function getAllowedFields(): array
    {
        return $this->allowedShowing;
    }

    /**
     * Default sort applied when no ?sort= is present.
     */
    public function getDefaultSort(): string
    {
        return $this->defaultSorting;
    }

    /**
     * Relations eager-loaded on every query.
     */
    public function getDefaultRelations(): array
    {
        return $this->defaultRelations;
    }

    /**
     * Relation whitelist used by pivot route resolution for security.
     */
    public function getAllowedRelations(): array
    {
        return $this->allowedRelations;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
