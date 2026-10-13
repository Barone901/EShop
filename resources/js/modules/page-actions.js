/**
 * Page Actions Module
 *
 * Deklaratívne akcie cez data atribúty (namiesto inline JS v šablónach):
 * - <select data-navigate-on-change> presmerovanie na URL vybranej option
 */

const PageActions = {
    init() {
        document.addEventListener('change', (e) => {
            if (e.target.matches('select[data-navigate-on-change]')) {
                window.location.href = e.target.value;
            }
        });
    },
};

export default PageActions;
