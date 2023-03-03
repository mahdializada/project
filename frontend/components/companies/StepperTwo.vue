<template>
  <v-stepper-content step="2">
    <v-row class="pt-1">
      <v-col cols="12" class="py-0 mb-2 d-flex justify-center">
        <input
          class="d-none"
          type="file"
          ref="profileImageInput"
          accept="image/png, image/jpeg, image/jpg"
          @change="choiceProfileImage"
        />
        <v-img
          :src=" profileImageUrl ? profileImageUrl : '/images/avatars/avatar1.svg'"
          aspect-ratio="1"
          class="blue-grey lighten-4 rounded elevation-3  show-cursor  "
          @click="$refs.profileImageInput.click()"
          max-width="100"
          max-height="100"
        />

      </v-col>


      <v-col cols="12" sm="6" class="py-0">
        <AutoComplete
          :items="items"
          item-text="name"
          item-value="id"
          :label="$tr('label_required',$tr('country'))"
          v-model="user.country_id.$model"
          :rules="UserRules.countryRule(user, $root)"
        />
      </v-col>

      <v-col cols="12" sm="6" class="py-0">
        <AutoComplete
          :items="items"
          item-text="name"
          item-value="id"
          :label="$tr('label_required',$tr('state'))"
          v-model="user.state_id.$model"
          :rules="UserRules.countryStateRule(user, $root)"
        />
      </v-col>


      <v-col cols="12" sm="6" class="py-0">
        <AutoComplete
          :items="items"
          item-text="name"
          item-value="id"
          :label="$tr('label_required',$tr('current_country'))"
          v-model="user.current_country_id.$model"
          :rules="UserRules.currentCountryRule(user, $root)"
        />
      </v-col>


      <v-col cols="12" sm="6" class="py-0">
        <AutoComplete
          :items="items"
          item-text="name"
          item-value="id"
          :label="$tr('label_required',$tr('nationality'))"
          v-model="user.national_id.$model"
          :rules="UserRules.nationalRule(user, $root)"
        />

      </v-col>


      <v-col cols="12" sm="6" class="py-0">
        <AutoComplete
          :items="items"
          item-text="name"
          item-value="id"
          :label="$tr('label_required',$tr('team'))"
          v-model="user.team_id.$model"
          :rules="UserRules.teamRule(user, $root)"
        />

      </v-col>

      <v-col cols="12" sm="6" class="py-0">
        <AutoComplete
          :items="items"
          :label="$tr('label_required',$tr('project'))"
          item-text="name"
          item-value="id"
          v-model="user.project_id.$model"
          :rules="UserRules.projectRule(user, $root)"
        />
      </v-col>


      <v-col cols="12" class="py-0">
        <v-textarea
          no-resize
          outlined
          :label="$tr('label_required',$tr('address'))"
          rows="1"
          row-height="15"
          v-model="user.address.$model"
          :rules="UserRules.addressRule(user, $root)"
        ></v-textarea>
      </v-col>

      <v-col class="py-0" cols="12">
        <div class="v-label">{{ $tr('label_required',$tr('gender')) }}</div>
        <v-radio-group row :rules="UserRules.genderRule(user, $root)" v-model="user.gender.$model">
          <v-radio label="male" :value="1"/>
          <v-radio label="female" :value="0"/>
        </v-radio-group>
      </v-col>


    </v-row>

    <StepperActionButtons
      @onNext="()=> changeStepper(3)"
      @onBack="()=>goesToPreviousStepper(1)"
    />
  </v-stepper-content>
</template>

<script>
import StepperActionButtons from "../stepper/StepperActionButtons";
import AutoComplete from "../common/AutoComplete";
import {UserRules} from "../../validations/rules/user-rules";

export default {
  name: "StepperTwo",
  components: {AutoComplete, StepperActionButtons},
  props: {
    user: {
      type: Object,
      required: true,
    },
  },


  data() {
    return {
      UserRules: UserRules,
      profileImageUrl: "",
      items: [
        {
          id: 1,
          name: "fake name (Just For Now)"
        }
      ]
    }
  },

  methods: {
    choiceProfileImage(event) {
      event.preventDefault()
      const choiceImage = event.target.files[0]
      const fileExtension = choiceImage.type
      const allowedExtensions = ['image/jpeg', 'image/jpg', 'image/png']
      if (!allowedExtensions.includes(fileExtension)) {
        return
      }
      this.user.image.$model = choiceImage
      this.profileImageUrl = URL.createObjectURL(choiceImage)
    },

    isStepTwoDataValid() {
      let isCountryAndStateInValid = this.user.country_id.$invalid || this.user.state_id.$invalid
      let isCurrentCountryAndNationalInValid = this.user.current_country_id.$invalid || this.user.national_id.$invalid
      let isProjectAndTeamInValid = this.user.team_id.$invalid || this.user.project_id.$invalid
      let isGenderAndAddressInValid = this.user.gender.$invalid || this.user.address.$invalid
      let isProfilePictureInValid = this.user.image.$invalid


      return isCountryAndStateInValid === false
        && isCurrentCountryAndNationalInValid === false
        && isProjectAndTeamInValid === false
        && isGenderAndAddressInValid === false && isProfilePictureInValid === false
    },

    goesToPreviousStepper(step){
      this.$emit("onChangeStepper", step)

    },

    changeStepper(step) {
      this.$emit("validateForm");
      if (this.isStepTwoDataValid()) {
        this.$emit("onChangeStepper", step)
      } else if (this.user.image.$invalid) {
        // this.$toastr.e(this.$tr('upload_user_profile'))
			this.$toasterNA("red",this.$tr("upload_user_profile"));

      } else {
        // this.$toastr.e(this.$tr('please_fill_all_fields_correctly'))
			this.$toasterNA("red",this.$tr("please_fill_all_fields_correctly"));

      }
    }
  }
}
</script>

<style scoped>

</style>
