<?php
namespace App\DTO;

enum Gender: string
{
    case M = 'M';
    case F = 'F';
    case B = 'B';
    
    public static function isValid(?Gender $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'), true);
    }
}