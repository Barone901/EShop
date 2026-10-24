<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Support;

/**
 * Spolocne regularne vyrazy pre kontrolu vstupov.
 *
 * Ten isty vzor sa pouziva na serveri (validacne pravidlo regex) aj v prehliadaci
 * (HTML atribut pattern, ktory cita resources/js/modules/form-validator.js),
 * takze klient a server kontroluju vstup rovnako.
 *
 * Vzory su zapisane tak, aby boli platne v PHP (PCRE s priznakom u) aj v JavaScripte.
 */
final class InputPattern
{
    /** Pouzivatelske meno (registracia, profil): pismena, medzery a pomlcky. */
    public const USERNAME = "[\\p{L}\\s\\-]+";

    /** Meno osoby: pismena (aj s diakritikou), medzery, pomlcka, apostrof, bodka. */
    public const PERSON_NAME = "[\\p{L}][\\p{L} '.\\-]*";

    /** Mesto: pismena, cislice (napr. Bratislava 4), medzery, pomlcka, bodka. */
    public const CITY = "[\\p{L}][\\p{L}0-9 .\\-]*";

    /** Telefon: volitelne +, potom cislice, medzery, pomlcky alebo lomky (6 - 20 znakov). */
    public const PHONE = "\\+?[0-9][0-9 \\-\\/]{5,19}";

    /** PSC: pismena, cislice, medzera alebo pomlcka (2 - 10 znakov), napr. 010 01. */
    public const POSTAL_CODE = "[A-Za-z0-9][A-Za-z0-9 \\-]{1,9}";

    /** ICO, DIC, IC DPH: pismena a cislice bez medzier (2 - 20 znakov), napr. SK2023456789. */
    public const COMPANY_ID = "[A-Za-z0-9]{2,20}";

    /** Chybove hlasky pre jednotlive vzory (server aj klient). */
    private const MESSAGES = [
        self::USERNAME => 'The name may only contain letters, spaces and hyphens.',
        self::PERSON_NAME => 'Please use only letters, spaces, hyphens and apostrophes.',
        self::CITY => 'Please enter a valid city name.',
        self::PHONE => 'Please enter a valid phone number, e.g. +421 905 123 456.',
        self::POSTAL_CODE => 'Please enter a valid postal code, e.g. 010 01.',
        self::COMPANY_ID => 'Please use only letters and digits without spaces.',
    ];

    /**
     * Laravel validacne pravidlo pre dany vzor (cely retazec musi vyhovovat).
     */
    public static function rule(string $pattern): string
    {
        return 'regex:/^(?:' . $pattern . ')$/u';
    }

    /**
     * Chybova hlaska pre dany vzor.
     */
    public static function message(string $pattern): string
    {
        return self::MESSAGES[$pattern] ?? 'The value has an invalid format.';
    }

    /**
     * Doplni do validacnych pravidiel FormRequestu pravidlo regex pre polia so vzorom.
     *
     * @param  array<string, array<int, mixed>>  $rules
     * @param  array<string, string>  $fields  nazov pola => vzor
     * @return array<string, array<int, mixed>>
     */
    public static function applyTo(array $rules, array $fields): array
    {
        foreach ($fields as $field => $pattern) {
            $rules[$field][] = self::rule($pattern);
        }

        return $rules;
    }

    /**
     * Hlasky pre FormRequest::messages() - kluc "pole.regex" => hlaska.
     *
     * @param  array<string, string>  $fields  nazov pola => vzor
     * @return array<string, string>
     */
    public static function messagesFor(array $fields): array
    {
        $messages = [];

        foreach ($fields as $field => $pattern) {
            $messages["{$field}.regex"] = self::message($pattern);
        }

        return $messages;
    }
}
