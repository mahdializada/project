<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="1000" persistent>
      <v-card>
        <v-card-title class="text-h5 grey lighten-2 pa-2">
          <div class="d-flex">
            {{ $tr("product_view") }}
          </div>
          <div class="ml-auto">
            <CloseBtn @click="dialog = false" />
          </div>
        </v-card-title>

        <v-card-text style="height: 500px" class="pa-0">
          <div class="d-flex flex-row">
            <v-card style="width: 35%; height: 500px" class="pl-0">
              <div class="">
                <div class="" style="width: 100%">
                  <CustomCarousel
                    :items="itemsImage"
                    class="pb-2"
                    :height="'200px'"
                    :show_arrows="true"
                    :cycle="false"
                    :show_arrows_on_hover="true"
                    ref="CarouselRef"
                  />
                </div>
                <div class="ml-2">
                  <h4>{{ $tr("images") }}</h4>
                </div>
                <div class="d-flex flex-row flex-wrap ml-1" style="width: 100%">
                  <div
                    v-for="(item, index) in images"
                    :key="index"
                    class="d-flex mt-1"
                    style="
                      width: 90px;
                      height: 88px;
                      margin-right: 6px;
                      position: relative;
                    "
                  >
                    <img
                      :src="item.image"
                      width="100%"
                      style="border-radius: 8px"
                      alt=""
                      class="cursor-pointer"
                      @click="$refs.CarouselRef.current_index = index"
                    />
                    <v-avatar
                      color="#9e9e9e57"
                      class="position-absolute"
                      style="top: 30%; left: 28%"
                      size="40"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 512 512"
                        style="width:20px; height:20px; color:white"
                      >
                        <path
                          fill="currentColor"
                          d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"
                        />
                      </svg>
                    </v-avatar>
                  </div>
                </div>
              </div>
            </v-card>
            <v-card class="mx-2 mt-2" style="width: 65%">
              <v-tabs
                v-model="tab"
                style="width: 70%"
                height="40"
                active-class="activeClass"
                show-arrows
                :hide-slider="true"
                class="custom"
                center-active
              >
                <v-tab
                  v-for="item in items1"
                  :key="item.tab"
                  class="customTab px-3 me-1"
                  @click="tabChange"
                >
                  {{ item.tab }}
                </v-tab>
              </v-tabs>

              <v-tabs-items v-model="tab">
                <v-tab-item>
                  <v-card
                    flat
                    style="overflow: hidden; overflow-y: auto; width: 100%"
                  >
                    <v-card-text class="pa-0" style="height: 435px">
                      <v-container style="padding: 0">
                        <v-row class="mx-2">
                          <v-col cols="4" class="pa-1 mt-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("description") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1 mt-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.description
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="background-color: #f9f9f9" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("name") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.name
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("pcode") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.pcode
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="background-color: #f9f9f9" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("parent_sku") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.parent_sku
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("type") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.type
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="background-color: #f9f9f9" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("number_of_sales") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.number_of_sales
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("available") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.available
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="background-color: #f9f9f9" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("unit") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.unit
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("vat_tax") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.VAT_tax
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="background-color: #f9f9f9" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("published") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.is_published == 1 ? "yes" : "no"
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("supplier") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item.supplier?.supplier_name
                            }}</strong>
                          </v-col>
                        </v-row>
                        <v-row style="background-color: #f9f9f9" class="mx-2">
                          <v-col cols="4" class="pa-1">
                            <h4 style="color: #707070; font-size: 1rem">
                              {{ $tr("brand") }}
                            </h4>
                          </v-col>
                          <v-col
                            cols="7"
                            class="pa-1"
                            id="span_right_side_styles"
                          >
                            <strong style="color: #777; font-size: 1rem">{{
                              item?.brand?.name
                            }}</strong>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <v-card
                    flat
                    style="overflow: hidden; overflow-y: auto; width: 100%"
                  >
                    <v-card-text class="pa-0" style="height: 435px">
                      <v-container style="padding: 0">
                        <v-expansion-panels class="px-2 mt-2">
                          <v-expansion-panel
                            v-for="inventory in item.inventory_ssp"
                            :key="inventory.id"
                          >
                            <v-expansion-panel-header>
                              <v-col cols="12" class="pa-0" md="12">
                                <v-avatar color="primary" size="35">
                                  <svg
                                    style="
                                      width: 25px;
                                      height: 25px;
                                      color: white;
                                    "
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 640 512"
                                  >
                                    <path
                                      fill="currentColor"
                                      d="M0 488V171.3c0-26.2 15.9-49.7 40.2-59.4L308.1 4.8c7.6-3.1 16.1-3.1 23.8 0L599.8 111.9c24.3 9.7 40.2 33.3 40.2 59.4V488c0 13.3-10.7 24-24 24H568c-13.3 0-24-10.7-24-24V224c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32V488c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24zm488 24l-336 0c-13.3 0-24-10.7-24-24V432H512l0 56c0 13.3-10.7 24-24 24zM128 400V336H512v64H128zm0-96V224H512l0 80H128z"
                                    />
                                  </svg>
                                </v-avatar>
                                &nbsp;&nbsp;
                                <span>
                                  {{ $tr("inventroy") }}
                                </span>
                              </v-col>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <div>
                                <h4 class="ml-2" style="color: #bdbdbd">
                                  {{ $tr("general_info") }}
                                </h4>
                              </div>
                              <v-container style="padding: 0">
                                <v-row class="mx-2">
                                  <v-col cols="4" class="pa-1 mt-1">
                                    <h4 style="color: #707070; font-size: 1rem">
                                      {{ $tr("sku") }}
                                    </h4>
                                  </v-col>
                                  <v-col
                                    cols="7"
                                    class="pa-1 mt-1"
                                    id="span_right_side_styles"
                                  >
                                    <strong
                                      style="color: #777; font-size: 1rem"
                                      >{{ inventory.sku }}</strong
                                    >
                                  </v-col>
                                </v-row>
                                <v-row
                                  style="background-color: #f9f9f9"
                                  class="mx-2"
                                >
                                  <v-col cols="4" class="pa-1">
                                    <h4 style="color: #707070; font-size: 1rem">
                                      {{ $tr("quantity") }}
                                    </h4>
                                  </v-col>
                                  <v-col
                                    cols="7"
                                    class="pa-1"
                                    id="span_right_side_styles"
                                  >
                                    <strong
                                      style="color: #777; font-size: 1rem"
                                      >{{ inventory.quantity }}</strong
                                    >
                                  </v-col>
                                </v-row>
                                <v-row class="mx-2">
                                  <v-col cols="4" class="pa-1 mt-1">
                                    <h4 style="color: #707070; font-size: 1rem">
                                      {{ $tr("price_per_unit") }}
                                    </h4>
                                  </v-col>
                                  <v-col
                                    cols="7"
                                    class="pa-1 mt-1"
                                    id="span_right_side_styles"
                                  >
                                    <strong
                                      style="color: #777; font-size: 1rem"
                                      >{{ inventory.price_per_unit }}</strong
                                    >
                                  </v-col>
                                </v-row>
                                <!-- <v-row
                                  style="background-color: #f9f9f9"
                                  class="mx-2"
                                >
                                  <v-col cols="4" class="pa-1">
                                    <h4 style="color: #707070; font-size: 1rem">
                                      {{ $tr("quantity") }}
                                    </h4>
                                  </v-col>
                                  <v-col
                                    cols="7"
                                    class="pa-1"
                                    id="span_right_side_styles"
                                  >
                                    <strong
                                      style="color: #777; font-size: 1rem"
                                      >{{ inventory.quantity }}</strong
                                    >
                                  </v-col>
                                </v-row> -->
                              </v-container>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>
                      </v-container>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
              </v-tabs-items>
            </v-card>
          </div>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="dialog = false"> I accept </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import CustomCarousel from "../../common/CustomCarousel.vue";
import TableTabs from "../../common/TableTabs.vue";
import CloseBtn from "../../design/Dialog/CloseBtn.vue";

export default {
  data() {
    return {
      dialog: false,
      itemsImage: [
        "/images/1.jpg",
        "/images/2.jpg",
        "/images/3.jpg",
        "/images/4.jpg",
      ],
      images: [
        { id: "1", image: "/images/1.jpg" },
        { id: "2", image: "/images/2.jpg" },
        { id: "3", image: "/images/3.jpg" },
        { id: "4", image: "/images/4.jpg" },
      ],
      tab: null,
      contents: [],
      general_info: true,
      items1: [
        { tab: "general_info", content: "Tab 1 Content" },
        { tab: "inventory", content: "Tab 2 Content" },
      ],
      item: [],
      inventoryItem: [],
    };
  },
  methods: {
    showViewDialog(items) {
      this.dialog = !this.dialog;
      console.log(items);
      this.item = items;
    },
    tabChange() {},
  },
  components: { CloseBtn, CustomCarousel, TableTabs },
};
</script>

<style scoped>
.custome .v-tab {
  border-bottom: 1px solid var(--v-surface-darken1);
  border-top: 1px solid transparent;
}
.customTab.v-tab {
  max-width: unset !important;
}
.customTab {
  display: flex;
  justify-content: space-between;
  border-top-right-radius: 8px;
  border-top-left-radius: 8px;
  position: relative;
  background-color: var(--v-primary-base);
  border-top: 0.2px solid var(--v-surface-lighten1);
  border-left: 0.2px solid var(--v-surface-lighten1);
  width: 100%;
}
.customTab::before {
  background-color: unset !important;
}
.customTab:after {
  content: " ";
  position: absolute;
  display: block;
  width: 30px;
  height: 100%;
  top: 0;
  right: 0;
  z-index: -1;
  background-color: var(--v-primary-base);
  border-top-right-radius: 8px;
  border-top-left-radius: 8px;
  transform-origin: top right;
  -ms-transform: skew(25deg, 0);
  -webkit-transform: skew(25deg);
  transform: skew(25deg);
  border-right: 0.2px solid var(--v-surface-lighten1);
}
.activeClass:after {
  background-color: var(--v-surface-lighten1);
  border-right: 0.2px solid var(--v-tabBackground-darken2);
}

.activeClass {
  background-color: var(--v-surface-lighten1);
  color: var(--v-primary-base) !important;
  border-top: 0.2px solid var(--v-tabBackground-darken2);
  border-left: 0.2px solid var(--v-tabBackground-darken2);
}
</style>
