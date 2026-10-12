<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

trait CopiesDemoImages
{
    /**
     * Skopiruje ukazkovy obrazok z database/seeders/images na disk "public"
     * a vrati cestu, ktora sa uklada do databazy (rovnako ako pri uploade cez admin).
     */
    protected function demoImage(string $file, string $directory): string
    {
        $path = "{$directory}/{$file}";

        Storage::disk('public')->put($path, file_get_contents(database_path("seeders/images/{$file}")));

        return $path;
    }
}
