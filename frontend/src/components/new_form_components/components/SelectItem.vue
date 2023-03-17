<template>
  <div
    :class="`item__card  ${
      item.icon ? 'pa-1' : isSmall ? 'pa-1' : 'px-3 py-2'
    } cursor-pointer ${selected ? 'selected' : ''}  ${
      disable ? 'disabled' : ''
    } ${isSmall ? 'small_width' : ''}`"
    @click="$emit('click', item.id)"
    @dblclick="$emit('dblclick')"
  >
    <div class="item__text">
      <v-skeleton-loader
        v-if="loading"
        class="w-full item__card__loader"
        type="table-cell"
      />
      <div v-else :class="` ${disable ? 'disabled' : ''}`">
        <span>
          <v-avatar size="30" class="me-1" v-if="icon">
            <v-icon v-if="item.icon" color="primary">{{ item.icon }}</v-icon>
            <img v-else :src="item.image" alt="" />
          </v-avatar>
          {{ $tr(item.name) }} <br />
          <span v-if="hasDetails"
            >&nbsp;&nbsp;{{ item.details ?? "" }}&nbsp;&nbsp;</span
          >
        </span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    disable: Boolean,
    selected: Boolean,
    item: Object,
    icon: Boolean,
    loading: Boolean,
    isSmall: Boolean,
    hasDetails: Boolean,
  },
  methods: {},
};
</script>

<style>
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
