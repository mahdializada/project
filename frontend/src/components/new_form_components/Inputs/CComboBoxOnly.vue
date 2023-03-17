<template>
  <v-combobox
    v-bind="$attrs"
    v-on="$listeners"
    ref="productNameInputRef"
    outlined
    background-color="var(--new-input-bg)"
    class="form-new-item form-custom-auto-complet"
    filled
    dense
    multiple
    hide-details
    hide-selected
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
        </div>
      </div>
    </template>
    <template v-slot:[`selection`]="{ item }"> </template>
  </v-combobox>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
  props: {
    itemText: String,
    multiple: Boolean,
    canAddNewItem: Boolean,
  },
  data() {
    return {
      search: null,
    };
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
/* .form-custom-auto-complete .v-input__prepend-inner {
  margin-top: 16px;
  margin-right: 8px !important;
} */
</style>
