import Alpine from 'alpinejs';

window.Alpine = Alpine;
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
