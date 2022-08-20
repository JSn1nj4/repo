<?php

namespace App\Enums;

use App\Enums\Traits\ChecksValues;
use App\Enums\Traits\OutputsValueLists;

enum HostEditableField: string {
    use ChecksValues,
        OutputsValueLists;

    case NAME = 'name';
    case URL_BASE = 'url_base';
    case SEPARATOR = 'separator';
    case SHORTHAND = 'shorthand';
}
