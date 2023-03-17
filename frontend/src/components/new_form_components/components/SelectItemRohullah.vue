<template>
  <div
    :class="`item__card px-1 cursor-pointer ${selected ? 'selected' : ''}  ${
      disable ? 'disabled' : ''
    } `"
    @click="$emit('click', item.id)"
    @dblclick="$emit('dblclick')"
    style="padding: 4px 12px"
  >
    <div class="item__text">
      <v-skeleton-loader
        v-if="loading"
        class="w-full item__card__loader"
        type="table-cell"
      />
      <div v-else :class="`d-flex align-center ${disable ? 'disabled' : ''}`">
        <img v-if="icon" :src="item.image" alt="" height="50px" class="me-1" />
        <img
          v-if="logo"
          :src="item.logo"
          alt=""
          height="30px"
          width="30px"
          class="me-1 rounded-circle"
        />
        <FlagIcon v-if="flag" :flag="item.iso2" :round="true" class="me-1" />
        <span :class="`d-block  ${selected ? 'selected-text' : 'grey--text'}`">
          {{ $tr(item.name) }}
          <span
            v-if="hasDetails"
            :class="`d-block  ${selected ? 'selected-text' : 'grey--text'}`"
            style="font-size: 14px; font-weight: 500"
            >{{ item.details ?? "" }}</span
          >
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import FlagIcon from "../../common/FlagIcon.vue";

export default {
  props: {
    disable: Boolean,
    selected: Boolean,
    item: Object,
    icon: Boolean,
    loading: Boolean,
    isSmall: Boolean,
    hasDetails: Boolean,
    flag: Boolean,
    logo: Boolean,
  },
  methods: {},
  components: { FlagIcon },
};
</script>

<style>
.selected-text {
  color: #2e7be6;
}
.item__card__loader .v-skeleton-loader__table-cell {
  height: 35px !important;
}
</style>

<style lang="scss" scoped>
.item__card {
  width: auto;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin: 6px !important;
  border-radius: 8px;
  transition: all 0.1s;
  border: 1px solid #2e7be62b !important;
}
.item__card.selected {
  border: 2px solid #2e7be680 !important;
  box-shadow: 0 0 10px #2e7be633;
}
.disabled {
  color: grey !important;
}
.disabled:hover {
  cursor: not-allowed;
}
.small_width {
  margin: 6px !important;
  border-radius: 8px;
  transition: all 0.1s;
  border: 1px solid #2e7be62b !important;
}
</style>
