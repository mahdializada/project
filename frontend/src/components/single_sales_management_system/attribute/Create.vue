<template>
  <v-form @submit.prevent="submitHandler" ref="attrRef">
    <v-card>
      <v-card-title class="grey lighten-4"> Create Attribute </v-card-title>
      <v-card-text
        class="overflow-auto px-4 py-3"
        style="max-height: 50vh !important"
      >
        <CustomInput
          type="textfield"
          :label="$tr('label_required', $tr('name'))"
          v-model="form.name"
          :error-messages="nameErrMsg"
        />
        <div>
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                v-model="selectedAttr"
                :label="$tr('label_required', $tr('attributes'))"
                type="textfield"
                :error-messages="attributeErrMsg"
                class="me-md-4"
              />
            </div>
            <div class="w-100">
              <FormBtn
                @click="addselectedAttr"
                :title="'add_plus'"
                :class="`mt-md-4`"
              />
            </div>
          </div>
          <div class="selected d-flex flex-wrap">
            <SelectedItem
              v-for="(selectedAttr, i) in selectedAttrs"
              :key="i"
              @close="removeselectedAttr(selectedAttr)"
              :title="selectedAttr"
            />
          </div>
        </div>
      </v-card-text>
      <div class="grey lighten-4 rounded-0 py-2 d-flex justify-end">
        <v-btn :loading="isLoading" class="me-3" type="submit" color="primary"
          >{{ $tr('submit') }}</v-btn
        >
      </div>
    </v-card>
  </v-form>
</template>
<script>
import FormBtn from "../../design/components/FormBtn.vue";
import CustomInput from "../../design/components/CustomInput.vue";
import SelectedItem from "../../design/components/SelectedItem";
import Helpers from "../../../helpers/helpers";
import handleException from "../../../helpers/handle-exception";

export default {
  name: "create-attribute-page",
  components: { CustomInput, SelectedItem, FormBtn },
  data() {
    return {
      attributeErrMsg: "",
      nameErrMsg: "",
      selectedAttrs: [],
      form: {
        name: null,
      },
      selectedAttr: null,
      isLoading: false,
    };
  },
  methods: {
 
    validateForm() {
      let isValid = false;
      if (this.form.name && this.selectedAttrs.length) {
        isValid = true;
      }
      if (this.form.name == null) {
        this.nameErrMsg = this.$tr("item_required", this.$tr("name"));
        isValid = false;
      }
      if (this.selectedAttrs.length == 0) {
        this.attributeErrMsg = this.$tr("item_required", this.$tr("attribute"));
        isValid = false;
      }
      return isValid;
    }, 
    async submitHandler() {
      if (this.validateForm()) {
        try {
          this.isLoading = true;
          const formData = Helpers.getFormData({
            ...this.form,
            values: JSON.stringify(this.selectedAttrs),
          });
          const response = await this.$axios.post(
            "single-sales/attribute-ssp",
            formData
          ); 
          if(response?.status === 200 ){
            this.$toasterNA("green", this.$tr('successfully_inserted'));
              const datas = response.data;  
              this.$emit("pushRecord", datas); 
            this.clearForm();  
          } 
          this.isLoading = false;
        } catch (error) {
          this.isLoading = false;
          handleException.handleApiException(this, error);
        }
      }
    },
    clearForm() {
      this.form.name = null;
      this.selectedAttr = null;
      this.selectedAttrs = [];
      this.nameErrMsg = "";
      this.attributeErrMsg = "";
    },
    removeselectedAttr(name) {
      this.selectedAttrs = this.selectedAttrs.filter(
        (s_name) => s_name != name
      );
    },
    addselectedAttr() {
      if (
        this.selectedAttr &&
        !this.selectedAttrs.includes(this.selectedAttr)
      ) {
        this.selectedAttrs.push(this.selectedAttr);
        this.attributeErrMsg = "";
        this.selectedAttr = null;
      }
    },
  },
  watch: {
    "form.name": {
      handler: function (value) {
        if (value) {
          this.nameErrMsg = "";
        }
      },
      deep: true,
    },
  },
};
</script>
