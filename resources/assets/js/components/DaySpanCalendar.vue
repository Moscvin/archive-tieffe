<template>
  <vue-cal style="height: 550px;"
      id="vuecal"
      :selected-date="selectedCalendarDate"
      :time-cell-height="timeCellHeight"
      :events="events"
      :on-event-click="onEventClick"
      :drag-to-create-threshold="0"
      :time-from="2 * 60"
      :time-to="22 * 60"
      :time-step="30"
      events-on-month-view
      active-view="day"
      hide-view-selector
      :disable-views="['years', 'year', 'month', 'week']"
      @ready="render_event($event)"
      @view-change="switchView"
      locale="it" ref="hourCalendar"
      :split-days="daySplits"
      sticky-split-labels>

    <template v-slot:split-label="{ split, view }">
      <strong :style="`color: ${split.color}`">{{ split.label }}</strong>
    </template>
    
    <template v-slot:event="{ event, view }">
      <div class="vuecal__event-title" v-html="event.title" />
      <div>
        <b><small v-html="event.client.phone" /></b>
      </div>
      <div class="vuecal__event-title" v-html="event.client.address" />
      <small class="vuecal__event-time">
        <span>{{ event.start.formatTime("HH:mm") }} - {{ event.end.formatTime("HH:mm") }}</span>
      </small>
    </template>
  </vue-cal>

</template>


<script>
import VueCal from 'vue-cal'
import 'vue-cal/dist/i18n/it.js'
import $ from "jquery";
import 'vue-cal/dist/vuecal.css'

export default {
  props: {
    events: {
      default() {
        return [];
      }
    },
    technicians: {
      default() {
        return [];
      }
    },
    selectedCalendarDate: {
      default() {
        return [];
      }
    },
  },
  data: () => ({
    rendered: false,
    timeCellHeight: 20,
    everyYearHolidays: [
        '-01-01',
        '-01-06',
        '-04-25',
        '-05-01',
        '-06-02',
        '-08-15',
        '-11-01',
        '-12-25',
        '-12-26',
    ],
    holidays : [
        '2020-04-13',
        '2021-04-05',
        '2022-04-18',
        '2023-04-10',
        '2024-04-01',
        '2025-04-21',
        '2026-04-06',
        '2027-03-29',
        '2028-04-17',
        '2029-04-02',
        '2030-04-22',
        '2031-04-14',
        '2032-03-29',
        '2033-04-18',
        '2034-04-10',
        '2035-03-26',
        '2036-04-14',
        '2037-04-06',
        '2038-04-26',
        '2039-04-11',
        '2040-04-02',
        '2041-04-22',
        '2042-04-07',
        '2043-03-30',
        '2044-04-18',
        '2045-04-10',
        '2046-03-26',
        '2047-04-15',
        '2048-04-06',
        '2049-04-19',
        '2050-04-11',
        '2051-04-03',
        '2052-04-22',
        '2053-04-07',
        '2054-03-30',
        '2055-04-19',
        '2056-04-03',
        '2057-04-23',
        '2058-04-15',
        '2059-03-31',
        '2060-04-19',
        '2061-04-11',
        '2062-03-27',
        '2063-04-16',
        '2064-04-07',
        '2065-03-30',
        '2066-04-12',
        '2067-04-04',
        '2068-04-23',
        '2069-04-15',
        '2070-03-31',
        '2071-04-20',
        '2072-04-11',
        '2073-03-27',
        '2074-04-16',
        '2075-04-08',
        '2076-04-20',
        '2077-04-12',
        '2078-04-04',
        '2079-04-24',
        '2080-04-08',
        '2081-03-31',
        '2082-04-20',
        '2083-04-05',
        '2084-03-27',
        '2085-04-16',
        '2086-04-01',
        '2087-04-21',
        '2088-04-12',
        '2089-04-04',
        '2090-04-17',
        '2091-04-09',
        '2092-03-31',
        '2093-04-13',
        '2094-04-05',
        '2095-04-25',
        '2096-04-16',
        '2097-04-01',
        '2098-04-21',
        '2099-04-13',
    ]

  }),
  components: { VueCal },
  computed: {
    daySplits () {
      var styleClasses = ['calendar-first-column', 'calendar-second-column']
      let a = this.technicians.map((item, index) => {
        let styleClass = styleClasses[+(index % 2 == 1)]
        return {
          label: item.family_name+' '+item.name,
          color: 'black',
          class: styleClass
        }
      })
      return a
    }
  },
  mounted() {

  },
  methods: {
    scrollReady() {
      const calendar = document.querySelector('#vuecal .vuecal__bg')
      const hours = 12
      calendar.scrollTo({ top: hours * this.timeCellHeight, behavior: 'smooth' })
    },
    onEventClick (event, e) {
      
      if(e.button == 0) {
        this.$emit("event-selected", event);
      }
      e.stopPropagation();
    },
    render_event(event) {

      var date = this.$moment(this.$refs.hourCalendar.view.startDate).format('YYYY-MM-DD');
      this.detectHoliday(date);

      if(this.rendered == false){
        this.$emit("event-rendered", event);
        this.rendered = true;
      }
      this.scrollReady()
      let childNode = document.querySelector('.vuecal__title-bar').children

      childNode[1].before(childNode[2]);
    },
    switchView({ view, startDate, endDate, week }) {

      //console.log(this.$moment(this.$refs.hourCalendar.view.startDate).format('DD/MM/YYYY'));
      
      var date = this.$moment(this.$refs.hourCalendar.view.startDate).format('YYYY-MM-DD');

      this.detectHoliday(date);

      this.$emit("change-date-picker-value", date);
      this.$emit("switch-view", { view, startDate, endDate, week });
    },
    detectHoliday(date){

        var result = [];

        result = result.concat(this.holidays);
        result = result.concat(this.getCurrentYearHolidays(date));

        if((new Date(date)).getDay() == 0 || result.includes(date)){
          document.querySelector('.vuecal__title-bar').classList.add('holidayTitleDate');
        }
        else {
          document.querySelector('.vuecal__title-bar').classList.remove('holidayTitleDate');
        }
    },
    getCurrentYearHolidays(date)
    {
        var d = new Date(date);
        var currentYear = d.getFullYear();
        var currentYearHolidays = [];

        for(var i = 0; i < this.everyYearHolidays.length; i++) {
            currentYearHolidays[i] = currentYear + this.everyYearHolidays[i];
        }

        //var easterDate = this.getEasterDate(currentYear);
        //currentYearHolidays.push(easterDate);

        return currentYearHolidays;
    },
    getEasterDate(Y) {
        var C = Math.floor(Y/100);
        var N = Y - 19*Math.floor(Y/19);
        var K = Math.floor((C - 17)/25);
        var I = C - Math.floor(C/4) - Math.floor((C - K)/3) + 19*N + 15;
        I = I - 30*Math.floor((I/30));
        I = I - Math.floor(I/28)*(1 - Math.floor(I/28)*Math.floor(29/(I + 1))*Math.floor((21 - N)/11));
        var J = Y + Math.floor(Y/4) + I + 2 - C + Math.floor(C/4);
        J = J - 7*Math.floor(J/7);
        var L = I - J;
        var M = 3 + Math.floor((L + 40)/44);
        var D = L + 28 - 31*Math.floor(M/4);

        return Y + '-' + this.padout(M) + '-' + this.padout(D);
    },
    padout(number) { return (number < 10) ? '0' + number : number; }

  }


};


</script>
