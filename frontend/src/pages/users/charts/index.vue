<template>
<v-container fluid class="pa-0">
  <v-row>
    <v-col cols="12" class="py-0">
      <PageHeader
          Icon="mdi-chart-bar"
          Title="menu.user_chart"
          :Breadcrumb="breadcrumb"
       />
    </v-col>
    <v-col cols="12" class="py-0">
      <v-card class=" py-1 mb-2" elevation="1" outlined>
          <h4 class="title ms-2 mb-2">{{ $tr("search_filter") }}</h4>
          <h5 class="title2 ms-3">{{ $tr("search_type") }}</h5>
          <v-row class="mx-3 mb-0"  align="center">
            <v-radio-group row class="my-1 mb-0" v-model="filter_type" hide-details="true">
              <v-radio label="Week"   value="week" />
              <v-radio label="Month"  value="month" />
              <v-radio label="Year"   value="year" />
            </v-radio-group>
            <v-menu
              v-model="menu"
              :close-on-content-click="false"
              :nudge-right="40"
              transition="scale-transition"
              offset-y
              min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="date"
                  label="search_date"
                  append-icon="mdi-calendar"
                  class="ms-2"
                  readonly
                  outlined
                  rounded
                  dense
                  hide-details=""
                  v-bind="attrs"
                  v-on="on"

                ></v-text-field>
              </template>
              <v-date-picker :type="filter_type=='week'?'date':'month'" :active-picker="filter_type=='year'?'YEAR':''" v-model="date" @input="manualFilter"></v-date-picker>
            </v-menu>
            <v-spacer/>
            <CustomButton
                  icon="fa-filter"
                  text="filter"
                />
          </v-row>
      </v-card>
    </v-col>
  </v-row>
  <Chart :data.sync="get_data" :categories.sync="get_categories" @prev="prev" @next="next" :key="key">
       <v-tabs
                  v-model="tabIndex"
                  height ="60"
                  background-color="primary"
                  active-class="active-background"
                  show-arrows
                  dark
                  center-active
                >
       <client-only>
          <v-tab v-for="(item, i) in tabItems" :key="i" class="px-3">
            <div class="d-flex flex-column justify-start">
            <p
              :class="`${
                item.isSelected ? 'selected' : 'not-selected'
              } tab-title text-capitalize mb-0 pb-1`"
            >
              {{ item.text }}
            </p>
            <span class="text-left">{{filteredData}}</span>
            </div>
          </v-tab>
        </client-only>
       </v-tabs>
  </Chart>
  <ProgressBar v-if="api_call"/>
</v-container>
</template>

<script>
import Chart from '../../../components/common/Charts/ApexChart.vue'
import {mapActions, mapGetters} from 'vuex';
import CustomButton from '../../../components/design/components/CustomButton.vue';
import CustomCard from '../../../components/common/CustomCard.vue';
import ProgressBar from '../../../components/common/ProgressBar.vue';
import PageHeader from '../../../components/design/PageHeader.vue';
export default {
  // moved to created
  // async async Data({ store }) {
  //   await store.dispatch("userChart/weekFilter", new Date().toISOString().slice(0,10));
  // },
  components:{
    Chart,
    CustomButton,
    CustomCard,
    ProgressBar,
    PageHeader
  },

  data(){
    return{
      today:        new Date(),
      changeDate:   new Date(),
      date:         new Date().toISOString().substr(0, 10),
      menu:         false,
      filter_type:  'week',
      tabIndex:     0,
      filteredData: [],
       tabItems: [
      {text: "total_user", isSelected: 1, key: "all"},
      {text: "signed_user", isSelected: 0, key: "active"},
      {text: "approved_user", isSelected:  0, key: "active"},
    ],
      breadcrumb: [
      {text: "dashboard", exact: true, to: "/"},
      {text: "user_list", disabled: true, to: "/users/charts", icon: "mdi-chart-bar"},
    ],

    data:             [],
    category:         [],
    rightSideData:    [],
    leftSideData:     [],

    rightSideCategory:[],
    leftSideCategory: [],
    leftSide:         true,
    key:              0,

    }
  },
// cfffdffd
  watch:{
    tabIndex: function (value) {
      this.filter(this.tabItems[value].key);
      this.assignTotals(value);
    },
    filter_type: function (){
          this.changeDate = this.today;
         if(this.filter_type=="week"){
            this.filterByWeek("manual");
         }
         if (this.filter_type=="month") {
              this.filterByMonth("manual");
         }
          if(this.filter_type=="year"){
           this.filterByYear("manual");
         }
    },

  },
   computed: {
    ...mapGetters({
      api_call:       "userChart/get_api_call",
      get_data:       "userChart/get_data",
      get_categories: "userChart/get_categories",
    }),
  },
 async created(){
    await this.weekFilter(new Date().toISOString().slice(0,10));

  },
  methods:{
    ...mapActions({

      weekFilter:  "userChart/weekFilter",
      monthFilter: "userChart/monthFilter",
      yearFilter:  "userChart/yearFilter",
    }),
    // toggleData(){
    //   this.key++;
    //     if(this.leftSide){
    //         var half_length    = Math.ceil(this.get_data?.length / 2);
    //         this.category      = this.get_categories;
    //         // this.category      = this.get_categories?.slice(0,half_length);
    //         this.data          = this.get_data;
    //         // this.data          = this.get_data?.slice(0,half_length);
    //         this.leftSide      = false;
    //     }
    //     else{
    //       var half_length     = Math.ceil(this.get_data?.length / 2);
    //       this.data           = this.get_data?.slice(half_length,this.get_data?.length);
    //       this.category       = this.get_categories?.slice(half_length,this.get_categories?.length);
    //       this.leftSide       = true;

    //     }
    // },

    assignTotals(value) {
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? item.isSelected = 1 : item.isSelected = 0
        return item;
      });},
       getTotalLength(key){
        // return this.getTotals(key,this.system_name);
        return 5000;
      },
      filter(key){
        this.filteredData = this.FilterUser(key);
      },
      FilterUser(key){
        if(key==="all"){
          return 534;
        }
        else{
          return 300
        }
      },

      next(){
         if(this.filter_type=="week"){
            this.filterByWeek("next");
         }
         if (this.filter_type=="month") {
              this.filterByMonth("next");
         }
          if(this.filter_type=="year"){
           this.filterByYear("next");
         }
      },
      prev(){
         if(this.filter_type=="week"){
            this.filterByWeek("prev");
         }
         if (this.filter_type=="month") {
              this.filterByMonth("prev");
         }
         if(this.filter_type=="year"){
           this.filterByYear("prev");
         }
      },

      manualFilter(){
        this.menu       = false;
        this.changeDate = new Date(this.date);
        //  if(this.filter_type=="week"){
        //     this.filterByWeek("manual");
        //  }
        //  if (this.filter_type=="month") {
        //       this.filterByMonth("manual");
        //  }
        //  if(this.filter_type=="year"){
        //    this.filterByYear("manual");
        //  }

      },

    async filterByMonth(next){
        let number = next=="next"?1 :next=="manual"?0:-1;
         Date.UTC(2019, 5, 11)
         this.changeDate = new Date(this.changeDate.getFullYear(), this.changeDate.getMonth()+number, this.changeDate.getDate());
        let date = this.changeDate.toISOString().slice(0,10);
      this.date  = date;
         await     this.monthFilter(date);
          this.key++;

      },
      async filterByYear(next){
            let number = next=="next"?1 :next=="manual"?0:-1;
            this.changeDate = new Date(this.changeDate.getFullYear()+number, this.changeDate.getMonth(), this.changeDate.getDate());
            let date   = this.changeDate.toISOString().slice(0,10);
            await this.yearFilter(date);
            this.date =date;
              this.key++;
      },

     async filterByWeek(next){
            let number = next=="next"?7 :next=="manual"?0:-7;
            this.changeDate = new Date(this.changeDate.getFullYear(), this.changeDate.getMonth(), this.changeDate.getDate() +number);
            let date = this.changeDate.toISOString().slice(0,10);
            this.date =date;
            await this.weekFilter(date);
            this.key++;


      }

  }


}
</script>

<style scoped>
.title,
.title2 {
  font-size: 1rem !important;
  line-height: 1rem !important;
}

.radio-label {
  font-size: 1rem;
  font-weight: 500;
}
</style>
