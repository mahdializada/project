<template>
  <div>
    <CustomInput
      @blur="$v.designRequest.category_id.$touch()"
      v-model="$v.designRequest.category_id.$model"
      :rules="validate($v.designRequest.category_id, $root, 'category')"
      :label="$tr('label_required', $tr('category'))"
      :items="categories"
      item-value="id"
      item-text="title"
      :placeholder="$tr('choose_item', $tr('category'))"
      type="autocomplete"
      class="w-full"
      :loading="isFetchingCategory"
      @change="onCategoryChange"
    />
    <CustomInput
      @blur="$v.designRequest.label_id.$touch()"
      v-model="$v.designRequest.label_id.$model"
      :rules="validate($v.designRequest.label_id, $root, 'label')"
      :label="$tr('label_required', $tr('label'))"
      :items="labels"
      item-value="id"
      item-text="title"
      :placeholder="$tr('choose_item', $tr('label'))"
      type="autocomplete"
      class="w-full"
      :loading="isFetchingLabel"
    />
  </div>
</template>

<script>
import CustomInput from '../design/components/CustomInput';
import GlobalRules from '~/helpers/vuelidate';
import DesignRequestValidtions from '~/validations/design-request-validations';

export default {
  name: 'LabelCategory',
  components: { CustomInput },

  props: {
    categories: {
      required: true,
      type: Array,
    },
    item: {
      required: false,
      type: Object,
    },
  },

  data() {
    return {
      validate: GlobalRules.validate,
      itemData: 'product found',
      labels: [],
      isFetchingCategory: false,
      isFetchingLabel: false,
      designRequest: JSON.parse(JSON.stringify(DesignRequestValidtions.schema)),
    };
  },
  validations: {
    designRequest: DesignRequestValidtions.validations,
  },
  async created() {},
  methods: {
    // check validations
    checkValidation() {
      return !this.itemData.note.$invalid;
    },
    async onCategoryChange() {
      await this.getLabels();
    },
    async getLabels() {
      try {
        const params = {
          sub_system: 'Design Request',
          category_id: this.$v.designRequest.category_id.$model,
        };
        this.isFetchingLabel = true;
        const response = await this.$axios.get('labels/id', { params });
        this.labels = response.data.labels;
        this.isFetchingLabel = false;
      } catch (error) {
        this.isFetchingLabel = false;
      }
    },
  },
};
</script>

<style scoped></style>
