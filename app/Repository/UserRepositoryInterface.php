<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function updatePassword(int $id, string $newPassword);
}

?>