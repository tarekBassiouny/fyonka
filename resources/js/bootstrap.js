/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
import Alpine from 'alpinejs';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';

window.Alpine = Alpine;
Alpine.start();
window.axios = axios;
window.TomSelect = TomSelect;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
