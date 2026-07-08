<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\BaseAction;
use Illuminate\Database\Eloquent\Model;

class ShowAction extends BaseAction
{
    public function execute(int $id, array $includes = []): ?Model
    {
        return $this->repository->findById($id, $includes);
    }
}
