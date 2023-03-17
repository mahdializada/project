<template>
  <div class="topDiv">
    <div class="topTitle">
      <h1
        class="text-center text-h4 font-weight-bold border-bottom py-2 text-uppercase"
        style="color: #58595b"
      >
        {{ $tr("members") }}
      </h1>
    </div>
    <div class="countries mx-3 my-3">
      <div class="d-flex flex-column flex-md-row align-md-center">
        <div class="w-full">
          <CustomInput
            :items="customSelect"
            :label="$tr('label_required',$tr('member'))"
            :placeholder="$tr('choose_item',$tr('member'))"
            type="autocomplete"
            class="me-md-4 mb-md-2 mb-0"
          />
        </div>
        <div class="w-100">
          <FormBtn title="add_plus" class="mt-1 mb-2" />
        </div>
      </div>
      <div class="selected d-flex flex-wrap pb-2">
        <SelectedItem @close="Itemclose" title="Haneef_IT" />
        <SelectedItem @close="Itemclose" title="Asad_Designing" />
      </div>
    </div>
  </div>
</template>
<script>
import CustomInput from "~/components/design/components/CustomInput.vue";
import FormBtn from "~/components/design/components/FormBtn.vue";
import SelectedItem from "~/components/design/components/SelectedItem.vue";

export default {
  props: {
    autoEditData: {
      require: true,
      type: Object,
    },
  },
  components: { CustomInput, SelectedItem, FormBtn },
  data() {
    return {
      customSelect: ["Foo", "Bar", "Fizz", "Buzz"],
    };
  },
  methods: {
    setNull(data) {
      // for (const [key, value] of Object.entries(this.AccountForm)) {
      //   this.AccountForm[key] = data[key];
      // }
    },
  },
};
</script>

<style>
.topDiv {
  position: relative;
}
.topTitle {
  top: 0px;
  position: sticky;
  z-index: 2;
  background-color: white;
}
.name {
  color: #585965;
}
.role {
  color: #595e84;
}
.border-bottom {
  border-bottom: 2px solid #bfc8d7;
}
.subtitle-1 {
  color: #737885;
}
.border {
  border: 1px solid #b6c1d2;
}
.style.theme--light.v-btn.v-btn--has-bg {
  background-color: #edf2f9 !important;
}
.main-Card
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--is-booted.v-text-field--enclosed.v-text-field--outlined.v-text-field--placeholder
  .v-input__control {
  border-radius: 3px !important;
}
@media screen and (min-width: 600px) {
  .buttons {
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
}
</style>
