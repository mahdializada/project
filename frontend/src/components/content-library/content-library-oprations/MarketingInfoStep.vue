<template>
  <div class="h-full mt-5">
    <div class="mb-3">
      <CRadioWIthBody
        :items="seasons"
        @blur="form.season.$touch()"
        :text="$tr('is_the_content_for_the_specific_reason?')"
        @onChange="(v) => (form.season.$model = v)"
        :rules="validateRule(form.season, $root, $tr('season'))"
        :invalid="form.season.$invalid"
      />
    </div>
    <div class="mt-3">
      <InputCard
        :title="this.$tr('content_risks_of_violating_advertising_policy')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.risk_palicy.$model"
            v-for="item in riskPalicy"
            :key="item.id"
            :item="item"
            :selected="item.id == form.risk_palicy.$model"
            :disable="!item.active"
            @click="onrisk_palicySelected(item)"
            :rules="
              validateRule(
                form.risk_palicy, // validate objec
                $root, // context
                $tr('risk_palicy') // lable for feedback
              )
            "
            :invalid="form.risk_palicy.$invalid"
          >
          </SelectItem>
        </div>
      </InputCard>
    </div>
    <div class="mt-3">
      <CRadioWIthBody
        :items="mainOrCustomer"
        @blur="form.main_or_customer.$touch()"
        :text="$tr('is_there_an_oreder_of_the_custormer_to_do_someting?')"
        @onChange="(v) => (form.main_or_customer.$model = v)"
        :rules="
          validateRule(form.main_or_customer, $root, $tr('main_or_customer'))
        "
        :invalid="form.main_or_customer.$invalid"
      />
    </div>
    <div class="mt-3">
      <InputCard
        :title="this.$tr('information_offering')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.info_offer.$model"
            v-for="item in infoOffer"
            :key="item.id"
            :item="item"
            :selected="item.id == form.info_offer.$model"
            :disable="!item.active"
            @click="oninfo_offerSelected(item)"
            :rules="
              validateRule(
                form.info_offer, // validate objec
                $root, // context
                $tr('info_offer') // lable for feedback
              )
            "
            :invalid="form.info_offer.$invalid"
          >
          </SelectItem>
        </div>
      </InputCard>
    </div>
  </div>
</template>

<script>
import CRadioWIthBody from "../../new_form_components/Inputs/CRadioWIthBody.vue";
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";
import SelectItem from "../../new_form_components/components/SelectItem.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,

      seasons: [
        { value: "yes", label: "yes" },
        { value: "no", label: "no" },
      ],
      mainOrCustomer: [
        { value: "yes", label: "yes" },
        { value: "no", label: "no" },
      ],
      riskPalicy: [
        { id: "no_risk", name: "no_risk", active: true },
        {
          id: "average",
          name: "average",
          active: true,
        },
        {
          id: "high",
          name: "high",
          active: true,
        },
      ],
      infoOffer: [
        { id: "none", name: "none", active: true },
        { id: "basic", name: "basic", active: true },
        {
          id: "average",
          name: "average",
          active: true,
        },
        {
          id: "high",
          name: "high",
          active: true,
        },
      ],
    };
  },
  computed: {},
  methods: {
    async validate() {
      this.form.season.$touch();
      this.form.risk_palicy.$touch();
      this.form.info_offer.$touch();
      this.form.main_or_customer.$touch();

      return (
        !this.form.season.$invalid &&
        !this.form.risk_palicy.$invalid &&
        !this.form.main_or_customer.$invalid &&
        !this.form.info_offer.$invalid
      );
    },
    async loaded() {},
    onrisk_palicySelected(item) {
      if (item.active == true) {
        this.form.risk_palicy.$model = item.id;
      }
    },
    oninfo_offerSelected(item) {
      if (item.active == true) {
        this.form.info_offer.$model = item.id;
      }
    },
  },
  components: { CRadioWIthBody, InputCard, SelectItem },
};
</script>

<style>
</style>