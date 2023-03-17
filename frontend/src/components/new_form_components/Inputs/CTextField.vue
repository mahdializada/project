<template>
  <InputCard pbNone v-bind="$attrs" v-on="$listeners">
    <template slot-scope="{ attrs, listeners }">
      <v-text-field
        :class="`form-new-item form-custom-text-field  pa-0 ${
          hasItems || hasCustomBtn ? 'has-append' : ''
        } ${small ? 'small' : ''}`"
        background-color="var(--new-input-bg)"
        outlined
        :rounded="rounded"
        v-bind="attrs"
        v-on="listeners"
        :dense="dense"
      >
        <template v-if="hasItems" slot="append" class="pe-0">
          <v-btn fab small color="primary" class="ma-0" @click="add">
            <v-icon dark size="20"> mdi-plus </v-icon>
          </v-btn>
        </template>
        <template v-else-if="hasCustomBtn" slot="append" class="pe-0">
          <v-btn fab small color="primary" class="ma-0" @click="add">
            <v-icon dark size="20"> {{ btnIcon }} </v-icon>
          </v-btn>
        </template>
      </v-text-field>
      <CFeildItems
        v-if="hasItems && !small"
        :items="items"
        :icon="attrs['prepend-inner-icon']"
        @removeItem="(index) => $emit('removeItem', index)"
      />
      <div
        class="single-text-filed-values d-flex flex-wrap Scroll"
        v-if="hasItems && small"
      >
        <div
          v-for="(item, i) in items"
          :key="i"
          class="
            single-text-filed-value
            ps-1
            d-flex
            align-center
            justify-center
          "
        >
          {{ item }}
          <v-btn
            fab
            text
            x-small
            color="primary"
            class="ma-0"
            @click="$emit('removeItem', i)"
          >
            <v-icon dark size="16"> mdi-close </v-icon>
          </v-btn>
        </div>
      </div>
    </template>
  </InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
import CFeildItems from "./CFeildItems.vue";
export default {
  props: {
    hasItems: Boolean,
    items: Array,
    small: Boolean,
    hasCustomBtn: Boolean,
    btnIcon: String,
    rounded: {
      type: Boolean,
      default: true,
    },
    dense: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    add() {
      this.$emit("add");
    },
  },
  components: {
    InputCard,
    CFeildItems,
  },
};
</script>

<style>
.form-custom-text-field .v-input__prepend-inner {
  margin-top: 16px;
  margin-right: 8px !important;
}
.form-custom-text-field.has-append .v-input__slot {
  padding-right: 8px !important;
}
.v-application--is-rtl .form-custom-text-field.has-append .v-input__slot {
  padding-right: 24px !important;
  padding-left: 8px;
}
.form-custom-text-field.has-append .v-input__append-inner {
  margin-top: 8px !important;
}
.form-custom-text-field.small .v-btn {
  height: 28px;
  width: 28px;
}
.form-custom-text-field.has-append.small .v-input__append-inner {
  margin-top: 6px !important;
}
.form-custom-text-field.has-append.small .v-input__slot {
  padding-right: 6px !important;
  padding-left: 24px !important;
}
.v-application--is-rtl .form-custom-text-field.has-append.small .v-input__slot {
  padding-right: 24px !important;
  padding-left: 6px !important;
}
.single-text-filed-values {
  max-height: 78px;
  overflow-y: auto;
}
.single-text-filed-value {
  border: 1px solid rgba(0, 0, 0, 0.05);
  border-radius: 4px;
  color: #777;
  font-size: 12px;
  margin: 4px;
}
.single-text-filed-value .v-btn {
  height: 28px;
  width: 28px;
}

.custom-number input[type="number"]::-webkit-inner-spin-button,
.custom-number input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
