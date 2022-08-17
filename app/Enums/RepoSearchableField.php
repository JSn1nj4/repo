<?php

namespace App\Enums;

use App\Enums\Traits\OutputsValueLists;

enum RepoSearchableField: string {
    use OutputsValueLists;

    case ID = 'id';
    case NAME = 'name';
    case SLUG = 'slug';
}
