  <template>
  <div>
    <v-col cols="12" style="padding: 0px; margin-top: 0px">
      <v-card style="height: 90px" class="elevation-5">
        <h4 style="margin-left: 5px; font-weight: bold" class="ml-2">
          Actions
        </h4>
        <v-row align="center" justify="center" class="mt-3" style="margin: 0px">
          <v-btn
            style="margin-right: 5px"
            class="pa-5"
            color="blue-grey"
            justify="center"
            depressed
            small
            :class="selected.length > 0 ? '' : 'customstyle'"
            :disabled="tabkey == 'all'"
            v-if="tabkey !== 'blocked'"
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
            :class="selected.length > 0 ? '' : 'customstyle'"
            :disabled="tabkey == 'all'"
            v-if="tabkey !== 'blocked'"
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
            :class="selected.length > 0 ? '' : 'customstyle'"
            :disabled="tabkey == 'all'"
            v-if="tabkey !== 'blocked'"
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
            :class="selected.length > 0 ? '' : 'customstyle'"
            :disabled="tabkey == 'all'"
            @click="changeStatus('blocked')"
            v-if="tabkey !== 'blocked'"
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
            @click="deleted('delete')"
            :class="selected.length > 0 ? '' : 'customstyle'"
            :disabled="tabkey == 'all'"
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
            @click="deleted('restore')"
            :class="selected.length > 0 ? '' : 'customstyle'"
            :disabled="tabkey == 'all'"
            v-if="this.tabkey=='removed'"
            ><div class="d-block" style="color: white">
              <i class="fa fa-trash-arrow-up"></i>
              <div>Restore</div>
            </div>
          </v-btn>
          <strong>
            <v-select
              :class="selected.length > 0 ? '' : 'customstyle'"
              depressed
              outlined
              v-model="status"
              :items="actions"
              label="Select Status"
              style="width: 150px; height: 40px; margin-right: 5px"
              justify="center"
              dense
              :disabled="tabkey == 'all'"
            ></v-select>
          </strong>
          <v-btn
            class="info pa-5"
            depressed
            small
            :class="selected.length > 0 ? '' : 'customstyle'"
            :disabled="tabkey == 'all'"
            @click="changeStatus('change_status')"
          >
            <div class="d-block" style="color: white">
              <i class="fa fa-check"></i>
              <div>Apply</div>
            </div>
          </v-btn>
        </v-row>
      </v-card>
    </v-col>
    <template>
      <v-card class="mt-4">
        <v-tabs show-arrows class="custom" light>
          <v-tabs-slider color="blue lighten-3"></v-tabs-slider>
          <v-tab
            class="tab"
            v-for="item in items"
            :key="item.tab"
            @click="tabChange(item.value)"
          >
              <v-icon>{{ item.icon }}</v-icon>
            <v-spacer></v-spacer>
            {{ item.tab }}
             <v-spacer></v-spacer>
            <!-- <v-badge class="mt-3 mr-2" color="white" @click="countData">
            </v-badge> -->
          </v-tab>
        </v-tabs>
        <div class="table-responsive">
          <v-data-table
            v-model="selected"
            item.key="id"
            :headers="top"
            :items="value"
            :loading="load"
            @click:row="selectedRow"
            show-select
            height="500px"
            class="elevation-1 data"
          >
            <template v-slot:[`item.status`]="{ item }">
              <v-chip :color="getColor(item.status)" dark>
                <div style="width:50px;" class="text-center">
                  {{ item.status }}
                </div>
              </v-chip>
            </template>

            <template v-slot:[`item.logo`]="{item}">
              <v-avatar >
                <img :src="`http://localhost:8000/${item.logo}`" alt="no photo" />
              </v-avatar>
            </template>

          </v-data-table>
        </div>
      </v-card>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    top: Array,
    value: Array,
    load: Boolean,
  },
  data() {
    return {
      selected: [],
      tabkey: "all",
      id:"",
      items: [
        { tab: "All", value: "all", icon: "mdi-border-all" },
        { tab: "Active", value: "active", icon: "mdi-thumb-up" },
        { tab: "Inactive", value: "inactive", icon: "mdi-cancel" },
        { tab: "Blocked", value: "blocked", icon: "mdi-close-circle-outline" },
        { tab: "Warning", value: "warning", icon: "mdi-alert" },
        { tab: "Removed", value: "removed", icon: "mdi-delete" },
        { tab: "Pending", value: "Pending", icon: "mdi-account-off" },
      ],
      status: "",
      actions: ["active", "inactive", "warning", "pending","removed"],
    };
  },
  methods: {
    getColor(status) {
      if (status == "blocked") return "red";
      else if (status == "inactive") return "grey";
      else if (status == "warning") return "secondary";
      else if (status == "removed") return "green";
      else if (status == "pending") return "pink";
      else return "blue";
    },
    tabChange(item) {
      this.tabkey = item;
      this.$emit("getRecord", item);
      this.selected = [];
    },
    changeStatus(type = null) {
     let changeId = this.selected.map((item) => item.id);
            let item = {
                ids: changeId,
                // id:this.selected[0].id,
                status: this.status,
                type: type,
            };
            if (this.status == this.selected[0].status) {
                alert("Do not select the same status!");
                this.status = "";
            }else if (this.selected == null) {
                alert("Please select one status !");
            }else {
                this.$emit("changeStatus", item, this.tabkey);
                this.selected = [];
                this.status = "";
            }
    },

    selectedRow(item) {
      if (this.selected.find((i) => i.id == item.id)) {
        this.selected = this.selected.filter((i) => i.id != item.id);
      } else {
        this.selected.push(item);
      }
    },
    deleted(type) {
       if (this.tabkey == "all") {
        alert("You cannot delete when All tab is selected!");
      } else {
        let delId = this.selected.map((item) => item.id);
        let data = {
          ids: delId,
          tabkey: this.tabkey,
          type: type,
        };
        this.$emit("deleteItem", data);
      };
      this.selected=[];
  },
  },
};
</script>

<style scoped>
.customstyle {
  opacity: 0.7 !important;
  pointer-events: none !important;
  cursor: allowed !important;
}
.data {
  cursor: pointer;
}
.tab {
  width: 270px;
  border-radius: 10px 30px 0px 0px;
  border-left-color: rgb(18, 17, 17) !important;
  background-color: white;
}
.custom {
  color: white;
}
</style>
