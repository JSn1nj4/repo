<?php

namespace App\Enums;

use App\Enums\Traits\ChecksValues;
use App\Enums\Traits\OutputsValueLists;

enum AccountEditableField: string {
    use ChecksValues,
        OutputsValueLists;

    case NAME = 'name';
    case SLUG = 'slug';
    case SHORTHAND = 'shorthand';
}
