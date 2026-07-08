<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\DestroyAction;
use App\Actions\IndexAction;
use App\Actions\ShowAction;
use App\Actions\StoreAction;
use App\Actions\UpdateAction;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BaseService
{
    public function __construct(
        protected BaseRepositoryInterface $repository,
        protected array $actions = [],
    ) {
        $defaults = [
            'index' => new IndexAction($this->repository),
            'show' => new ShowAction($this->repository),
            'store' => new StoreAction($this->repository),
            'update' => new UpdateAction($this->repository),
            'destroy' => new DestroyAction($this->repository),
        ];

        $this->actions = array_merge($defaults, $this->actions);
    }

    public function index(array $queryContext = []): LengthAwarePaginator
    {
        return $this->actions['index']->execute($queryContext);
    }

    public function show(int $id, array $includes = []): Model
    {
        return $this->actions['show']->execute($id, $includes);
    }

    public function store(array $data): Model
    {
        return DB::transaction(fn () => $this->actions['store']->execute($data));
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(fn () => $this->actions['update']->execute($id, $data));
    }

    public function destroy(int $id): bool
    {
        return DB::transaction(fn () => $this->actions['destroy']->execute($id));
    }

    public function destroyMany(array $criteria): int
    {
        return DB::transaction(fn () => $this->actions['destroy']->executeWithFilter($criteria));
    }
}
