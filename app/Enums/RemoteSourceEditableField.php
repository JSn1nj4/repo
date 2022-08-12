<?php

namespace App\Enums;

use App\Enums\Traits\OutputsAllValues;

enum RemoteSourceEditableField: string {
    use OutputsAllValues;

    case NAME = 'name';
    case URL_BASE = 'url_base';
    case SEPARATOR = 'separator';
}
