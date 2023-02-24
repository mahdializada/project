<template>
  <div>
    <template>
      <div>
        <MyView />

      </div>
    </template>
    <template>
      <v-col class="mb-5" cols="12" style=" margin-top: 10px">

        <v-card style="height: 120px">

          <h4 style="margin-left: 5px">Actions</h4>
          <v-row>
            <v-col cols="5">
              <div class="ml-8 mb-0">
                <v-row>
                  <v-col cols="10">
                    <v-text-field label="Search..." append-icon="mdi-magnify" v-model="searchValue" outlined focused
                      dense rounded solo small class="mt-5 ml-0" @keyup="search">
                    </v-text-field>
                  </v-col>
                </v-row>
              </div>
            </v-col>

            <v-col cols="7">
              <v-row class="mt-5 content-right">
                <v-btn @click="viewModel" style="height: 45px; width:100px;" class="blue-grey mr-1 " depressed solo
                  :class="selected.length > 0 ? '' : 'customDisabled'">
                  <div style="color:white; ">
                    <i class="fa fa-eye"></i>
                    <div>view</div>
                  </div>
                </v-btn>
                <v-btn style="height: 45px; width:100px; margin-right: 5px;" depressed dense solo class=""
                  color="indigo darken-1" @click="editModel" :class="selected.length > 0 ? '' : 'customDisabled'"
                  justify="center">
                  <div class="d-block" style="color:white;">
                    <i class="fa fa-user-pen"></i>
                    <div>Edit</div>
                  </div>
                </v-btn>


                <v-btn depressed dense solo color="error" @click="deleted('delete')"
                  :class="selected.length > 0 ? '' : 'customDisabled'" justify="center"
                  style="margin-right: 5px; color: white; height: 45px; width:100px;">
                  <div class="d-block" style="color: white">
                    <i class="fa fa-trash"></i>
                    <div>Delete</div>
                  </div>
                </v-btn>


                <v-btn depressed dense solo class="btn primary" v-if="tabKey == 'removed'" @click="deleted('restore')"
                  :class="selected.length > 0 ? '' : 'customDisabled'" justify="center"
                  style="margin-right: 5px; height: 45px; width:100px;">
                  <div class="d-block" style="color: white">
                    <i class="fa fa-trash-arrow-up"></i>
                    <div>Restore</div>
                  </div>
                </v-btn>
                <v-btn style="height: 45px; width:120px;" color="primary" class="pa-2" depressed dense solo
                  @click="createDialog">
                  <div class="d-block">
                    <i class="fa fa-file-arrow-up"></i>
                    <div>Create New</div>
                  </div>
                </v-btn>

              </v-row>
            </v-col>
          </v-row>
        </v-card>
      </v-col>

      <v-card>
        <v-tabs background-color="primary" dark>
          <v-tab v-for="item in tabItems" :key="item.tab" show-arrows @click="tabChange(item.value)">
            <div style="margin-right:100px" class="d-flex row">
              <div>

                <v-icon style="margin-right:30px">{{ item.icon }}</v-icon> {{ item.tab }}
              </div>

            </div>
          </v-tab>
        </v-tabs>

        <v-tabs-items>
          <v-data-table v-model="selected" :headers="headers" :items="items.data" item-key="id" show-select
            class="elevation-1" :loading="loading" @click:row="selectRow">



            <template v-slot:[`item.photo`]="{ item }">
              <v-avatar>
                <img :src="`http://localhost:8000/scholarships/photo/${item.photo}`"
                  alt="lost">
              </v-avatar>

            </template>

            <template v-slot:item.status="{ item }">
              <v-chip :color="getColor(item.status)" dark style=" text-align:center;" justify="center">
                <div class="text-center" justify="center" style=" width:70px; text-align:center;">
                  {{ item.status }}
                </div>
              </v-chip>

            </template>

            template


            <template v-slot:item.created_at="{ item }">
              <span>{{ new Date(item.created_at).toLocaleString() }}</span>
            </template>

            <template v-slot:[`item.scholarship_title`]="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span v-bind="attrs" v-on="on" v-html="item.scholarship_title?.substring(0, 30)"></span>
                </template>
                <span v-html="item.scholarship_title"></span>
              </v-tooltip>
            </template>

            <template v-slot:[`item.is_active`]="{ item }">
            <v-avatar>
              <v-switch

                @click="changeStatus(item.id)"
                :input-value="item.is_active ?? 0"
                class="pa-0"
              ></v-switch>
            </v-avatar>
          </template>

          </v-data-table>
        </v-tabs-items>
      </v-card>
    </template>



  </div>


</template>

<script>
import MyView from "./MyView.vue";
export default {
  component: { MyView},
  props: {
    headers: Array,
    items: Array,
    loading: Boolean,
    search_tab: String,
  },
  data() {
    return {
      actions: ["active", "inactive"],
      status: "",
      tabKey: "all",
      selected: [],
      tabItems: [
        { tab: "ALL", value: "all", icon: "mdi-border-all" },
        { tab: "ACTIVE", value: "1", icon: "mdi-thumb-up-outline" },
        { tab: "INACTIVE", value: "'0'", icon: "mdi-cancel" },

      ],

      searchType: null,
      company_id: "",
      flag: false,
    };
  },

  methods: {
    // start of search methods
    filterData(data) {
      this.$emit("filterData", data);
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

        let data = {
          searchData: this.searchValue,
          searchTab: this.search_tab,
          type: "search",
        };
        console.log(this.search_tab);
        const result = await this.$axios.get("scholarship", { params: data });
        this.$emit("searchResult", result);

      }
    },
    // end of search methods


    createDialog() {
      this.$emit("createModel");
    },
    getColor(status) {
      if (status == "1") return "green";
      else if (status == "0") return "red";

      else return "blue";
    },
    changeStatus(id) {
      this.loading = true;
      this.$emit("changeStatus", id, this.tabKey);
      this.loading = false;
      this.selected = [];
    },

    deleted(type) {
      if (this.tabKey == "all") {
        alert("You cannot delete when All tab is selected!");
      } else {
        let delId = this.selected.map((item) => item.id);
        let data = {
          ids: delId,
          tabKey: this.tabKey,
          type: type,
        };
        this.$emit("deleteItem", data);
      }
    },
    editModel() {
      if (this.selected.length == 1) {
        this.$emit("editModel", this.selected[0]);
        this.selected = [];
      } else alert("Please select one record!");
    },
    openModel() {
      this.$emit("insertModel");
    },
    tabChange(item) {
      this.selected = [];
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
  },
};
</script>

<style scoped>
.customDisabled {
  opacity: 0.5;
  pointer-events: none !important;
  cursor: not-allowed !important;
}
</style>
