<template xmlns="http://www.w3.org/1999/html">
  <v-stepper-content step="1" class="pa-2">
    <v-row class="pt-3">

      <v-col cols="12" sm="6" class="py-0">
        <TextField
          :label="$tr('label_required',$tr('firstname'))"
          v-model.trim="user.firstname.$model"
          :rules="UserRules.firstnameRule(user, $root)"
          :invalid="user.firstname.$invalid"
          :maxLength="user.firstname.$params.maxLength.max"
        />
      </v-col>

      <v-col cols="12" sm="6" class="py-0">
        <TextField
          :label="$tr('label_required',$tr('lastname'))"
          v-model.trim="user.lastname.$model"
          :rules="UserRules.lastnameRule(user, $root)"
          :invalid="user.lastname.$invalid"
          :maxLength="user.lastname.$params.maxLength.max"
        />
      </v-col>


      <v-col cols="12" sm="6" class="py-0">
        <TextField
          :label="$tr('label_required',$tr('phone'))"
          v-model.trim="user.phone.$model"
          :rules="UserRules.phoneRule(user, $root)"
          :invalid="user.phone.$invalid"
        />
      </v-col>

      <v-col cols="12" sm="6" class="py-0">
        <TextField
          :label="$tr('label_required',$tr('whatsapp'))"
          v-model.trim="user.whatsapp.$model"
          :rules="UserRules.whatsappRule(user, $root)"
          :invalid="user.whatsapp.$invalid"
        />
      </v-col>
      <v-col cols="12" sm="6" class="py-0">
        <TextField
          :label="$tr('label_required',$tr('email'))"
          type="email"
          v-model.trim="user.email.$model"
          :rules="UserRules.emailRule(user, $root)"
          :invalid="user.email.$invalid"
        />
      </v-col>
      <v-col cols="12" sm="6" class="py-0">
        <v-menu
          v-model="showBirthDatePicker"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <TextField
              v-model.trim="user.birth_date.$model"
              :rules="UserRules.birthDateRule(user, $root)"
              :invalid="user.birth_date.$invalid"
              :label="$tr('label_required',$tr('birthday_date'))"
              v-bind="attrs"
              v-on="on"
            />
          </template>
          <v-date-picker
            no-title
            v-model.trim="user.birth_date.$model"
            @input="showBirthDatePicker = false"
          ></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>

    <v-row class="mb-3 mt-n3">
      <v-col class="py-0" cols="12">
        <v-checkbox
          label="Automatically generate password"
          :input-value="1"
          v-model="user.generate_password.$model"
          hide-details
        />
      </v-col>

      <v-col cols="12" class="pb-0" v-if="!user.generate_password.$model">
        <v-row>
          <v-col cols="12" sm="6" class="py-0">
            <TextField
              :label="$tr('label_required',$tr('password'))"
              type="password"
              v-model.trim="user.password.$model"
              :rules="UserRules.passwordRule(user, $root)"
              :invalid="user.password.$invalid"
            />
          </v-col>
          <v-col cols="12" sm="6" class="py-0">
            <TextField
              :label="$tr('label_required',$tr('confirm_password'))"
              type="password"
              v-model.trim="user.confirm_password.$model"
              :rules="UserRules.password2Rule(user, $root)"
              :invalid="user.confirm_password.$invalid"/>
          </v-col>
        </v-row>
      </v-col>

      <v-col class="py-0 " :class=" user.generate_password.$model ? '' : 'mt-n3'" cols="12">
        <v-checkbox
          label="Require user to change their password at first sign in"
          :input-value="1"
          v-model="user.change_password.$model"
          hide-details
        />
      </v-col>

      <v-col class="py-0" cols="12">
        <v-checkbox
          :label="$tr('label_required',$tr('email_the_password'))"
          :input-value="1"
          v-model="user.email_password.$model"
          hide-details
        />
      </v-col>
    </v-row>


    <!--  Step 1 Action Buttons   -->
    <StepperActionButtons
      @onNext="()=> changeStepper(2)"
      :disable-back="true"
    />
  </v-stepper-content>
</template>

<script>
import StepperActionButtons from "../stepper/StepperActionButtons";
import TextField from "../common/TextField";
import {UserRules} from "../../validations/rules/user-rules";


export default {
  name: "StepperOne",
  components: {TextField, StepperActionButtons},
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      UserRules: UserRules,
      showBirthDatePicker: false,
    }
  },
  methods: {

    isStepOneDataValid() {
      let isFullNameInValid = this.user.firstname.$invalid || this.user.lastname.$invalid
      let isPhoneAndWhatsAppInValid = this.user.phone.$invalid || this.user.whatsapp.$invalid
      let isEmailAndBirthDateInvalid = this.user.email.$invalid || this.user.birth_date.$invalid
      let isPasswordInValid = false

      if (!this.user.generate_password.$model) {
        isPasswordInValid = this.user.password.$invalid || this.user.confirm_password.$invalid
      }

      return isFullNameInValid === false
        && isPhoneAndWhatsAppInValid === false
        && isEmailAndBirthDateInvalid === false
        && isPasswordInValid === false
    },

    changeStepper(step) {
      this.$emit("validateForm");
      if (this.isStepOneDataValid()) {
        this.$emit("onChangeStepper", step)
        return
      }
      // this.$toastr.e(this.$tr('please_fill_all_fields_correctly'))
			this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

    }
  }
}
</script>

<style scoped>

</style>
