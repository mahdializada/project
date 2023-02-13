<template>
  <div>
    <v-card class="mb-6">
      <strong class="ml-4">Actions</strong>
        <v-col cols="12" md="12" sm="12" class="text-center">
          <v-btn :class="selected.length > 0 ? '' : 'customstyle'" @click="openViewDialog" small class="btn black">
          <div>
           <i class="fa fa-eye" style="color:white"></i>
           <h4 style="color:white">View</h4>
          </div>
         </v-btn>
          <v-btn small class="btn primary" @click="openModel">
          <div>
            <i class="fa fa-plus"></i>
            <h4>Insert</h4>
          </div>
          </v-btn>
          <v-btn
            small
            class="btn success"
            @click="editItem"
            :class="selected.length > 0 ? '' : 'customstyle'"
          >
          <div>
            <i class="fa-solid fa-user-pen"></i>
            <h4>Update</h4>
            </div>
          </v-btn>
          <v-btn
            small
            class="btn info"
            :class="selected.length > 0 ? '' : 'customstyle'"
            @click="deleteItem('restore')"
            v-if="tabkey == 'blocked'"
          >
            <div>
            <i class="fa-sharp fa-solid fa-gear"></i>
            <h4>Restore</h4>
          </div>
          </v-btn>
          <v-btn
            small
            class="btn red"
            @click="deleteItem('deleted')"
            style="color: white;"
            :class="selected.length > 0 ? '' : 'customstyle'"
          >
            <div>
              <i class="fa-solid fa-trash"></i>
            <h4>Delete</h4>
          </div>
          </v-btn>
          <v-select
            x-small
            style="display: inline-block;height:5%"
            v-model="status"
            :items="lists"
            dense
            label="Select Status"
            solo
          ></v-select>
          <v-btn
            small
            @click="changestatus('change_status')"
            color="primary"
          >
          <div>
            <i class="fa-solid fa-check"></i>
            <h4>Apply</h4>
          </div>
          </v-btn>
        </v-col>
    </v-card>
  <template>
    <v-card>
      <v-tabs background-color="primary" dark show-arrows>
        <v-tabs-slider color="blue lighten-3"></v-tabs-slider>
        <v-tab
          style="width:300px"
          v-for="item in items"
          :key="item.tab"
          @click="tabChange(item.value)"
        >
        <v-icon>
          {{item.icon}}
        </v-icon>
        <v-spacer></v-spacer>
        {{ item.tab }}
        <v-spacer></v-spacer>
        <v-badge  class="mt-3 mr-2" color="success" :content="item.total"></v-badge>
        </v-tab>
      </v-tabs>
      <v-data-table
        v-model="selected"
        item-key="id"
        :headers="headers"
        :items="value.data"
        @click:row="selectedRow"
        :loading="load"
        show-select
        class="elevation-1"

      >
      <template v-slot:item.status="{ item }">
            <v-chip :color="getColor(item.status)" dark>
              {{ item.status }}
            </v-chip>
      </template>
      <template v-for="(head , index) in headers" v-slot:[`header.${head.value}`]="{ headers }" >
        <v-tooltip bottom :key="index">
          <template v-slot:activator="{ on }">
            <span v-on="on">{{head.text}}</span>
          </template>
          <span>{{head.text}}</span>
        </v-tooltip>
          <v-btn icon color="grey lighten-2"  :key="index">
            <v-icon class='no-rotate' @click.stop small color="grey">{{  "mdi-"+head.icon }}</v-icon>
          </v-btn>
      </template>
        <template v-slot:[`item.image`]="{item}">
          <v-avatar>
            <img :src="`http://localhost:8000/${item.image}`" alt="" srcset="">
          </v-avatar>
        </template>

      </v-data-table>
    </v-card>
  </template>
</div>
</template>
<script>
export default {
props: {
  headers: Array,
  load: Boolean,
  value:Array,
},
data() {
  return {
    lists: ['active', 'inactive', 'pending', 'blocked'],
    status: '',
    tabkey: 'all',
    selected: [],
    id: '',
    type: '',
    items: [
      { tab: 'All', value: 'all', icon: "mdi-table" },
      { tab: 'Active', value: 'active' ,icon: "mdi-thumb-up" },
      { tab: 'Inactive', value: 'inactive',icon: "mdi-cancel" },
      { tab: 'Pending', value: 'pending',icon: "mdi-account-off" },
      { tab: 'Blocked', value: 'blocked',icon: "mdi-close-circle" },
    ],
  }
},
watch:{
  value:function(){
  this.items=[
      { tab: 'All', value: 'all', icon: "mdi-table" , total:this.value?.select?.alltotal },
      { tab: 'Active', value: 'active' ,icon: "mdi-thumb-up", total:this.value?.select?.activetotal },
      { tab: 'Inactive', value: 'inactive',icon: "mdi-cancel", total:this.value?.select?.inactivetotal },
      { tab: 'Pending', value: 'pending',icon: "mdi-account-off" , total:this.value?.select?.pendingtotal},
      { tab: 'Blocked', value: 'blocked',icon: "mdi-close-circle" , total:this.value?.select?.blockedtotal},
  ]
  }
},

methods: {
  // open view Dialog from component
  openViewDialog(){
    this.$emit('onView',this.selected[0]);
    this.selected=[];
  },
  // change the color of every status
  getColor(status){
    if(status=='active'){
      return "blue";
    }
    else if(status=='inactive'){
      return "#757575";
    }
    else if(status == 'pending'){
      return "#ff6f52c7";
    }
    else{
      return "red"
    }
  },
  changestatus(type = null) {
    let changeId = this.selected.map((item) => item.id)
    let item = {
      ids: changeId,
      // id:this.selected[0].id,
      status: this.status,
      type: type,
    }
    if (this.status == this.selected[0].status) {
      alert('Do not select the same status!')
      this.status = ''
    } else {
      this.$emit('change_status', item, this.tabkey)
      this.selected = []
      this.status = ''
    }
  },
  tabChange(item) {
    this.tabkey = item
    this.searchtab;
    this.$emit('getRecord', item)
  },
  openModel() {
    this.$emit('openModel')
  },
  deleteItem(type) {
    if (this.tabkey == 'all') {
      alert('can not deleted! Please select the other tabkey')
    } else {
      let delId = this.selected.map((item) => item.id)
      let data = {
        ids: delId,
        tabkey: this.tabkey,
        type: type,
      }
      this.$emit('deleteItem', data)
      this.selected = []
    }
  },
  editItem() {
    if (this.selected.length == 1) this.$emit('editItem', this.selected[0])
    else {
      alert('please select just one Record')
    }
    this.selected = []
  },
  selectedRow(item) {
    if (this.selected.find((i) => i.id == item.id)) {
      this.selected = this.selected.filter((i) => i.id != item.id)
    } else {
      this.selected.push(item)
    }
  },
},
}
</script>
<style>
.customstyle {
opacity: 0.3;
pointer-events: none;
cursor: not-allowed !important;
}
.no-rotate {
  transform: none !important;
  opacity: 1 !important;
}
</style>
