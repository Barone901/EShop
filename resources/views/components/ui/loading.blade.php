{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
@props(['text' => 'Loading...'])

<div class="flex items-center justify-center py-16" role="status">
    <div class="text-center">
        <div class="mx-auto mb-4 size-10 animate-spin rounded-full border-2 border-line border-t-brand-600 motion-reduce:animate-none" aria-hidden="true"></div>
        <p class="text-muted">{{ $text }}</p>
    </div>
</div>
