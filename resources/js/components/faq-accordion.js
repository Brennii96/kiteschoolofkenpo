export function initFaqAccordion() {
    let openFaq = null;

    const plusSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-5 h-5">
                        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/>
                     </svg>`;

    const minusSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-5 h-5">
                        <path d="M3.75 7.25a.75.75 0 0 0 0 1.5h8.5a.75.75 0 0 0 0-1.5h-8.5Z"/>
                     </svg>`;

    const toggleFAQContent = (button) => {
        const faqItem = button.closest('.faq-item');
        const faqContent = faqItem.querySelector('.faq-content');
        const faqIcon = faqItem.querySelector('.faq-icon');

        const isOpen = faqItem.classList.contains('open');

        if (openFaq && openFaq !== faqItem) {
            openFaq.classList.remove('open');
            openFaq.querySelector('.faq-content').style.maxHeight = '0';
            openFaq.querySelector('.faq-icon').innerHTML = plusSVG;
        }

        if (isOpen) {
            faqItem.classList.remove('open');
            faqContent.style.maxHeight = '0';
            faqIcon.innerHTML = plusSVG;
            openFaq = null;
        } else {
            faqItem.classList.add('open');
            faqContent.style.maxHeight = `${faqContent.scrollHeight}px`;
            faqIcon.innerHTML = minusSVG;
            openFaq = faqItem;
        }
    }

    document.body.addEventListener('click', (event) => {
        const button = event.target.closest('.toggle-accordion');
        if (button) {
            toggleFAQContent(button);
        }
    });
}
