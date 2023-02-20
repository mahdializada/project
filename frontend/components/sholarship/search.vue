<template>
  <div class="pa-0 mx-0">

    <filtering ref="filterRef" @filterData="filterData" />
    <v-card>
      <h4 class="ml-4 my-4">Search & Filter</h4>
      <v-row>
        <v-col cols="5">
          <div class="ml-8 mb-0">
            <h4>Search by ID</h4>
            <v-row>
              <v-col cols="4">
                <v-radio-group
                  v-model="searchType"
                  column
                  class="mt-0"
                  mandatory
                >
                  <v-radio label="ID" value="id"></v-radio>
                  <v-radio label="content" value="content"></v-radio>
                </v-radio-group>
              </v-col>

              <v-col cols="8">
                <v-text-field
                  label="Search..."
                  append-icon="mdi-magnify"
                  v-model="searchValue"
                  outlined
                  focused
                  dense
                  rounded
                  solo
                  small
                  class="mt-1 ml-0"
                  @keyup="search"
                >
                </v-text-field>
              </v-col>
            </v-row>
          </div>
        </v-col>
        <v-col class="" cols="4">
          <v-row>
            <div class="text-center justify-center">
              <v-btn
                class="mt-11 justfiy justify-center"
                outlined
                color="indigo"
                solo
                @click="openFilter"
                style="text-align: center"
              >
                <div class="d-block">
                  <i class="fa fa-filter"></i>
                  <div>Filter</div>
                </div>
              </v-btn>



            </div>
          </v-row>
        </v-col>
        <v-col cols="3">
          <v-row>
            <v-btn
              style="height: 37px; font-weight: 900"
              color="primary"
              class="mt-11 pa-2"
              depressed
              dense
              x-small
              solo
              @click="dialog"
              ><div class="d-block">
                <i class="fa fa-file-arrow-up"></i>
                <div>Create Company</div>
              </div></v-btn
            >
          </v-row>
        </v-col>
      </v-row>
    </v-card>
  </div>
</template>

<script>


import Filtering from "./Filtering.vue";
export default {
  components: { Filtering },
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
      searchType: null,
      company_id: "",
      flag: false,
    };
  },
  methods: {
    filterData(data) {
      this.$emit("filterData", data);
    },
    openFilter() {
      this.$refs.filterRef.openFilter();
    },

    dialog() {
      this.$emit("createModel");
      console.log("component is run.");
    },
    async search(searchValue) {
      if (searchValue.key == "Enter" && this.searchValue)
       this.flag = true;
      console.log(searchValue.key);
      if (
        (searchValue.key == "Enter" && this.searchValue) ||
        (searchValue.key == "Backspace" && !this.searchValue && this.flag)
      ) {
        if (searchValue.key == "Backspace" && !this.searchValue && this.flag)
          this.flag = false;
        if (this.searchType == "id") {
          let data = {
            searchData: this.searchValue,
            searchTab: this.search_tab,
            type: "idSearch",
          };
          const result = await this.$axios.get("company", { params: data });
          this.$emit("IDsearchResult", result.data);
        } else {
          let data = {
            searchData: this.searchValue,
            searchTab: this.search_tab,
            type: "nameSearch",
          };
          const result = await this.$axios.get("company", { params: data });
          this.$emit("IDsearchResult", result.data);
        }
      }
    },
  },
};
</script>

<style scoped>
</style>
