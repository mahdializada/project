<template>
  <div>
    <Header @openD="openD" :allList="allList" :header="header" @getfilter="datafilter"  @saveChanges="saveChanges" :download_data="datas.data"
    @searchresult="se_result"
    :search_tab="searchtab"
    />
    <Stepper ref="stepperRef" @submit="pushData" />
    <socialview ref="viewRef" />
    <template>
      <div class="text-center">
        <v-dialog v-model="dialog" width="500">
          <v-form @submit.prevent="submit">
          <v-card>
            <v-card-title class="text-h5 grey lighten-2">
              Socail Media
            </v-card-title>
            <div>
              <v-col cols="12">
                <v-text-field
                  v-model="name"
                  label="social media name"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="website"
                  label="social media website"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-autocomplete
                name="created_by"
                :items="selectSocial"
                item-text="first_name"
                item-value="id"
                @click="getuser"
                v-model="created_by"
                clearable
                dense
                solo-inverted
                >
                </v-autocomplete>
              </v-col>
              <v-col>
                <v-autocomplete
                v-model="updated_by"
                :items="selectSocial"
                item-text="first_name"
                item-value="id"
                @click="getuser"
                clearable
                dense
                solo-inverted
                >

                </v-autocomplete>
              </v-col>
              <v-col cols="12">
                <input
                type="file"
                @change="onChange"
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="status"
                  label="social media status"
                ></v-text-field>
              </v-col>
            </div>
            <v-card-text></v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="white"
                class="btn red"
                text
                @click="closeDialog"
              >
                close
              </v-btn>
              <v-btn type="submit" class="btn primary" text>
                {{isEdit?"Update":"Save"}}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-form>
        </v-dialog>

      </div>
    </template>
    <Datatables
      :headers="header"
      :value="datas"
      :load="loading"
      @deleteItem="deleteItem"
      @getRecord="getData"
      @openModel="openModel"
      @editItem="editItem"
      @change_status="selectStatus"
      @onView="onView"
    />
  </div>
</template>
<script>

import Datatables from '~/components/Common/socialmedia/datatables.vue';
import Header from "~/components/Common/socialmedia/header.vue";
import Stepper from '~/components/Common/socialmedia/Stepper.vue';
import socialview from '~/components/Common/socialmedia/socialview.vue';


  export default {

    data(){
      return {
          selectSocial:[],
          created_by:'',
          updated_by:'',
          items: [],
          searchtab:'all',
          search: '',
          checkbox: true,
          loading:false,
          dialog:false,
          name:"",
          website:"",
          status:"pending",
          image:"",
          id:"",
          // view part
          intials: "",
          view:[],
          // end of view part
          isEdit:false,
          load:false,
          allList:[
              {text:"Id",value:"id",icon:"information"},
              {text:"Updated_by",value:"user",icon:"information"},
              {text:"Name",value:"name",icon:"information"},
              {text:"Website",value:"website",icon:"information"},
              {text:"Image",value:"image",icon:"information"},
              {text:"Status",value:"status",icon:"information"},
            ],
         header:this.allList,
         datas:[],
         i:[],

        };
    },

     created(){
      if (typeof window !== 'undefined') {
        this.header = JSON.parse(localStorage.getItem("columns") || "[]");
      }
      this.getData();
      this.searchtab = 'all';
    },
    methods:{
      async getuser(){
        const user = await this.$axios.get('user');
        this.selectSocial=user.data.data;
        console.log("in user",user);
      },
      // search emited from searchresult
      se_result(data){
        this.datas.data=data;
      },
      // filter
      datafilter(data){
        this.datas.data=data;
      },

      saveChanges(){
        if (typeof window !== 'undefined') {
        this.header = JSON.parse(localStorage.getItem("columns") || "[]");
      }
      },
     async selectStatus(item,tabkey){
        const st= await this.$axios.put(`socialmedia/id`, item);
        console.log(st);
        await this.getData(tabkey);
      },
      openModel(){
        this.dialog=true;
      },
      async getData(item=null){
        this.loading=true;
        this.searchtab=item;
        const show= await this.$axios.get("socialmedia",{params:{
          tabkey:item,
        }});
        this.datas=show.data;
        this.loading=false;
      },
      // file upload function
      onChange(e){
        this.image=e.target.files[0];
        console.log(e.target.files[0]);
      },
      async submit(){
        let form=new FormData();
        form.append('name',this.name);
        form.append('image',this.image);
        form.append('website',this.website);
        form.append('status',this.status);
        form.append('created_by',this.created_by);
        form.append('updated_by',this.updated_by);
        // let insert={
        //   "name":this.name,
        //   "status":this.status,
        // };
        if(this.isEdit){
          await this.update();
        }else{
        const sub=await this.$axios.post('socialmedia',form);
          if(sub.status==201){
            // this.datas.unshift(sub.data);
            this.getData();
          }
        }
        this.name="";
        this.status="";
        this.image="";
        this.website="";
        this.created_by="";
        this.updated_by="";
        this.load=false;
        this.dialog=false;
        this.isEdit=false;
      },

      // I change the insert varaible to form

        async update(form){
            const update= await this.$axios.put(`socialmedia/${this.id}`,form);
            if(update.status==201){
              this.datas=this.datas.map((item)=>{
                if(item.id==this.id){
                  return update.data;
                }else{
                  return item;
                }
              });
            }
          },
            closeDialog() {
            this.dialog = false;
            this.isEdit = false;
            this.name = "";
            this.website = "";
            this.created_by = "";
            this.updated_by = "";
            this.image = "";
            this.status = "";
          },
         async deleteItem(data){
            const del=await this.$axios.delete(`socialmedia/id`,{params:data});
            if(del.status==200){
             this.load=true;
             await this.getData(data.tabkey);
             this.load=false;
            }
          },
          editItem(item){
            this.dialog=true;
            this.isEdit = true;
            this.id=item.id;
            this.name=item.name;
            this.website=item.website;
            this.created_by=item.created_by;
            this.updated_by=item.updated_by;
            this.image=item.image;
            this.status=item.status;
      },
      pushData(item){
        this.datas.push(item);
        // this.getData();
      },
      openD(){
        this.$refs.stepperRef.openDialog();
      },
      // view part
    async onView(item){
      console.log("in index",item);
      this.$refs.viewRef.openView(item);

    },
    async viewData(id){
     const view=await this.$axios.get(`socialmedia/${this.id}`);
     console.log(view);
     this.view=view.data;
     this.intials=this.view;
    },

    },

    components: { Datatables, Header, Stepper,socialview}
}
</script>
