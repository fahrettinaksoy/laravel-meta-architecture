<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\BaseAction;
use Illuminate\Database\Eloquent\Model;

class StoreAction extends BaseAction
{
    public function execute(array $data): Model
    {
        return $this->repository->create($data);
    }
}
