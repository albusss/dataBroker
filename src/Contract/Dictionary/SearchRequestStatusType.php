<?php

declare(strict_types=1);

namespace App\Contract\Dictionary;

enum SearchRequestStatusType: string
{
    case New        = 'new';
    case InProgress = 'in_progress';
    case Done       = 'done';
    case Error      = 'error';
}
