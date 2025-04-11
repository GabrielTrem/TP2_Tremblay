<?php

namespace App\Exceptions;

use Exception;

class NotAdminException extends Exception
{
    public function status()
    {
        return UNAUTHORIZED;
    }
    public function message()
    {
        return 'You are unauthorized to access this route because you are not a admin!';
    }
}
