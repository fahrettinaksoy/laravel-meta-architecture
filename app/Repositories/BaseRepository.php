<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function paginate(array $queryContext = []): LengthAwarePaginator
    {
        $perPage = $queryContext['limit'] ?? $queryContext['per_page'] ?? 15;

        return $this->queryBuilder()
            ->paginate($perPage)
            ->appends(request()->query());
    }

    public function all(array $queryContext = []): Collection
    {
        return $this->queryBuilder()->get();
    }

    public function findById(int $id, array $includes = []): ?Model
    {
        $query = $this->queryBuilder(withRequestFilters: false);

        if (! empty($includes)) {
            $query->with($includes);
        }

        return $query->where($this->model->getKeyName(), $id)->firstOrFail();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $item = $this->model->newQuery()->findOrFail($id);
        $item->update($data);

        return $item->refresh();
    }

    public function delete(int $id): bool
    {
        $item = $this->model->newQuery()->findOrFail($id);

        return $item->delete();
    }

    public function deleteMany(array $criteria): int
    {
        $query = $this->model->newQuery();

        if (! empty($criteria['ids'])) {
            $criteria[$this->model->getKeyName()] = $criteria['ids'];
            unset($criteria['ids']);
        }

        foreach ($criteria as $field => $value) {
            is_array($value)
                ? $query->whereIn($field, $value)
                : $query->where($field, $value);
        }

        return $query->delete();
    }

    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    public function getBy(string $field, mixed $value): Collection
    {
        return $this->model->where($field, $value)->get();
    }

    /**
     * Build a spatie/laravel-query-builder instance configured from the model's
     * allowed filters/sorts/includes/fields. Reads the current request for
     * ?filter, ?sort, ?include and ?fields parameters.
     *
     * @param bool $withRequestFilters When false, request-driven filters and
     *                                 sorts are skipped (e.g. single-record show).
     */
    protected function queryBuilder(bool $withRequestFilters = true): QueryBuilder
    {
        $builder = QueryBuilder::for($this->model->newQuery());

        if (! $this->model instanceof BaseModel) {
            return $builder;
        }

        $builder
            ->allowedIncludes(...$this->model->getAllowedIncludes())
            ->defaultSort($this->model->getDefaultSort());

        if ($withRequestFilters) {
            $builder
                ->allowedFilters(...$this->model->getAllowedFilters())
                ->allowedSorts(...$this->model->getAllowedSorts());
        }

        $allowedFields = $this->model->getAllowedFields();

        if (! empty($allowedFields)) {
            $builder->allowedFields(...$allowedFields);
        }

        $defaultRelations = $this->model->getDefaultRelations();

        if (! empty($defaultRelations)) {
            $builder->with($defaultRelations);
        }

        return $builder;
    }
}
