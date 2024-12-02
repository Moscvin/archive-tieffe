<template>
    <calendario
        :chars="chars"
        :viewdate="viewdate"
        :serchevents="events"
        :invoices_to="invoices_to"
        v-on:viewName="operations($event); operationsByDate($event);">
    </calendario>
</template>
<script>
export default {
  props: ["chars", "invoices_to"],
  data() {
    return {
      viewdate: {
        date: "",
        date_end: "",
        date_start: "",
        name: "",
        month: "",
        year: ""
      },
      events: [],
      interventi: []
    };
  },
  methods: {
    operations(e) {
      this.viewdate.date = e.date;
      this.viewdate.date_end = e.date_end;
      this.viewdate.date_start = e.date_start;
      this.viewdate.name = e.name;
      this.viewdate.month = this.$moment().format("MM");
      this.viewdate.year = parseInt(this.$moment().format("YYYY"));

      /* axios.get("/planning/operation").then(
        function(res) {
          this.interventi = res.data;
        }.bind(this)
      ); */
    },
    operationsByDate(e){
      var dates = {
        date_start: this.$moment(e.startDate).format('YYYY-MM-DD'),
        date_end: this.$moment(e.endDate).format('YYYY-MM-DD')
      }
      axios.get("/planning/operation/hours_calendar?date_start="+dates.date_start+"&date_end="+dates.date_end).then(function(res){
          this.events = res.data;
        }.bind(this)
      );
    }
  }
};


</script>
