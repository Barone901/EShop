/**
 * Checkout Module
 *
 * Predvyplnenie adries z uložených adries, "rovnaká ako doručovacia"
 * a loading stav pri odoslaní objednávky.
 */

const SHIPPING_FIELDS = {
    shipping_name: 'name',
    shipping_email: 'email',
    shipping_phone: 'phone',
    shipping_address: 'address',
    shipping_city: 'city',
    shipping_postal_code: 'postal',
};

const BILLING_FIELDS = {
    billing_name: 'name',
    billing_email: 'email',
    billing_phone: 'phone',
    billing_address: 'address',
    billing_city: 'city',
    billing_postal_code: 'postal',
    billing_company_name: 'company',
    billing_ico: 'ico',
    billing_dic: 'dic',
    billing_ic_dph: 'icdph',
};

// Polia kopírované pri zaškrtnutí "Same as shipping"
const COPIED_FIELDS = ['name', 'email', 'phone', 'address', 'city', 'postal_code', 'country'];

const field = (id) => document.getElementById(id);

const Checkout = {
    form: null,
    sameAsShipping: null,
    defaultCountry: '',

    init() {
        this.form = field('checkout-form');
        if (!this.form) return;

        // Predvolená krajina z PHP (OrderAddress::DEFAULT_COUNTRY)
        this.defaultCountry = this.form.dataset.defaultCountry;

        this.sameAsShipping = field('same-as-shipping');

        this.initAddressSelect('shipping-address-select', (option) => this.fillShippingFields(option));
        this.initAddressSelect('billing-address-select', (option) => this.fillBillingFields(option));
        this.initSameAsShipping();
        this.initSubmitLoading();
    },

    initAddressSelect(selectId, fill) {
        const select = field(selectId);
        if (!select) return;

        const selected = select.options[select.selectedIndex];
        if (selected?.value) {
            fill(selected);
        }

        select.addEventListener('change', () => {
            const option = select.options[select.selectedIndex];
            if (option.value) {
                fill(option);
            }
        });
    },

    initSameAsShipping() {
        if (!this.sameAsShipping) return;

        const billingSelector = field('billing-address-selector');

        this.sameAsShipping.addEventListener('change', () => {
            if (this.sameAsShipping.checked) {
                this.copyShippingToBilling();
            }

            billingSelector?.classList.toggle('hidden', this.sameAsShipping.checked);
        });

        // Pri zmene doručovacej adresy aktualizuj aj fakturačnú
        document.querySelectorAll('[id^="shipping_"]').forEach((input) => {
            input.addEventListener('input', () => {
                if (!this.sameAsShipping.checked) return;

                const billingField = field(input.id.replace('shipping_', 'billing_'));
                if (billingField) {
                    billingField.value = input.value;
                }
            });
        });
    },

    initSubmitLoading() {
        this.form.addEventListener('submit', () => {
            field('submit-order-btn').disabled = true;
            field('submit-text').classList.add('hidden');
            field('submit-loading').classList.remove('hidden');
        });
    },

    fillFields(fieldMap, option) {
        Object.entries(fieldMap).forEach(([id, key]) => {
            field(id).value = option.dataset[key] || '';
        });
    },

    fillShippingFields(option) {
        this.fillFields(SHIPPING_FIELDS, option);
        field('shipping_country').value = option.dataset.country || this.defaultCountry;

        if (this.sameAsShipping?.checked) {
            this.copyShippingToBilling();
        }
    },

    fillBillingFields(option) {
        this.fillFields(BILLING_FIELDS, option);
        field('billing_country').value = option.dataset.country || this.defaultCountry;
    },

    copyShippingToBilling() {
        COPIED_FIELDS.forEach((name) => {
            field(`billing_${name}`).value = field(`shipping_${name}`).value;
        });
    },
};

export default Checkout;
