/**
 * Form Validation Module
 *
 * Validácia formulárov na strane klienta (formuláre s atribútom data-validate).
 *
 * Pravidlá sa berú z HTML atribútov poľa, ktoré zodpovedajú validácii na serveri:
 *   required, type="email", minlength, maxlength, pattern (+ data-pattern-message)
 * a z vlastného atribútu data-rules pre pravidlá, ktoré HTML nemá:
 *   data-rules="password|match:#password"
 *
 * Validuje sa pri opustení poľa (blur) a pri odoslaní formulára.
 * Skryté a vypnuté polia sa preskakujú.
 *
 * [AI] Rozšírené s pomocou AI (Claude): pravidlá z HTML atribútov, pattern, skryté polia,
 *      poradie submit handlerov, parametre pravidiel s dvojbodkou, mazanie chyby len daného poľa.
 *
 * [AI] Upravené s pomocou AI (Claude): chybový stav cez aria-invalid, hláška s triedou validation-error.
 */

import Toast from './toast';

const FIELD_SELECTOR = 'input:not([type="hidden"]):not([type="submit"]):not([type="button"]), textarea, select';

// Pole -> element s jeho chybovou hláškou
const errorElements = new WeakMap();

const FormValidator = {
    // Validačné pravidlá: vrátia true alebo chybovú hlášku
    rules: {
        required: (value, input) => {
            if (input.type === 'checkbox') {
                return input.checked || 'This field is required.';
            }
            return value.trim() !== '' || 'This field is required.';
        },

        email: (value) => {
            if (!value) return true;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(value) || 'Please enter a valid email address.';
        },

        minLength: (value, input, length) => {
            if (!value) return true;
            return value.length >= length || `The minimum length is ${length} characters.`;
        },

        maxLength: (value, input, length) => {
            if (!value) return true;
            return value.length <= length || `The maximum length is ${length} characters.`;
        },

        pattern: (value, input) => {
            if (!value) return true;
            return FormValidator.matchesPattern(value, input.getAttribute('pattern'))
                || input.dataset.patternMessage
                || 'The value has an invalid format.';
        },

        match: (value, input, targetSelector, message = 'The values do not match.') => {
            const target = document.querySelector(targetSelector);
            if (!target) return true;
            return value === target.value || message;
        },

        password: (value) => {
            if (!value) return true;
            const hasMinLength = value.length >= 8;
            const hasUppercase = /[A-Z]/.test(value);
            const hasLowercase = /[a-z]/.test(value);
            const hasNumber = /[0-9]/.test(value);

            if (!hasMinLength) return 'The password must be at least 8 characters long.';
            if (!hasUppercase) return 'The password must contain a capital letter.';
            if (!hasLowercase) return 'The password must contain a lowercase letter.';
            if (!hasNumber) return 'The password must contain a number.';
            return true;
        },
    },

    /**
     * Inicializácia validácie na formulároch.
     */
    init(formSelector = '[data-validate]') {
        document.querySelectorAll(formSelector).forEach((form) => this.setupForm(form));
    },

    /**
     * Nastavenie validácie pre konkrétny formulár.
     */
    setupForm(form) {
        // Vypne vstavané bubliny prehliadača, chyby zobrazujeme sami
        form.setAttribute('novalidate', 'true');

        this.fields(form).forEach((input) => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => {
                // Pri písaní chybu skryjeme, znova sa overí pri blur/submit
                if (input.getAttribute('aria-invalid') === 'true') {
                    this.clearFieldError(input);
                }
            });
        });

        // capture: validácia beží pred ostatnými submit handlermi (napr. loading stav v checkout.js),
        // pri chybe ich zastaví, aby sa tlačidlo nezablokovalo
        form.addEventListener('submit', (e) => {
            if (this.validateForm(form)) return;

            e.preventDefault();
            e.stopImmediatePropagation();
            Toast.error('Please correct the errors in the form.');
            form.querySelector('[aria-invalid="true"]')?.focus();
        }, { capture: true });
    },

    /**
     * Polia formulára, ktoré sa validujú.
     */
    fields(form) {
        return [...form.querySelectorAll(FIELD_SELECTOR)].filter((input) => this.ruleList(input).length > 0);
    },

    /**
     * Zoznam pravidiel poľa z HTML atribútov a data-rules, napr. ["required", "maxLength:255"].
     */
    ruleList(input) {
        const list = [];

        if (input.required) list.push('required');
        if (input.type === 'email') list.push('email');
        if (input.hasAttribute('minlength')) list.push(`minLength:${input.getAttribute('minlength')}`);
        if (input.hasAttribute('maxlength')) list.push(`maxLength:${input.getAttribute('maxlength')}`);
        if (input.hasAttribute('pattern')) list.push('pattern');

        if (input.dataset.rules) {
            list.push(...input.dataset.rules.split('|').filter((rule) => !list.includes(rule)));
        }

        return list;
    },

    /**
     * Validácia celého formulára.
     */
    validateForm(form) {
        // Bez skratky: chyba sa ukáže pri všetkých chybných poliach naraz
        return this.fields(form)
            .map((input) => this.validateField(input))
            .every(Boolean);
    },

    /**
     * Validácia jedného poľa.
     */
    validateField(input) {
        // Skryté (napr. sekcia mimo obrazovky) a vypnuté polia sa neodosielajú/nevalidujú
        if (input.disabled || input.offsetParent === null) {
            this.clearFieldError(input);
            return true;
        }

        const errorMessage = this.runRules(input);

        if (errorMessage === null) {
            this.clearFieldError(input);
            return true;
        }

        this.showFieldError(input, errorMessage);
        return false;
    },

    /**
     * Vyhodnotí pravidlá poľa. Vráti prvú chybovú hlášku, alebo null ak je hodnota validná.
     */
    runRules(input) {
        for (const rule of this.ruleList(input)) {
            const [ruleName, ...params] = this.parseRule(rule);
            const ruleFunc = this.rules[ruleName];

            if (!ruleFunc) continue;

            const result = ruleFunc(input.value, input, ...params);
            if (result !== true) return result;
        }

        return null;
    },

    /**
     * Rozdelí pravidlo na názov a najviac dva parametre, napr. "match:#password:Hláška".
     * Posledný parameter (hláška) môže obsahovať dvojbodku.
     */
    parseRule(rule) {
        const [ruleName, first, ...rest] = rule.split(':');

        if (first === undefined) return [ruleName];
        if (rest.length === 0) return [ruleName, first];

        return [ruleName, first, rest.join(':')];
    },

    /**
     * Otestuje hodnotu voči HTML pattern (celá hodnota musí vyhovovať, rovnako ako v prehliadači).
     */
    matchesPattern(value, pattern) {
        try {
            return new RegExp(`^(?:${pattern})$`, 'u').test(value);
        } catch {
            // Neplatný vzor nesmie zablokovať formulár, server hodnotu skontroluje aj tak
            return true;
        }
    },

    /**
     * Zobrazenie chyby pri poli.
     */
    showFieldError(input, message) {
        this.clearFieldError(input);

        // Chybový stav (aj červený okraj cez CSS) nesie len aria-invalid
        input.setAttribute('aria-invalid', 'true');

        const errorEl = document.createElement('p');
        errorEl.className = 'validation-error';
        errorEl.textContent = message;

        input.after(errorEl);
        errorElements.set(input, errorEl);
    },

    /**
     * Odstránenie chyby pri poli.
     */
    clearFieldError(input) {
        input.removeAttribute('aria-invalid');

        // Len chyba tohto poľa (viac polí môže mať spoločného rodiča)
        errorElements.get(input)?.remove();
        errorElements.delete(input);
    },
};

export default FormValidator;
