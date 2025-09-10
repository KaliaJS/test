<?php

namespace App\Enums;

enum ProductType: int
{
    case BURGER = 1;
    case SNACK = 2;
    case DRINK = 3;
    case DESSERT = 4;
    case SAUCE = 5;
}
