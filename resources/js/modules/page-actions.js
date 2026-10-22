/**
 * Page Actions Module
 *
 * Deklaratívne akcie cez data atribúty (namiesto inline JS v šablónach):
 * - <form data-confirm="...">       potvrdenie pred odoslaním
 * - <select data-navigate-on-change> presmerovanie na URL vybranej option
 * - <button data-copy-url="...">    skopírovanie URL do schránky
 */

const PageActions = {
    init() {
        document.addEventListener('submit', (e) => {
            const form = e.target.closest('form[data-confirm]');
            if (form && !window.confirm(form.dataset.confirm)) {
                e.preventDefault();
            }
        });

        document.addEventListener('change', (e) => {
            if (e.target.matches('select[data-navigate-on-change]')) {
                window.location.href = e.target.value;
            }
        });

        document.addEventListener('click', (e) => {
            const copyBtn = e.target.closest('[data-copy-url]');
            if (copyBtn) {
                navigator.clipboard.writeText(copyBtn.dataset.copyUrl);
                window.alert('Link copied!');
            }
        });
    },
};

export default PageActions;
