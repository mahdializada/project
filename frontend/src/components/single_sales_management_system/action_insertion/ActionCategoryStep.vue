<template>
  <div>
    <div class="h-full d-flex align-center-justify-center">
      <div class="w-full">
        <CAutoComplete
          @blur="form.action_category.$touch()"
          v-model="form.action_category.$model"
          :title="$tr('label_required', $tr('action_category'))"
          :rules="
            validateRule(
              form.action_category, // validate objec
              $root, // context
              $tr('action_category') // lable for feedback
            )
          "
          :items="action_categories"
          item-value="id"
          item-text="name"
          :placeholder="$tr('choose_item', $tr('action_category'))"
          :invalid="form.action_category.$invalid"
          prepend-inner-icon="fa fa-th"
        />
      </div>
    </div>

    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CTextField
          v-model.trim="statement"
          :items="form.statements.$model"
          :title="$tr('label_required', $tr('statements'))"
          :placeholder="$tr('statements')"
          prepend-inner-icon="mdi-bullseye-arrow"
          :rules="
            validateRule(
              form.statements, // validate objec
              $root, // context
              $tr('statements') // lable for feedback
            )
          "
          :invalid="form.statements.$invalid"
          :hasItems="true"
          @add="addItem"
          @removeItem="removeItem"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CTextField
          v-if="this.form.$model.action_category == 'instruction'"
          v-model="form.value.$model"
          :title="$tr('label_required', $tr('value'))"
          :rules="
            validateRule(
              form.value, // validate objec
              $root, // context
              $tr('value') // lable for feedback
            )
          "
          :placeholder="$tr('value')"
          :invalid="form.value.$invalid"
          prepend-inner-icon="fa fa-atom"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CFileUpload
          v-if="this.form.$model.action_category == 'instruction'"
          v-model="form.attachment.$model"
          :rules="validateRule(form.attachment, $root, $tr('attachment'))"
          :attachments="attachments"
        />
      </div>
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import CFileUpload from "../../new_form_components/Inputs/CFileUpload.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
export default {
  name: "ActionCategoryStep",
  components: { CAutoComplete, CTextField, CFileUpload },
  props: {
    form: Object, // default prop
    attachments:{
      type:Array,
      default:function(){
        return [];
      }
    }
  },
  data() {
    return {
      validateRule: GlobalRules.validate, // gloabl function fro validate
      statement: "",
      attachmentPath: "",
      action_categories: [
        { id: "statement", name: this.$tr("statement") },
        { id: "instruction", name: this.$tr("instruction") },
      ],
    };
  },
  methods: {
    async loaded() {
      this.attachmentPath = "";
    },
    async validate() {
      // validate function must validate this step here and return true of false
      this.form.action_category.$touch();
      this.form.statements.$touch();
      this.form.value.$touch();
      if(this.attachments.length>0){
        this.form.attachment.$touch();
      }

      if (this.form.$model.action_category == "instruction") {
        if(this.attachments.length<=0)
        return (
          !this.form.action_category.$invalid &&
          !this.form.statements.$invalid &&
          !this.form.value.$invalid &&
          !this.form.attachment.$invalid
        );
        else{
          return (
              !this.form.action_category.$invalid &&
              !this.form.statements.$invalid &&
              !this.form.value.$invalid
          );
        }
      } else {
        return (
          !this.form.action_category.$invalid && !this.form.statements.$invalid
        );
      }
    },
    addItem() {
      if (this.statement?.length > 0) {
        this.form.$model.statements.push(this.statement);
        this.statement = "";
      }
    },
    removeItem(index) {
      this.form.$model.statements = this.form.$model.statements.filter(
        (item, i) => i !== index
      );
    },
  },
};
</script>
