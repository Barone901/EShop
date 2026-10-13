import Alpine from 'alpinejs';

import './eshop';

import Menu from './menu';

// Alpine.js pre Blade komponenty s x-data (napr. modal pri mazani uctu)
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    Menu.init();
});
