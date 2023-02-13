<template>
  <div>
    <v-card class="flex-grow-1 d-flex v-sheet pa-0 ma-0 elevation-5">
      <div class="d-flex pa-0 ma-0">
        <div class="d-flex flex-column" style="width: 150px">
          <h4 class="ml-2">Search & Filter</h4>
          <h5 class="ml-10">Search Type</h5>
          <div class="d-flex">
            <v-radio-group
              v-model="sear"
              column
              class="pt-0 ml-12 mt-0 mr-12"
              style="width: 100px"
            >
              <v-radio
                label="ID"
                value="id"
                class="mb-0"
                style="width: 50px"
              ></v-radio>
              <v-radio
                label="Content"
                value="content"
                style="width: 50px"
              ></v-radio>
            </v-radio-group>
          </div>
        </div>

        <div class="d-flex flex-column" style="width: 290px">
          <span>
            <v-text-field
              :loading="loading"
              class="mt-10"
              label="Search..."
              append-icon="mdi-magnify"
              @keyup="search"
              v-model="searchData"
              solo
              dense
              depressed
              style="
                border-radius: 25px 25px 25px 25px;
                border: 1px solid lightgrey;
                height: 40px;
              "
            ></v-text-field>
          </span>
        </div>

        <v-row>
          <div class="flex-column ml-12 mt-4">
            <v-btn
              style="height: 45px; font-weight: 900"
              outlined
              color="primary"
              class="mt-9 pa-2"
              depressed
              dense
              x-small
              solo
              text
              @click="openModel"
            >
              <div class="d-block">
                <i class="fa fa-filter"></i>
                <div>Filter</div>
              </div>
            </v-btn>

            <v-btn
            style="height: 45px; font-weight: 900"
            outlined
            color="primary"
            class="mt-9 pa-2"
            depressed
            dense
              x-small
              solo
              text
              @click="openColumnModel"
              ><div class="d-block">
                <i class="fa fa-table-columns"></i>
                <div>Column</div>
              </div></v-btn
              >
              <Header ref="openColumn"/>
              <v-btn
              style="height: 45px; font-weight: 900"
              @click="openDownloadModel"
              outlined
              color="primary"
              class="mt-9 pa-2"
              depressed
              dense
              x-small
              solo
              text
              ><div class="d-block">
                <i class="fa fa-download"></i>
                <div>Download</div>
              </div></v-btn
            >
          </div>
          <Download ref="openDownload" />
          <div class="flex-column ml-10 mt-4">
            <v-btn
            style="height: 45px; font-weight: 900"
            color="primary"
              class="mt-9 pa-2"
              depressed
              dense
              x-small
              solo
              ><div class="d-block">
                <i class="fa fa-file-arrow-up"></i>
                <div>Manual Upload</div>
              </div></v-btn
            >

            <v-btn
              style="height: 45px; font-weight: 900"
              color="primary"
              class="mt-9 pa-2"
              depressed
              dense
              x-small
              solo
              @click="openInsertModel"
              ><div class="d-block">
                <i class="fa fa-plus"></i>
                <div>Create Team</div>
              </div></v-btn
            >
            <InsertionModel ref="insertModelRef" @someEvent="someEvent"/>
          </div>
        </v-row>
      </div>
    </v-card>

    <v-dialog v-model="dialog" max-width="1000px">
      <v-card class="v-sheet">
        <v-card-title class="text-h5">
          <span class="mr-3"><i class="fa fa-filter"></i></span>Filter Teams
          <v-spacer></v-spacer>
          <span style="float: right" class="justify-content-center"
            ><i class="fa fa-circle-xmark icon1" @click="dialog = false"></i
              ></span>
            </v-card-title>
        <v-card-text class="pa-0">
          <v-row justify="space-around">
            <v-col cols="4" sm="8" md="4" class="pr-0">
              <v-card height="450px" class="elevation-5" style="margin:5px">
                <v-card-title>
                  <h3 class="font-weight-black" style="margin-left: 40%">
                    Data
                  </h3>
                </v-card-title>
                <v-card-text>
                  <h4 class="text-left font-weight-black">Departments</h4>
                  <v-select solo dense outlined placeholder="Departments">
                  </v-select>
                  <h4 class="text-left font-weight-black">Team</h4>
                  <v-text-field solo dense outlined placeholder="Name">
                  </v-text-field>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="4" sm="8" md="4" class="pr-0 pl-0">
              <v-card height="450px" class="elevation-5" style="margin: 5px">
                <v-card-title align-center text-center>
                  <h3 class="font-weight-black" style="margin-left:30%">
                    Data Range
                  </h3>
                </v-card-title>
                <v-card-text>
                  <h4 class="text-left font-weight-black">Created At</h4>
                  <v-text-field
                  solo
                  dense
                  outlined
                    append-icon="mdi-calendar"
                    placeholder="Created At"
                  >
                  </v-text-field>
                  <h4 class="text-left font-weight-black">Updated At</h4>
                  <v-text-field
                    append-icon="mdi-calendar"
                    solo
                    dense
                    outlined
                    placeholder="Updated At"
                    >
                  </v-text-field>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="4" sm="8" md="4">
              <v-card height="450px" class="elevation-5" style="margin:5px">
                <v-card-title>
                  <h3 class="font-weight-black" style="margin-left: 30%">
                    ID Filtering
                  </h3>
                </v-card-title>
                <v-card-text class="pb-2">
                  <h4 class="text-left font-weight-black">ID</h4>
                  <v-text-field
                    solo
                    dense
                    outlined
                    placeholder="1000100"
                  >
                </v-text-field>
                  <v-text-field class="mt-2" dense outlined> </v-text-field>
                </v-card-text>
                <v-card-action>
                  <v-btn-toggle
                    rounded
                    dense
                    solo
                    small
                    class="p-0 mr-3 mb-5"
                    style="float: right"
                  >
                  <v-btn style="height: 20px; font-size: 10px">Exclode</v-btn>
                  <v-btn style="height: 20px; font-size: 10px">Inclode</v-btn>
                  </v-btn-toggle>
                </v-card-action>
              </v-card>
            </v-col>
          </v-row>
          <v-card-action>
            <span>
              <v-btn class="mt-2" color="warning" style="float:right;" solo depressed>Submit</v-btn>
            </span>
          </v-card-action>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>


<script>
import Header from './Header.vue';
import Download from '../team/Download.vue';
import InsertionModel from './InsertionModel.vue';
export default {
  components: { InsertionModel, Download, Header },
  name: "TeamSearch",
  props: {
    insert_data: {
      type: [],
      default: function () {
        return [];
      },
    },
    download_data:{
      type : [],
      default: function (){
        return [];
      } 
    }
    },
    column_data:{
      type : [],
      default: function (){
        return [];
      } 
    },
  data() {
    return {
      searchData:"",
      sear: "id",
      loading: false,
      select: null,
      dialog: false,
    };
  },

  methods: {
    search(){
      let filter = {
        searchData: this.searchData,
        column: this.sear,
      };
      this.$emit("search", filter);
    },
    someEvent(){
      this.$emit('getData');
    },
    openModel() {
      this.dialog = true;
    },
    openInsertModel(){
      this.$refs.insertModelRef.openDialog(this.insert_data);
    },
    openDownloadModel(){
      this.$refs.openDownload.openDialog(this.download_data);
    },
    openColumnModel(){
      this.$refs.openColumn.openDialog(this.column_data);
    },
    saveChanges(){
      this.$emit('saveChanges');
    }
  },
};
</script>

<style>
.icon1 {
  cursor: pointer;
}

.v-dialog:not(.v-dialog--fullscreen) {
  max-height: 100%;
}
.te {
  text-align: center;
}
.v-messages theme--light {
  display: none;
}
</style>
