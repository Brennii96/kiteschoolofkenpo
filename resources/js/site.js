document.addEventListener('DOMContentLoaded', () => {

    document.body.addEventListener('click', (event) => {
        const accordionButton = event.target.closest('.toggle-accordion');
        if (accordionButton) {
            toggleFAQContent(accordionButton);
        }
    });

});

const toggleFAQContent = button => {
    const faqItem = button.closest('.faq-item');
    const faqContent = faqItem.querySelector('.faq-content');
    const faqIcon = faqItem.querySelector('.faq-icon');

    const plusSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
      </svg>`;

    const minusSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
        <path d="M3.75 7.25a.75.75 0 0 0 0 1.5h8.5a.75.75 0 0 0 0-1.5h-8.5Z" />
      </svg>`;

    if (content.style.maxHeight && faqContent.style.maxHeight !== '0px') {
        faqContent.style.maxHeight = '0';
        faqIcon.innerHTML = plusSVG;
    } else {
        faqContent.style.maxHeight = `${faqContent.scrollHeight}px`; // Use template literal for cleaner syntax
        faqIcon.innerHTML = minusSVG;
    }
}
