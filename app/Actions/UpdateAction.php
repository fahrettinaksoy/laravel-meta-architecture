<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\BaseAction;
use Illuminate\Database\Eloquent\Model;

class UpdateAction extends BaseAction
{
    public function execute(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }
}
