import Vue from 'vue';

import router from '@/router';
import App from './views/App';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

Vue.config.productionTip = false;

Vue.use(ElementUI);

new Vue({
    el: '#app',
    router,
    render: h => h(App),
});
