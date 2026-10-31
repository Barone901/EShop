/**
 * Page Actions Module
 *
 * Deklaratívne akcie cez data atribúty (namiesto inline JS v šablónach):
 * - <form data-confirm="...">       potvrdenie pred odoslaním
 * - <select data-navigate-on-change> presmerovanie na URL vybranej option
 * - <button data-copy-url="...">    skopírovanie URL do schránky
 *
 * [AI] Upravené s pomocou AI (Claude): kopírovanie cez await + Toast namiesto alert.
 */
import Toast from './toast';
import { delegate } from '../utils/dom';

const PageActions = {
    init() {
        delegate('submit', 'form[data-confirm]', (e, form) => {
            if (!window.confirm(form.dataset.confirm)) {
                e.preventDefault();
            }
        });

        delegate('change', 'select[data-navigate-on-change]', (e, select) => {
            window.location.href = select.value;
        });

        delegate('click', '[data-copy-url]', async (e, btn) => {
            try {
                // navigator.clipboard existuje len v zabezpečenom kontexte (HTTPS, localhost)
                await navigator.clipboard.writeText(btn.dataset.copyUrl);
                Toast.success('Link copied!');
            } catch {
                Toast.error('Could not copy the link.');
            }
        });
    },
};

export default PageActions;
