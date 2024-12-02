/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");
window.Vue = require("vue");

import Vue from 'vue'
import vSelect from 'vue-select'
import Multiselect from 'vue-multiselect'
import JsonExcel from 'vue-json-excel'
import 'whatwg-fetch'
import VueCookies from 'vue-cookies'



Vue.component('v-select', vSelect)
Vue.component('multiselect', Multiselect)
Vue.component('downloadExcel', JsonExcel)
Vue.use(VueCookies)
// set default config
Vue.$cookies.config('7d')
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import {
  DatePicker,
  Switch,
  TimeSelect,
  TimePicker,
  Loading,
  Notification
} from "element-ui";
Vue.use(DatePicker);
Vue.use(Switch);
Vue.use(TimeSelect);
Vue.use(TimePicker);
Vue.use(Loading.directive);
Vue.prototype.$loading = Loading.service;
Vue.prototype.$notify = Notification;
import lang from "element-ui/lib/locale/lang/it";
//import lang from "resources/assets/js/it";

//var lang = require("./components/it");
import locale from "element-ui/lib/locale";

// configure language
locale.use(lang);
const moment = require("moment");
require("moment/locale/it");

Vue.use(require("vue-moment"), {moment});
var VueScrollTo = require('vue-scrollto');

Vue.use(VueScrollTo);


import print from "print-js";


Vue.prototype.$print = print;

import FullCalendar from "./components/FullCalendar";

Vue.component("full-calendar", FullCalendar);
Vue.component("day-span", require("./components/DaySpanCalendar").default);
Vue.component("small-cal", require("./components/SmallCalendar").default);

Vue.component("customer", require("./components/Customer.vue").default);
Vue.component("nuovo-intervento", require("./components/NuovoIntervento.vue").default);

Vue.component("calendario", require("./components/Calendario.vue").default);
Vue.component("calendar", require("./components/Calendar.vue").default);
Vue.component("intervention-map", require("./components/Map.vue").default);
Vue.component("da-programmare", require("./components/DaProgrammare.vue").default);
Vue.component("programmare", require("./components/Programmare.vue").default);
Vue.component("da-effettuare", require("./components/DaEffettuare.vue").default);
Vue.component("da-verificare", require("./components/DaVerificare.vue").default);
Vue.component("da-preventivare", require("./components/DaPreventivare.vue").default);
Vue.component("da-fatturare", require("./components/DaFatturare.vue").default);

Vue.component("works-per-month", require("./components/WorksPerMonth.vue").default);
Vue.component("works-invoiced", require("./components/WorksInvoiced.vue").default);

Vue.component("daily-summary", require("./components/DailySummary.vue").default);
Vue.component("monthly-summary", require("./components/MonthlySummary.vue").default);
Vue.component("daily-work", require("./components/DailyWorks.vue").default);

Vue.component("home", require("./components/Home.vue").default);

const app = new Vue({
  el: "#app",
  data: {},
});
