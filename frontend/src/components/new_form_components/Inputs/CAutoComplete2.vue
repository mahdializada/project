<template>
  <InputCard pbNone v-bind="$attrs" v-on="$listeners">
    <template slot-scope="{ attrs, listeners }">
      <v-autocomplete
        class="form-new-item form-custom-auto-complete"
        background-color="var(--new-input-bg)"
        outlined
        rounded
        :menu-props="{ bottom: true, offsetY: true }"
        v-bind="attrs"
        v-on="listeners"
        :item-text="itemText"
      >
        <template v-slot:[`selection`]="{ item }">
          <div class="v-select__selection--comma">
            {{ itemText ? item[itemText] + " " + item[p_name] : item
            }}{{ multiple ? "," : "" }}
          </div>
        </template>
        <template v-slot:[`item`]="{ item, on, attrs }">
          <v-list-item
            class="select-list-item-custom pe-1"
            v-on="on"
            v-bind="attrs"
          >
            <div
              :class="`select-item-custom w-full d-flex justify-space-between align-center`"
              style="font-weight: 500"
            >
              <span> {{ item[itemText] }}</span>
              <span class="d-flex align-center">
                {{ item[p_name] }}
                <v-checkbox
                  v-model="attrs.inputValue"
                  hide-details
                  color="primary"
                  class="ms-1 mt-0 pt-0"
                ></v-checkbox>
              </span>
            </div>
          </v-list-item> </template
      ></v-autocomplete>
    </template>
  </InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
  props: {
    itemText: String,
    p_name: String,
    multiple: Boolean,
  },
  components: { InputCard },
};
</script>
<style>
.form-custom-auto-complete .v-input__prepend-inner {
  margin-top: 16px;
  margin-right: 8px !important;
}
</style>
