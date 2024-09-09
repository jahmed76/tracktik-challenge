<?php
namespace App\DTO;

enum Gender: string
{
    case MALE = 'M';
    case FEMALE = 'F';
    case BOTH = 'B';
    
    public static function isValid(?Gender $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'), true);
    }
}