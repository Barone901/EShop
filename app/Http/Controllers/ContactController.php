<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller pre kontaktny formular.
 */
class ContactController extends Controller
{
    /**
     * Zobrazi kontaktny formular.
     */
    public function index(): View
    {
        return view('pages.contact.index');
    }

    /**
     * Spracuje odoslanie kontaktneho formulara (validaciu robi ContactRequest).
     * Sprava sa zatial nikam neposiela, iba sa potvrdi prijatie.
     */
    public function send(ContactRequest $request): RedirectResponse
    {
        return redirect()->route('contact')
            ->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
