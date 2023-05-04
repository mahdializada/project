<template>
  <div>
    <template>
      <DepHeader></DepHeader>
    </template>
    <!-- <template>
      <div>

        <v-dialog v-model="viewValue" width="1250" persistent>
          <v-card class="mx-auto" max-width="1000">
            <v-system-bar>
              <v-spacer></v-spacer>
              <v-icon>mdi-close</v-icon>
            </v-system-bar>

            <v-toolbar color="indigo" dark>

              <v-toolbar-title>Department Profile</v-toolbar-title>

              <v-spacer></v-spacer>

              <v-btn icon>
                <v-icon>mdi-magnify</v-icon>
              </v-btn>
            </v-toolbar>

            <v-container fluid>
              <v-row dense>
                <v-col
                  v-for="card in cards"
                  :key="card.title"
                  :cols="card.flex"
                >
                  <v-card>
                    <v-img
                      :src="card.src"
                      class="white--text align-end"
                      gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                      height="200px"
                    >
                      <v-card-title v-text="card.title"></v-card-title>
                    </v-img>

                    <v-card-actions>
                      <v-spacer></v-spacer>

                      <v-btn icon>
                        <v-icon>mdi-heart</v-icon>
                      </v-btn>

                      <v-btn icon>
                        <v-icon>mdi-bookmark</v-icon>
                      </v-btn>

                      <v-btn icon>
                        <v-icon>mdi-share-variant</v-icon>
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-dialog>
      </div>
    </template> -->

    <template>
      <div>
        <v-dialog v-model="viewValue" width="1300" persistent>
          <v-card style="background-color: aliceblue" height="600">
            <v-toolbar color="primary" dark height="150">
              <v-toolbar-title style="margin-top: -80px; margin-left: 27px">
                <strong>Department Profile</strong>
              </v-toolbar-title>
              <v-spacer></v-spacer>
              <v-icon
                style="margin-top: -80px; margin-right: 20px"
                @click="viewValue = false"
                >mdi-close-circle-outline</v-icon
              >
            </v-toolbar>
            <div class="overlay">
              <v-card
                class="flex-grow-1 d-flex v-sheet pa-0 ma-0 mx-auto"
                max-width="1250"
                height="180"
              >
                <div class="d-flex flex-grow- align-center">
                  <div
                    class="d-flex flex-column align-center"
                    style="width: 100px"
                  >
                    <v-avatar
                      size="120"
                      style="
                        margin-left: 80px;
                        background-color: #1976d2;
                        border: 10px solid lightgray;
                      "
                    >
                      <span style="color: white" class="fa-2x">{{
                        companies?.department_name?.charAt(0).toUpperCase()
                      }}</span>
                    </v-avatar>
                  </div>
                </div>
                <div class="d-flex" style="width: 700px; color: aqua">
                  <span>
                    <v-dialog
                      v-model="viewLoader"
                      hide-overlay
                      persistent
                      width="300"
                    >
                      <v-card color="primary" dark>
                        <v-card-text>
                          Please stand by
                          <v-progress-linear
                            indeterminate
                            color="white"
                            class="mb-0"
                          ></v-progress-linear>
                        </v-card-text>
                      </v-card>
                    </v-dialog>
                    <h2
                      class="ml-16 mt-11"
                      style="height: 20px; color: #1976d2"
                    >
                      {{ companies.department_name }}
                    </h2>
                    <v-btn
                      v-if="companies.status"
                      class="ml-16 mt-8"
                      rounded
                      outlined
                      color="teal"
                      dense
                      dark
                      ><strong color="teal">{{ companies.status }}</strong></v-btn
                    >
                  </span>
                </div>
                <div class="d-flex" style="width: 500px">
                  <span class="mr-2" style="margin-top: 71px">
                    <i class="fa-solid fa-user-plus"></i>
                  </span>
                  <span class="mt-16">
                    <h2>{{ companies.department_name }}</h2>
                  </span>
                  <span class="mt-15 ml-5">
                    <v-btn
                      rounded
                      style="background-color: lightgrey; height: 45px"
                      >{{ companies.created_at }}</v-btn
                    >
                  </span>
                </div>
              </v-card>
            </div>
            <div
              class="d-flex ml-10 mt-10"
              style="width: 1160px; border-bottom: 1px solid grey"
            >
              <v-tabs
                background-color="#f0f8ff"
                v-model="tab"
                align-with-title
                style="border-bottom-color: transparent"
              >
                <v-tabs-slider
                  color="white"
                  style="height: 1px"
                ></v-tabs-slider>
                <v-tab v-for="item in items" :key="item" class="nav-tabs">
                  {{ item }}
                </v-tab>
              </v-tabs>
            </div>
            <v-tabs-items v-model="tab" class="ml-14">
              <v-tab-item v-for="item in items" :key="item">
                <!-- // table -->
                <!--
                <v-card flat style="background-color: #f0f8ff !important">
                  <v-card-text>{{ text }}</v-card-text>

                </v-card> -->
                <v-card
                  color="grey"
                  width="1200px"
                  outlined
                  class="pa-0 primary"
                >
                  <!-- <template> -->
                    <v-simple-table dense class="table pa-5">


                          <tr class="text-left">
                            <th  id="id1">Country</th>
                            <td >
                              <v-tooltip bottom>
                            <template v-slot:activator="{ on: tooltip }">
                              <span
                                color="primary"
                                dark
                                outlined
                                dense
                                solo
                                v-bind="attrs"
                                v-on="{ ...tooltip, ...menu }"
                              >
                                Countries
                              </span>
                            </template>
                            welcome
                            <span v-for="(system,index) in item.systems" :key="index" >{{ system.system_name }}<br></span>
                          </v-tooltip>
                            </td>
                             <th id="id1">Created by</th>
                            <td >Ocatavia Mann</td>
                          </tr>
                          <!-- <template v-slot:item.companies="{ item }"> -->
                          <tr class="text-left hover">
                            <th id="quantity1">Company</th>
                            <td>
                              <v-tooltip bottom>
                              <template v-slot:activator="{ on: tooltip }">
                                <span
                                  color="primary"
                                  dark
                                  outlined
                                  dense
                                  solo
                                  v-bind="attrs"
                                  v-on="{ ...tooltip, ...menu }"
                                >
                                  Companies
                                </span>
                              </template>
                              <!-- <span>{{ companies.company.company_name}}</span> -->
                              <span>{{ companies.company.company_name}}</span>
                            </v-tooltip>
                          </td>
                          <th id="quantity1">Created at</th>
                          <td>{{ companies.created_at }}</td>
                        </tr>
                      <!-- </template> -->



                    </v-simple-table>
                  <!-- </template> -->
                </v-card>
              </v-tab-item>
            </v-tabs-items>
          </v-card>
        </v-dialog>
      </div>
    </template>

    <!-- <template>
      <div class="text-center">
        <v-dialog v-model="upload" width="1150" persistent>
          <v-stepper v-model="step" alt-labels non-linear>
            <v-stepper-header style="background-color: aliceblue">
              <v-stepper-step complete step="1"> Choose File </v-stepper-step>

              <v-divider></v-divider>

              <v-stepper-step step="2" complete> File Upload </v-stepper-step>

              <v-divider></v-divider>

              <v-stepper-step step="3" complete> Check </v-stepper-step>

              <v-divider></v-divider>

              <v-stepper-step step="3" complete> Done </v-stepper-step>
              <v-btn
                class="ma-3"
                outlined
                fab
                small
                color="indigo"
                @click="closeDialog"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-stepper-header>
            <v-stepper-items>
              <v-steppper-content>
              <template>
                    <dropzone
                      style="border:1px dashed darkgray;"
                      type="file"
                      v-model="image"
                      @vdropzone-file-added="onFileAdded"
                      @vdropzone-error="onError"
                      @vdropzone-success="onSuccess"
                      @vdropzone-complete="onComplete"
                      :previewElement="false"
                      ref="myDropzone" id="dropzone" :options="dropzoneOptions">
                    </dropzone>
                  </template>
                </v-steppper-content>
            </v-stepper-items>
          </v-stepper>
        </v-dialog>
      </div>
    </template> -->

    <template>
      <div class="text-center">
        <v-dialog v-model="stepper" width="1150" persistent>
          <v-stepper v-model="step" alt-labels non-linear>
            <v-stepper-header style="background-color: aliceblue">
              <v-stepper-step :complete="e1 > 1" step="1">
                Genral
              </v-stepper-step>

              <v-divider></v-divider>

              <v-stepper-step step="2">Done</v-stepper-step>
              <v-btn
                class="ma-3"
                outlined
                fab
                small
                color="indigo"
                @click="closeDialog"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-stepper-header>
            <v-stepper-items>
              <v-stepper-content step="1">
                <v-form
                  ref="form"
                  @submit.prevent="submit"
                  v-model="valid"
                  lazy-validation
                >
                  <div class="mb-15 pa-5 overflow-y-auto" style="height: 300px">
                    <v-row>
                      <v-col cols="10">
                        <label for="">Country *</label>
                        <v-select
                          @change="getCompany('search')"
                          v-model="country_id"
                          :items="countries"
                          item-text="name"
                          item-value="id"
                          :rules="[(v) => !!v || 'Country Name is required']"
                          outlined
                          dense
                          placeholder="Choose country"
                          required
                          class="mt-1"
                        ></v-select>
                      </v-col>
                      <v-col cols="2">
                        <v-btn outlined class="mt-7" color="indigo">
                          Add+
                        </v-btn>
                      </v-col>
                    </v-row>
                    <v-row class="demo">
                      <v-col cols="10">
                        <label for="">Company *</label>
                        <v-select
                          v-model="com_name"
                          :items="companies"
                          item-text="company_name"
                          item-value="id"
                          :rules="[(v) => !!v || 'Company Name is required']"
                          outlined
                          dense
                          placeholder="Choose Company"
                          required
                          class="mt-1"
                        ></v-select>
                      </v-col>
                      <v-col cols="2">
                        <v-btn outlined class="mt-7" color="indigo">
                          Add+
                        </v-btn>
                      </v-col>
                    </v-row>
                    <v-row class="demo">
                      <v-col cols="12">
                        <label for="">Department Name*</label>
                        <v-text-field
                          v-model="dep_name"
                          :rules="nameRules"
                          outlined
                          dense
                          class="mt-1"
                          placeholder="Department Name"
                          required
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </div>
                  <div>
                    <v-btn
                      color="primary"
                      type="submit"
                      style="float: right"
                      @click="validate()"
                    >
                      submit <i class="fa fa-check"></i
                    ></v-btn>
                  </div>
                </v-form>
              </v-stepper-content>
              <v-stepper-content step="2">
                <v-btn sm color="primary" style="float: right">
                  Start Over <i class="mdi mdi-restore"></i>
                </v-btn>
              </v-stepper-content>
            </v-stepper-items>
          </v-stepper>
        </v-dialog>
      </div>
    </template>
    <!-- <template>
      <v-dialog v-model="stepper" width="1120">
        <v-stepper v-model="curr" color="green">
          <v-stepper-header
            style="background-color: aliceblue"
          >
          <v-stepper-step
          v-for="(step, n) in steps"
          :key="n"
          :complete="stepComplete(n + 1)"
              :step="n + 1"
              :rules="[(value) => !!step.valid]"
              :color="stepStatus(n + 1)"
              style="display:inline-block !important;text-align: center;"
              >
              <v-divider></v-divider>
              {{ step.name }}
          </v-stepper-step>
          </v-stepper-header>
          <v-stepper-content v-for="(step, n) in steps" :step="n + 1" :key="n">
            <v-container>
            <v-form
              :ref="'stepForm'"
              v-model="step.valid"
              lazy-validation
            >
              <v-row>
                <v-col cols="10">
                  <label>Country *</label>
                    <v-text-field outlined dense class="mt-1" placeholder="Choose Country"
                    :rules="step.rules"
                    > </v-text-field>
                  </v-col>
                  <v-col cols="2">
                    <v-btn outlined color="primary" class="mt-7">Add+</v-btn>
                  </v-col>
                </v-row>
              <v-row>
                <v-col cols="10">
                  <label>Company *</label>
                    <v-text-field outlined dense class="mt-1" placeholder="Choose Company"
                    :rules="step.rules"
                    > </v-text-field>
                  </v-col>
                  <v-col cols="2">
                    <v-btn outlined color="primary" class="mt-7">Add+</v-btn>
                  </v-col>
                </v-row>
              </v-form>
            </v-container>
            {{ step.name }}
            <v-card class="mb-12" height="200px">
              <v-card-text>
                <v-form :ref="'stepForm'" v-model="step.valid" lazy-validation>
                  <v-text-field
                    label="Enter something..."
                    :rules="step.rules"
                  ></v-text-field>
                </v-form>
              </v-card-text>
            </v-card>
            <v-btn
              v-if="n + 1 < lastStep"
              color="primary"
              @click="validate(n)"
              :disabled="!step.valid"
              >Next</v-btn
            >
            <v-btn v-else color="success" @click="done()">Done</v-btn>
            <v-btn text @click="curr = n">Prev</v-btn>
          </v-stepper-content>
        </v-stepper>
      </v-dialog>
    </template> -->

    <!-- <template>
      <div class="text-center">
        <v-dialog v-model="dialog" persistent width="500">
          <v-card>
            <v-form @submit.prevent="submit">
              <v-card-title class="text-h6 pink lighten-2 justify-center">
                Multi Department Form
              </v-card-title>
              <v-card-text>
                <v-row>
                  <v-col cols="12">
                    <v-select
                      v-model="com_name"
                      :items="companies"
                      item-text="company_name"
                      item-value="id"
                    >
                    </v-select>
                  </v-col>
                  <v-col cols="12" sm="12">
                    <img
                      :src="image"
                      alt=""
                      class="rounded"
                      v-if="image"
                      style="width: 150px; height: 150px; position: relative"
                    />
                  </v-col>
                  <v-col cols="12" sm="12">
                    <input type="file" @change="onChange" ref="file" />
                  </v-col>
                  <v-col cols="12">
                    <v-text-field v-model="dep_name" label="dep_name">
                    </v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field v-model="status" label="status">
                    </v-text-field>
                  </v-col>
                </v-row>
              </v-card-text>
              <v-divider></v-divider>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="closeDialog">
                  Cancel
                </v-btn>
                <v-btn color="primary" type="submit" solo dark>
                  submit
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-dialog>
      </div>
    </template> -->

    <Datatable
      :fields="header"
      :contents="departments"
      :load="loading"
      :deps="filterDep"
      @getRecord="getData"
      @DeleteRecord="DelData"
      @sendRecord="changeStatus"
      @search="search"
      @dialogStepper="dialogStepper"
      @viewModel="viewDialog"
      :download-data="departments"
    >
    </Datatable>
  </div>
</template>


<script>
import Datatable from "~/components/department/datatable.vue";
import DepHeader from "../../components/department/depHeader.vue";

// import Dropzone from 'nuxt-dropzone'
// import 'nuxt-dropzone/dropzone.css'

export default {
  components: { Datatable, DepHeader },
  data() {
    return {
      tab: null,
      items: ["General", "Remarks", "Status Transformation"],
      text: "welcome dear in nuxt and laravel developing",
      viewValue: false,
      valid: true,
      e1: 0,
      stepper: false,
      upload: false,
      step: 1,
      tabKey: "all",
      nameRules: [
        (v) => !!v || "DepartmentName is required",
        (v) => (v && v.length <= 30) || "Name must be less than 30 characters",
      ],
      // curr: 1,
      // lastStep: 4,
      // steps: [
      //   { name: "General", rules: [(v) => !!v || "Required."], valid: true },
      //   { name: "Department", rules: [(v) => !!v || "Required."], valid: true },
      //   {
      //     name: "Remarks",
      //     rules: [
      //       (v) => (v && v.length >= 4) || "Enter at least 4 characters.",
      //     ],
      //     valid: true,
      //   },
      //   { name: "Done" },
      // ],
      // valid: false,
      // stepForm: [],
      filterDep: [],
      viewLoader: false,
      image: "",
      filters: {},
      dep_name: "",
      profile: "",
      com_name: "",
      country_id: "",
      depname: [],
      intials: "",
      status: "",
      dep_status: "",
      id: "",
      // isEdit: false,
      // load: false,
      dialog: false,
      companies: [],
      countries: [],
      com_id: "",
      header: [
        { text: "ID", value: "id" },
        { text: "LOGO", value: "logo" },
        { text: "NAME", value: "department_name" },
        { text: "COMPANIES", value: "company.company_name" },
        { text: "STATUS", value: "status" },
      ],
      departments: [],
      // dropzoneOptions: {
      //   url: 'department',
      //   thumbnailWidth: 150,
      //   maxFilesize: 0.5,
      //   headers: { "My-Awesome-Header": "header value" }
      // }
    };
  },
  created() {
    this.getData();
  },
  methods: {
    // stepComplete(step) {
    //   return this.curr > step;
    // },
    // stepStatus(step) {
    //   return this.curr > step ? "green" : "blue";
    // },
    // validate(n) {
    //   this.steps[n].valid = false;
    //   let v = this.$refs.stepForm[n].validate();
    //   if (v) {
    //     this.steps[n].valid = true;
    //     // continue to next
    //     this.curr = n + 2;
    //   }
    // },
    // done() {
    //   this.curr = 5;
    // },
    async viewDialog(demo) {
      this.viewLoader = true;
      this.companies = demo;
      this.viewLoader = false;
      this.viewValue = true;
      // this.depname=item.department_name;
      // this.dep_status=item.status;
      // // console.log(this.depname);
      // await this.getData();

      // console.log(res);
      // this.depname = res.department_name;

      // if(item.id == this.id){
      // console.log(res);
      // await this.getData(item.tabkey);
      // }
    },
    async validate() {
      this.$refs.form.validate();
    },
    async search(filter) {
      this.getData1(filter);
    },
    async getData2(demo1) {
      const res = await this.$axios.get(`department/${demo1}`);
      this.depname = res.data;
      this.intials = this.depname;
    },
    async getData1(pa) {
      const re = await this.$axios.get("department", { params: pa });
      this.departments = re.data;
    },

    async getData(item = null) {
      this.loading = true;
      const result = await this.$axios.get("department", {
        params: {
          tabkey: item,
        },
      });
      this.departments = result.data;
      this.loading = false;
    },
    async changeStatus(item, tabkey) {
      const newStatus = await this.$axios.put(`department/id`, item);
      console.log(newStatus);
      await this.getData(tabkey);
    },
    async DelData(data) {
      const del = await this.$axios.delete(`department/id`, { params: data });
      if (del.status == 200) {
        this.load = true;
        await this.getData(data.tabkey);
        this.load = false;
      }
    },
    async getCompany(type = null) {
      let data = {
        country_id: this.country_id,
        type: type,
      };
      const com = await this.$axios.get("company", { params: data });
      console.log(com);
      this.companies = com.data;
    },
    // async openMD() {
    //   this.stepper = true;
    //   const companyData = await this.$axios.get("company");
    //   this.companies = companyData.data;
    // },
    onChange(e) {
      this.profile = e.target.files[0];
      this.image = window.URL.createObjectURL(this.profile);
    },
    resetDialog() {
      this.dialog = false;
      this.image = "";
      this.dep_name = "";
      this.com_name = "";
      this.status = "";
      this.$refs.file.value = null;
    },
    async submit() {
      this.load = true;
      let form = new FormData();
      // form.append("logo", this.profile);
      form.append("department_name", this.dep_name);
      form.append("company_id", this.com_name);
      const store = await this.$axios.post("department", form);
      if (store?.status == 201) {
        this.resetDialog();
        // this.departments.unshift(store?.data);
        this.getData();
      }
    },

    closeDialog() {
      this.dialog = false;
      this.stepper = false;
      this.step = 1;
    },
    async dialogStepper() {
      this.stepper = true;
      const companyData = await this.$axios.get("country");
      this.countries = companyData.data;
    },
    // uploadStepper(){
    //   this.upload = true;
    // },
    // onFileAdded(e) {
    //     console.log(e);

    //   },
    //   onError(e) {

    //   },
    //   onSuccess(e) {

    //   },
    //   onComplete(e) {
    //   }
  },
};
</script>

<style scoped>
div.overlay {
  margin-top: -90px;
  position: relative;
}
.demo {
  margin-top: -20px;
}
.nav-tabs {
  /* border: 1px solid rgb(180, 188, 194) !important; */
  border-radius: 4px 4px 0 0;
  padding-left: 0px;
  margin-bottom: -1px;
  border-bottom-color: red !important;

}
.v-application--is-ltr
  .v-tabs--align-with-title
  > .v-tabs-bar:not(.v-tabs-bar--show-arrows):not(.v-slide-group--is-overflowing)
  > .v-slide-group__wrapper
  > .v-tabs-bar__content
  > .v-tabs-slider-wrapper
  + .v-tab {
  /* border: 1px solid rgb(180, 188, 194) !important; */
  border-bottom-color: transparent !important;
  margin-left: 0px !important;
  /* border-right: 1px solid rgb(205, 213, 220); */
  /* border-bottom-style: inset; */
}
.v-tab--active {
  border: 1px solid rgb(180, 188, 194) !important;
  border-bottom-color: red !important;
  margin-left: 0px !important;
}
.solid {
  border: 1px solid rgb(221, 227, 233);
  border-radius: 4px;
}

.table th,
.table td {
  padding: 15px;
}

.table .hover {
  background-color: #d1d1db;
}

@media (max-width: 768px) {
  .table td,
  .table th,
  .table tr {
    display: block;
    margin-top: -1px;
  }

  .table th {
    text-align: left;
  }

  .table td {
    padding-left: 30px;
  }
}
.v-window {
  margin-left: 40px !important ;
  margin-right: 90px;
  margin-top: 10px;
}
</style>

