<?php

namespace App\Actions;

use App\Models\RemoteSource;

/**
 * Check that a matching remote source entity exists
 */
class CheckRemoteSourceExists extends BareAction
{
    /**
     * @param string|null $name
     * @param string|null $url_base
     * @return bool
     */
    public function __invoke(?int $id = null, ?string $name = null, ?string $url_base = null): bool
    {
        if($id !== null) {
            return RemoteSource::find($id) !== null;
        }

        if($name !== null) {
            return RemoteSource::where('name', '=', $name)
                    ->count() > 0;
        }

        if($url_base !== null) {
            return RemoteSource::where('url_base', '=', $url_base)
                    ->count() > 0;
        }

        return false;
    }
}
