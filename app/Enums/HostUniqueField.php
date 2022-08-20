<?php

namespace App\Enums;

use App\Enums\Traits\ChecksValues;
use App\Enums\Traits\OutputsValueLists;

enum HostUniqueField: string {
    use ChecksValues,
        OutputsValueLists;

    case ID = 'id';
    case NAME = 'name';
    case URL_BASE = 'url_base';
    case SHORTHAND = 'shorthand';
}
