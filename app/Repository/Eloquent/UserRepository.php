<?php

namespace App\Repository\Eloquent;

use App\Repository\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    // public function updatePassword(int $id, string $newPassword)
    // {
    //     $item = $this->model->findOrFail($id);
    //     $item->update(['password' => $newPassword]);
    // }

    // public function getCriticByFilmId(int $id)
    // {
    //     $critics = $this->model->critics();
    //     return $critics->where('film_id', $id)->first();
    // }
}

?>