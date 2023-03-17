<template>
  <div>
    <template>
      <TeamEdit
        :role="role"
        v-for="(selectedItem, i) in autoEditData"
        v-show="current == i"
        :key="i"
        :editData="selectedItem"
        :totals="autoEditData.length"
        :currentIndex="current"
        isAutoEdit
        @toggleProgressBar="() => $emit('toggleProgressBar')"
        @close="() => $emit('close')"
        @onItemChange="onItemChange"
        ref="teamEdit"
        @nextItem="nextItem"
      />
    </template>
  </div>
</template>

<script>
import TeamEdit from "./TeamEdit.vue";
export default {
  components: {
    TeamEdit,
  },
  props: {
    autoEditData: {
      type: Array,
    },
    role: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      model: 0,
      current: 0,
    };
  },
  methods: {
    async onItemChange(actionType) {
      this.$emit("toggleProgressBar");
      if (
        actionType === "next" &&
        this.current < this.autoEditData.length - 1
      ) {
        this.current = this.current + 1;
      } else if (actionType === "back" && this.current !== 0) {
        this.current = this.current - 1;
      }
      this.$emit("toggleProgressBar");
    },
    async nextItem() {
      if (this.current < this.autoEditData.length - 1) {
        this.current += 1;
      }
    },
  },
};
</script>

<style>
.main-Card {
  background-color: #f9fafd !important;
}
.main-Card .title {
  color: #b6c1d2 !important;
}
.main-Card .v-stepper .v-stepper__header .v-stepper__step__step {
  display: none !important;
}
.main-Card .v-stepper__step.v-stepper__step--active .v-stepper__label .v-btn {
  background-color: var(--v-primary-base);
  color: white;
}
</style>
