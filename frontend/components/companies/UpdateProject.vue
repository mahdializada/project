

<template>
  <v-card width="900">
    <!--  Udpate Company Header  -->
    <v-card-title primary-title>
      <span>Update Project</span>
      <v-spacer />
      <v-btn icon color="blue-grey lighten-2" @click="closeDialog" outlined>
        <v-icon color="error">mdi-close</v-icon>
      </v-btn>
    </v-card-title>

    <!--  Update Company Body  -->
    <v-card-text>
      <v-form ref="projectForm" lazy-validation>
        <div v-for="(project, index) in $v.projects.$each.$iter" :key="index">
          <v-row class="pt-3">
            <v-col cols="12" sm="6" class="py-0">
              <TextField
                :label="$tr('project')"
                v-model.trim="project.name.$model"
                :rules="nameRules(project)"
              />
            </v-col>
            <v-col cols="12 py-0" sm="6" class="py-0">
              <v-select
                :label="$tr('label_required',$tr('business'))"
                :items="businessTypes"
                item-text="name"
                item-value="id"
                v-model="project.business_type_id.$model"
                :rules="businessTypeRules(project)"
                required
                outlined
                dense
                clear-icon="mdi-close"
                clearable
              ></v-select>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-btn color="primary" @click="onSubmit"> Submit </v-btn>
            </v-col>
          </v-row>
        </div>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import TextField from "../common/TextField";
import { mapGetters, mapActions } from "vuex";
import { required, minLength, maxLength } from "vuelidate/lib/validators";
export default {
  components: {
    TextField,
  },
  props: ["editData"],
  async beforeMount() {
    this.editData.forEach((element) => {
      const newField = {
        id: element.id,
        name: element.name,
        business_type_id: element.business_type_id,
      };
      this.projects.push(newField);
    });
    try {
      await this.getBusinessTypes('business_types');
    } catch (error) {
    }
  },
  data() {
    return {
      projects: [],
    };
  },
  validations: {
    projects: {
      $each: {
        id: {},
        name: {
          required,
          minLength: minLength(2),
          maxLength: maxLength(100),
        },
        business_type_id: {
          required,
        },
      },
    },
  },
  computed: {
    ...mapGetters({
      businessTypes: "businessTypes/items",
    }),
  },
  methods: {
    ...mapActions({
      getBusinessTypes: "businessTypes/fetchItems",
      UpdateProjectsStore: "projects/updateProjects",
    }),
    closeDialog() {
      this.$emit("close");
    },
    async onSubmit() {
      // validate form inputs
      this.$refs.projectForm.validate();
      this.$v.projects.$touch();
      if (!this.$v.projects.$invalid) {
        await this.updateProjects(this.projects);
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
			this.$toasterNA("red",this.$tr("please_fill_all_fields_correctly"));

      }
    },
    async updateProjects(projects) {
      /* ==========> insert new Food into DB  <========*/
      try {
        this.showProgressBar = true;
        const response = await this.$axios.put(
          "projects/" + this.editData[0].id,
          projects
        );
        this.showProgressBar = false;
        this.assignData(response);
      } catch (error) {
        this.showProgressBar = false;
        if (error.response.status == 422) {
          // return server errors
          const errors = error.response.data.errors;
          for (let error in errors) {
            for (let er in errors[error]) {
              // this.$toastr.e(errors[error][er]);
			        this.$toasterNA("red",errors[error][er]);

            }
          }
        }
        //  else if (error.response.status === 401) {
        //   await this.$router.push('/admin/login')
        //   this.$toastr.e(error.response.statusText)
        // }
        else {
          // this.$toastr.e(error.response.statusText);
          this.$toasterNA("red",error.response.statusText);

        }
      }
    },
    assignData(response) {
      if (response.status === 202) {
        const data = response.data.data;
        this.UpdateProjectsStore(data);
        this.resetForm();
        this.closeDialog();
        this.$toasterNA("green", this.$tr('successfully_updated'));
      }
    },
    resetForm() {
      this.$refs.projectForm.reset();
      this.$v.projects.$reset();
      this.projects = []
    },
    // validateForm() {
    //   this.$refs.projectForm.validate();
    //   this.$v.project.$touch();
    // },
    nameRules(project) {
      return [
        (_) =>
          project.name.required ||
          this.$tr("required",this.$tr("project")),
        (_) =>
          project.name.minLength ||
          this.$tr("min_length", 
            this.$tr("project"),this.$tr("2")
          ),
        (_) =>
          project.name.maxLength ||
          this.$tr("max_length", [
            this.$tr("project",this.$tr("project"),this.$tr("10")),
          ]),
      ];
    },
    businessTypeRules(project) {
      return [
        (_) =>
          project.business_type_id.required ||
          this.$tr("required", [
            this.$tr("business"),
          ]),
      ];
    },
  },
};
</script>

<style scoped>
.show-cursor {
  cursor: pointer;
}
</style>
