import Alpine from 'alpinejs';
import { subscriptionPayment } from './components/subscription-payment.js';

window.Alpine = Alpine;
Alpine.data('subscriptionPayment', subscriptionPayment);
Alpine.start();


document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('faq')) {
        import('./components/faq-accordion.js')
            .then(({initFaqAccordion}) => {
                initFaqAccordion();
            })
            .catch(error => {
                console.error('Failed to load FAQ accordion script:', error);
            });
    }
    if (document.querySelector('[data-google-map-container]')) {
        import('./components/google-map-loader.js')
            .then(({initializeGoogleMapsOnPage}) => {
                initializeGoogleMapsOnPage();
            })
            .catch(error => {
                console.error('Failed to load Google Map loader script:', error);
            });
    }
});
