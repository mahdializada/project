<template>
  <div>
    <div>
      <v-col>
        <Header></Header>
      </v-col>
      <v-col cols="12">
        <search
          @createModel="createModel"
          :d_data="companies"
          @filterData="filtered"
          :country_data="countries"
          :system_data="getSystems"
          @fetchData="fetchData"
          @IDsearchResult="IDsearchResult"
          :search_tab="searchTab"
        />
      </v-col>
    </div>

    <template>
      <div class="text-center">
        <v-dialog v-model="dialog" width="1000px" height="">
          <v-card>
            <template>
              <v-stepper v-model="e1">
                <v-row
                  :class="isEdited ? '' : 'updateCom'"
                  class="bg-green accent-1"
                >
                  <v-col cols="9">
                    <h2 class="pa-5" style="color: gray"><b>Edit Company </b></h2>
                  </v-col>
                  <v-col cols="3">
                    <v-btn
                      class="ma-3"
                      outlined
                      fab
                      small
                      color="indigo"
                      @click="closeDialog"
                      style="float: right"
                    >
                      <v-icon>mdi-close</v-icon>
                    </v-btn>
                  </v-col>
                </v-row>
                <div class="container">
                  <v-card :class="isEdited ? '' : 'updateCom'">
                    <v-card-title>
                      <v-row>
                        <v-col cols="2">
                          <v-list-item-avatar
                            v-model="imageEdit"
                            tile
                            size="120"
                            color="blue"
                            dense
                          >
                            <img
                              :src="imageEdit"
                              v-if="imageEdit"
                              style="width: 180px; height: 180px"
                              alt=""
                            />
                          </v-list-item-avatar>
                        </v-col>
                        <v-col cols="10">
                          <h3 class="d-inline" style="pb-2">
                            {{ isEdited ? this.company_name : "" }}
                          </h3>
                          <v-stepper-header class="update-stepper__header" >
                            <v-stepper-step :complete="e1 > 1" step="1">
                              General
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step :complete="e1 > 2" step="2">
                              Company
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step :complete="e1 > 3" step="3">
                              Address
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step :complete="e1 > 4" step="4">
                              Social Media
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step :complete="e1 > 5" step="5">
                              Done
                            </v-stepper-step>

                          </v-stepper-header>
                        </v-col>
                      </v-row>
                    </v-card-title>
                  </v-card>
                </div>
                <v-stepper-header :class="isEdited ? 'updateCom' : '' " >
                  <v-stepper-step :complete="e1 > 1" step="1">
                    General
                  </v-stepper-step>

                  <v-divider></v-divider>

                  <v-stepper-step :complete="e1 > 2" step="2">
                    Company
                  </v-stepper-step>

                  <v-divider></v-divider>

                  <v-stepper-step :complete="e1 > 3" step="3">
                    Address
                  </v-stepper-step>

                  <v-divider></v-divider>

                  <v-stepper-step :complete="e1 > 4" step="4">
                    Social Media
                  </v-stepper-step>

                  <v-divider></v-divider>

                  <v-stepper-step :complete="e1 > 5" step="5">
                    Done
                  </v-stepper-step>
                  <v-btn
                    color="red lighten-2"
                    solo
                    dense
                    rounded
                    fab
                    class="mx-2 my-4"
                    small
                    @click="closeDialog"
                  >
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                </v-stepper-header>

                <v-form ref="form" v-model="valid" @submit.prevent="submitCom">
                  <v-stepper-items>
                    <v-stepper-content step="1">

                      <v-form ref="form1" lazy-validation v-model="valid1"  @submit.prevent="submitForm1(valid1)" >
                      <v-card
                        class="pa-5 ma-5 overflow-auto"
                        style="height: 400px"
                      >
                        <v-row>
                          <v-col cols="12">
                            <Label>Country</Label>

                            <v-autocomplete
                              v-model="country_id"
                              Class=""
                              solo
                              outlined
                              item-text="name"
                              item-value="id"
                              :items="countries"
                              required
                              clearable
                              :error-messages="errors"
                              :rules="countryRules"
                              placeholder="Please select a country"

                              data-vv-name="country_id" data-vv-scope="form1"
                            >
                            </v-autocomplete>

                          </v-col>
                          <v-col cols="12">
                            <Label>System</Label>

                            <v-autocomplete
                              v-model="system_id"
                              Class=""
                              outlined
                              item-text="system_name"
                              item-value="id"
                              :rules="systemRules"
                              required
                              :items="getSystems"
                              clearable
                              placeholder="Select a system"
                              data-vv-name="system_id" data-vv-scope="form1"
                            ></v-autocomplete>

                          </v-col>
                          <v-col cols="12">
                            <Label>Investment Type</Label>
                            <v-select
                              v-model="investment_type"
                              outlined
                              :items="['main company', 'others']"
                              :rules="invesmentRules"
                              required
                              clearable
                              placeholder="Investment type"
                              data-vv-name="investment_type" data-vv-scope="form1"
                            ></v-select>
                          </v-col>
                        </v-row>
                      </v-card>

                      <v-btn
                        color="primary"
                        class="float-right"
                        type="submit"


                      >
                        Next
                      </v-btn>


                      <v-btn color="red lighten-3" class="mr-4" @click="customReset">
                        Reset Form
                      </v-btn>

                      <v-btn color="indigo lighten-3" @click="resetValidation">
                        Reset Validation
                      </v-btn>
                      </v-form>

                    </v-stepper-content>

                    <v-stepper-content step="2">

                      <v-form  ref="form2" lazy-validation  v-model="valid2" @submit.prevent="submitForm2(valid2)" >

                      <v-card
                        class="ma-5 pa-5"
                        style="height: 400px"
                        height="400px"
                      >
                        <v-row>
                          <v-col cols="12">
                            <label for="company">Company</label>
                            <v-text-field
                              v-model="company_name"
                              placeholder="Select a name for the company..."
                              outlined
                              dense
                              :rules="companyRules"
                              required
                              :counter="10"
                            >
                            </v-text-field>
                          </v-col>
                          <v-col cols="12">
                            <label for="email">Email</label>
                            <v-text-field
                              v-model="email"
                              dense
                              placeholder="Enter an email..."
                              outlined
                              :rules="emailRules"
                              required
                            >
                            </v-text-field>
                          </v-col>
                        </v-row>
                        <v-row>
                          <v-col
                            cols="2"
                            class=""
                            style="color: #000000de !important"
                          >
                            <v-label>Company Logo</v-label>
                            <v-avatar
                              size="120"
                              style="background-color: lightgrey"
                            >
                              <span
                                ><i
                                  class="fa fa-user"
                                  style="font-size: 70px; color: white"
                                ></i
                              ></span>
                            </v-avatar>
                          </v-col>
                          <v-col cols="10">
                            <input
                              type="file"
                              :rules="logoRules"
                              required
                              @change="onChange"
                            />
                          </v-col>
                        </v-row>
                      </v-card>

                      <v-btn
                        color="primary"
                        class="float-right"
                        justify="end"
                        type="submit"


                      >
                        Next
                      </v-btn>
                      <v-btn
                        color="lightgray"
                        style="background-color: lightgray"
                        class="float-left mr-4"
                        @click="e1 = 1"
                      >
                        Back
                      </v-btn>


                      <v-btn color="red lighten-3" class="mr-4" @click="customReset">
                        Reset Form
                      </v-btn>

                      <v-btn color="indigo lighten-3" @click="resetValidation">
                        Reset Validation
                      </v-btn>
                    </v-form>
                    </v-stepper-content>
                    <v-stepper-content step="3">
                      <v-form  ref="form3" lazy-validation  v-model="valid3" @submit.prevent="submitForm3(valid3)" >
                      <v-card
                        class="ma-5 pa-5"
                        style="height: 400px"
                        height="400px"
                      >
                        <div>
                          <v-row>
                            <v-col cols="12">
                              <label for="Company">State/District</label>
                              <v-text-field
                                v-model="state"
                                placeholder="Insert the state..."
                                outlined
                                dense
                                :rules="stateRules"
                                required
                                :counter="10"
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="12">
                              <label for="email">City</label>
                              <v-text-field
                                v-model="city"
                                :rules="cityRules"
                                :counter="10"
                                required
                                dense
                                placeholder="Enter the city..."
                                outlined
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="12">
                              <label for="email">Address Line</label>
                              <v-textarea
                                v-model="address"
                                :rules="addressRules"
                                required
                                dense
                                placeholder="Enter the address line..."
                                outlined
                              >
                              </v-textarea>
                            </v-col>
                          </v-row>
                        </div>
                      </v-card>

                      <v-btn
                        color="primary"
                        class="float-right"
                        justify="end"
                        type="submit"
                      >
                        Next
                      </v-btn>
                      <v-btn
                        color="lightgray"
                        style="background-color: lightgray"
                        class="float-left mr-4"
                        @click="e1 = 2"
                      >
                        Back
                      </v-btn>
                      <v-btn color="red lighten-3" class="mr-4" @click="customReset">
                        Reset Form
                      </v-btn>

                      <v-btn color="indigo lighten-3" @click="resetValidation">
                        Reset Validation
                      </v-btn>
                    </v-form>
                    </v-stepper-content>

                    <v-stepper-content step="4">
                      <v-form  ref="form4" lazy-validation  v-model="valid4" @submit.prevent="submitForm4(valid4)" >
                      <v-card
                        class="ma-5 pa-5"
                        style="height: 400px"
                        height="400px"
                      >
                        <div>
                          <v-row>
                            <v-col cols="12">
                              <label for="social">Social Media</label>
                              <v-autocomplete
                                v-model="media_id"
                                placeholder="Select a social media"
                                outlined
                                dense
                                :rules="mediaRules"
                                required
                                :items="socialMedia"
                                item-text="name"
                                item-value="id"
                                clearable
                                @change="changeLink"
                              >
                              </v-autocomplete>
                            </v-col>
                            <v-col cols="12">
                              <label for="link">Link</label>
                              <v-text-field
                                v-model="link"
                                :rules="linkRules"
                                required
                                v-show="showLink"
                                dense
                                placeholder="Enter the link ..."
                                outlined
                              >
                              </v-text-field>
                            </v-col>
                          </v-row>
                        </div>
                      </v-card>

                      <v-btn
                        color="primary"
                        class="float-right"
                        type="submit"
                        :disabled="!valid"

                      >
                        {{isEdited ? "Update":"Save"}}
                      </v-btn>
                      <v-btn
                        color="lightgray"
                        style="background-color: lightgray"
                        class="float-left mr-4"
                        @click="e1 = 3"
                      >
                        Back
                      </v-btn>

                      <v-btn color="red lighten-3" class="mr-4" @click="customReset">
                        Reset Form
                      </v-btn>

                      <v-btn color="indigo lighten-3" @click="resetValidation">
                        Reset Validation
                      </v-btn>
                    </v-form>
                    </v-stepper-content>

                    <v-stepper-content step="5">
                      <v-card class="ma-5 pa-5" style="height: 400px">
                        <v-row>
                          <v-col cols="4"></v-col>
                          <v-col cols="4">
                            <v-row>
                              <v-col cols="3"></v-col>
                              <v-col cols="8">
                                <img
                                  class="ma-50 pa-auto"
                                  src="done-icon.svg"
                                  justify="center"
                                  width="150px"
                                  height="150px"
                                  alt="No photo"
                                />
                              </v-col>
                              <v-col cols="1"></v-col>
                            </v-row>
                          </v-col>
                          <v-col cols="4"></v-col>
                        </v-row>
                      </v-card>
                      <v-divider></v-divider>
                      <v-btn
                        color="red lighten-2"
                        success
                        class="float-right"
                        @click="closeDialog"
                      >
                        Close
                      </v-btn>
                      <v-btn
                        color="lightgray"
                        style="background-color: lightgray"
                        class="float-left mr-4"
                        @click="e1 = 4"
                      >
                        Back
                      </v-btn>

                    </v-stepper-content>
                  </v-stepper-items>
                </v-form>
              </v-stepper>
            </template>
          </v-card>
        </v-dialog>
      </div>
    </template>

    <Datatable
      :headers="header"
      :items="companies"
      :loading="loading"
      @getRecord="getData"
      @insertModel="insertModel"
      @editModel="editItem"
      @deleteItem="deleteItem"
      @changeStatus="changeStatus"
    >
    </Datatable>
  </div>
</template>

<script>
import Datatable from "~/components/company/datatable.vue";
import search from "~/components/company/search.vue";
import Header from "~/components/company/Header.vue";
import Dropzone from "nuxt-dropzone";
import "nuxt-dropzone/dropzone.css";




export default {
  components: { Datatable, search, Dropzone, Header },


  // setup () {
  //   return { v$: useVuelidate() }
  // },

  data() {
    return {
      options: {
        url: "http://httpbin.org/anything",
      },
      searchTab: "All",
      search: "",
      showLink: false,
      valid: false,
      e1: 1,
      load: false,
      loading: false,
      dialog: false,
      countries: [],
      company_name: "",
      email: "",
      investment_type: "",
      investment: "",
      address: "",
      city: "",
      state: "",
      logo: "",
      status: "",
      id: "",
      media_id: "",
      link: "",
      socialMedia: [],
      system_id: "",
      system_name:'',
      country_name:"",
      getSystems: [],
      country_id: "",
      isEdited: false,
      valid: false,
      valid1: false,
      companyRules: [
        (v) => !!v || "Name is required",
        (v) => v.length <= 10 || "Name must be less than 10 characters"
      ],
      stateRules: [
        (v) => !!v || "Name is required",
        (v) => v.length <= 10 || "Name must be less than 10 characters"
      ],
      cityRules: [
        (v) => !!v || "Name is required",
        (v) => v.length <= 10 || "Name must be less than 10 characters"
      ],
      linkRules: [(v) => !!v || "Name is required"],
      countryRules: [(v) => !!v || "Company is required"],
      systemRules: [(v) => !!v || "System is required"],
      logoRules: [(v) => !!v || "Logo is required"],
      addressRules: [(v) => !!v || "Address is required"],
      invesmentRules: [(v) => !!v || "Investment type is required"],
      mediaRules: [(v) => !!v || "Social Media is required"],
      emailRules: [
        (v) => !!v || "E-mail is required",
        (v) => /.+@.+/.test(v) || "E-mail must be valid",
      ],
      header: [
        { text: "ID", value: "id" },
        { text: "Company", value: "company_name" },
        { text: "Systems", value: "systems" },
        { text: "Email", value: "email" },
        { text: "Logo", value: "logo" },
        { text: "Investment Type", value: "investment_type" },
        { text: "Country", value: "location.country.name" },
        { text: "Status", value: "status" },
        { text: "Created At", value: "created_at" },
        { text: "Updated At", value: "updated_at" },
      ],
      companies: [],
    };
  },
  validations () {
    return {
      company_name: { required }, // Matches this.firstName
      country_id: { required }, // Matches this.lastName
      contact: {
        email: { required, email } // Matches this.contact.email
      }
    }
  },

  created() {
    this.getData();
    this.searchTab="All";
  },
  methods: {

    submitForm1(isValid) {
        if(this.$refs.form1.validate()){
          this.e1 = this.e1 + 1;
          console.log("successful")
        }
        else{
          console.log("invalid")
        }
    },
    submitForm2(isValid) {
        if(this.$refs.form2.validate()){
          this.e1 = this.e1 + 1;
          console.log("successful")
        }
        else{
          console.log("invalid")
        }
    },
    submitForm3(isValid) {
        if(this.$refs.form3.validate()){
          this.e1 = this.e1 + 1;
          console.log("successful")
        }
        else{
          console.log("invalid")
        }
    },
    submitForm4(isValid) {
        if(this.$refs.form4.validate()){
          this.e1 = this.e1 + 1;
          this.submitCom();
          console.log("successful")
        }
        else{
          console.log("invalid")
        }
    },

    validate() {
      this.$refs.form.validate();
    },
    reset() {
      this.$refs.form.reset();
    },
    resetValidation() {
      this.$refs.form.resetValidation();
    },
    onChange(e) {
      this.logo = e.target.files[0];
      console.log(this.logo);
    },
    async submitCom() {
      let form = new FormData();
      form.append("logo", this.logo);
      form.append("country_id", this.country_id);
      form.append("company_name", this.company_name);
      form.append("investment_type", this.investment_type);
      form.append("system_id", this.system_id);
      form.append("state", this.state);
      form.append("city", this.city);
      form.append("address", this.address);
      form.append("email", this.email);
      form.append("media_id", this.media_id);
      form.append("link", this.link);


      const result = await this.$axios.post("company", form);
      if (result.status == "201") {
        // this.companies.unshift(result.data);
        this.getData();

      }

    },
    async submitUpdate(form){
      const updated = await this.$axios.put(`company/${this.id}`, form);
      if(updated.status == 200){
        this.companies = this.companies.map((item)=>{
          if(item.id == this.id){
            return updated.data;
          }else{
            return item;
          }
        });
      }
    },
    changeLink() {
      this.showLink = !this.showLink;
    },
    filtered(data) {
      this.companies = data;
    },
    async fetchData() {
      const companyData = await this.$axios.get("country");
      this.countries = companyData.data;
      const systemData = await this.$axios.get("system");
      this.getSystems = systemData.data;
      const mediaData = await this.$axios.get("socialmedia");
      console.log(mediaData.data);
      this.socialMedia = mediaData.data.data;
    },
    createModel() {
      this.dialog = true;
      this.fetchData();
    },
    async changeStatus(item, tabKey) {
      const newStatus = await this.$axios.put("company/id", item);

      await this.getData(tabKey);
    },
    async insertModel() {
      const countries = await this.$axios.get("country");

      this.countries = countries.data;

      this.dialog = true;
    },
    async getData(item = null) {
      this.loading = true;
      this.searchTab = item;
      const result = await this.$axios.get("company", {
        params: {
          tabKey: item,
        },
      });
      this.companies = result.data;

      this.loading = false;
    },
    IDsearchResult(data){
      this.companies = data;
    },
    async submit() {
      this.load = true;
      let info = {
        id : this.country_id,
        company_name: this.company_name,
        email: this.email,
        investment_type: this.investment_type,
        city: this.city,
        state: this.state,
        addressLine: this.addressLine,
        media_id :this.media_id,
        link : this.link,
      };
      const updated = await this.$axios.put(`company/${this.id}`, form);
      if(updated.status == 200){
        this.companies = this.companies.map((item)=>{
          if(item.id == this.id){
            return updated.data;
          }else{
            return item;
          }
        });
      }
      this.load = false;
      this.dialog = false;
      // this.reset();
    },
    async deleteItem(data) {
      const dell = await this.$axios.delete(`company/id`, { params: data });
      if (dell.status == 200) {
        this.load = true;
        await this.getData(data.tabKey);
        this.load = false;
      }
      this.customReset();
    },
    editItem(item) {

      this.isEdited = true;
      console.log(item);
      this.dialog = true;
      this.country_id = item.location.country_id;
      this.company_name = item.company_name;
      this.email = item.email;
      this.investment_type = item.investment_type;
      this.log = item.logo;
      this.system_id = item.systems.id;
      this.system_name = item.systems.system_name;
      this.country_name = item.location.country.name;
      this.city = item.location.city;
      this.state = item.location.state_district;
      this.address = item.location.address_line;
      this.fetchData();
      this.dialog = true;

    },
    async update(info) {
      const update = await this.$axios.put(`company/${this.id}`, info);
      if (update.status == 200) {
        this.companies = this.companies.map((item) => {
          if (item.id == this.id) {
            return update.data;
          } else {
            return item;
          }
        });
      }
      this.customReset();
    },
    customReset() {
      this.company_name = "";
      this.email = "";
      this.logo = "";
      this.investment_type = "";
      this.state = "";
      this.city = "";
      this.address = "";
      this.link = "";
      this.country_id = "";
      this.system_id = "";
      this.media_id = "";
    },
    closeDialog() {
      this.dialog = false;
      this.isEdited = false;
      this.e1 = 1;
      this.customReset();
      this.resetValidation();
    },
  },
};
</script>
<style scoped>
.updateCom {
  display: none;
}
.update-stepper__header {
  box-shadow: 0px 0px 0px 0px rgb(0 0 0 / 0%), 0px 0px 0px 0px rgb(0 0 0 / 0%),
    0px 0px 0px 0px rgb(0 0 0 / 0%);
}
</style>
