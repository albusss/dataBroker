<?php

declare(strict_types=1);

namespace App\Contract\Dictionary;

enum ParserType
{
    case ParserAbcheck;
    case ParserAddresses;
    case ParserAdvancedPeopleSearch;
    case ParserAnywho;
    case ParserBackgroundcheck;
    case ParserClustrmaps;
    case ParserCyberbackgroundchecks;
}
