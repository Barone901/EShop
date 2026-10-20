/**
 * Checkout Module
 *
 * "Rovnaká ako doručovacia" a loading stav pri odoslaní objednávky.
 */

// Polia kopírované pri zaškrtnutí "Same as shipping"
const COPIED_FIELDS = ['name', 'email', 'phone', 'address', 'city', 'postal_code', 'country'];

const field = (id) => document.getElementById(id);

const Checkout = {
    form: null,
    sameAsShipping: null,

    init() {
        this.form = field('checkout-form');
        if (!this.form) return;

        this.sameAsShipping = field('same-as-shipping');

        this.initSameAsShipping();
        this.initSubmitLoading();
    },

    initSameAsShipping() {
        if (!this.sameAsShipping) return;

        this.sameAsShipping.addEventListener('change', () => {
            if (this.sameAsShipping.checked) {
                this.copyShippingToBilling();
            }
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

    copyShippingToBilling() {
        COPIED_FIELDS.forEach((name) => {
            field(`billing_${name}`).value = field(`shipping_${name}`).value;
        });
    },
};

export default Checkout;
