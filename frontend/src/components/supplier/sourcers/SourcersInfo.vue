<template>
  <div class="h-full mt-5">
    <CTitle :text="$tr('sourcers_info')" />
    <div class="w-full">
        <CTextField
          @blur="form.name.$touch()"
          v-model="form.name.$model"
          :title="$tr('name')"
          :placeholder="$tr('name')"
          prepend-inner-icon="fa fa-box"
          :rules="
            validateRule(
              form.name, // validate objec
              $root, // context
              $tr('name') // lable for feedback
            )
          "
          :invalid="form.name.$invalid"
        />
      </div>
    <div class="w-full mt-3">
        <CTextField
          @blur="form.last_name.$touch()"
          v-model="form.last_name.$model"
          :title="$tr('last_name')"
          :placeholder="$tr('last_name')"
          prepend-inner-icon="fa fa-box"
          :rules="
            validateRule(
              form.last_name, // validate objec
              $root, // context
              $tr('last_name') // lable for feedback
            )
          "
          :invalid="form.last_name.$invalid"
        />
      </div>
    <div class="w-full mt-3">
        <CTextField
          @blur="form.phone_number.$touch()"
          v-model="form.phone_number.$model"
          :title="$tr('phone_number')"
          :placeholder="$tr('phone_number')"
          prepend-inner-icon="fa fa-globe"
          :rules="
            validateRule(
              form.phone_number, // validate objec
              $root, // context
              $tr('phone_number') // lable for feedback
            )
          "
          :invalid="form.phone_number.$invalid"
        />
      </div>
    <div class="w-full mt-3">
        <CTextField
          @blur="form.email.$touch()"
          v-model="form.email.$model"
          :title="$tr('email')"
          :placeholder="$tr('email')"
          prepend-inner-icon="mdi-link-variant"
          :rules="
            validateRule(
              form.email, // validate objec
              $root, // context
              $tr('email') // lable for feedback
            )
          "
          :invalid="form.email.$invalid"
        />
      </div>
  </div>
</template>

<script>
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import CTextField from "~/components/new_form_components/Inputs/CTextField.vue";
import GlobalRules from "~/helpers/vuelidate";

export default {
    props: {
      form: Object,
    },
    components: { CTitle, CTextField, },
  data(){
    return{
        validateRule: GlobalRules.validate,
    }
  },
  methods:{
    async validate() {
        let isValid =
          !this.form.name.$invalid &&
          !this.form.last_name.$invalid &&
          !this.form.phone_number.$invalid &&
          !this.form.email.$invalid 
        return isValid;
      },
       loaded() {
      },
  },
};
</script>
