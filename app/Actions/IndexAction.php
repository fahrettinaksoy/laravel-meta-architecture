<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\BaseAction;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexAction extends BaseAction
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }
}
