<template>
  <div class="connection__container">
    <div class="mb-3">
      <CTextField
        v-model="form.landing_page_link.$model"
        :title="$tr('label_required', $tr('landing_page_link'))"
        :placeholder="$tr('landing_page_link')"
        prepend-inner-icon="mdi-link-variant "
        :rules="
          validateRule(form.landing_page_link, $root, $tr('landing_page_link'))
        "
        :hasCustomBtn="true"
        :disabled="form.product_has_link.$model"
        btnIcon="mdi-content-paste "
        :invalid="form.landing_page_link.$invalid"
        @add="copyLinkFromClipboard"
      />
    </div>

    <div class="mb-3" v-if="checkQuickBy()">
      <CRadioWIthBody
        :items="quickByItems"
        @onChange="(v) => (form.quick_by.$model = v)"
        :text="this.$tr('Is the product has quick by?')"
        :value="form.quick_by.$model"
      >
      </CRadioWIthBody>
    </div>
    <div class="mb-3">
      <CRadioWIthBody
        :items="items"
        :text="this.$tr('connection_save_as_template')"
        @onChange="(v) => (form.save_as_template.$model = v)"
        :value="form.save_as_template.$model"
        :pbNone="form.save_as_template.$model == 'yes'"
      >
        <div v-if="form.save_as_template.$model == 'yes'">
          <div class="d-flex align-center">
            <p :class="`my-1 input-title`">
              {{ $tr("template_name") }}
            </p>
          </div>
          <v-text-field
            :class="`form-new-item form-custom-text-field`"
            background-color="var(--new-input-bg)"
            outlined
            :rounded="true"
            :placeholder="$tr('template_name')"
            v-model="form.template_name.$model"
            :rules="
              validateRule(form.template_name, $root, $tr('template_name'))
            "
            :invalid="form.template_name.$invalid"
          >
          </v-text-field>
        </div>
      </CRadioWIthBody>
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CRadioWIthBody from "../../new_form_components/Inputs/CRadioWIthBody.vue";
import HandleException from "~/helpers/handle-exception";
import CRadioButton from "../../new_form_components/Inputs/CRadioButton.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import { required, requiredIf } from "vuelidate/lib/validators";
import CCheckBoxWithBody from "../../new_form_components/Inputs/CCheckBoxWithBody.vue";

export default {
  props: {
    form: Object,
    data: Object,
  },
  data() {
    return {
      quickByItems: [
        {
          value: true,
          label: this.$tr("yes"),
        },
        {
          value: false,
          label: this.$tr("no"),
        },
      ],
      items: [
        {
          value: "yes",
          label: this.$tr("yes"),
        },
        {
          value: "no",
          label: this.$tr("no"),
        },
      ],
      validateRule: GlobalRules.validate,
      template: {
        save_as_template: "no",
        name: null,
      },
    };
  },

  validations: {
    template: {
      save_as_template: { required },
      name: {
        required: requiredIf(function (value) {
          return value.save_as_template == "yes";
        }),
      },
    },
  },

  methods: {
    checkQuickBy() {
      const res = this.data?.companies?.find((row) => {
        if (row.id == this.data.company_id && row.name == "UAE-Teebalhoor")
          return true;
      });
      if (res != undefined) {
        return true;
      }
      return false;
    },
    loaded() {
      this.checkQuickBy() ? (this.form.quick_by.$model = true) : "";
    },
    isURL(str) {
      var pattern = new RegExp(
        "^(https?:\\/\\/)?" + // protocol
          "((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|" + // domain name
          "((\\d{1,3}\\.){3}\\d{1,3}))" + // OR ip (v4) address
          "(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // port and path
          "(\\?[;&a-z\\d%_.~+=-]*)?" + // query string
          "(\\#[-a-z\\d_]*)?$",
        "i"
      ); // fragment locator
      return pattern.test(str);
    },
    async copyLinkFromClipboard() {
      try {
        this.$toasterNA("red", this.$tr("cant copy from clipboard"));

        return;
        const clipboard = navigator.clipboard;
        const link = await clipboard.readText();
        if (link && this.isURL(link)) {
          this.form.landing_page_link.$model = link;
        }
      } catch (error) {}
    },
    async validate() {
      try {
        this.form.landing_page_link.$touch();
        this.form.template_name.$touch();
        this.form.save_as_template.$touch();
        const isValid =
          !this.form.landing_page_link.$invalid &&
          !this.form.save_as_template.$invalid &&
          !this.form.template_name.$invalid;
        if (isValid) {
          let connection = this.data;
          delete connection.companies;
          console.log();
          const url = "advertisement/connection/generate_link";
          const { data } = await this.$axios.post(url, connection);
          if (data.result) {
            this.$toasterNA("green", this.$tr("successfully_inserted"));
            const insertedRecord = data.connection;
            // this.form.generated_link.$model = insertedRecord.generated_link;
            // this.form.connection_id.$model = insertedRecord.id;
            this.form.$model.ads[0].generated_link =
              insertedRecord.generated_link;
            this.form.$model.ads[0].connection_id = insertedRecord.id;
            return true;
          } else {
            // this.$toastr.e(this.$tr(data.message || "something_went_wrong"));
            this.$toasterNA(
              "red",
              this.$tr(data.message || "something_went_wrong")
            );
            return false;
          }
        }
        if (isValid && this.form.generated_link.$model) {
          return true;
        }
        return false;
      } catch (error) {
        console.log("error", error);
        HandleException.handleApiException(this, error);
      }
    },
  },
  components: {
    CRadioWIthBody,
    CRadioButton,
    CTextField,
    CCheckBoxWithBody,
  },
};
</script>

<style scoped lang="scss"></style>
