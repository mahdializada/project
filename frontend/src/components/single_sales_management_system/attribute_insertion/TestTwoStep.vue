<template>
  <div class="h-full d-flex align-center-justify-center mt-5">
    <div class="w-full">
      <CAutoComplete
        v-model="form.item_id.$model"
        :label="$tr('label_required', $tr('category'))"
        :rules="
          validateRule(
            form.item_id, // validate objec
            $root, // context
            $tr('category') // lable for feedback
          )
        "
        :items="countries"
        item-value="id"
        item-text="name"
        :placeholder="$tr('choose_item', $tr('category'))"
        :invalid="form.item_id.$invalid"
        prepend-inner-icon="fa fa-th"
        :loading="countryLoading"
      />
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      countryLoading: false,
      countries: ['Afghanistan','America','London'],
    };
  },
  methods: {
    async validate() {
      this.form.item_id.$touch();
      return !this.form.item_id.$invalid;
    },
    async loaded() {
      this.countryLoading = true;
      try {
        const { data } = await this.$axios.get("common/countries");
        // this.countries = data.countries;
      } catch (error) {}
      this.countryLoading = false;
    },
  },
  components: { CAutoComplete },
};
</script>
