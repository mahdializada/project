<template>
  <div class="h-full mt-5">
    <CTitle :text="$tr('content_info')" />
    <div class="w-full mb-2">
      <CRadioWIthBody
        :items="requestWhen"
        @blur="form.requested_when.$touch()"
        :text="$tr('request_when')"
        @onChange="(v) => (form.requested_when.$model = v)"
        :rules="validateRule(form.requested_when, $root, $tr('request_when'))"
        :invalid="form.requested_when.$invalid"
      />
    </div>
    <div class="mb-3">
      <CRadioWIthBody
        :items="ContentSource"
        @blur="form.content_source.$touch()"
        :text="$tr('content_source')"
        @onChange="(v) => (form.content_source.$model = v)"
        :rules="validateRule(form.content_source, $root, $tr('content_source'))"
        :invalid="form.content_source.$invalid"
      />
    </div>
    <div class="mt-3">
      <InputCard
        :title="this.$tr('content_type')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.content_type.$model"
            v-for="item in contentType"
            :key="item.id"
            :item="item"
            :icon="true"
            :selected="item.id == form.content_type.$model"
            :disable="!item.active"
            @click="oncontent_typeSelected(item)"
            :rules="
              validateRule(
                form.content_type, // validate objec
                $root, // context
                $tr('content') // lable for feedback
              )
            "
            :invalid="form.content_type.$invalid"
          >
            <img :src="item.image" alt="" />
          </SelectItem>
        </div>
      </InputCard>
    </div>
    <div class="mt-3 pb-3">
      <InputCard
        :title="this.$tr('content_language')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.content_language.$model"
            v-for="item in contentLanguage"
            :key="item.id"
            :item="item"
            :icon="true"
            :selected="item.id == form.content_language.$model"
            :disable="!item.active"
            @click="oncontent_languageSelected(item)"
            :rules="
              validateRule(
                form.content_language, // validate objec
                $root, // context
                $tr('content') // lable for feedback
              )
            "
            :invalid="form.content_language.$invalid"
          >
            <img :src="item.image" alt="" />
          </SelectItem>
        </div>
      </InputCard>
    </div>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import CRadioWIthBody from "../../new_form_components/Inputs/CRadioWIthBody.vue";
import SelectItem from "../../new_form_components/components/SelectItem.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      path: "",
      isVideo: false,
      requestWhen: [
        {
          value: "product_is_sold_for_the_first_time",
          label: "product_is_sold_for_the_first_time",
        },
        { value: "during_the_sales_phase", label: "during_the_sales_phase" },
      ],
      ContentSource: [
        { value: "creation", label: "creation" },
        { value: "copy", label: "copy" },
      ],
      contentType: [
        { id: "video", name: "video", icon: "fa fa-video", active: true },
        {
          id: "banner",
          name: "banner",
          icon: "fa fa-flag",
          active: true,
        },
        {
          id: "animate_banner",
          icon: "fa fa-file-video",
          name: "animate_banner",
          active: true,
        },
      ],
      contentLanguage: [
        { id: "english", name: "english", image: "/english.jpg", active: true },
        {
          id: "UAE",
          name: "UAE",
          image: "/uae.jpg",
          active: true,
        },
      ],
    };
  },
  computed: {},

  methods: {
    async validate() {
      this.form.requested_when.$touch();
      this.form.content_source.$touch();
      this.form.content_type.$touch();
      this.form.content_language.$touch();
      return (
        !this.form.requested_when.$invalid &&
        !this.form.content_source.$invalid &&
        !this.form.content_type.$invalid &&
        !this.form.content_language.$invalid
      );
    },
    async loaded() {},
    oncontent_typeSelected(item) {
      if (item.active == true) {
        this.form.content_type.$model = item.id;
      }
    },
    oncontent_languageSelected(item) {
      if (item.active == true) {
        this.form.content_language.$model = item.id;
      }
    },
  },
  components: {
    CTitle,
    InputCard,
    CRadioWIthBody,
    SelectItem,
    NoCheckboxItemContainer,
  },
};
</script>

<style>
.sales__type__container {
  display: flex;
  justify-content: space-between;
}
</style>