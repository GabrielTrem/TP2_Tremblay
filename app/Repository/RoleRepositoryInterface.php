<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;

interface RoleRepositoryInterface extends RepositoryInterface
{
    public function getIdByName(string $name);
}

?>