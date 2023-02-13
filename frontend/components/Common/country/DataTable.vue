<template>
  <div>
    <v-col cols="12" style="padding: 0px; margin-top: 10px">
      <v-card style="height: 90px">
        <h4 style="margin-left: 5px">Actions</h4>
        <v-row align="center" justify="center" style="margin: 15px">
          <v-btn
            style="
              margin-right: 5px;
              width: 90px;
              height: 40px;
              font-weight: 1000;
            "
            depressed
            dense
            small
            solo
            color="error"
            justify="center"
            :class="selected.length > 0 ? '' : 'customstyle'"
            :disabled="tabKey == 'all'"
            @click="changeStatus((status = 'blocked'))"
            v-if="tabKey !== 'blocked'"
          >
            <div class="d-block">
              <i class="fa fa-cancel"></i>
              <div>Block</div>
            </div>
          </v-btn>
          <span>
            <v-select
              :class="selected.length > 0 ? '' : 'customstyle'"
              v-model="status"
              :items="actions"
              label="Select Status"
              style="width: 120px; margin-right: 5px; height: 35px"
              justify="center"
              solo
              dense
              :disabled="tabKey == 'all'"
            ></v-select>
          </span>
          <v-btn
            style="
              margin-right: 5px;
              width: 90px;
              height: 40px;
              font-weight: 1000;
            "
            dense
            small
            solo
            class="info"
            :class="selected.length > 0 ? '' : 'customstyle'"
            :disabled="tabKey == 'all'"
            @click="changeStatus"
          >
            <div class="d-block">
              <i class="fa fa-check"></i>
              <div>Apply</div>
            </div>
          </v-btn>
        </v-row>
      </v-card>
    </v-col>
    <template>
      <v-card class="mt-4">
        <v-tabs background-color="primary" dark class="costumTab" show-arrows>
          <v-tab
            class="tab font-weight-bold"
            v-for="item in items"
            :key="item.tab"
            active-class
            @click="tabChange(item.value)"
          >
            <v-icon>{{ item.icon }}</v-icon>
            <v-spacer></v-spacer>
            {{ item.tab }}
            <v-spacer></v-spacer>
            <v-badge class="mt-3 mr-2" color="white" @click="countData">
            </v-badge>
          </v-tab>
        </v-tabs>
        <div class="table-responsive">
          <v-data-table
            v-model="selected"
            item.key="id"
            :headers="top"
            :items="country"
            :loading="load"
            @click:row="selectedRow"
            show-select
            height="500px"
            class="elevation-1 data"
            id="my-table"
          >
            <template v-slot:item.iso2="{ item }">
              <v-avatar>
                <span :class="' fi fi-' + item.iso2.toLowerCase()"></span>
              </v-avatar>
            </template>

            <template v-slot:item.status="{ item }">
              <v-chip :color="getColor(item.status)" dark>
                {{ item.status }}
              </v-chip>
            </template>
          </v-data-table>
        </div>
      </v-card>
    </template>
  </div>
</template>

<script>
import "/node_modules/flag-icons/css/flag-icons.min.css";

export default {
  props: {
    top: Array,
    country: Array,
    load: Boolean,
  },
  data() {
    return {
      selected: [],
      tabKey: "all",
      items: [
        { tab: "All", value: "all", icon: "mdi-table" },
        { tab: "Active", value: "active", icon: "mdi-thumb-up" },
        { tab: "Inactive", value: "inactive", icon: "mdi-block-helper" },
        { tab: "Block", value: "blocked", icon: "mdi-close-circle" },
      ],
      actions: ["active", "inactive"],
      status: "",
    };
  },
  methods: {
    getColor(status) {
      if (status == "blocked") return "red";
      else if (status == "inactive") return "orange";
      else return "blue";
    },
    tabChange(item) {
      this.tabKey = item;
      this.$emit("getRecord", item);
      this.selected = [];
    },
    changeStatus() {
      if (this.tabKey == "all") {
        alert("opps");
      }
      let item = {
        id: this.selected[0].id,
        tabKey: this.tabKey,
        status: this.status,
      };
      if (this.status == this.selected[0].status) {
        alert("You are in this status");
      } else {
        this.$emit("changeStatus", item);
        this.selected = [];
        this.status = [];
      }
    },
    countData() {},
    selectedRow(item) {
      if (this.selected.find((i) => i.id == item.id)) {
        this.selected = this.selected.filter((i) => i.id != item.id);
      } else {
        this.selected.push(item);
      }
    },
    search(searchData) {
      if (searchData.key == "Enter") {
        let filter = {
          searchData: this.searchData,
          tabkey: this.tabkey,
        };
        this.$emit("search", filter);
      }
    },
  },
};
</script>

<style scoped>
.customstyle {
  opacity: 0.7 !important;
  pointer-events: none !important;
  cursor: not-allowed !important;
}
.data {
  cursor: pointer;
}
.tab {
  width: 270px;
}
/* .costumTab .v-tab{
  border-radius: 0px 120px 0px 0px;
}

.costumTab .v-tab--active{
  background-color: white;
} */

/* .tab font-weight-bold v-tab v-tab--active */
</style>
