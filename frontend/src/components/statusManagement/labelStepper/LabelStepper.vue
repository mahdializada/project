<template>
  <stepper
    ref="labelStepper"
    :title="$tr('label')"
    cookieName="label"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="labelRules"
    :submit="submit"
    @close="show = false"
    @reset="reset"
    :showBack="showBack"
  />
</template>

<script>
import { mapActions } from "vuex";
import LabelStep from "./LabelStep.vue";
import SubSystemStep from "./SubSystemStep.vue";
import Validations from "~/validations/validations";
import Helpers from "~/helpers/helpers";
import HandleException from "~/helpers/handle-exception";
import Stepper from "../../horizontal_stepper/Stepper.vue";
export default {
  components: { Stepper },
  props: {
    tabKey: String,
  },
  data() {
    return {
      show: false,
      showBack: true,
      isEdit: false,
      id: "",
      steps: [
        { 
          title: this.$tr("sub_system"),
          component: SubSystemStep,
          props: {},
          hints: [],
        },
        {
          title: this.$tr("label"),
          component: LabelStep,
          props: { isEdit: this.isEdit },
          hints: [],
        },
      ],
      isSubmitting: false,

      form: {
        sub_systems: [],
        tab: [],
        labels: [
          {
            id: this.generateID(),
            category: [],
            label_name: "",
            color: "#FF0000FF",
            title: "",
            status: false,
          },
        ],
        slug: this.$route.params.slug,
      },
      labelRules: {
        form: {
          sub_systems: Validations.requiredValidation,
          tab: Validations.requiredValidation,
          labels: {
            $each: {
              label_name: Validations.requiredValidation,
              color: Validations.requiredValidation,
              title: Validations.requiredValidation,
              status: Validations.requiredValidation,
            },
          },
        },
      },
    };
  },

  methods: {
    ...mapActions({
      fetchLabels: "labels/fetchLabels",
    }),
    reset() {
      (this.isEdit = false), (this.id = "");
      this.form = {
        sub_systems: [],
        tab: [],
        labels: [
          {
            id: "",
            category: [],
            label_name: "",
            color: "#FF0000FF",
            title: "",
            status: false,
          },
        ],
        slug: this.$route.params.slug,
      };
    },
    toggle(item = null) {
      if (item) {
        this.isEdit = true;
        this.id = item.id;
        this.form.tab = JSON.parse(item.tabs);
        this.form.labels[0].label_name = item.label;
        this.form.labels[0].title = item.title;
        this.form.labels[0].category = item.label_category;
        this.form.sub_systems = item.sub_systems.map((item) => {
          return item.id;
        });
        this.form.labels[0].color = item.color;
        this.form.labels[0].status = item.status == "archive" ? true : false;
      }
      this.steps[1].props.isEdit = this.isEdit;
      this.show = !this.show;
    },

    async submit(formRef, vuelidate) {
      try {
        let response = {};
        if (this.isEdit) {
          this.form.id = this.id;
          console.log(this.form);
          response = await this.$axios.put("labels/id", this.form);
        } else {
          response = await this.$axios.post("labels", this.form);
        }
        if (response?.status === 201 || response?.data?.result) {
          this.reset();
          this.fetchLabels({
            slug: this.$route.params.slug,
            key: this.tabKey,
          });
          return true;
        } else {
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    generateID() {
      return (
        "Id" +
        Math.floor(
          (Date.now() *
            Math.floor(
              Math.random() * Math.floor(Math.random() * Date.now())
            )) /
            (Math.random() * 1000000000)
        )
      );
    },
  },
};
</script>

<style></style>
