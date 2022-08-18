<?php

namespace App\Enums;

use App\Enums\Traits\OutputsValueLists;

enum RepoEditableField: string {
    use OutputsValueLists;

    case NAME = 'name';
    case SLUG = 'slug';
}
