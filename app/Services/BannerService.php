<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Collection;

/**
 * Sluzba pre pracu s bannermi.
 */
class BannerService
{
    /**
     * Ziska aktivne bannery zoradene podla poradia.
     */
    public function getActiveBanners(): Collection
    {
        return Banner::active()
            ->ordered()
            ->with('page')
            ->get();
    }
}
