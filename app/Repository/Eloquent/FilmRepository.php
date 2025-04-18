<?php

namespace App\Repository\Eloquent;

use App\Repository\FilmRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class FilmRepository extends BaseRepository implements FilmRepositoryInterface
{
    //https://laracasts.com/discuss/channels/laravel/remove-pivot-table-entries-when-deleting-records
    function deleteFilmInCascade(string $id){
        $film = $this->model->findOrFail($id); 
        $film->actors()->detach();
        $film->critics()->delete();
        $film->delete(); 
    }
}

?>