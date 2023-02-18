<template>
  <div>
    <template>
      <div>
        <v-card class="mt-2" >
          <h3 class="ma-4">Actions</h3>
          <v-row>
            <v-col cols="12" md="1"></v-col>
            <v-col cols="12" md="3" sm="12">
              <v-text-field
              small
              style="border-radius: 30px;"
              label="search"
              v-model="searchValue"
              outlined
              dense
              append-icon="mdi-magnify"
              @keyup="searches"
              ></v-text-field>

            </v-col>
            <v-col cols="12" md="1"></v-col>
            <v-col cols="12" md="7" sm="12">
              <!-- <v-btn :class="selected.length > 0 ? '' : 'customstyle'" @click="openViewDialog" class="btn black">
                <div>
                  <i class="fa fa-eye" style="color:white"></i>
                  <h4 style="color:white">View</h4>
                </div>
              </v-btn> -->
              <v-btn @click="openDialog" color="primary">
                <div>
                  <i class="fa fa-plus"></i>
                  <h4>Create</h4>
                </div>
              </v-btn>
              <v-btn class="btn success" @click="editItem" :class="selected.length > 0 ? '' : 'customstyle'">
                <div>
                  <i class="fa-solid fa-user-pen"></i>
                  <h4>Update</h4>
                </div>
              </v-btn>
              <v-btn class="btn red" @click="deleted" style="color: white;"
                :class="selected.length > 0 ? '' : 'customstyle'">
                <div>
                  <i class="fa-solid fa-trash"></i>
                  <h4>Delete</h4>
                </div>
              </v-btn>
              <!--<v-btn  class="btn info" :class="selected.length > 0 ? '' : 'customstyle'"
                @click="deleteItem('restore')" v-if="tabkey == 'blocked'">
                <div>
                  <i class="fa-sharp fa-solid fa-gear"></i>
                  <h4>Restore</h4>
                </div>
              </v-btn>

              <v-select style="display: inline-block;height:5%" v-model="status" :items="lists" dense
                label="Select Status" solo></v-select>
              <v-btn  @click="changestatus('change_status')" color="primary">
                <div>
                  <i class="fa-solid fa-check"></i>
                  <h4>Apply</h4>
                </div>
              </v-btn> -->
            </v-col>

            <!-- <v-col cols="12" md="1" sm="12"></v-col> -->
          </v-row>
        </v-card>
      </div>
    </template><br>
    <v-tabs background-color="primary" dark show-arrows>
      <v-tabs-slider color="blue lighten-3"></v-tabs-slider>
      <v-tab style="width:300px" v-for="item in items" :key="item.tab" @click="tabChange(item.value)">
        <v-icon>
          {{ item.icon }}
        </v-icon>
        <v-spacer></v-spacer>
        {{ item.tab }}
        <v-spacer></v-spacer>
      </v-tab>
    </v-tabs>
    <v-data-table v-model="selected" item-key="id" :headers="headers" :items="value.data" @click:row="selectedRow"
      :loading="load" show-select class="elevation-1">
      <template v-slot:[`item.is_active`]="{ item }">
              <v-switch
                :loading="load"
                @click="changeStatus(item.id)"
                :input-value="item.is_active ?? 0"
                class="pa-0"
              ></v-switch>
          </template>
          <template v-slot:[`item.photo`]="{ item }">
        <v-avatar>
          <img :src="`http://localhost:8000/${item.photo}`" alt="" srcset="">
        </v-avatar>
      </template>
      <!-- <template v-slot:[`item.descriptions`]="{ item }">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <p v-bind="attrs" v-on="on" v-html="item.descriptions?.substring(0, 20) + '...'"></p>
          </template>
          <p v-html="item.descriptions"></p>
        </v-tooltip>
      </template> -->

    </v-data-table>
    <!-- <template>
      <v-dialog v-model="see_more_model" width="300">
        <v-card style="height:auto">
          <v-card-title style="background-color:#26c6da" color="primary">
            <h4>More Details</h4>
            <v-spacer></v-spacer>
            <v-btn fab x-small style="float: right;background-color: #26c6da;" @click="closeModel">
              <svg width="50px" height="50px" viewBox="0 0 700 560" fill="currentColor'" style="transform: scaleY(-1);">
                <g>
                  <path
                    d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z" />
                  <path
                    d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z" />
                </g>
              </svg>
            </v-btn>
          </v-card-title>
          <v-card-text class="mt-2">
            <h2 v-html="moreDetail?.descriptions"></h2>
          </v-card-text>
        </v-card>
      </v-dialog>
    </template> -->
  </div>
</template>
<script>
export default {
  props: {
    headers: Array,
    load: Boolean,
    value: Array,
    search_tab: String,
  },
  data() {
    return {
      selected: [],
      tabkey: 'all',
      searchValue:'',
      see_more_model: false,
      actions: ["Active", "In Active"],
      items: [
        { tab: "All", value: "all", icon: "mdi-border-all" },
        { tab: "Active", value: "1", icon: "mdi-thumb-up-outline" },
        { tab: "Inactive", value: "'0'", icon: "mdi-cancel" },
      ],
      datas: [],
      moreDetail: null,
      flag: false,
    };
  },
  created() {

  },
  methods: {
    async searches(searchValue) {
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
          const result = await this.$axios.get("users", { params: data });
          console.log("in result",result);
          this.$emit("searchResult", result);

      }
    },
    // end of search methods
    openDialog() {
      this.$emit('openDialog');
    },
    tabChange(item) {
      this.tabkey = item;
      this.$emit("getData", item);
    },
    changeStatus(id) {
      this.loading = true;
      this.selected = [];
      this.$emit("changeStatus", id, this.tabkey);
      this.loading = false;
      this.selected = [];
    },
    selectedRow(item) {
      if (this.selected.find((i) => i.id == item.id)) {
        this.selected = this.selected.filter((i) => i.id != item.id)
      } else {
        this.selected.push(item);
      }
    },
    editItem() {
      if (this.selected.length == 1) {
        this.$emit('editItem', this.selected[0])
      }
      else {
        alert('please select just one Record');
      }
      this.selected = []
    },
    deleted() {
      if (this.tabkey == "all") {
        alert("can not deleted! Please select the other tabkey");
      }else{
      let delId = this.selected.map((item) => item.id);
      let data = {
        ids: delId,
        tabkey:this.tabkey,
      };
      this.$emit('deleteItem', data);
      this.selected = [];
      }
    },
    see_more(i) {
      this.moreDetail = i;
      this.see_more_model = true;
    },
    closeModel() {
      this.see_more_model = false;
    }
  }
}
</script>
<style scoped>
>>>.customstyle {
  opacity: 0.3;
  pointer-events: none;
  cursor: not-allowed !important;
}
</style>
