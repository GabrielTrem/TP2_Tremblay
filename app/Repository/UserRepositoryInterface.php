<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function userHasCriticForFilm(string $user_id, string $film_id);
}

?>