<template>
  <da-programmare 
      :chars="chars"
      :invoices_to="invoices_to"
      :viewdate="viewdate"
      :serchevents="events"
      :interventis="interventi"

      v-on:viewName="operations($event); operationsByDate($event);"
  ></da-programmare>
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
      this.viewdate.month = this.$moment(e.date).format("MM");
      this.viewdate.year = parseInt(this.$moment(e.date).format("YYYY"));

      axios.get("/planning/operation", {
        params: {
          date: e.date,
        }
      }).then(
        function(res) {
          this.interventi = res.data;
        }.bind(this)
      );
      console.log(e.date);
    },
    operationsByDate(e){
      var current_date = this.$moment().format("YYYY-MM-DD");
      axios.get("/planning/operation/calendar", {
        params: {
          date_start: e.date_start,
          date_end: e.date_end,
        }
      }).then(function(res){
          this.events = res.data;             
        }.bind(this)
      ); 
    }
  }
};

 

</script>

