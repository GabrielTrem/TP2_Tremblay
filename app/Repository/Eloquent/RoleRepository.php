<?php

namespace App\Repository\Eloquent;

use App\Repository\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getIdByName(string $name)
    {
        return $this->model->where('name', $name)->first()->id;
    }
}

?>