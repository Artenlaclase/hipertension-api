<?php

namespace App\Enums;

enum LiquidType: string
{
    case Water = 'water';
    case Infusion = 'infusion';
    case Juice = 'juice';
    case Broth = 'broth';
    case Other = 'other';
}
