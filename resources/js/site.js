import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();


document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('faq')) {
        import('./components/faq-accordion.js')
            .then(({ initFaqAccordion }) => {
                initFaqAccordion();
            })
            .catch(error => {
                console.error('Failed to load FAQ accordion script:', error);
            });
    }
});
