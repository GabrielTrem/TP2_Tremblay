<?php

namespace App\Repository\Eloquent;

use App\Repository\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function userHasCriticForFilm(string $user_id, string $film_id)
    {
        $user = $this->model->findOrFail($user_id);
        $critics = $user->critics();
        return $critics->where('film_id', $film_id)->exists();
    }
}

?>