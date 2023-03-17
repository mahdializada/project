<template>
  <div class="w-full h-full d-flex justify-center pt-5">
    <div class="w-full h-full">
      <div class="mb-3">
        <CSelectMulti
          title="Choose attributes"
          placeholder="Attribute"
          :items="attributes"
          :selectedItems="form.attribute.$model"
          v-model="selectModel"
          item-text="name"
          item-value="name"
          multi
          hasItems
          @add="addItems"
          @removeItem="removeItem"
          return-object
          prepend-inner-icon="mdi-bookmark"
          :loading="attributeLoading"
          @blur="form.attribute.$touch()"
          :invalid="form.attribute.$invalid"
          :rules="
            validateRule(
              form.attribute, // validate objec
              $root, // context
              $tr('attribute') // lable for feedback
            )
          "
        />
      </div>
      <!-- <div class="mb-3">
        <InputCard
          :title="$tr('label_required',$tr('attribute'))"
          :sub_title="'You can add one or more attribute for your product.'"
        >
          <div class="d-flex flex-wrap py-2">
            <v-card
              v-for="(attr, index) in form.newAttribute.$each.$iter"
              :key="index"
              class="add-new-card rounded-lg overflow-y-auto mx-1 pa-3"
              outlined
              width="300"
              height="400"
            >
              <div class="d-flex justify-end">
                <v-icon @click="removePlatform(index)" color="red darken-2"
                  >mdi-close</v-icon
                >
              </div>
              <div class="mb-2">Attribute {{ index }}</div>
              <div>
                <CTextField
                  small
                  px="px-0"
                  py="py-0"
                  dense
                  title="Attribute Name"
                  placeholder="Name"
                  v-model="attr.name.$model"
                  :rules="
                    validateRule(
                      attr.name, // validate objec
                      $root, // context
                      $tr('name') // lable for feedback
                    )
                  "
                  :invalid="attr.name.$invalid"
                />
              </div>
              <div>
                <CTextField
                  v-model.trim="attValue[index]"
                  small
                  px="px-0"
                  py="py-0"
                  dense
                  title="Attribute Value"
                  placeholder="Value"
                  :hasItems="true"
                  @add="addValue(attr.value.$model, index)"
                  @removeItem="removeItem2"
                  :items="attr.value.$model"
                />
              </div>
            </v-card>
            <AddNewCard @addNewPlatform="addNewPlatform" />
          </div>
        </InputCard>
      </div> -->
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CSelectMulti from "../../../new_form_components/Inputs/CSelectMulti.vue";
import InputCard from "../../../new_form_components/components/InputCard.vue";
import AddNewCard from "../../../single_sales_management_system/ipa_component/AddNewCard.vue";
import CTextField from "../../../new_form_components/Inputs/CTextField.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      errorMessage: "",
      attributes: [],

      validateRule: GlobalRules.validate,
      selectModel: [],
      selectedItems: [],
      attValue: [],
      attValues: [],
      attributeLoading: false,
    };
  },
  methods: {
    async loaded() {
      this.selectModel = [];
      this.attributeLoading = true;
      try {
        const url = "product-management/pr-attributes?fields=id,name&key=all";
        const { data } = await this.$axios.get(url);
        this.attributes = data.data;
        console.log(this.attributes[0].id);
        if(this.form.$model.editData){
          console.log(this.form.$model.editData);

          this.selectModel = this.form.$model.editData.attributes;
          this.addItems();
        }
      } catch (error) {
      }
      this.attributeLoading = false;
    },
    async validate() {
      this.form.attribute.$touch();

      // if (this.form.newAttribute.$model.length > 0) {
      //   this.form.attribute.$touch();
      //   this.form.newAttribute.$touch();
      //   return (
      //     !this.form.attribute.$invalid && !this.form.newAttribute.$invalid
      //   );
      // } else {
      //   this.form.attribute.$touch();
      //   return !this.form.attribute.$invalid;
      // }
      return !this.form.attribute.$invalid;
    },
    addItems() {
      for (let i = 0; i < this.selectModel.length; i++) {
        const el = this.selectModel[i];
        let index = this.form.attribute.$model.findIndex(
          (item) => item.id == el.id
        );
        if (index == -1) {
          this.form.attribute.$model.push(el);
        }
      }
      this.selectModel = [];
    },
    removeItem(item) {
      this.form.attribute.$model = this.form.attribute.$model.filter(
        (el) => el.id != item.id
      );
    },

    addValue(modelValue, index) {
      if (this.attValue[index]) modelValue.push(this.attValue[index]);
      this.attValue[index] = "";
      return;
      if (this.attribute.value.$model?.length > 0) {
        this.attribute.value.$model.push(this.attValue);
        this.attValue = "";
      }
    },
    removeItem2(index) {
      this.attribute.value.$model = this.attribute.value.$model.filter(
        (item, i) => i !== index
      );
    },
    addNewPlatform() {
      this.form.newAttribute.$model.push({
        name: "",
        value: [],
      });
    },
    removePlatform(index) {
      this.form.newAttribute.$model.splice(index, 1);
    },
  },
  components: { CSelectMulti, InputCard, AddNewCard, CTextField },
};
</script>
