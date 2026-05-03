let stripeScriptPromise = null;

function loadStripeScript() {
    if (window.Stripe) {
        return Promise.resolve();
    }

    if (stripeScriptPromise) {
        return stripeScriptPromise;
    }

    stripeScriptPromise = new Promise((resolve, reject) => {
        const script = document.createElement('script');

        script.src = 'https://js.stripe.com/v3/';
        script.async = true;
        script.addEventListener('load', resolve, { once: true });
        script.addEventListener('error', () => reject(new Error('Stripe failed to load.')), { once: true });

        document.head.appendChild(script);
    });

    return stripeScriptPromise;
}

export function subscriptionPayment(stripeKey) {
    return {
        stripe: null,
        elements: null,
        cardElement: null,
        selectedPlan: null,
        processing: false,
        cardError: '',
        cardMounted: false,

        async initializeStripe() {
            if (this.stripe) {
                return;
            }

            await loadStripeScript();

            this.stripe = Stripe(stripeKey);
            this.elements = this.stripe.elements({ locale: 'auto' });
        },

        async selectPlan(detail) {
            this.selectedPlan = detail;
            this.cardError = '';

            await this.$nextTick();

            if (!this.cardMounted) {
                try {
                    await this.initializeStripe();
                } catch {
                    this.cardError = 'Payment form failed to load. Please refresh the page and try again.';

                    return;
                }

                this.cardElement = this.elements.create('card', {
                    style: {
                        base: {
                            color: '#ffffff',
                            fontFamily: '"Roboto", sans-serif',
                            fontSize: '16px',
                            fontSmoothing: 'antialiased',
                            '::placeholder': { color: '#999999' },
                        },
                        invalid: {
                            color: '#e31213',
                            iconColor: '#e31213',
                        },
                    },
                });
                this.cardElement.mount('#card-element');
                this.cardElement.on('change', (e) => {
                    this.cardError = e.error ? e.error.message : '';
                });
                this.cardMounted = true;
            }

            document.getElementById('payment-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        async subscribe() {
            if (this.processing || !this.selectedPlan) return;
            if (!this.stripe || !this.cardElement) {
                this.cardError = 'Payment form is still loading. Please try again.';

                return;
            }

            this.processing = true;
            this.cardError = '';

            const { paymentMethod, error } = await this.stripe.createPaymentMethod({
                type: 'card',
                card: this.cardElement,
            });

            if (error) {
                this.cardError = error.message;
                this.processing = false;
                return;
            }

            try {
                const response = await fetch('/members/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        payment_method: paymentMethod.id,
                        price_id: this.selectedPlan.priceId,
                    }),
                });

                const data = await response.json();

                if (response.status === 426) {
                    const { error: confirmError } = await this.stripe.confirmCardPayment(data.payment);
                    if (confirmError) {
                        this.cardError = confirmError.message;
                        this.processing = false;
                        return;
                    }
                } else if (!response.ok) {
                    this.cardError = data.message ?? 'Subscription failed. Please try again.';
                    this.processing = false;
                    return;
                }

                window.location.href = '/members/profile';
            } catch {
                this.cardError = 'An unexpected error occurred. Please try again.';
                this.processing = false;
            }
        },
    };
}
