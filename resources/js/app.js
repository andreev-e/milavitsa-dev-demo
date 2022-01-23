import Vue from 'vue';

import router from '@/router';
import App from './views/App';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import i18n from './lang'; // Internationalization

Vue.config.productionTip = false;

Vue.use(ElementUI, {
  i18n: (key, value) => i18n.t(key, value),
});

new Vue({
    el: '#app',
    router,
    i18n,
    render: h => h(App),
});
