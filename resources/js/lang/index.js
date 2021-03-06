import Vue from 'vue';
import VueI18n from 'vue-i18n';
import elementEnLocale from 'element-ui/lib/locale/lang/en'; // element-ui lang
import elementRuLocale from 'element-ui/lib/locale/lang/ru-RU'; // element-ui lang
import enLocale from './en';
import ruLocale from './ru';

Vue.use(VueI18n);

const messages = {
  en: {
    ...enLocale,
    ...elementEnLocale,
  },
  ru: {
    ...ruLocale,
    ...elementRuLocale,
  },
};

const i18n = new VueI18n({
  // set locale
  // options: en | ru | vi | zh
  locale: 'ru',
  // set locale messages
  messages,
});

export default i18n;
