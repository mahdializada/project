<template>
  <InputCard pbNone v-bind="$attrs" v-on="$listeners">
    <template slot-scope="{ attrs, listeners }">
      <v-combobox
        v-bind="attrs"
        v-on="listeners"
        validate-on-blur
        ref="productNameInputRef"
        class="form-new-item form-custom-auto-complete"
        background-color="var(--new-input-bg)"
        outlined
        rounded
        :menu-props="{ bottom: true, offsetY: true, class: 'test' }"
        @change="onChange"
      >
        <template v-slot:[`no-data`]>
          <div v-if="!canAddNewItem" class="text-center">
            {{ $tr("no_data") }}
          </div>
          <div class="d-flex pa-1 align-center" v-else>
            <div class="me-1">{{ $tr("no_data_available") }}</div>
            <div class="ms-5" style="cursor: pointer">
              <span
                @click="$refs.productNameInputRef.blur()"
                class="font-weight-bold primary--text"
                >{{ $tr("create") }}
              </span>
              {{ $tr("new_product") }}
            </div>
          </div>
        </template>

        <template v-slot:[`selection`]="{ item }">
          <div class="v-select__selection--comma">
            {{ itemText ? item[itemText] : item }}{{ multiple ? "," : "" }}
          </div>
        </template>
        <template v-slot:[`item`]="{ item, on, attrs }">
          <v-list-item
            class="select-list-item-custom pe-1"
            v-on="on"
            v-bind="attrs"
          >
            <div
              :class="`select-item-custom w-full d-flex justify-space-around align-center`"
              style="font-weight: 500"
            >
              {{ itemText ? item[itemText] : item }}
              <v-checkbox
                v-model="attrs.inputValue"
                hide-details
                color="primary"
                class="ms-auto mt-0 pt-0"
              ></v-checkbox>
            </div>
          </v-list-item> </template
      ></v-combobox>
    </template>
  </InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
  props: {
    itemText: String,
    multiple: Boolean,
    canAddNewItem: Boolean,
  },
  components: { InputCard },
  methods: {
    onChange(item) {
      this.$emit("onChange", item);
    },
  },
};
</script>
<style>
.form-custom-auto-complete .v-input__prepend-inner {
  margin-top: 16px;
  margin-right: 8px !important;
}
</style>
