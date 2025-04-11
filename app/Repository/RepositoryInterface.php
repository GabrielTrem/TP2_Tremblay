<?php
namespace App\Repository;
use Illuminate\Database\Eloquent\Model;
interface RepositoryInterface
{
    public function create(array $content);
    public function getById(int $id);
    public function getAll(int $perPage = 0);
    public function update($id, array $content);
    public function delete(int $id);
}
?>