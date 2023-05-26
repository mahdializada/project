<template>
  <div>
    <template>
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
                :loading="loading"
                v-model="searchValue"
                :items="items"
                @keyup="search"
                label="Search..."
                append-icon="mdi-magnify"
                solo
                dense
                depressed
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
                :class="selected.length > 0 ? '' : 'customDisabled'"
                v-if="tabKey !== 'blocked'"
              >
                <div class="d-block" style="color: white">
                  <i class="fa fa-eye"></i>
                  <div>view</div>
                </div>
              </v-btn>
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
      <br>


      <!-- dataTable -->
      <v-card>
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
         <template v-slot:[`item.status`]="{ item }">
              <v-chip :color="getColor(item.status)" dark>
                {{ item.status }}
              </v-chip>
          </template>
        </v-data-table>
      </v-card>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    top: Array,
    record: Array,
    load: Boolean,
  },
  data() {
    return {
      actions: ["Active", "In Active"],
      status: "",
      tabKey: "all",
      selected: [],
    };
  },
  methods: {
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
    openModel() {
      this.$emit("openModel");
    },
    selectRow(item) {
      if (this.selected.find((i) => i.id == item.id)) {
        this.selected = this.selected.filter((i) => i.id != item.id);
      } else {
        this.selected.push(item);
      }
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

</style>
