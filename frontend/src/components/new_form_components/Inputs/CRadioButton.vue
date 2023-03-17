<template>
  <InputCard pbNone v-bind="$attrs" v-on="$listeners">
    <template slot-scope="{ attrs, listeners }">
      <div class="gender-selection-wrapper">
        <v-radio-group
          @change="valueChange"
          :value="value"
          v-bind="attrs"
          v-on="listeners"
          row
          class="d-flex justify-space-between custom__radio__group"
        >
          <div class="custom__title">
            {{ text }}
          </div>
          <div class="d-flex">
            <v-radio
              v-for="(item, i) in items"
              :key="i"
              class="me-4"
              :label="$tr(item.label)"
              :value="item.value"
            ></v-radio>
          </div>
        </v-radio-group>
        <slot name="radio-body" />
      </div>
    </template>
  </InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
  props: {
    items: Array,
    value: null,
    text: String,
  },
  data() {
    return {};
  },
  methods: {
    valueChange(value) {
      this.$emit("input", value);
    },
  },
  components: { InputCard },
};
</script>
<style >
.custom__radio__group .v-input--radio-group__input {
  justify-content: space-between;
}
</style>
<style scoped>
.gender-selection-wrapper {
  height: 70px;
}
.custom__title {
  font-size: 18px;
  font-weight: 500;
  color: #777;
}
</style>
