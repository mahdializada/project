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
        <template v-slot:[`item.comment`]="{item}">
          <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <p v-bind="attrs" v-on="on" v-html="item.comment?.substring(0, 15) + '...'"></p>
          </template>
          <p v-html="item.comment"></p>
        </v-tooltip>
        </template>
    </v-data-table>
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
          const result = await this.$axios.get("comment", { params: data });
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
