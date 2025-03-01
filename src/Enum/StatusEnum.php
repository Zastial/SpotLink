<?php

namespace App\Enum;

enum StatusEnum: int
{
    case CREATED = 1;
    case AWAITING_VALIDATION = 2;
    case VALIDATED = 3;
    case TOTAL_REFUSED = 4;
    case PARTIAL_REFUSED = 5;
}