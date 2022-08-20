<?php

namespace App\Enums;

use App\Enums\Traits\ChecksValues;
use App\Enums\Traits\OutputsValueLists;

enum AccountSearchableField: string {
    use ChecksValues,
        OutputsValueLists;

    case ID = 'id';
    case NAME = 'name';
    case SLUG = 'slug';
    case SHORTHAND = 'shorthand';
}
