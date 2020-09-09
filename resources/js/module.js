import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import http from 'http-client';
import AWN from "awesome-notifications";
window.$ = require('jquery');

import ClubManagementForm from './components/ClubManagementForm';

Vue.prototype.$http = http;
Vue.prototype.$notify = new AWN({position: 'top-right'});
Vue.use(BootstrapVue);
Vue.prototype.$url = portal.APP_URL + '/' + portal.A_OR_P + '/' + portal.ACTIVITY_SLUG + '/' + portal.MODULE_INSTANCE_SLUG + '/' + portal.ALIAS;
Vue.prototype.$url2 = portal.APP_URL + '/' + 'api' + '/' + portal.A_OR_P + '/' + portal.ACTIVITY_SLUG + '/' + portal.MODULE_INSTANCE_SLUG + '/' + portal.ALIAS;


let vue = new Vue({
    el: '#flyerless-club-management-root',
    
    components: {
        ClubManagementForm,
    }
});
