import './bootstrap';

import Alpine from 'alpinejs';
import publicCatalog from './public-catalog';

window.Alpine = Alpine;

Alpine.data('publicCatalog', publicCatalog);

Alpine.start();
