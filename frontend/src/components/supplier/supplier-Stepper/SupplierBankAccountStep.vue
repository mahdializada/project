<template>
  <div :key="updateKey">
    <MultiInputItem
      :items="form.bank.$each.$iter"
      @addMore="addMore"
      @removeItem="removeItem"
      :title="'bank_account'"
    >
      <template v-slot:content="{ item }">
        <div class="h-full d-flex" style="height: fit-content !important">
          <div class="w-full">
            <CTextField
              :title="$tr('label_required', $tr('bank_name'))"
              placeholder="Supplier Trading Name"
              type="textfield"
              class="me-md-2"
              rounded
              v-model="item.bank_name.$model"
              @blur="item.bank_name.$touch()"
              :rules="validateRule(item.bank_name, $root, $tr('bank_name'))"
              :invalid="item.bank_name.$invalid"
            />
          </div>
          <div class="w-full">
            <CTextField
              :title="$tr('label_required', $tr('bank_account_name'))"
              placeholder="Supplier Name"
              type="textfield"
              class="me-md-2"
              rounded
              v-model="item.bank_account_name.$model"
              @blur="item.bank_account_name.$touch()"
              :rules="
                validateRule(
                  item.bank_account_name,
                  $root,
                  $tr('bank_account_name')
                )
              "
              :invalid="item.bank_account_name.$invalid"
            />
          </div>
        </div>

        <div class="h-full d-flex" style="height: fit-content !important">
          <div class="w-full">
            <CTextField
              :title="$tr('label_required', $tr('bank_account_number'))"
              placeholder="Supplier Trading Name"
              type="number"
              class="me-md-2"
              rounded
              v-model="item.bank_account_number.$model"
              @blur="item.bank_account_number.$touch()"
              :rules="
                validateRule(
                  item.bank_account_number,
                  $root,
                  $tr('bank_account_number')
                )
              "
              :invalid="item.bank_account_number.$invalid"
            />
          </div>
          <div class="w-full">
            <CTextField
            :title="$tr('label_required', $tr('bank_account_number_iban'))"
            placeholder="Supplier Name"
            type="number"
            rounded
              v-model="item.bank_account_number_iban.$model"
              @blur="item.bank_account_number_iban.$touch()"
              :rules="
                validateRule(
                  item.bank_account_number_iban,
                  $root,
                  $tr('bank_account_number_iban')
                  )
                  "
              :invalid="item.bank_account_number_iban.$invalid"
              />
              <v-checkbox v-model="item.isChecked.$model" style="float:right;margin-top:-8rem"></v-checkbox>
          </div>
        </div>
        <div class="h-full d-flex" style="height: fit-content !important">
          <div class="w-full">
            <CTextField
              :title="$tr('label_required', $tr('swift_code'))"
              placeholder="Supplier Trading Name"
              type="number"
              class="me-md-2"
              rounded
              v-model="item.swift_code.$model"
              @blur="item.swift_code.$touch()"
              :rules="validateRule(item.swift_code, $root, $tr('swift_code'))"
              :invalid="item.swift_code.$invalid"
            />
          </div>
          <div class="w-full">
            <CTextField
              :title="$tr('label_required', $tr('address'))"
              placeholder="Supplier Name"
              type="textfield"
              class="me-md-2"
              rounded
              v-model="item.address.$model"
              @blur="item.address.$touch()"
              :rules="validateRule(item.address, $root, $tr('address'))"
              :invalid="item.address.$invalid"
            />
          </div>
        </div>
      </template>
    </MultiInputItem>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import MultiInputItem from "../../new_form_components/cat_product_selection/MultiInputItem.vue";
export default {
  computed: {},
  props: { form: Object },
  components: {
    CTitle,
    CTextField,
    MultiInputItem,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      updateKey: 0,
      isChecked:true,
    };
  },
  methods: {
    async validate() {
      this.form.bank.$touch();
      return !this.form.bank.$invalid;
    },
    async loaded() {
    },
    addMore() {
      this.form.bank.$model.push({
        bank_name: "",
        bank_account_name: "",
        bank_account_number: "",
        bank_account_number_iban: "",
        swift_code: "",
        address: "",
      });
    },
    removeItem(item) {
      if (this.form.bank.$model.length > 1)
        this.form.bank.$model.splice(item, 1);
    },
  },
};
</script>

<style></style>
