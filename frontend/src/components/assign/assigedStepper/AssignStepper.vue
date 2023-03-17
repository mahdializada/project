<template>
  <v-container fluid class="pa-0">
    <client-only>
      <Stepper
        :title="title"
        cookieName="assign_stepper"
        @close="show = false"
        :show="show"
        :steps="steps"
        :form="form"
        :validateRules="validateRules"
        :submit="submit"
        @reset="reset"
      />
    </client-only>
  </v-container>
</template>

<script>
import Stepper from "~/components/horizontal_stepper/Stepper.vue";
import TeamStep from "./TeamStep.vue";
import UserStep from "./UserStep.vue";
import Validations from "~/validations/validations";

export default {
  props: {
    selected: {
      type: Array,
      require: true,
    },
    title: {
      type: String,
    },
    url: {
      type: String,
    }
  },
  components: {
    Stepper,
  },
  data() {
    return {
      show: false,
      steps: [
        {
          title: "team",
          skip: true,
          component: TeamStep,
          props: {},
          hints: [],
        },
        {
          title: "user",
          component: UserStep,
          props: {},
          hints: [],
        },
      ],
      teams: [],
      form: {
        team: [],
        user: [],
      },
      validateRules: {
        form: {
          team: Validations.requiredValidation,
          user: Validations.requiredValidation,
        },
      },
    };
  },
  methods: {
    reset(){
      this.form = {
            team: [],
            user: [],
          };
    },
    async submit(formRef, vuelidate) {
      try {
        const response = await this.$axios.post(
          this.url,
          {
            user_ids: this.form.user,
            assign_ids: this.selected.map((u) => u.id),
          }
        );
        if (response?.status === 201) {
          this.$emit("fetchNewData");
          return true;
        } else {
          return false;
        }
      } catch (error) {
        if (error.response?.status === 404) {
          // this.$toastr.e(this.$tr("record_not_found"));
				    this.$toasterNA("red", this.$tr("record_not_found"));

        } else {
				    this.$toasterNA("red", this.$tr("something_went_wrong"));

          // this.$toasterNA("red", this.$tr('something_went_wrong'));
        }
      }
    },
    showAssignDialog(){
      this.show = true;
    },
    async fetchTeams() {
      if (this.teams.length) {
        return;
      }
      try {
        const url = "teams?key=teams-only";
        const response = await this.$axios.get(url);
        this.teams = response?.data?.data;
      } catch (error) {}
    },
  },
};
</script>
