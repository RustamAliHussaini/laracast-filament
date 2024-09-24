<?php

namespace App\Enums;

enum TalkStatus : string
{
    case SUBMITTED = 'Submitted';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';

    public function getColor(): string
    {
        return match ($this){
            self::APPROVED => 'success',
            self::SUBMITTED => 'primary',
            self::REJECTED => 'danger',
        };
    }
}
