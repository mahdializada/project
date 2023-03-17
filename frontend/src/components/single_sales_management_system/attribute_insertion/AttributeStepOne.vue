<template>
  <div class="w-full h-full d-flex justify-center pt-5">
    <div class="w-full h-full">
      <div class="mb-3">
        <InputCard
          :title="'Attribute*'"
          :sub_title="'You can add one or more attribute for your product.'"
        >
          <div class="d-flex flex-wrap py-2">
            <v-card
              class="add-new-card rounded-lg overflow-y-auto mx-1 pa-3 mb-3"
              :style="
                !isEdit ? 'width:400px;height:400px' : 'width:100%;height:400px'
              "
              v-for="(attribute, index) in form.attributes.$each.$iter"
              :key="index"
            >
              <span class="mb-2">Attributes </span>
              <v-icon
                color="grey"
                v-if="form.attributes.$model.length > 1"
                @click="deleteRow(index, attribute)"
                style="margin-left: 250px"
                >mdi-close</v-icon
              >

              
              <div>
                <CTextField
                small
                px="px-0"
                py="py-0"
                v-model="attribute.arabic_name.$model"
                :rules="
                validateRule(
                  attribute.arabic_name, // validate objec
                  $root, // context
                  $tr('arabic_name') // lable for feedback
                )
              "
              :invalid="attribute.name.$invalid"
                :title="$tr('arabic_name')"
                dense
                placeholder="Arabic Name"
                />
              </div>

              <div class="d-flex">
                <CTextField
                  small
                  px="px-0"
                  py="py-0"
                  v-model="attribute.name.$model"
                  :title="$tr('label_required', $tr('English Name'))"
                  :rules="
                    validateRule(
                      attribute.name, // validate objec
                      $root, // context
                      $tr('name') // lable for feedback
                    )
                  "
                  :invalid="attribute.name.$invalid"
                  dense
                  placeholder="English Name"
                />

              </div>

              <div>
                <CTextField
                  v-model="attribute.statement.$model"
                  :items="attribute.selectedAttr.$model"
                  @keydown="addInEnter($event, index)"
                  small
                  px="px-0"
                  py="py-0"
                  dense
                  title="Attribute Value"
                  placeholder="Value"
                  :rules="
                    validateRule(
                      attribute.selectedAttr, // validate objec
                      $root, // context
                      $tr('attributes') // lable for feedback
                    )
                  "
                  :invalid="attribute.selectedAttr.$invalid"
                  :hasItems="true"
                  @add="addValue(index)"
                  @removeItem="(attrIndex) => removeItem2(attrIndex, index)"
                />
              </div>
            </v-card>
            <AddNewCard
            class="add-new-card rounded-lg overflow-y-auto mx-1 pa-3 mb-3"
            :style="
              !isEdit ? 'width:400px;height:400px' : 'width:100%;height:400px'
            "

              v-if="!isEdit"
              :label="'add_another_attribute'"
            
              @addNewPlatform="addNewPlatform()"
            >
            </AddNewCard>
          </div>
        </InputCard>
      </div>
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";
import AddNewCard from "../../single_sales_management_system/ipa_component/AddNewCard.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";

export default {
  props: {
    form: Object, // default prop
    isEdit: false,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
    };
  },
  methods: { 
    async loaded() {},
    addInEnter(item, index) {
      if (item.key == "Enter") {
        this.addValue(index);
      }
    },
    async validate() {
      // validate function must validate this step here and return true of false
      console.log("my console", this.form.attributes);
      this.form.attributes.$touch();
      
      return !this.form.attributes.$invalid;
    },
    addNewPlatform() {
      this.form.attributes.$model.push({
        name: "",
        arabic_name: "",
        statement: "",
        selectedAttr: [],
      });
    },

    addValue(index) {
      if (this.form.attributes.$each[index].statement?.$model.length > 0) {
        this.form.attributes.$each[index].selectedAttr.$model.push(
          this.form.attributes.$each[index].statement.$model
        );
        // this.form.$model.selectedAttr.push(this.statement);

        this.form.attributes.$each[index].statement.$model = "";
      }
    },
    removeItem2(attrIndex, index) {
      console.log("my index", index);
      this.form.attributes.$each[index].selectedAttr.$model =
        this.form.attributes.$each[index].selectedAttr.$model.filter(
          (item, i) => i !== attrIndex
        );
    },
    deleteRow(index, attribute) {
      this.form.attributes.$model = this.form.attributes.$model.filter(
        (item, i) => i != index
      );
    },
  },
  components: { InputCard, AddNewCard, CTextField },
};
</script>
