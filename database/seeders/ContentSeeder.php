<?php

// [AI] Vygenerovane s pomocou AI (Claude).
// [AI] Upravene s pomocou AI (Claude): bohatsi obsah clankov (h3, ol, blockquote, pre, img, odkaz), aby ho pokryvali styly .article-content.

namespace Database\Seeders;

use App\Enums\PageStatus;
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
            'status' => PageStatus::Published,
            'published_at' => now()->subDays(20),
            'author_id' => $author?->id,
            'is_featured' => true,
            'views_count' => 128,
        ]);

        $dentRepair = Page::create([
            'title' => 'Paintless Dent Repair Explained',
            'slug' => 'paintless-dent-repair-explained',
            'excerpt' => 'Small bodywork dents can be repaired without repainting and without removing interior parts.',
            'content' => $this->dentRepairContent(),
            'featured_image' => $this->demoImage('banner-dent-repair.jpg', 'pages'),
            'status' => PageStatus::Published,
            'published_at' => now()->subDays(10),
            'author_id' => $author?->id,
            'views_count' => 57,
        ]);

        Page::create([
            'title' => 'Choosing the Right Socket Set',
            'slug' => 'choosing-the-right-socket-set',
            'excerpt' => '1/4", 3/8" or 1/2"? A short guide to drive sizes and what each is good for.',
            'content' => $this->socketSetContent(),
            'status' => PageStatus::Published,
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
            'status' => PageStatus::Draft,
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

    /**
     * Obsah clanku o oprave karoserie bez lakovania.
     */
    private function dentRepairContent(): string
    {
        return '<p>Paintless dent repair uses pulling tools glued to the panel surface.</p>'
            .'<h2>Advantages</h2>'
            .'<ul><li>Original paint stays intact</li><li>Repair takes minutes, not days</li><li>No interior parts need to be removed</li></ul>'
            .'<h2>How the repair works</h2>'
            .'<ol><li>Clean the panel with isopropyl alcohol.</li><li>Glue a pulling tab to the centre of the dent.</li><li>Pull the dent out gradually with a slide hammer or bridge puller.</li><li>Remove the glue and level high spots with a tap-down tool.</li></ol>'
            .'<h3>When it is not suitable</h3>'
            .'<p>Sharp creases and dents with cracked paint still need a classic bodywork repair.</p>'
            .'<blockquote>Warm the panel slightly with a heat gun first - the metal moves more easily and the paint is less likely to crack.</blockquote>'
            .'<p>See all <a href="/products">bodywork tools in our shop</a>.</p>';
    }

    /**
     * Obsah clanku o vybere sady hlavic (s obrazkom a tabulkou momentov).
     */
    private function socketSetContent(): string
    {
        $image = '/storage/'.$this->demoImage('banner-chassis.jpg', 'pages');

        return '<p>The drive size determines how much torque a socket can transfer.</p>'
            .'<h2>Drive sizes</h2>'
            .'<ul><li><strong>1/4"</strong> - small screws, interior work</li><li><strong>3/8"</strong> - universal size for most repairs</li><li><strong>1/2"</strong> - wheels, chassis, high torque</li></ul>'
            .'<h3>Typical tightening torques</h3>'
            .'<pre>Wheel nuts (passenger car)   110-140 Nm   1/2"'."\n"
            .'Brake caliper bolts           80-120 Nm   1/2"'."\n"
            .'Spark plugs                   20-30 Nm    3/8"'."\n"
            .'Oil drain plug                25-35 Nm    3/8"</pre>'
            .'<p><img src="'.$image.'" alt="Chassis work with a 1/2-inch socket set"></p>'
            .'<blockquote>For chassis bolts always use impact sockets - chrome sockets can crack under an impact wrench.</blockquote>'
            .'<p>Browse our <a href="/products">socket sets and wrenches</a>.</p>';
    }
}
