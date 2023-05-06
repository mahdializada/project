<template>
  <div>
    <v-col cols="12">
        <teamHead />
    </v-col>

    <v-col cols="12">
      <TeamSearch :insert_data="teams" @search="searchData" :download_data="teams" @getData="getData" />
    </v-col>

    <v-col cols="12">
      <DataTable
      :top="header"
      :value="teams"
      :load="loading"
      @getRecord="getData"
      @changeStatus="selectStatus"
      @deleteItem="deletedItem"
        />
    </v-col>

  </div>
</template>

<script>
import TeamHead from '~/components/team/TeamHead.vue';
import DataTable from '~/components/team/DataTable.vue';
import TeamSearch from '~/components/team/TeamSearch.vue';
export default {
  name:"index.vue",
  components: { TeamHead, DataTable, TeamSearch },
  data(){
    return{
          search: '',
          checkbox: true,
          loading:false,
          dialog:false,
          name:"",
          status:"pending",
          id:"",
          isEdit:false,
          load:false,
      header:[
          {text:" ID ",value:"id"},
          {text:"Logo",value: "logo" },
          {text:"Name",value:"team_name"},
          {text:"Department",value:"department.department_name"},
          {text:"Note",value:"note"},
          {text:"Status",value:"status"},
      ],
      teams:[],
    }
  },
  created(){
    if (typeof window !== 'undefined') {
        this.header = JSON.parse(localStorage.getItem(this.$route?.name) || "[]");
      }
      this.getData();
      this.searchData();
    },
  methods:{
    saveChanges(){
        if (typeof window !== 'undefined') {
        this.header = JSON.parse(localStorage.getItem(this.$route?.name) || "[]");
      }
      },
    async searchData(filter){
        const re = await this.$axios.get("team", {params:filter
        });
        this.teams = re.data;

    },
    async selectStatus(item, tabkey){
      const se = await this.$axios.put(`team/id`, item);
      console.log(se);
       this.getData(tabkey);
    },
    async getData(item=null){
        this.loading=true;
        const show = await this.$axios.get("team",{params:{
          tabkey:item,
        }});

        this.teams=show.data;
        this.loading=false;
    },
    async deletedItem(data) {
      const del = await this.$axios.delete(`team/id`, { params:data });
      if (del.status == 200) {
        this.load = true;
        await this.getData(data.tabkey);
        this.load = false;
      }
    },
  }
}
</script>

<style>

</style>
