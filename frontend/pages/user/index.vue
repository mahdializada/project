<template>
  <div>
    <Head @openModel="openModel" @openModelF="openModelF" :d_data="users" @filterData="filtered"  @IDsearchResult="IDsearchResult"
          :search_tab="searchTab" />
  <br>
    <!-- insert modal -->
    <template>
      <div class="text-center">
        <v-dialog v-model="dialog" width="1000" height="600" persistent>
          <v-stepper
            v-model="step"
            alt-labels
            non-linear
            style="border=none;"
            small
            solo
            dense
          >
            <!-- edit stepper -->
            <v-row :class="isEdit ? '' : 'userEdit'" class="bg-green accent-1">
              <v-col cols="9">
                <h2 class="pa-5" style="color: gray"><b>User Edit </b></h2>
              </v-col>
              <v-col cols="3">
                <v-btn
                  class="ma-3"
                  outlined
                  fab
                  small
                  color="indigo"
                  @click="colesDialog"
                  style="float: right"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-col>
            </v-row>
            <div class="container">
              <v-card :class="isEdit ? '' : 'userEdit'">
                <v-card-title>
                  <v-row>
                    <v-col cols="2">
                      <v-list-item-avatar
                        tile
                        size="120"
                        color="blue"
                        dense
                      >
                        <img
                          style="width: 180px; height: 180px"
                           :src="Editprofile"
                            v-if="Editprofile"
                            alt=""
                        />
                      </v-list-item-avatar>

                  <!-- <template >
                    <v-avatar>
                      <img :src="`http://localhost:8000/${item.image}`" alt="" />
                    </v-avatar>
                  </template> -->
                </v-col>
                <v-col cols="10">
                  <h3 class="d-inline" style="pb-2">
                    {{ isEdit ? this.first_name : "" }}
                  </h3>
                     <v-stepper-header  >
                        <v-stepper-step prepend-icon="mdi-user" :complete="step > 1" step="1">
                          Genral
                        </v-stepper-step>

                        <v-divider></v-divider>

                        <v-stepper-step :complete="step > 2" step="2">
                          Acount
                        </v-stepper-step>

                        <v-divider></v-divider>

                        <v-stepper-step :complete="step > 3" step="3"
                          >Personal</v-stepper-step
                        >
                        <v-divider></v-divider>

                        <v-stepper-step :complete="step > 3" step="4"
                          >Done</v-stepper-step
                        >
                      </v-stepper-header>
                    </v-col>
                  </v-row>
                </v-card-title>
              </v-card>
            </div>
            <!-- insert stepper -->
            <v-stepper-header :class="isEdit ? 'userEdit' : ''">
              <v-stepper-step
                prepend-icon="mdi-user"
                :complete="step > 1"
                step="1"
              >
                Genral
              </v-stepper-step>

              <v-divider></v-divider>

              <v-stepper-step :complete="step > 2" step="2">
                Acount
              </v-stepper-step>

              <v-divider></v-divider>

              <v-stepper-step :complete="step > 3" step="3"
                >Personal</v-stepper-step
              >
              <v-divider></v-divider>

              <v-stepper-step :complete="step > 3" step="4"
                >Done</v-stepper-step
              >

              <v-btn
                class="ma-3"
                outlined
                fab
                small
                color="indigo"
                @click="colesDialog"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-stepper-header>
            <v-form @submit.prevent="submit">
              <v-stepper-items>
                <v-stepper-content step="1">
                  <div style="border-bottom:1px ">
                    <h3 class="text-center" :class="isEdit ? '' : 'userEdit'" style="font-size:28px; margin-top: -9px;" >GENRAL</h3>
                    <hr :class="isEdit ? '' : 'userEdit'">
                  </div>
                  <div class="mb-15 pa-5 overflow-y-auto" style="height: 300px">
                    <v-row>
                      <input type="text" hidden v-model="id">
                      <v-col cols="12">
                        <label for="">Country*</label>
                        <v-autocomplete
                          ref="country"
                          v-model="country_id"
                          :rules="[
                            () => !!country_id || 'This field is required',
                          ]"
                          :items="countries"
                          :loading="loading1"
                          item-text="name"
                          item-value="id"
                          outlined
                          dense
                          @change="getCompany('search')"
                          solo
                          label="country"
                          placeholder="Choose country"
                          required
                        >
                        </v-autocomplete>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="6">
                        <br />
                        <label for="">Company*</label>
                        <v-autocomplete
                          v-model="company_id"
                          :rules="[
                            () => !!company_id || 'This field is required',
                          ]"
                          :items="companies"
                          :loading="loading2"
                          item-text="company_name"
                          item-value="id"
                          @change="getDepartment('search')"
                          outlined
                          dense
                          solo
                          label="Company"
                          placeholder="Choose company"
                          required
                        ></v-autocomplete>
                      </v-col>
                      <v-col cols="6">
                        <br />
                        <label for="">Department*</label>
                        <v-autocomplete
                          ref="department"
                          v-model="department_id"
                          :loading="loading3"
                          :rules="[
                            () => !!department_id || 'This field is required',
                          ]"
                          :items="departments"
                          item-text="department_name"
                          item-value="id"
                          outlined
                          dense
                          solo
                          label="Department"
                          placeholder="Choose departmet"
                          required
                        ></v-autocomplete>
                      </v-col>
                    </v-row>
                  </div>
                  <v-btn color="primary" @click="step = 2" style="float: right">
                    Next <i class="fa-solid fa-angles-right"></i
                  ></v-btn>
                </v-stepper-content>
                <v-stepper-content step="2">
                  <div style="border-bottom: 1px">
                    <h3 class="text-center" :class="isEdit ? '' : 'userEdit'" style="font-size:28px; margin-top: -9px;">
                      ACOUNT
                    </h3>
                    <hr :class="isEdit ? '' : 'userEdit'" />
                  </div>
                  <div class="mb-15 pa-5 overflow-y-auto" style="height: 300px">
                    <v-row>
                      <v-col cols="6">
                        <label for="">FirstName*</label>
                        <v-text-field
                          v-model="first_name"
                          label="First Name"
                          outlined
                          dense
                          solo
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6">
                        <label for="">LastName*</label>
                        <v-text-field
                          v-model="last_name"
                          label="Last Name"
                          outlined
                          dense
                          solo
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6">
                        <label for="">userName*</label>
                        <v-text-field
                          outlined
                          v-model="user_name"
                          label="User Name"
                          append-icon="mdi-lock"
                          dense
                          solo
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6">
                        <label for="">Email*</label>
                        <v-text-field
                          outlined
                          label="Email"
                          v-model="email"
                          append-icon="mdi-email"
                          dense
                          solo
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6" :class="isEdit ? 'userEdit' : ''">
                        <label for="">Password*</label>
                        <v-text-field
                          outlined
                          label="Password"
                          append-icon="mdi-lock"
                          v-model="password"
                          dense
                          solo
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6">
                        <label for="">Phone*</label>
                        <v-text-field
                          outlined
                          v-model="phone"
                          label="Phone"
                          dense
                          solo
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </div>
                  <v-btn @click="step = 1">
                    <i class="fa-solid fa-angles-left"></i>
                    Prev
                  </v-btn>
                  <v-btn color="primary" @click="step = 3" style="float: right">
                    Next <i class="fa-solid fa-angles-right"></i
                  ></v-btn>
                </v-stepper-content>
                <v-stepper-content step="3">
                  <div style="border-bottom: 1px">
                    <h3 class="text-center" :class="isEdit ? '' : 'userEdit'" style="font-size:28px; margin-top: -9px; ">
                      PERSONAL
                    </h3>
                    <hr :class="isEdit ? '' : 'userEdit'" />
                  </div>
                  <div class="mb-15 pa-5 overflow-y-auto" style="height: 300px">
                    <v-row >
                      <v-col cols="2">
                        <v-avatar
                          size="120"
                          style="background-color: lightgrey"
                        >
                          <span :class="profile ? 'userEdit' : ''"
                            ><i
                              class="fa fa-user"
                              style="font-size: 80px; color: white"
                            ></i
                          ></span>
                          <img
                            :src="profile"
                            v-if="profile"
                            style="width: 120px; height: 120px"
                            alt=""
                          />
                        </v-avatar>
                      </v-col>
                      <v-col cols="10">
                        <template>
                          <!-- <dropzone
                          style="border:1px dashed darkgray;"
                          @vdropzone-complete="onComplete"
                            ref="myDropzone" id="dropzone" :options="dropzoneOptions">
                          </dropzone> -->
                          <input
                            type="file"

                            ref="file"
                            @change="onChange"
                            class="dropzone form-control"
                            style="border: 1px dashed; width: 100%"
                          />
                        </template>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12">
                        <label for="">Gender*</label>
                        <v-autocomplete
                          ref="gender"
                          v-model="gender"
                          :rules="[() => !!gender || 'This field is required']"
                          :items="genders"
                          
                          outlined
                          dense
                          solo
                          label="gender"
                          placeholder="Choose gender"
                          required
                        ></v-autocomplete>
                        <label for="">Birth Date*</label>
                        <v-text-field
                          outlined
                          v-model="birth_date"
                          label="yy/mm/dd"
                          dense
                          type="date"
                          solo
                        ></v-text-field>
                        <label for="">Country*</label>
                        <v-autocomplete
                          ref="country"
                          v-model="country_id"
                          :rules="[
                            () => !!country_id || 'This field is required',
                          ]"
                          :items="countries"
                          item-text="name"
                          item-value="id"
                          outlined
                          dense
                          solo
                          label="country"
                          placeholder="Choose country"
                          required
                        ></v-autocomplete>
                      </v-col>
                      <!-- <v-col cols="12">
                        <label for="">Addrress*</label>
                        <v-textarea
                          outlined
                          dense
                          solo
                          label=" Insert the addrress"
                          placeholder="Insert addrress"
                        >
                        </v-textarea>
                      </v-col> -->
                    </v-row>
                  </div>
                  <v-btn text @click="step = 2">
                    <i class="fa-solid fa-angles-left"></i>
                    Prev
                  </v-btn>
                  <v-btn color="primary" @click="step = 4" style="float: right">
                    Next <i class="fa-solid fa-angles-right"></i
                  ></v-btn>
                </v-stepper-content>
                <v-stepper-content step="4">
                  <div class="mb-15 pa-5 overflow-y-auto" style="height: 300px">
                    <v-row>
                      <v-col cols="4 "></v-col>
                      <v-col cols="4">
                        <v-row>
                          <v-col cols="2"> </v-col>
                          <v-col cols="6">
                            <img
                              src="done-icon.svg"
                              style="width: 200px; height: 200px"
                              alt=""
                            />
                          </v-col>
                          <v-col cols="4"></v-col>
                        </v-row>
                      </v-col>
                      <v-col cols="4"></v-col>
                    </v-row>
                  </div>
                  <v-btn text @click="step = 3">
                    <i class="fa-solid fa-angles-left"></i>
                    Prev
                  </v-btn>
                  <v-btn
                    type="submit"
                    color="primary"
                    class="btn btn-primary"
                    style="float: right"
                    text
                    :loading="load"
                  >
                    {{ isEdit ? "Update" : "Save" }}
                  </v-btn>
                </v-stepper-content>
              </v-stepper-items>
            </v-form>
          </v-stepper>
        </v-dialog>
      </div>
    </template>
    <Datatable
      :top="header"
      :record="users"
      :load="loading"
      @getRecord="getData"
      @editItem="editItem"
      @deleteItem="deleteItem"
      @changeStatus="changeStatus"
    />
  </div>
</template>

<script>
import Datatable from "../../components/user/datatable.vue";
import Head from "../../components/user/head.vue";
import Dropzone from "nuxt-dropzone";
import "nuxt-dropzone/dropzone.css";
export default {
  data() {
    return {
      options: {
        url: "http://localhost:8000/api/assets/images/users",
      },
      searchTab: "All",
      checkbox4: false,
      genders: ["male", "Fmale"],
      departments: [],
      companies: [],
      countries: [],
      company_id: "",
      country_id: "",
      user_id: "",
      department_id: "",
      step: 1,
      first_name: "",
      last_name: "",
      user_name: "",
      email: "",
      phone: "",
      image: "",
      password: "",
      birth_date: "",
      gender: "",
      dialog: false,
      dialog2: false,
      dialog3: false,
      load: false,
      id: "",
      loading: false,
      country_id: "",
      isEdit: false,
      profile: "",
      nameEdit: "",
      loading1:false,
      loading2:false,
      loading3:false,
      Editprofile:"",
      dropzoneOptions: {
        url: "http://localhost:8000/api/user",
        thumbnailWidth: 150,
        maxFilesize: 0.5,
        maxFile: 1,
      },
      header: [
        { text: "ID", value: "id" },
        { text: "First Name", value: "first_name" },
        { text: "LastName", value: "last_name" },
        { text: "Profile", value: "image" },
        { text: "User Name", value: "user_name" },
        { text: "Email", value: "email" },
        { text: "Phone", value: "phone" },
        { text: "Status", value: "status" },
        { text: "Gender", value: "gender" },
        { text: "Birth Date", value: "birth_date" },
        { text: "Department", value: "Departments" },
        { text: "Company", value: "departments[0].company.company_name" },
        { text: "permission Type", value: "permission_type" },
      ],
      users: [],
    };
  },
  created() {
    this.getData();
  },
  methods: {
    IDsearchResult(data){
      this.users.data = data;
    },
    filtered(data){
        this.users.data= data;
    },
    async getCountry() {
      const country = await this.$axios.get("country");
      // console.log(country);
      this.countries = country.data;
      console.log("country:",country.data);
    },
    async getCompany(type) {
      let search = {
        country_id: this.country_id,
        type: type,
      };
      this.loading2=true;
      const company = await this.$axios.get("company", { params: search });


      this.companies = company.data;
       console.log("comany",company.data);
      this.loading2=false;

    },
    async getDepartment(type) {
      let search = {
        company_id: this.company_id,
        type: type,
      };
      this.loading3=true;
      const department = await this.$axios.get("department", {params: search});
      this.departments = department.data;
      console.log("department",department.data);
      this.loading3=false;
    },
    openModel() {
      this.dialog = true;
      this.getCountry();
    },
    openModelF() {
      this.dialog3 = true;
    },
    reset() {
       (this.first_name = ""),
        (this.last_name = ""),
        (this.user_name = ""),
        (this.email = ""),
        (this.password = ""),
        (this.companies = ""),
        (this.phone = ""),
        (this.department_id= ""),
        (this.country_id = ""),
        (this.company_id = ""),
        (this.gender = ""),
        (this.birth_date = ""),
        (this.image = "");
        (this.profile = "");
        (this.dialog = false);
    },
    async getData(item = null) {
      this.loading = true;
      const result = await this.$axios.get("user", {
        params: {
          tabKey: item,
        },
      });
      this.users = result.data;
      this.loading = false;
      console.log(result);
    },
    onChange(e) {
      this.image = e.target.files[0];
      console.log(this.image);

      this.profile = window.URL.createObjectURL(this.image);
    },
    async onComplete(response) {
      if (response.status == "success") {
        console.log("upload success");
      } else {
        console.log("not uploaded");
      }
    },
    async submit() {
      console.log(this.country_id);
      this.load = true;
      let user = new FormData();
      user.append("department_id", this.department_id);
      user.append("first_name", this.first_name);
      user.append("image", this.image);
      user.append("last_name", this.last_name);
      user.append("user_name", this.user_name);
      user.append("email", this.email);
      user.append("password", this.password);
      user.append("phone", this.phone);
      user.append("gender", this.gender);
      user.append("birth_date", this.birth_date);
      if (this.isEdit) {
        this.update();
        this.colesDialog();
        this.reset();
      } else {
        const store = await this.$axios.post("user", user);
        if ((store.status = 201)) {
          this.users.data.push(store.data.data);
          this.getData();
        }
      }
      this.load = false;
      this.dialog = false;
      this.isEdit = false;
      this.reset();
      this.$refs.file.value = null;
    },
    async changeStatus(item, tabKey) {
      const newStatus = await this.$axios.put("user/id", item);

      await this.getData(tabKey);
    },
    async update() {
      // let data = {
      //   department_id: this.department_id,
      //   first_name: this.first_name,
      //   last_name: this.last_name,
      //   user_name: this.user_name,
      //   email: this.email,
      //   password: this.password,
      //   phone: this.phone,
      //   gender: this.gender,
      //   birth_date: this.birth_date
      // }
      // const update = await this.$axios.put(`user/${this.id}`,data);

      let user = new FormData();
      user.append("id",this.id);
      user.append("department_id", this.department_id);
      user.append("first_name", this.first_name);
      user.append("image", this.image);
      user.append("last_name", this.last_name);
      user.append("user_name", this.user_name);
      user.append("email", this.email);
      user.append("password", this.password);
      user.append("phone", this.phone);
      user.append("gender", this.gender);
      user.append("birth_date", this.birth_date);
      const update = await this.$axios.post("user",user);
      if (update.status == 200) {
        console.log(this.users.data);
        console.log(update.data);
        this.users.data = this.users.data.map((item) => {
          if (item.id == update.data.id) {
            return update.data;
            
          } else {
            return item;
          }
        });
      }
    },
    async deleteItem(data) {
      const del = await this.$axios.delete(`user/id`, { params: data });
      if (del.status == 200) {
        this.load = true;
        await this.getData(data.tabKey);
        this.load = false;
      }
    },
    async editItem(item){
      console.log("iiteem:",item);
      this.dialog = true;
      // const department = await this.$axios.get(`user/${this.id}`,item);
      this.nameEdit = item.first_name;
      this.Editprofile=`http://localhost:8000/${item.image}`;
      this.id = item.id;
      this.isEdit = true;
      this.user_id = item.id;
      this.first_name = item.first_name;
      this.last_name = item.last_name;
      this.user_name = item.user_name;
      this.email = item.email;
      this.phone = item.phone;
      this.gender = item.gender;
      this.country_id = item.departments[0].company.location.country_id;
      this.department_id = item.departments[0].id;
      this.company_id = item.departments[0].company.id;
      this.birth_date = item.birth_date;
      this.profile = `http://localhost:8000/${item.image}`;
      this.loading1=true;
      await this.getCountry();
      this.loading1=false;
      this.loading2=true;
      await this.getCompany('search');
      this.loading2=false;
      this.loading3=true;
      await this.getDepartment('search');
      this.loading3=false;

    },
    colesDialog() {
      this.dialog2 = false;
      this.dialog = false;
      this.isEdit = false;
      this.reset();
      this.step = 1;
    },
  },
  components: { Datatable, Head, Dropzone },
};
</script>

<style scoped>
.userEdit {
  display: none;
}
.v-stepper__header {
  box-shadow: 0px 0px 0px 0px rgb(0 0 0 / 0%), 0px 0px 0px 0px rgb(0 0 0 / 0%),
    0px 0px 0px 0px rgb(0 0 0 / 0%);
}
</style>
