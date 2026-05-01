import Vue from 'vue'
import MaskedInput from 'vue-text-mask'


import VueTheMask from 'vue-the-mask'
import masker from 'vue-the-mask/src/masker.js';
import tokens from 'vue-the-mask/src/tokens';


import Maska from 'maska'

import PhoneMaskInput from  "vue-phone-mask-input";

Vue.filter('mask', (value, mask) => masker(value, mask, true, tokens));
Vue.use(VueTheMask)
Vue.use(Maska)
Vue.component('PhoneMaskInput', PhoneMaskInput)

Vue.component('MaskedInput', MaskedInput)

