<?php

namespace App\Repository\Eloquent;

use App\Repository\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function getIdByName(string $name)
    {
        return $this->model->where('name', $name)->first()->id;
    }
}

?>