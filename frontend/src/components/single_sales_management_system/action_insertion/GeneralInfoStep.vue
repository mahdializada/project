<template>
  <div>
    <div class="h-full d-flex align-center-justify-center">
      <div class="w-full">
        <CAutoComplete
          v-model="form.dependable_id.$model"
          :title="$tr('label_required', $tr(isTypeSelected()))"
          :rules="
            validateRule(
              form.dependable_id, // validate objec
              $root, // context
              isTypeSelected() // lable for feedback
            )
          "
          :items="ispp_or_ipa"
          item-value="id"
          item-text="code"
          :placeholder="$tr('choose_item', $tr(isTypeSelected()))"
          :invalid="form.dependable_id.$invalid"
          :loading="is_fetching_related_type"
          prepend-inner-icon="#"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CTextArea
          v-model.trim="goal"
          :items="form.goals.$model"
          :title="$tr('label_required', $tr('goals'))"
          :placeholder="$tr('goals')"
          prepend-inner-icon="mdi-bullseye-arrow"
          :rules="
            validateRule(
              form.goals, // validate objec
              $root, // context
              $tr('goals') // lable for feedback
            )
          "
          :invalid="form.goals.$invalid"
          :hasItems="true"
          @add="addItem"
          @removeItem="removeItem"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CAutoComplete
          @blur="form.priority.$touch()"
          v-model="form.priority.$model"
          :title="$tr('label_required', $tr('priority'))"
          :rules="
            validateRule(
              form.priority, // validate objec
              $root, // context
              $tr('priority') // lable for feedback
            )
          "
          :items="priorities"
          item-value="id"
          item-text="name"
          :placeholder="$tr('choose_item', $tr('priority'))"
          :invalid="form.priority.$invalid"
          prepend-inner-icon="fa fa-signal mdi-rotate-270 mdi-flip-h"
        />
      </div>
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import CTextArea from "../../new_form_components/Inputs/CTextArea.vue";
export default {
  name: "GeneralInfoStep",
  components: { CAutoComplete, CTextArea },
  props: {
    form: Object, // default prop
  },
  data() {
    return {
      ispp_or_ipa: [],
      goal: "",
      is_fetching_related_type: false,

      validateRule: GlobalRules.validate, // gloabl function fro validate

      priorities: [
        { id: "very high", name: this.$tr("very_high") },
        { id: "high", name: this.$tr("high") },
        { id: "normal", name: this.$tr("normal") },
        { id: "low", name: this.$tr("low") },
      ],
    };
  },
  watch: {},
  methods: {
    async loaded() {
      this.fetchIsppOrIPA(this.form.$model.type);
    },
    async validate() {
      // validate function must validate this step here and return true of false
      this.form.dependable_id.$touch();
      this.form.goals.$touch();
      this.form.priority.$touch();
      return (
        !this.form.dependable_id.$invalid &&
        !this.form.goals.$invalid &&
        !this.form.priority.$invalid
      );
    },

    async fetchIsppOrIPA(type) {
      try {
        this.is_fetching_related_type = true;
        const url = `single-sales/${type}/get?all=true`;
        const { data } = await this.$axios.get(url);
        this.is_fetching_related_type = false;
        this.ispp_or_ipa = data;
      } catch (error) {
        console.log("error", error);
        this.is_fetching_related_type = false;
      }
    },
    isTypeSelected() {
      return this.form.$model.type ?? "ispp_or_ipa";
    },
    addItem() {
      if (this.goal?.length > 0) {
        this.form.$model.goals.push(this.goal);
        this.goal = "";
      }
    },
    removeItem(index) {
      this.form.$model.goals = this.form.$model.goals.filter(
        (item, i) => i !== index
      );
    },
  },
};
</script>
