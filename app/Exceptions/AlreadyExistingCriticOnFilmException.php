<?php

namespace App\Exceptions;

use Exception;

class AlreadyExistingCriticOnFilmException extends Exception
{
    public function status()
    {
        return UNAUTHORIZED;
    }
    public function message()
    {
        return 'You can not create more than 1 critic per film!';
    }
}
