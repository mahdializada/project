<template>
  <v-stepper-content step="3">
    <v-row class="pt-3">
      <v-col cols="12" sm="6" class="py-0">
        <AutoComplete
          :items="items"
          item-text="name"
          item-value="id"
          v-model="user.department_id.$model"
          :rules="UserRules.departmentRule(user, $root)"
          :label="$tr('label_required',$tr('department'))"
        />
      </v-col>

      <v-col cols="12" sm="6" class="py-0">
        <AutoComplete
          :items="items"
          item-text="name"
          item-value="id"
          v-model="user.role_id.$model"
          :rules="UserRules.roleRule(user, $root)"
          :label="$tr('label_required',$tr('role'))"
        />
      </v-col>
    </v-row>
    <h3> Permissions </h3>
    <v-row class="mb-3">
      <v-col class="py-0">
        <v-checkbox
          :label="$tr('label_required',$tr('item_name',$tr('permission')))"
          v-model="user.permissions.$model"
          value="1"
          hide-details
        ></v-checkbox>
      </v-col>

      <v-col class="py-0">
        <v-checkbox
          :label="$tr('label_required',$tr('item_name',$tr('permission')))"
          v-model="user.permissions.$model"
          value="1"
          hide-details
        ></v-checkbox>
      </v-col>

      <v-col class="py-0">
        <v-checkbox
          :label="$tr('label_required',$tr('item_name',$tr('permission')))"
          v-model="user.permissions.$model"
          value="1"
          hide-details
        ></v-checkbox>
      </v-col>

      <v-col class="py-0">
        <v-checkbox
          :label="$tr('label_required',$tr('item_name',$tr('permission')))"
          v-model="user.permissions.$model"
          value="1"
          hide-details
        ></v-checkbox>
      </v-col>

      <v-col class="py-0">
        <v-checkbox
          :label="$tr('label_required',$tr('item_name',$tr('permission')))"
          v-model="user.permissions.$model"
          value="1"
          hide-details
        ></v-checkbox>
      </v-col>

      <v-col class="py-0">
        <v-checkbox
          :label="$tr('label_required',$tr('item_name',$tr('permission')))"
          v-model="user.permissions.$model"
          value="1"
          hide-details
        ></v-checkbox>
      </v-col>

    </v-row>

    <StepperActionButtons
      :disable-next="true"
      :show-submit="true"
      :is-submitting="isSaving"
      @onSubmit="onSubmit"
      @onBack="()=>goesToPreviousStepper(2)"
    />
  </v-stepper-content>
</template>

<script>
import AutoComplete from "../common/AutoComplete";
import StepperActionButtons from "../stepper/StepperActionButtons";
import {UserRules} from "../../validations/rules/user-rules";
import Helpers from "../../helpers/helpers"
import {mapActions} from "vuex";

export default {
  name: "StepperThree",
  components: {StepperActionButtons, AutoComplete},
  props: {
    user: {
      type: Object,
      required: true,
    },
    actualUser: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      UserRules: UserRules,
      isSaving: false,
      items: [
        {
          id: 1,
          name: "fake name (Just For Now)"
        }
      ]
    }
  },

  methods: {

    ...mapActions({
      insertUser: "users/insertUser",
    }),

    async onSubmit() {
      this.$emit("validateForm");
      this.user.$touch();
      if (!this.user.$invalid) {
        this.isSaving = true;
        const formData = Helpers.getFormData(this.actualUser)
        await this.insertUserRecord(formData);
        this.isSaving = false;
      }
    },

    async insertUserRecord(user) {
      try {
        const response = await this.$axios.post("/users", user);
        if (response?.status === 201 && response?.data?.result) {
          // this.$toasterNA("green", this.$tr('successfully_updated'));
				this.$toasterNA("green", this.$tr('successfully_updated'));

          this.insertUser(response?.data?.data)
          this.$emit("resetForm");
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'))
			this.$toasterNA("red",this.$tr("something_went_wrong"));

        }
      } catch (error) {
        // this.$toasterNA("red", this.$tr('something_went_wrong'))
			this.$toasterNA("red",this.$tr("something_went_wrong"));
        
      }
    },


    goesToPreviousStepper(step) {
      this.$emit("onChangeStepper", step)
    },
  }
}
</script>

<style scoped>

</style>
