<template>
  <div
    :class="`item__card background py-1 px-3 cursor-pointer ${
      selected ? 'selected' : ''
    }`"
    @click="$emit('click')"
    @dblclick="$emit('dblclick')"
  >
    <div class="item__logo me-2">
      <v-skeleton-loader
        class="item__card__loader"
        v-if="loading"
        type="avatar"
      />
      <div v-else>
        <v-avatar size="35">
          <img :src="item[logoName]" :alt="item[item_text]" />
        </v-avatar>
      </div>
    </div>
    <div class="item__text">
      <v-skeleton-loader
        v-if="loading"
        class="w-full item__card__loader"
        type="table-cell"
      />
      <div v-else>
        {{ item[item_text] }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    selected: Boolean,
    item: Object,
    loading: Boolean,
    logoName: {
      type:String,
      default:"logo"
    },
    item_text:{
      type:String,
      default:'name'
    },
  },
};
</script>

<style>
.item__card__loader .v-skeleton-loader__avatar {
  height: 40px !important;
  width: 40px !important;
}
.item__card__loader .v-skeleton-loader__table-cell {
  height: 35px !important;
}
</style>

<style lang="scss" scoped>
.item__card {
  width: 250px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin: 6px !important;
  border-radius: 8px;
  border: 1px solid transparent;
  transition: all 0.1s;
}
.item__card.selected {
  border: 2px solid #2e7be680 !important;
  box-shadow: 0 0 10px #2e7be633;
}
</style>