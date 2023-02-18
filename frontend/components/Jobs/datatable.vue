<template>
  <div>
    <template>
              <JobView  ref="viewRef"/>
      <!-- Actions-->
      <v-col cols="12" style="padding: 0px; margin-top: 0px">
        <v-card style="height: 90px" class="elevation-5">
          <h4 style="margin-left: 5px; font-weight: bold" class="ml-2">
            Actions
          </h4>
          <v-row
            align="center"
            justify="center"

          >
            <v-col cols=1></v-col>
            <v-col cols="5">
              <div>
              <v-text-field
                label="Search..." 
                append-icon="mdi-magnify" 
                :loading="loading1"
                v-model="searchValue" 
                outlined focused
                dense rounded solo small  @keyup="search"
                style="
                  border: 1px solid lightgrey;
                  border-radius: 25px 25px 25px 25px;
                  height: 40px;
                  width: 290px;
                "
              ></v-text-field>
              </div>
            </v-col>
            <v-col cols="6">
              <v-btn
                style="margin-right: 5px"
                class="pa-5"
                color="blue-grey"
                justify="center"
                depressed
                small
                @click="viewModel"
                :class="selected.length > 0 ? '' : 'customDisabled'"
                v-if="tabKey !== 'blocked'"
              >
                <div class="d-block" style="color: white">
                  <i class="fa fa-eye"></i>
                  <div>view</div>
                </div>
              </v-btn>
              <!-- <view  ref="viewRef" /> -->
              <v-btn
                style="margin-right: 5px"
                class="pa-5"
                color="indigo darken-1"
                justify="center"
                depressed
                small
                @click="openEditModel"
                :class="selected.length > 0 ? '' : 'customDisabled'"
              >
                <div class="d-block" style="color: white">
                  <i class="fa fa-user-pen"></i>
                  <div>Edit</div>
                </div>
              </v-btn>
              <v-btn
                style="margin-right: 5px"
                class="pa-5"
                color="error"
                justify="center"
                depressed
                small
                @click="deleted()"
                ><div class="d-block" style="color: white">
                  <i class="fa fa-trash"></i>
                  <div>Delete</div>
                </div>
              </v-btn>
              <v-btn
                style="margin-right: 5px"
                class="pa-5"
                color="primary"
                justify="center"
                depressed
                small
                @click="openModel"
                solo
                ><div class="d-block">
                  <i class="fa fa-plus"></i>
                  <div>Create Job</div>
                </div></v-btn
              >
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <br />

      <!-- dataTable -->
      <v-card>
        <v-tabs class="tab" background-color="primary" show-arrows dark style="">
          <v-tabs-slider color="blue lighten-3"></v-tabs-slider>

          <v-tab
            class="tabstyle"
            v-for="item in tabItems"
            :key="item.tab"
            @click="tapChange(item.value)"
          >
            <div style="margin-right: 50px">
              <v-icon>{{ item.icon }}</v-icon>
            </div>
            {{ item.tab }}
            <v-spacer></v-spacer>
          </v-tab>
        </v-tabs>
        <v-data-table
          :headers="top"
          :items="record.data"
          v-model="selected"
          item-key="id"
          show-select
          @click:row="selectRow"
          class="elevation-1"
          :loading="load"
        >
          <template v-slot:[`item.is_active`]="{ item }">
            <v-avatar>
              <v-switch
                :loading="primary"
                @click="changeStatus(item.id)"
                :input-value="item.is_active ?? 0"
                class="pa-0"
              ></v-switch>
            </v-avatar>
          </template>
          <!-- <template v-slot:[`item.job_descriptions`]="{ item }">
            <span v-html="item.job_descriptions"></span>
          </template> -->
        </v-data-table>
      </v-card>
    </template>
  </div>
</template>

<script>
import JobView from "./JobView.vue";
export default {
  components: { JobView },
  props: {
    top: Array,
    record: Array,
    load: Boolean,
    search_tab: String,

  },
  data() {
    return {
      actions: ["Active", "In Active"],
      tabKey: "all",
      selected: [],
      user_id:'',
      loading1:false,
      searchType: null,
      flag: false,
      loading: false,
      tabItems: [
        { tab: "All", value: "all", icon: "mdi-border-all" },
        { tab: "Active", value: "1", icon: "mdi-thumb-up-outline" },
        { tab: "Inactive", value: "'0'", icon: "mdi-cancel" },
      ],
    };
  },
  methods: {
    async search(searchValue) {
      if (searchValue.key == "Enter" && this.searchValue)
        this.flag = true;
      console.log(searchValue.key);
      if (
        (searchValue.key == "Enter" && this.searchValue) ||
        (searchValue.key == "Backspace" && !this.searchValue && this.flag)
        ){
        if (searchValue.key == "Backspace" && !this.searchValue && this.flag)
          this.flag = false;

          let data = {
            searchData: this.searchValue,
            searchTab: this.search_tab,
            type: "search",
          };
          console.log(this.search_tab);
          this.loading1=true;
          const result = await this.$axios.get("job", { params: data });
          this.loading1=false;
          this.$emit("searchResult", result);

      }
    },
    viewModel() {
       if (this.selected.length == 1) {
        // this.$refs.viewRef.viewModel(this.selected[0]);
        this.$refs.viewRef.openView(this.selected[0]);
        this.selected = [];
      } else {
        alert("please Select one record");
      }
      this.selected = [];
    },
    openModel() {
      this.$emit("openModel");
    },
    tapChange(item) {
      this.tabKey = item;
      this.$emit("getRecord", item);
    },
    selectRow(item) {
      if (this.selected.find((i) => i.id == item.id)) {
        this.selected = this.selected.filter((i) => i.id != item.id);
      } else {
        this.selected.push(item);
      }
    },
    changeStatus(id) {
      this.loading = true;
      this.selected = [];
      this.$emit("changeStatus", id, this.tabKey);
      this.loading = false;
      this.selected = [];
    },
    openEditModel() {
      if (this.selected.length == 1) {
        this.$emit("editItem", this.selected[0]);
      } else {
        alert("please Select one record");
      }
      this.selected = [];
    },
    deleted() {
      let DelId = this.selected.map((item) => item.id);
      this.$emit("DeleteRecord", DelId);
      this.selected = [];
    },
  },
};
</script>

<style scoped >
.customDisabled {
  opacity: 0.8 !important;
  pointer-events: none !important;
  cursor: not-allowed !important;
}
.tabstyle {
  width: 270px;
  border-radius: 10px 30px 0px 0px;
}
.tabstyle:active{
  background-color:rgb(48, 167, 151) ;
}
</style>
