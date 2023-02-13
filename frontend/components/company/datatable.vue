<template>
  <div>
    <template>
      <v-col class="mb-5" cols="12" style=" margin-top: 10px">
        <v-card style="height: 90px">
          <h4 style="margin-left: 5px">Actions</h4>
          <v-row align="center" justify="center">
             <v-btn
             class="blue-grey mr-2"
             depressed
             solo
             :class="selected.length > 0 ? '' : 'customDisabled'"
             >
              <div class="d-block" style="color:white;">
                <i class="fa fa-eye"></i>
                <div>view</div>
              </div>
            </v-btn>
            <v-btn
              class=""
              color="indigo darken-1"

              @click="editModel"
              :class="selected.length > 0 ? '' : 'customDisabled'"
              justify="center"
              style="margin-right: 5px"
            >
              <div class="d-block" style="color:white;">
                <i class="fa fa-user-pen"></i>
                <div>Edit</div>
              </div>
            </v-btn>
            <v-btn
            class=""
              color="pink"


              :class="selected.length > 0 ? '' : 'customDisabled'"
              justify="center"
              style="margin-right: 5px"


          >

            <div class="d-block" style="color: white">
              <i class="fa fa-circle-xmark"></i>
              <div>Blocked</div>
            </div>
          </v-btn>
            <v-btn
            class=""
              color="blue darken-3"


              :class="selected.length > 0 ? '' : 'customDisabled'"
              justify="center"
              style="margin-right: 5px"

          >

            <div class="d-block" style="color: white">
              <i class="fa fa-gear"></i>
              <div>Auto Edit</div>
            </div>
          </v-btn>
            <v-btn
              color="error"
              @click="deleted('delete')"
              :class="selected.length > 0 ? '' : 'customDisabled'"
              justify="center"
              style="margin-right: 5px; color: white"
              >
              <div class="d-block" style="color: white">
              <i class="fa fa-trash"></i>
              <div>Delete</div>
            </div>
            </v-btn>


          <v-btn
            class="btn primary"
            v-if="tabKey == 'removed'"
            @click="deleted('restore')"
            :class="selected.length > 0 ? '' : 'customDisabled'"
            justify="center"
            style="margin-right: 5px"
          >
           <div class="d-block" style="color: white">
              <i class="fa fa-trash-arrow-up"></i>
              <div>Restore</div>
            </div>
           </v-btn>
           <span>
              <v-select
              v-model="status"
              :items="actions"
              class="my-2"
              filled
              label="Change Status"
              dense
              solo
              small
              depressed
              justify="center"
              style="margin-left: 5px;  margin-right: 5px; width: 150px"
              >
            </v-select>
          </span>
          <v-btn
            class="btn cyan"
            style="margin-right: 5px"
            @click="changeStatus('change_status')"
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

      <v-card>
        <v-tabs background-color="primary" dark>
          <v-tab
            v-for="item in tabItems"
            :key="item.tab"
            show-arrows
            @click="tabChange(item.value)"
          >
          <div style="margin-right:100px" class="d-flex row">
            <div>

              <v-icon style="margin-right:30px">{{ item.icon }}</v-icon> {{ item.tab }}
            </div>
            <div>
            <v-stepper></v-stepper>
            <v-badge
            class=" mt-4 ml-8"
            color="green"
            :content="item.total"
            >

            </v-badge>
          </div>
          </div>
          </v-tab>
        </v-tabs>

        <v-tabs-items>
          <v-data-table
            v-model="selected"
            :headers="headers"
            :items="items.data"
            item-key="id"
            show-select
            class="elevation-1"
            :loading="loading"
            @click:row="selectRow"
          >

            <template v-slot:item.systems="{ item }">

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
                      Systems
                    </v-btn>
                  </template>
                  <span v-for="(system,index) in item.systems" :key="index" >{{ system.system_name }}<br></span>

                </v-tooltip>

            </template>

            <template v-slot:item.logo="{item}">
              <v-avatar >
                <img :src="`http://localhost:8000/company/${item.logo}`" alt="no photo" />
              </v-avatar>
            </template>

            <template v-slot:item.status="{item}">
              <v-chip
                :color="getColor(item.status)"
                dark
                style=" text-align:center;"
                justify="center"
              >
              <div class="text-center" justify="center" style=" width:70px; text-align:center;">
                {{ item.status }}
              </div>
              </v-chip>

            </template>

            <template v-slot:item.created_at="{ item }">
              <span>{{ new Date(item.created_at).toLocaleString() }}</span>
            </template>


          </v-data-table>
        </v-tabs-items>
      </v-card>
    </template>
  </div>
</template>


<script>
export default {
  props: {
    headers: Array,
    items: Array,
    loading: Boolean,
  },
  data() {
    return {
      actions: ["pending ", "active", "inactive", "blocked", "removed"],
      status: "",
      tabKey: "all",
      selected: [],
      tabItems: [
        { tab: "ALL", value: "all", icon: "mdi-border-all" },
        { tab: "ACTIVE", value: "active", icon: "mdi-thumb-up-outline" },
        { tab: "INACTIVE", value: "inactive", icon: "mdi-cancel" },
        { tab: "PENDING", value: "pending", icon: "mdi-account-off" },
        { tab: "BLOCKED", value: "blocked", icon: "mdi-close-circle-outline" },
        { tab: "REMOVED", value: "removed", icon: "mdi-delete" },
      ],
    };
  },
  watch: {
  items:function(){
      this.tabItems = [
      { tab: "ALL", value: "all", icon: "mdi-border-all",  total: this.items?.countData?.allTotal, },
        { tab: "ACTIVE", value: "active",  icon: "mdi-thumb-up-outline" ,  total: this.items?.countData?.activeTotal,   },
        { tab: "INACTIVE", value: "inactive",     icon: "mdi-cancel", total: this.items?.countData?.inactiveTotal,    },
        { tab: "PENDING", value: "pending",   icon: "mdi-account-off" ,    total: this.items?.countData?.pendingTotal,     },
        { tab: "BLOCKED", value: "blocked",   icon: "mdi-close-circle-outline",  total: this.items?.countData?.blockedTotal,      },
        { tab: "REMOVED", value: "removed",   icon: "mdi-delete"  ,    total: this.items?.countData?.removedTotal },
      ]
    }
  },
  methods: {
    getColor(status){
      if (status == "blocked") return "red";
      else if (status == "inactive") return "grey";
      else if (status == "warning") return "secondary";
      else if (status == "removed") return "green";
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
