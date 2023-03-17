<template>
  <v-menu
    ref="parentMenu"
    close-on-click
    offset-y
    v-bind="$attrs"
    v-on="$listeners"
  >
    <template v-if="hideBtn == false" v-slot:activator="{ on, attrs }">
      <v-btn
        v-if="text"
        v-bind="attrs"
        v-on="on"
        color="primary"
        style="height: 30px"
        class="ms-2 px-2 font-weight-medium text-uppercase"
      >
        <v-icon class="me-1"> {{ icon }} </v-icon>
        {{ $tr(text) }}
      </v-btn>
      <v-btn
        v-else-if="outlinedIcon"
        v-bind="attrs"
        v-on="on"
        outlined
        dark
        style="height: 30px"
        class="ms-1 px-1 min-w-0"
      >
        <v-icon size="18px"> {{ icon }} </v-icon>
      </v-btn>
      <v-btn v-else icon v-bind="attrs" v-on="on" color="primary">
        <v-icon> {{ icon }} </v-icon>
      </v-btn>
    </template>
    <v-list dense>
      <template v-for="item in items">
        <v-menu
          :close-on-content-click="true"
          close-on-click
          v-if="item.children"
          :key="item.icon"
          open-on-hover
          offset-x
        >
          <template v-slot:activator="{ on, attrs }">
            <v-list-item
              link
              v-bind="attrs"
              v-on="on"
              style="height: 35px"
              :key="item.icon"
              @click="item.click(item)"
            >
              <v-list-item-icon>
                <v-icon size="20" class="ps-1 me-1" color="primary">
                  {{ item.icon }}
                </v-icon>
              </v-list-item-icon>
              <v-list-item-title style="font-weight: 500" class="pe-4">
                {{ $tr(item.title) }}
              </v-list-item-title>
            </v-list-item>
          </template>
          <v-list dense>
            <v-list-item
              link
              v-for="children in item.children"
              style="height: 35px"
              :key="children.icon"
              @click="
                () => {
                  $refs.parentMenu.isActive = false;
                  children.click(children);
                }
              "
            >
              <v-list-item-icon>
                <v-icon size="20" class="ps-1 me-1" color="primary">
                  {{ children.icon }}
                </v-icon>
              </v-list-item-icon>
              <v-list-item-title style="font-weight: 500" class="pe-4">
                {{ $tr(children.title) }}
              </v-list-item-title>
              <v-list-item-icon>
                <v-icon
                  size="20"
                  class="d-none"
                  :class="{ 'd-block': activeChildItem == children.title }"
                  color="primary"
                >
                  mdi-check
                </v-icon>
              </v-list-item-icon>
            </v-list-item>
          </v-list>
        </v-menu>
        <v-list-item
          v-else
          link
          style="height: 35px"
          :key="item.icon"
          @click="item.click(item)"
        >
          <v-list-item-icon>
            <v-icon size="20" class="ps-1 me-1" color="primary">
              {{ item.icon }}
            </v-icon>
          </v-list-item-icon>
          <v-list-item-title style="font-weight: 500" class="pe-4">
            {{ $tr(item.title) }}
          </v-list-item-title>
          <v-list-item-icon>
            <v-icon
              size="20"
              class="d-none"
              :class="{ 'd-block': activeItem == item.title }"
              color="primary"
            >
              mdi-check
            </v-icon>
          </v-list-item-icon>
        </v-list-item>
        <v-divider
          class="custom-devider"
          v-if="item.divider"
          :key="item.title"
        ></v-divider>
      </template>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  props: {
    hideBtn: {
      type: Boolean,
      default: false,
    },
    activeItem: {
      type: String,
      default: null,
    },
    activeChildItem: {
      type: String,
      default: null,
    },
    items: {
      type: Array,
      required: true,
    },
    text: {
      type: String,
      default: null,
    },
    icon: {
      type: String,
      required: null,
    },
    outlinedIcon: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style></style>
