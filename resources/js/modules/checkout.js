/**
 * Checkout Module
 *
 * Predvyplnenie adries z uložených adries, "rovnaká ako doručovacia"
 * a loading stav pri odoslaní objednávky.
 */
import { byId } from '../utils/dom';

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

const Checkout = {
    form: null,
    sameAsShipping: null,
    defaultCountry: '',

    init() {
        this.form = byId('checkout-form');
        if (!this.form) return;

        // Predvolená krajina z PHP (config/shop.php, default_country)
        this.defaultCountry = this.form.dataset.defaultCountry;

        this.sameAsShipping = byId('same-as-shipping');

        this.initAddressSelect('shipping-address-select', (option) => this.fillShippingFields(option));
        this.initAddressSelect('billing-address-select', (option) => this.fillBillingFields(option));
        this.initSameAsShipping();
        this.initSubmitLoading();
    },

    initAddressSelect(selectId, fill) {
        const select = byId(selectId);
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

        const billingSelector = byId('billing-address-selector');

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

                const billingField = byId(input.id.replace('shipping_', 'billing_'));
                if (billingField) {
                    billingField.value = input.value;
                }
            });
        });
    },

    initSubmitLoading() {
        this.form.addEventListener('submit', () => {
            byId('submit-order-btn').disabled = true;
            byId('submit-text').classList.add('hidden');
            byId('submit-loading').classList.remove('hidden');
        });
    },

    fillFields(fieldMap, option) {
        Object.entries(fieldMap).forEach(([id, key]) => {
            byId(id).value = option.dataset[key] || '';
        });
    },

    fillShippingFields(option) {
        this.fillFields(SHIPPING_FIELDS, option);
        byId('shipping_country').value = option.dataset.country || this.defaultCountry;

        if (this.sameAsShipping?.checked) {
            this.copyShippingToBilling();
        }
    },

    fillBillingFields(option) {
        this.fillFields(BILLING_FIELDS, option);
        byId('billing_country').value = option.dataset.country || this.defaultCountry;
    },

    copyShippingToBilling() {
        COPIED_FIELDS.forEach((name) => {
            byId(`billing_${name}`).value = byId(`shipping_${name}`).value;
        });
    },
};

export default Checkout;
