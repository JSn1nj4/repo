<?php

namespace App\Enums;

use App\Enums\Traits\OutputsValueLists;

enum RemoteSourceEditableField: string {
    use OutputsValueLists;

    case NAME = 'name';
    case URL_BASE = 'url_base';
    case SEPARATOR = 'separator';
}
