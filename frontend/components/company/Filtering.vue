<template>
  <v-dialog v-model="filterDialog" class="center">
    <v-card width="1200" height="560" class="ma-auto">
      <v-card-title>
        <v-row class="white">
          <v-col cols="12">
            <div class="d-flex">
              <h3><i class="fa fa-filter">&nbsp;&nbsp;</i>{{ title }}</h3>
              <v-spacer></v-spacer>
              <v-btn
                solo
                dense
                fab
                small
                @click="closeDownloadDialog"
                class=""
                style="background-color: white"

              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-card-title>
      <v-card-text class="mt-4">
        <v-row justify="space-around">
          <v-col cols="4" class=" pa-0">
            <v-card class="ml-0 pa-0" height="420" style="border-right: 9px solid lightgrey">
              <v-card-title>
                <v-row>
                  <v-col cols="4"></v-col>
                  <v-col cols="4">
                    <h3 class="font-weight-black">Data</h3>
                  </v-col>
                </v-row>
              </v-card-title>
              <v-card-text>
                <h4 class="font-weight-black mb-1">Country</h4>
                <v-autocomplete
                  :items="country_data"
                  v-model="country_id"
                  item-text="name"
                  item-value="id"
                  label="Country"
                  :loading = "DataLoad"
                  background-color="indigo accent-1"
                  clearable
                  dense
                  solo
                ></v-autocomplete>
                <h4 class="font-weight-black mb-1 mt-1">System</h4>
                <v-select :items="system_data"
                  v-model="system_id"
                  item-text="system_name"
                  item-value="id"
                  clearable

                  :loading = "DataLoad"
                label="System"  background-color="indigo accent-1" dense solo>
                </v-select>
                <h4 class="font-weight-black mb-1 mt-1">Invesment Type</h4>
                <v-select
                  v-model="invesment_type"
                  :items="['main company', 'others']"
                  label="Invesment Type"
                  dense
                  clearable
                  :loading = "DataLoad"
                  solo
                  background-color="indigo accent-1"
                ></v-select>
                <h4 class="font-weight-black mb-1 mt-1">Company Name</h4>
                <v-text-field
                  solo
                  dense
                  v-model="company_name"
                  background-color="indigo accent-1"
                  placeholder="Enter a company name"
                >
                </v-text-field>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="4" class=" pa-0">
            <v-card class="ml-0 pa-0" height="420" style="border-right: 9px solid lightgrey">
              <v-card-title>
                <v-row>
                  <v-col cols="4"></v-col>
                  <v-col cols="8">
                    <h3 class="font-weight-black">Date Range</h3>
                  </v-col>
                </v-row>
              </v-card-title>
              <v-card-text>
                <h4 class="font-weight-black mb-1">Created At</h4>
                <v-menu
                  v-model="menu1"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y

                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="created_date"

                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      background-color="indigo accent-1"
                      placeholder="Created At"

                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="created_date"
                    @input="menu1 = false"
                  ></v-date-picker>
                </v-menu>

                <h4 class="font-weight-black mb-1 mt-1">Updated At</h4>
                <v-menu
                  v-model="menu2"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"

                  class="bgColor"
                >
                  <template  class="bgColor" v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="updated_date"

                      prepend-icon="mdi-calendar"
                      color="indigo accent-1"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      class="bgColor"
                      background-color="indigo accent-1"
                      placeholder="Updated At"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="updated_date"
                    @input="menu2 = false"
                  ></v-date-picker>
                </v-menu>

              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="4" class=" pa-0">
            <v-card class="ml-0 pa-0" height="420" style="border-right: 9px solid lightgrey">
              <v-card-title>
                <v-row>
                  <v-col cols="4"></v-col>
                  <v-col cols="8">
                    <h3 class="font-weight-black">ID Filtering</h3>
                  </v-col>
                </v-row>
              </v-card-title>
              <v-card-text>
                <h4 class="font-weight-black mb-1">ID</h4>
                <v-text-field
                  solo
                  dense
                  v-model="company_id"
                  placeholder="Ex: 1101101"
                  background-color="indigo accent-1"
                  :class="searchByID?'':'customDisabled'"
                >
                </v-text-field>
                <v-card-action>
                  <v-btn-toggle
                    rounded
                    dense
                    solo
                    small
                    class="p-0 mt-1"
                    style="float: right"
                  >
                  <v-btn @click="searchID('exclude')" :class="exclude?'':'customDisabled'"   style="height: 20px; font-size: 10px">Include</v-btn>
                  <v-btn @click="searchID('include')" :class="include?'':'customDisabled'"  style="height: 20px; font-size: 10px">Exclode</v-btn>
                  </v-btn-toggle>
                </v-card-action>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-row>
          <v-col cols="8"></v-col>
          <v-col cols="4">
            <div class="d-flex">
            <v-spacer></v-spacer>
            <v-btn
              solo
              dense

              color="primary"
              class="mr-2"
              style="font-size:10px; font-weight: bold;"
              @click="submitFilter('filter')"
            >
              Submit
            </v-btn>
            <v-btn
              solo
              dense

              style="font-size:10px; font-weight: bold;"
              color="red lighten-1"
              class="mr-2"
              @click="clearFilter"
            >
              clear Filter
            </v-btn>
            <v-btn
              solo
              dense

              color="grey lighten-3"
              class="mr-5"

              @click="closeDownloadDialog"
              style="font-size:10px; font:black; font-weight: bold;"
            >
              Cancel
            </v-btn>

          </div>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props:{
    title:{
      type: String,
      default:'Filter departments'
    }
  },
  data() {
    return {
      filterDialog: false,
      date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
      menu2: false,
      menu1: false,
      d_data : [],
      country_data:[],
      system_data:[],
      invesment_type:'',
      system_id:'',
      company_id:'',
      include_company_id:'',
      country_id:'',
      system_id:'',
      company_name:'',
      created_date:'',
      updated_date:'',
      DataLoad : false,
      searchByID : true,
      exclude : false,
      include : true,

    };
  },
  methods: {
   async submitFilter(filter){
      let filterData = {
        country_id : this.country_id,
        system_id : this.system_id,
        investment_type : this.invesment_type,
        created_date: this.created_date,
        updated_date : this.updated_date,
        company_id: this.company_id,
        company_name: this.company_name,
        type: filter,
      };
      const filteredData = await this.$axios.get('company',{params:filterData});
      console.log(filteredData);
      this.$emit("filterData",filteredData.data);
      this.filterDialog = false;
    },
    clearFilter(){
      this.invesment_type= '';
      this.system_id ='';
      this.company_id ='';
      this.country_id='';
      this.system_id='';
      this.company_name='';
      this.created_date='';
      this.updated_date='';

    },
    searchID(type){
          if (type == 'exclude'){
            this.include = true;
            this.exclude = false;
            this.searchByID = !this.searchByID;
          }
          else{
            this.include = false;
            this.exclude = true;
            this.searchByID = !this.searchByID;
          }


    },
   async openFilter() {
     this.filterDialog = true;
     this.DataLoad = true;
      const systemData = await this.$axios.get("system");
      this.system_data = systemData.data;
      this.DataLoad = false;
      console.log(this.system_data);
      const countries = await this.$axios.get("country");
      this.country_data = countries.data;
      console.log(this.system_data);
    },
    closeDownloadDialog() {
      this.filterDialog = false;
    },
  },
};
</script>

<style scoped>
.bgColor{
  background-color: indigo accent-1 !important;
}
.customDisabled {
  opacity: 0.5;
  pointer-events: none !important;
  cursor: not-allowed !important;
}
</style>
