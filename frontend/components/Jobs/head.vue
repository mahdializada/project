<template>
  <div>
    <v-card style="height: 100px" class="pt-2">
      <v-row>
        <v-avatar
          size="80"
          style="
            margin-left: 40px;
            margin-top: 10px;
            background-color: lightblue;
          "
        >
          <span><i class="fa fa-briefcase" style="font-size: 30px"></i></span>
        </v-avatar>
        <span>
          <h2 class="ml-4 mt-5" style="height: 20px">Job List</h2>

          <span style="font-size: 15px" class="pb-2">
            <i
              class="fa fa-briefcase ml-4 mt-5"
              style="font-size: 12px; margin-right: 4px"
            ></i>
            Job Website Admin Panel<i class="fa fa-angles-right"></i>
            <i class="fa fa-briefcase"></i>
            Job List
          </span>
        </span>
      </v-row>
    </v-card>
    <br />
        <v-card class="flex-grow-1 d-flex v-sheet pa-0 ma-0 elevation-5">
      <div class="d-flex pa-0 ma-0">

        <div class="d-flex flex-column" >
          <span>
           
          </span>
        </div>

        <v-row>
        </v-row>
      </div>
    </v-card>
   
  </div>
</template>

<script>
import Filtering from "./Filtering.vue";
// import Manual from "./Manual.vue";


export default {
  components: {  Filtering },
  props: {
    d_data: {
      type: Array,
      default: function () {
        return [];
      },
    },
    search_tab: String,
  },
  data() {
    return {
      user_id:'',
      radioGroup: 1,
      searchType: null,
      searchValue: "",
    };
  },
  methods:{
       filterData(data){
      this.$emit('filterData',data);
    },
     openManual(){
      this.$refs.manualRef.openManual();
    },
     openFilter(){
      this.$refs.filterRef.openFilter();
    },
    openModel() {
      this.$emit("openModel");
    },
    openModelD(){
      this.$refs.downloadRef.opneModleD(this.d_data);
    },
    async search(searchValue) {
      if (searchValue.key == "Enter") {
        if (this.searchType == "id") {
          let data = {
            searchData: this.searchValue,
            searchTab: this.search_tab,
            type: "filter",
          };
        const result = await this.$axios.get("user", { params: data });
        this.$emit("IDsearchResult", result.data);

        } else {
          let data = {
            searchData: this.searchValue,
            searchTab: this.search_tab,
            type: "nameSearch",
          };
        const result = await this.$axios.get("user", { params: data });
        this.$emit("IDsearchResult", result.data);
        }


      }
    },
  },
  
}
</script>

<style scoped>
</style>