<template>
  <div>
    <div class="w-full mt-2">
      <InputCard
        :title="this.$tr('type')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchCompany = v)"
      >
        <HorizontalItemContainer
          v-model="form.value_input.$model"
          :items="filterValue"
          :loading="isFetchingValue"
          :no_data="filterValue.length < 1 && !isFetchingValue"
          @blur="form.value_input.$touch()"
          height="200px"
          :rules="
            validateRule(
              form.value_input, // validate objec
              $root, // context
              $tr('company') // lable for feedback
            )
          "
          :invalid="form.value_input.$invalid"
        >
        </HorizontalItemContainer>
      </InputCard>
      <div class="w-full mt-2">
        <InputCard
        :title="this.$tr('file')"
        :hasSearch="false"
        :hasPagination="false"
        v-if="form.value_input.$model == 'file'"
      >
        <HorizontalItemContainer
          v-model="form.file_type.$model"
          :items="fileTypes"
          :loading="isFetchingfileTypes"
          :no_data="fileTypes.length < 1 && !isFetchingfileTypes"
          @blur="form.file_type.$touch()"
          height="200px"
          :rules="
            validateRule(
              form.file_type, // validate objec
              $root, // context
              $tr('company') // lable for feedback
            )
          "
          :invalid="form.file_type.$invalid"
          :multi="true"
        >
        </HorizontalItemContainer>
      </InputCard>
      </div>
      <div class="w-full mt-3 d-flex" v-if="form.value_input.$model == 'number'">
        <div class="w-full me-lg-1 mb-lg-0">
          <CTextField
            v-model="form.num_min.$model"
            :title="$tr( $tr('min_number'))"
            :placeholder="$tr('min_number')"
            prepend-inner-icon="mdi-view-grid"
            :rules="validateRule(form.num_min, $root, $tr('num_min'))"
            :invalid="form.num_min.$invalid"
          />

        </div>
        <div class="w-full ms-lg-1">
          <CTextField
          
          v-model="form.num_max.$model"
          :title="$tr( $tr('max_number'))"
          :placeholder="$tr('max_number')"
          prepend-inner-icon="mdi-view-grid"
          :rules="validateRule(form.num_max, $root, $tr('max_number'))"
          :invalid="form.num_max.$invalid"
        />

        </div>
        
      </div>
      <div class="w-full mt-3 d-flex">
        <div class="w-full me-lg-1 mb-lg-0">
          <CTextField
            v-model="form.arabic_name.$model"
            :title="$tr( $tr('arabic_name'))"
            :placeholder="$tr('arabic_name')"
            prepend-inner-icon="mdi-view-grid"
            :rules="validateRule(form.arabic_name, $root, $tr('arabic_name'))"
            :invalid="form.arabic_name.$invalid"
          />

        </div>
        <div class="w-full ms-lg-1">
          <CTextField
          v-model="form.value_name.$model"
          :title="$tr( $tr('English Name'))"
          :placeholder="$tr('english_name')"
          prepend-inner-icon="mdi-view-grid"
          :rules="validateRule(form.value_name, $root, $tr('English Name'))"
          :invalid="form.value_name.$invalid"
        />

        </div>
        
      </div>


      <!-- <div class="w-full mt-3">
        <CTextField
          v-if="form.value_input.$model == 'text'"
          v-model="form.text_value.$model"
          :title="$tr('label_required', $tr('text'))"
          :placeholder="$tr('text_value')"
          prepend-inner-icon="mdi-view-grid"
          :rules="validateRule(form.text_value, $root, $tr('text_value'))"
          :invalid="form.text_value.$invalid"
        />
      </div>
      <div class="w-full mt-3">
        <CFileUploadCloud
          :accept="'*'"
          v-if="form.value_input.$model == 'file'"
          :key="key"
          py="py-3"
          :dropFileType="'File'"
          system_name="single-sales"
          v-model="form.file_value.$model"
          :rules="validateRule(form.file_value, $root, $tr('product_image'))"
        />
      </div>
      <div class="w-full mt-3">
        <CTextField
          v-if="form.value_input.$model == 'number'"
          v-model="form.number_value.$model"
          :title="$tr('label_required', $tr('number'))"
          :placeholder="$tr('number_value')"
          prepend-inner-icon="mdi-view-grid"
          :rules="validateRule(form.number_value, $root, $tr('number_value'))"
          :invalid="form.number_value.$invalid"
          type="number"
        />
      </div>
      <div class="w-full mt-3">
        <CTextField
          v-if="form.value_input.$model == 'link'"
          v-model="form.link_value.$model"
          :title="$tr('label_required', $tr('link'))"
          :placeholder="$tr('link_value')"
          prepend-inner-icon="mdi-view-grid"
          :rules="validateRule(form.link_value, $root, $tr('link_value'))"
          :invalid="form.link_value.$invalid"
        />
      </div> -->
    </div>
  </div>
</template>

<script>
import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import GlobalRules from "~/helpers/vuelidate";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CFileUploadCloud from "../../new_form_components/Inputs/CFileUploadCloud.vue";

export default {
  components: {
    InputCard,
    HorizontalItemContainer,
    CTextField,
    CFileUploadCloud,
  },
  props: {
    form: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      searchCompany: "",
      isFetchingValue: false,
      isFetchingfileTypes: false,
      key: 0,
      valueInputes: [
        { id: "text", name: "text", logo: "/text-format.png" },
        { id: "file", name: "file", logo: "/folder.png" },
        { id: "number", name: "number", logo: "/num.png" },
        { id: "link", name: "link", logo: "/link.png" },
      ],
      fileTypes: [
        { id: "jpg", name: "jpg", logo: "/text-format.png" },
        { id: "excel", name: "excel", logo: "/folder.png" },
        { id: "powerpoint", name: "powerpoint", logo: "/num.png" },
        { id: "word", name: "word", logo: "/link.png" },
        { id: "access", name: "access", logo: "/link.png" },
      ],
    };
  },
  // watch: {
  //   "form.attribute_section": function (type) {
  //     if (type == "value_input") {
        
  //     }
  //   },
  // },
  computed: {
    filterValue() {
      if (this.searchCompany) {
        const filter = (item) =>
          item?.name
            ?.toLowerCase()
            ?.includes(this.searchCompany?.toLowerCase());
        return this.valueInputes.filter(filter);
      }
      return this.valueInputes;
    },
  },
 
  methods: {
    loaded(){
      console.log("jsdfjdsl",this.form.$model);
      console.log(this.form.value_input.$model);
    },
    async validate() {
        console.log(this.form);
      this.form.value_input.$touch();
      this.form.value_name.$touch();
      this.form.arabic_name.$touch();
      this.form.file_type.$touch();
      this.form.num_min.$touch();
      this.form.num_max.$touch();
      // this.form.text_value.$touch();
      // this.form.file_value.$touch();
      // this.form.number_value.$touch();
      // this.form.link_value.$touch();
      let isValid =
        !this.form.value_name.$invalid &&
        !this.form.arabic_name.$invalid &&
        !this.form.file_type.$invalid &&
        !this.form.num_min.$invalid &&
        !this.form.num_max.$invalid &&
        !this.form.value_input.$invalid ;
        // !this.form.file_value.$invalid &&
        // !this.form.number_value.$invalid &&
        // !this.form.link_value.$invalid &&
        // !this.form.text_value.$invalid;
      return isValid;
    },
  },
};
</script>

<style></style>
