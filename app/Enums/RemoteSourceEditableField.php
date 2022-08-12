<?php

namespace App\Enums;

use App\Enums\Traits\CanGetValueArray;

enum RemoteSourceEditableField: string {
    use CanGetValueArray;

    case NAME = 'name';
    case URL_BASE = 'url_base';
    case SEPARATOR = 'separator';
}
