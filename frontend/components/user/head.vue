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
          <span><i class="fa fa-user" style="font-size: 30px"></i></span>
        </v-avatar>
        <span>
          <h2 class="ml-4 mt-5" style="height: 20px">User List</h2>

          <span style="font-size: 15px" class="pb-2">
            <i
              class="fa fa-user ml-4 mt-5"
              style="font-size: 12px; margin-right: 4px"
            ></i>
            User Management System <i class="fa fa-angles-right"></i>
            <i class="fa fa-user"></i>
            User List
          </span>
        </span>
      </v-row>
    </v-card>
    <br />
        <v-card class="flex-grow-1 d-flex v-sheet pa-0 ma-0 elevation-5">
      <div class="d-flex pa-0 ma-0">
        <div class="d-flex flex-column" style="width: 150px">
          <h4 class="ml-2">Search & Filter</h4>
          <h5 class="ml-10">Search Type</h5>
          <div class="d-flex">
            <v-radio-group
              v-model="searchType"
              column
              class="pt-0 ml-12 mt-0 mr-12"
              style="width: 100px"
              mandatory
            >
              <v-radio
                label="ID"
                value="id"
                class="mb-0"
                style="width: 50px"
              ></v-radio>
              <v-radio
                label="Content"
                value="content"
                style="width: 50px"
              ></v-radio>
            </v-radio-group>
          </div>
        </div>

        <div class="d-flex flex-column" style="width: 290px">
          <span>
            <v-text-field
              :loading="loading"
              v-model="searchValue"
              :items="items"
              @keyup="search"
              class="mt-10"
              label="Search..."
              append-icon="mdi-magnify"
              solo
              dense
              depressed
              style="
                border: 1px solid lightgrey;
                border-radius: 25px 25px 25px 25px;
                height: 40px;
              "
            ></v-text-field>
          </span>
        </div>

        <v-row>
          <div class="flex-column ml-12 mt-4">
            <v-btn
              style="height: 45px; font-weight: 900"
              outlined
              color="primary"
              class="mt-9 pa-2"
              depressed
              dense
              x-small
              solo
              text
              @click="openFilter"
              >
                <div class="d-block">
                  <i class="fa fa-filter"></i>
                  <div>Filter</div>
                </div>
              </v-btn>
              <filtering ref="filterRef" @filterData="filterData"/>

            <v-btn
              style="height: 45px; font-weight: 900"
              outlined
              color="primary"
              class="mt-9 pa-2"
              depressed
              dense
              x-small
              solo
              text
              ><div class="d-block"><i class="fa fa-table-columns"></i><div>Column</div></div></v-btn
            >

            <v-btn
              style="height: 45px; font-weight: 900"
              outlined
              color="primary"
              class="mt-9 pa-2"
              depressed
              dense
              x-small
              solo
              text
              @click="openModelD"
              ><div class="d-block"><i class="fa fa-download"></i><div>Download</div></div></v-btn
            >

          </div>
          <Download ref="downloadRef" />
          <div class="flex-column ml-16 mt-4 " >
            <v-btn
              style="height: 45px; font-weight: 900"
              color="primary"
              class="mt-9 pa-2"
              depressed
              dense
              x-small
              @click="openManual"
              solo
              ><div class="d-block"><i class="fa fa-file-arrow-up"></i><div>Manual Upload</div></div></v-btn
            >
            <ManualUpload ref="manualRef" />
            <v-btn
              style="height: 45px; font-weight: 900"
              color="primary"
              class="mt-9 pa-2"
              depressed
              dense
              x-small
              @click="openModel"
              solo
              ><div class="d-block"><i class="fa fa-user-plus"></i><div>Create User</div></div></v-btn
            >
          </div>
        </v-row>
      </div>
    </v-card>

  </div>
</template>

<script>
import Download from "./Download.vue";
import Filtering from "./Filtering.vue";
import ManualUpload from "./ManualUpload.vue";
// import Manual from "./Manual.vue";


export default {
  components: { Download , Filtering, ManualUpload },
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
