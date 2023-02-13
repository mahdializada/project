<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="1000" persistent>
      <v-stepper v-model="step" alt-labels non-linear>
        <v-stepper-header show-arrow>
          <v-stepper-step :complete="step > 1" step="1">
            General
          </v-stepper-step>
          <v-divider></v-divider>
          <v-stepper-step :complete="step > 2" step="2"> Team </v-stepper-step>
          <v-divider></v-divider>
          <v-stepper-step :complete="step > 3" step="3">
            Members
          </v-stepper-step>
          <v-divider></v-divider>
          <v-stepper-step :complete="step > 4" step="4">
            Remarks
          </v-stepper-step>
          <v-divider></v-divider>
          <v-stepper-step :complete="step > 5" step="5"> Done </v-stepper-step>
          <v-btn
            class="ma-3 pa-0"
            outlined
            fab
            text
            x-small
            @click="closeDialog"
          >
            <svg
              width="50px"
              height="50px"
              viewBox="0 0 700 560"
              fill="currentColor'"
              style="transform: scaleY(-1)"
            >
              <g>
                <path
                  d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
                />
                <path
                  d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
                />
              </g>
            </svg>
          </v-btn>
        </v-stepper-header>
        <v-form @submit.prevent="submit" ref="form" v-model="valid">
          <v-stepper-items>
            <v-stepper-content step="1">
              <div class="mb-15 pa-5 overflow-y-auto" style="height: 300px">
                <v-row>
                  <v-col cols="12">
                    <label for="">Country *</label>
                    <v-autocomplete
                      ref="country"
                      v-model="country_id"
                      :rules="[
                        () => !!country_id || 'country field is required.',
                      ]"
                      :items="countries"
                      item-text="name"
                      item-value="id"
                      outlined
                      chips
                      deletable-chips
                      dense
                      solo
                      :loading="load"
                      label="Choose Country"
                      @change="getCompany('search')"
                      required
                    >
                    </v-autocomplete>
                  </v-col>
                  <v-col cols="6">
                    <br />
                    <label for="">Company *</label>
                    <v-autocomplete
                      v-model="company_id"
                      :rules="[
                        () => !!company_id || 'Company field is required',
                      ]"
                      :items="companies"
                      item-text="company_name"
                      item-value="id"
                      @change="getDepartment('search')"
                      outlined
                      chips
                      deletable-chips
                      dense
                      solo
                      label="Choose Company"
                      required
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="6">
                    <br />
                    <label for="">Department *</label>
                    <v-autocomplete
                      ref="department"
                      v-model="department_id"
                      :rules="[
                        () => !!department_id || 'Department field is required',
                      ]"
                      :items="departments"
                      item-text="department_name"
                      item-value="id"
                      outlined
                      chips
                      deletable-chips
                      dense
                      solo
                      label="Choose Department"
                      required
                    ></v-autocomplete>
                  </v-col>
                </v-row>
              </div>
              <v-card>
                <v-btn color="primary" @click="step = 2" style="float: right">
                  Next <i class="fa-solid fa-angles-right"></i
                ></v-btn>
              </v-card>
            </v-stepper-content>
            <v-stepper-content step="2">
              <div class="mb-15 pa-5 overflow-y-auto" style="height: 300px">
                <v-row>
                  <v-col cols="2">
                    <v-avatar size="120" style="background-color: lightgrey">
                      <span
                        ><i
                          class="fa fa-user"
                          style="font-size: 70px; color: white"
                        ></i
                      ></span>
                    </v-avatar>
                    <h4 class="ml-2">Select a logo!</h4>
                  </v-col>
                  <v-col cols="10">
                    <template>
                      <v-file-input
                        v-model="logo"
                        filled
                        item-value="id"
                        :items="logos"
                        label="File input"
                        :rules="[() => !!logo || 'select a logo !']"
                        required
                        clearable
                        class="dropzone form-control"
                        style="border: 1px dashed; width: 100%"
                      >
                      </v-file-input>
                    </template>
                  </v-col>
                  ‌‌‌</v-row
                >
                <v-row>
                  <v-col cols="12">
                    <label for="">Team *</label>
                    <v-text-field
                      v-model="team_name"
                      placeholder="write team name"
                      outlined
                      dense
                      required
                      :rules="[() => !!team_name || 'Team field is required']"
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
              <div class="mb-15 pa-5 overflow-y-auto" style="height: 300px">
                <v-col cols="12">
                  <label for="">Member *</label>
                  <v-autocomplete
                    v-model="user_id"
                    :items="members"
                    item-text="first_name"
                    item-value="id"
                    :rules="[
                      () => !!user_id.length > 0 || 'Member field is required',
                    ]"
                    outlined
                    chips
                    deletable-chips
                    multiple
                    dense
                    solo
                    label="Choose Member"
                    required
                  ></v-autocomplete>
                </v-col>
              </div>
              <v-btn @click="step = 2">
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
                  <v-col cols="12">
                    <template>
                      <v-container fluid>
                        <v-textarea
                          v-model="note"
                          :rules="[() => !!note || 'Remark field is required']"
                          required
                          autocomplete="email"
                          label="Remarks"
                        ></v-textarea>
                      </v-container>
                    </template>
                  </v-col>
                </v-row>
              </div>
              <v-btn @click="step = 3">
                <i class="fa-solid fa-angles-left"></i>
                Prev
              </v-btn>
              <v-btn
                color="primary"
                :disabled="!valid"
                class="float-right"
                @click="step = 5"
              >
                Next <i class="fa-solid fa-angles-right"></i>
              </v-btn>
            </v-stepper-content>
            <v-stepper-content step="5">
              <div class="mb-15 pa-5" style="height: 300px">
                <v-row>
                  <v-col cols="4"></v-col>
                  <v-col cols="4">
                    <v-row>
                      <v-col cols="2"></v-col>
                      <v-col cols="6">
                        <img
                          class="ma-50 pa-auto"
                          src="done-icon.svg"
                          justify="center"
                          width="200px"
                          height="200px"
                        />
                      </v-col>
                      <v-col cols="4"></v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="4"></v-col>
                </v-row>
              </div>
              <v-btn @click="step = 4">
                <i class="fa-solid fa-angles-left"></i>
                Prev
              </v-btn>
              <v-btn
                color="primary"
                class="float-right"
                type="submit"
                :loading="load"
                text
                outlined
              >
                Submit
              </v-btn>
            </v-stepper-content>
          </v-stepper-items>
        </v-form>
      </v-stepper>
    </v-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dialog: false,
      countries: [],
      companies: [],
      departments: [],
      logos: [],
      members: [],
      step: 1,
      load: false,
      valid: false,
      company_id: "",
      country_id: "",
      logo: "",
      department_id: "",
      user_id: [],
      team_name: "",
      note: "",
    };
  },
  methods: {
    async openDialog() {
      this.dialog = true;
      const country = await this.$axios.get("country");
      console.log(country);
      this.countries = country.data;
    },
    async getCompany(type) {
      this.load = true;
      let search = {
        country_id: this.country_id,
        type: type,
      };
      const company = await this.$axios.get("company", { params: search });
      this.load = false;
      this.companies = company.data;
    },
    async getDepartment(type) {
      this.load = true;
      let search = {
        company_id: this.company_id,
        type: type,
      };
      const department = await this.$axios.get("department", {
        params: search,
      });
      this.departments = department.data;
      this.load = false;
      // member
      this.load = true;
      let Memsearch = {
        company_id: this.company_id,
        type: type,
      };
      const member = await this.$axios.get("user", { params: Memsearch });
      console.log(member);
      this.members = member.data;
      this.load = false;
    },
    async submit() {
      let form = new FormData();
      form.append("team_name", this.team_name);
      form.append("department_id", this.department_id);
      form.append("user_id", this.user_id);
      form.append("logo", this.logo);
      form.append("note", this.note);

      this.load = true;
      const result = await this.$axios.post("team", form);
      console.log(result);
      this.$emit("someEvent");
      this.reset();
      this.load = false;
      this.dialog = false;
      (this.step = 1), this.$refs.form.resetValidation();
      this.$refs.form.reset();
    },
    closeDialog() {
      (this.dialog = false), (this.step = 1), this.reset();
      this.$refs.form.resetValidation();
      this.$refs.form.reset();
    },
    reset() {
      (this.team_name = ""),
        (this.company_id = ""),
        (this.country_id = ""),
        (this.department_id = ""),
        (this.user_id = []),
        (this.note = ""),
        (this.logo = "");
    },
  },
};
</script>

<style>
</style>