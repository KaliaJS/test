<?php

namespace App\Enums;

enum UserRole: int
{
    case ADMIN = 1;
    case TERMINAL = 2;
    case EMPLOYEE = 3;
}
