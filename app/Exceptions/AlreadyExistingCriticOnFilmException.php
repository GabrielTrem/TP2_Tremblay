<?php

namespace App\Exceptions;

use Exception;

class AlreadyExistingCriticOnFilmException extends Exception
{
    public function status()
    {
        return CONFLICT;
    }
 
    public function message()
    {
        return 'You have already submitted a critique for this film!';
    }
}
