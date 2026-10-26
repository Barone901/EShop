<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\Concerns\CopiesDemoImages;
use Illuminate\Database\Seeder;

/**
 * Ukazkove clanky a bannery na homepage.
 */
class ContentSeeder extends Seeder
{
    use CopiesDemoImages;

    public function run(): void
    {
        $author = User::where('email', 'designer@example.com')->first();

        $glowPlugs = Page::create([
            'title' => 'How to Remove a Broken Glow Plug',
            'slug' => 'how-to-remove-a-broken-glow-plug',
            'excerpt' => 'A broken glow plug does not have to mean removing the cylinder head. Here is how our extractor kit saves hours of work.',
            'content' => '<p>Glow plugs in diesel engines often seize in the cylinder head and break when you try to unscrew them.</p>'
                .'<h2>What you need</h2>'
                .'<ul><li>Glow plug extractor kit</li><li>Penetrating oil</li><li>Torque wrench</li></ul>'
                .'<h2>Procedure</h2>'
                .'<ol><li>Let the penetrating oil work for at least an hour.</li><li>Drill the remaining part of the plug using the guide.</li><li>Pull it out with the extractor.</li></ol>'
                .'<p>The whole repair is done <strong>directly on the vehicle</strong>.</p>',
            'featured_image' => $this->demoImage('banner-glow-plugs.jpg', 'pages'),
            'meta_description' => 'Removing broken glow plugs without dismounting the cylinder head.',
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now()->subDays(20),
            'author_id' => $author?->id,
            'is_featured' => true,
            'views_count' => 128,
        ]);

        $dentRepair = Page::create([
            'title' => 'Paintless Dent Repair Explained',
            'slug' => 'paintless-dent-repair-explained',
            'excerpt' => 'Small bodywork dents can be repaired without repainting and without removing interior parts.',
            'content' => '<p>Paintless dent repair uses pulling tools glued to the panel surface.</p>'
                .'<h2>Advantages</h2>'
                .'<ul><li>Original paint stays intact</li><li>Repair takes minutes, not days</li><li>No interior parts need to be removed</li></ul>',
            'featured_image' => $this->demoImage('banner-dent-repair.jpg', 'pages'),
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now()->subDays(10),
            'author_id' => $author?->id,
            'views_count' => 57,
        ]);

        Page::create([
            'title' => 'Choosing the Right Socket Set',
            'slug' => 'choosing-the-right-socket-set',
            'excerpt' => '1/4", 3/8" or 1/2"? A short guide to drive sizes and what each is good for.',
            'content' => '<p>The drive size determines how much torque a socket can transfer.</p>'
                .'<h2>Drive sizes</h2>'
                .'<ul><li><strong>1/4"</strong> - small screws, interior work</li><li><strong>3/8"</strong> - universal size for most repairs</li><li><strong>1/2"</strong> - wheels, chassis, high torque</li></ul>',
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now()->subDays(3),
            'author_id' => $author?->id,
            'views_count' => 12,
        ]);

        // Koncept - na webe sa nezobrazuje, len v administracii
        Page::create([
            'title' => 'Plastic Welding in the Car Industry',
            'slug' => 'plastic-welding-in-the-car-industry',
            'excerpt' => 'Draft article about hot air and airless plastic repair.',
            'content' => '<p>Work in progress.</p>',
            'status' => Page::STATUS_DRAFT,
            'author_id' => $author?->id,
        ]);

        $banners = [
            ['Glow plug and injector tools', 'banner-glow-plugs.jpg', null, $glowPlugs->id],
            ['Plastic welding', 'banner-plastic-welding.jpg', '/products', null],
            ['Car tinsmith tools', 'banner-dent-repair.jpg', null, $dentRepair->id],
            ['Special tools for chassis', 'banner-chassis.jpg', '/category/special-tools', null],
        ];

        foreach ($banners as $sortOrder => [$name, $image, $link, $pageId]) {
            Banner::create([
                'name' => $name,
                'image_path' => $this->demoImage($image, 'banners'),
                'link_url' => $link,
                'page_id' => $pageId,
                'is_active' => true,
                'sort_order' => $sortOrder,
            ]);
        }
    }
}
