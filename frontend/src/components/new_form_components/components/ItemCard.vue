<template>
  <div
    :class="`item-card background cursor-pointer position-relative ${
      selected ? 'selected' : ''
    }`"
    @click="$emit('click')"
    @dblclick="$emit('dblclick')"
    :style="`${customWidth ? 'width:95px; padding:0px 1px;height:130px' : ''} ${
      customWidth2 ? 'width:80px; height:85px' : ''
    }`"
  >
    <v-btn
      v-if="hasFavorite"
      :class="`favorite-icon ${item.favorite ? 'favorite' : ''}`"
      elevation="0"
      fab
      text
      x-small
      @click.stop="$emit('toggleFavorite')"
    >
      <v-icon color="#ffb100">
        {{ item.favorite ? "mdi-star" : "mdi-star-outline" }}
      </v-icon>
    </v-btn>
    <div class="item-icon d-flex align-center justify-center">
      <div
        v-if="!loading"
        :class="`${hasDetails ? 'icon-bg1' : 'icon-bg'} text-center mt-1 ${
          item.type == 'category' ||
          item.type == 'sub_category' ||
          item.type == 'team'
            ? ''
            : 'icon-bg-none'
        }`"
      >
        <v-avatar
          v-if="customIcon"
          :size="35"
          color="#dbeafb"
          style="overflow: unset"
        >
          <span style="width: 80%" class="">
            <ItemIcon :icon="customIcon" />
          </span>
        </v-avatar>
        <v-avatar
          v-else-if="supplierIcon"
          color="#dbeafb"
          :size="35"
          style="overflow: unset"
        >
          <ItemIcon :icon="supplierIcon" />
        </v-avatar>
        <v-avatar v-else-if="nameLogo" :size="40" color="blue-grey darken-3">
          <span class="white--text text-h6 pa-1">
            {{ item[nameLogo][0].toUpperCase() }}
          </span>
        </v-avatar>
        <v-avatar v-else-if="hasLogo" :size="40">
          <v-img
            class="rounded-circle"
            width="40"
            height="40"
            :src="item.logo"
          ></v-img>
        </v-avatar>
        <ItemIcon v-else :icon="item.type" :flag="item.iso2" />
        <v-avatar v-if="item.icon" :size="50">
          <span v-html="item.icon" style="width: 80%"></span>
        </v-avatar>
        <v-avatar v-if="hasImg" :size="40">
          <v-img
            class="rounded-circle"
            width="40"
            height="40"
            :src="item.image"
          ></v-img>
        </v-avatar>
        <v-img v-if="fullImg" width="35" :src="item.image"></v-img>
      </div>
      <v-skeleton-loader v-else class="mt-1" type="avatar"></v-skeleton-loader>
    </div>
    <div
      :class="`
        item-name
        d-flex
        align-center
        justify-center
        text-center text-capitalize
      `"
      :style="`${
        selected
          ? hasDetails
            ? 'color: var(--v-primary-base);font-weight: 600;'
            : 'font-size: 13px;color: var(--v-primary-base);font-weight: 600;'
          : ''
      }`"
    >
      <span>
        {{ !loading ? $tr(item[item_text]) : "" }}
        <br />
        <slot name="cost" />
      </span>
      <v-skeleton-loader
        v-if="loading"
        class="mt-1 w-full"
        type="table-cell"
      ></v-skeleton-loader>
    </div>
    <div
      v-if="checkbox"
      class="selected-mark d-flex align-center justify-center"
    >
      <v-icon dark size="14">mdi-check-bold</v-icon>
    </div>
  </div>
</template>
<script>
import ItemIcon from "./ItemIcon.vue";
export default {
  props: {
    customWidth: Boolean,
    customWidth2: Boolean,
    hasDetails: Boolean,
    fullImg: Boolean,
    selected: Boolean,
    item: Object,
    loading: Boolean,
    hasLogo: Boolean,
    hasImg: Boolean,
    hasFavorite: Boolean,
    nameLogo: String,
    supplierIcon: String,
    item_text: {
      type: String,
      default: "name",
    },
    checkbox: {
      default: true,
      type: Boolean,
    },
    customIcon: {
      type: String,
      default: "",
    },
  },
  components: {
    ItemIcon,
  },
};
</script>
<style scoped>
* {
  fill: var(--v-primary-base);
}
.item-card {
  position: relative;
  width: 102px;
  height: 110px;
  margin: 6px !important;
  border-radius: 8px;
  border: 1px solid transparent;
  transition: all 0.1s;
}
.item-card:hover {
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.16);
}
.item-icon {
  height: 55%;
}
.item-name {
  height: 45%;
  font-size: 12px;
}
/* .item__selected {
  font-size: 13px;
  color: var(--v-primary-base);
  font-weight: 600;
} */
.icon-bg {
  height: 46px;
  width: 46px;
  background: #deecfd;
  border-radius: 50%;
}
.icon-bg1 {
  background: #deecfd;
  border-radius: 50%;
}
.icon-bg-none {
  background: transparent !important;
}
.item-card.selected {
  border: 1px solid #2e7be680 !important;
  box-shadow: 0 0 10px #2e7be633;
}
.selected-mark {
  height: 18px;
  width: 18px;
  border-radius: 50%;
  background: var(--v-primary-base);
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translate(-50%, 50%) scale(0);
  transition: all 0.1s;
}
.selected .selected-mark {
  transform: translate(-50%, 50%) scale(1);
}
.favorite-icon {
  position: absolute;
  top: 0px;
  right: 0px;
  opacity: 0 !important;
  transition: all 0.3s;
}
.item-card:hover .favorite-icon {
  opacity: 1 !important;
}
.favorite-icon.favorite {
  opacity: 1 !important;
}
</style>
