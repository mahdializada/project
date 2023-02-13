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
            class="mt-3"
            style="margin: 0px"
          >
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
              v-if="tabKey !== 'blocked'"
            >
              <div class="d-block" style="color: white">
                <i class="fa fa-user-pen"></i>
                <div>Edit</div>
              </div>
            </v-btn>
            <v-btn
              style="margin-right: 5px"
              class="pa-5"
              color="blue darken-3"
              justify="center"
              depressed
              small
              :class="selected.length > 0 ? '' : 'customDisabled'"
              v-if="tabKey !== 'blocked'"
            >
              <div class="d-block" style="color: white">
                <i class="fa fa-gear"></i>
                <div>Auto Edit</div>
              </div>
            </v-btn>

            <v-btn
              style="margin-right: 5px"
              class="pa-5"
              color="pink"
              justify="center"
              depressed
              small
              :class="selected.length > 0 ? '' : 'customDisabled'"
              @click="changeStatus('blocked')"
              v-if="tabKey !== 'blocked'"
            >
              <div class="d-block" style="color: white">
                <i class="fa fa-circle-xmark"></i>
                <div>Block</div>
              </div>
            </v-btn>
            <v-btn
              style="margin-right: 5px"
              class="pa-5"
              color="error"
              justify="center"
              depressed
              small
              :class="selected.length > 0 ? '' : 'customDisabled'"
              @click="deleted('deleted')"
              ><div class="d-block" style="color: white">
                <i class="fa fa-trash"></i>
                <div>Delete</div>
              </div>
            </v-btn>
            <v-btn
              style="margin-right: 5px"
              class="pa-5"
              color="cyan darken-2"
              justify="center"
              depressed
              small
              v-if="tabKey == 'removed'"
              :class="selected.length > 0 ? '' : 'customDisabled'"
              @click="deleted('restore')"
              ><div class="d-block" style="color: white">
                <i class="fa fa-trash-arrow-up"></i>
                <div>Restore</div>
              </div>
            </v-btn>
            <strong>
               <v-select
                :class="selected.length > 0 ? '' : 'customDisabled'"
                depressed
                v-model="status"
                :items="actions"
                label="Select Status"
                style="width: 150px; height: 40px; margin-right: 5px"
                justify="center"
                solo
                dense
                
              ></v-select>
            </strong>
            <v-btn
              class="info pa-5"
              depressed
              small
              @click="changeStatus('changeStatus')"
              :class="selected.length > 0 ? '' : 'customDisabled'"
            >
              <div class="d-block" style="color: white">
                <i class="fa fa-check"></i>
                <div>Apply</div>
              </div>
            </v-btn>
          </v-row>
        </v-card>
      </v-col>
      <br>


      <!-- dataTable -->
      <v-card>
        <v-tabs background-color="primary" show-arrows  dark style="">
          <v-tabs-slider color="blue lighten-3"></v-tabs-slider>
          
          <v-tab
          class="tabstyle"
            v-for="item in tabItems"
            :key="item.tab"
            @click="tapChange(item.value)"
          >
          <div style="margin-right:50px">

            <v-icon>{{ item.icon }}</v-icon>
          </div>
            {{ item.tab }}
          <v-spacer></v-spacer>
          <v-badge
            class="mt-2 mr-10"
            color="green"
            :content="item.total"
          ></v-badge>
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
         <template v-slot:[`item.status`]="{ item }">
              <v-chip :color="getColor(item.status)" dark>
                {{ item.status }}
              </v-chip>
          </template>
          <template v-slot:[`item.image`]="{ item }">
            <v-avatar>
              <img :src="` http://localhost:8000/${item.image}`" alt="" />
            </v-avatar>
          </template>
          <template v-slot:[`item.Departments`]="{item}">
              <v-tooltip bottom>
                  <template v-slot:activator="{ on: tooltip }">
                    <v-btn
                      color="primary"
                      dark
                      outlined
                      dense
                      solo
                      v-bind="attrs"
                      v-on="{ ...tooltip, ...menu }"
                    >
                      Departments
                    </v-btn>
                  </template>
                  <span v-for="(department,index) in item.departments" :key="index" >
                    {{ department.department_name}}
                    <br>
                  </span>

                </v-tooltip>
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
      actions: ["pending", "active", "inactive", "blocked", "removed"],
      status: "",
      tabKey: "all",
      selected: [],
      tabItems: [
        { tab: "All", value: "all", icon: "mdi-border-all" },
        { tab: "Pending", value: "pending", icon: "mdi-account" },
        { tab: "Active", value: "active", icon: "mdi-thumb-up-outline" },
        { tab: "Inactive", value: "inactive", icon: "mdi-cancel" },
        { tab: "Blocked", value: "blocked", icon: "mdi-close-circle-outline" },
        { tab: "removed", value: "removed", icon: "mdi-delete" },
      ],
    };
  },
  watch:{
    record: function () {
      this.tabItems = [
        {tab: "All",value: "all", icon: "mdi-table", total: this.record?.TolalCount?.allTotal},
        {tab: "Pending",value: "pending",icon: "mdi-account-off", total: this.record?.TolalCount?.pendingTotal},
        { tab: "Active",value: "active",icon: "mdi-thumb-up", total: this.record?.TolalCount?.activeTotal},
        {tab: "Inactive",value: "inactive",icon: "mdi-cancel", total: this.record?.TolalCount?.inactiveTotal},
        {tab: "Blocked",value: "blocked",icon: "mdi-close-circle", total:this.record?.TolalCount?.blockedTotal},
        {tab: "removed",value: "removed",icon: "mdi-delete", total: this.record?.TolalCount?.removedTotal}
      ];
    },
  },
  methods: {
      getColor(status) {
      if (status == "blocked") return "warning";
      else if (status == "inactive") return "grey";
      else if (status == "removed") return "red";
      else if (status == "pending") return "pink";
      else return "blue";
    },
    changeStatus(type = null) {
      let changeId = this.selected.map((item) => item.id);
      let item = {
        ids: changeId,
        status: this.status,
        type: type,
      };
      if (this.status == this.selected[0].status) {
        alert("Select a different status!");
      } else {
        this.$emit("changeStatus", item, this.tabKey);
        console.log(item);
        this.selected = [];
        this.status = [];
      }
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
    openEditModel() {
      if (this.selected.length == 1) {
        this.$emit("editItem", this.selected[0]);
      } else {
        alert("please Select one record");
      }
      this.selected = [];
    },
    deleted(type) {
      if (this.tabKey == "all") {
        alert("you can't delete in tab all");
      } else {
        let delId = this.selected.map((item) => item.id);
        let data = {
          ids: delId,
          tabKey: this.tabKey,
          type: type,
        };
        this.$emit("deleteItem", data);
        this.selected = [];
      }
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