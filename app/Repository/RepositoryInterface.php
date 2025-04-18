<?php
namespace App\Repository;
use Illuminate\Database\Eloquent\Model;
interface RepositoryInterface
{
    public function create(array $content) : Model;
    public function getById(int $id) : ?Model;
    public function getAll(int $perPage = 0);
    public function update(int $id, array $content) : Model;
    public function delete(int $id);
}
?>