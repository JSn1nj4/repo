<?php

namespace App\Enums;

use App\Enums\Traits\OutputsValueLists;

enum AccountEditableField: string {
    use OutputsValueLists;

    case NAME = 'name';
    case SLUG = 'slug';
    case SHORTHAND = 'shorthand';
}
