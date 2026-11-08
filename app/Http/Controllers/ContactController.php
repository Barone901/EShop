<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Controller pre kontaktny formular.
 */
class ContactController extends Controller
{
    /**
     * Zobrazi kontaktny formular.
     */
    public function index(Request $request): View
    {
        return view('pages.contact.index', [
            // Prihlaseny pouzivatel ma predvyplnene meno a e-mail
            'user' => $request->user(),
            'contactEmail' => config('shop.contact_email'),
            'patterns' => ContactRequest::PATTERNS,
        ]);
    }

    /**
     * Spracuje odoslanie kontaktneho formulara (validaciu robi ContactRequest)
     * a posle spravu obchodu e-mailom (Laravel Mail; lokalne mailer "log" -> storage/logs).
     */
    public function send(ContactRequest $request): RedirectResponse
    {
        Mail::to(config('shop.contact_email'))->send(new ContactMessage($request->validated()));

        return redirect()->route('contact')
            ->with('success', __('Thank you for your message! We will get back to you soon.'));
    }
}
