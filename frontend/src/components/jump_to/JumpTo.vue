<template>
  <client-only>
    <div class="jumpto">
      <v-btn
        ref="button"
        class="drawer-button ps-0"
        color="#00bc81"
        dark
        @click="openMenu"
      >
        <lottie-player
          src="/assets/jumpTo.json"
          speed="1"
          style="width: 40px; height: 40px"
          loop
          autoplay
        >
        </lottie-player>
      </v-btn>

      <v-navigation-drawer
        v-model="right"
        fixed
        :right="!$vuetify.rtl"
        hide-overlay
        temporary
        width="310"
        @shortkey.native="theAction"
        v-shortkey="{
          esc: ['esc'],
          ctrlP: ['ctrl', 'p'],
        }"
      >
        <div style="height: 12vh">
          <div class="d-flex align-center pa-2">
            <svg
              width="25.418"
              height="18.72"
              viewBox="0 0 25.418 18.72"
              class="me-1"
            >
              <path
                id="shoes-icon"
                d="M-2037.452,17.554q-4.949-.005-9.9,0c-.323,0-.645-.031-.639-.436s.336-.422.654-.421q2.474.005,4.949,0c1.65,0,3.3,0,4.949,0,.328,0,.648.047.642.437s-.326.42-.643.42Zm5.738-.862a.879.879,0,0,1-.4-.123q-7.358-4.076-14.711-8.158c-.04-.023-.077-.05-.166-.108l.863-1.623,4.52,2.6c3.159,1.817,6.314,3.641,9.481,5.443a1.983,1.983,0,0,0,.929.247c2.266.021,4.533.011,6.8.011h.509a1.619,1.619,0,0,1-1.6,1.706c-.919.013-1.838.017-2.758.017C-2029.4,16.7-2030.556,16.695-2031.714,16.691Zm-15.671-1.714c-.306,0-.6-.076-.6-.442s.3-.427.6-.428c.88,0,1.759,0,2.64,0,.864,0,1.728,0,2.592,0,.3,0,.6.035.6.429s-.3.439-.6.44l-2.88,0Zm16.351-.882a1.875,1.875,0,0,1-.839-.228c-.854-.462-1.687-.961-2.7-1.547.388-.321.722-.584,1.039-.865a.422.422,0,0,0,.065-.661c-.223-.24-.458-.148-.658.048-.3.3-.582.62-.9.9a.44.44,0,0,1-.391.079c-.472-.243-.923-.525-1.43-.82.525-.423,1.007-.817,1.5-1.2.248-.2.424-.42.183-.711-.219-.265-.469-.164-.7.023-.589.472-1.182.938-1.793,1.423l-1.4-.8c.626-.523,1.234-1.017,1.824-1.528.245-.212.678-.418.349-.824-.345-.425-.637-.049-.907.173-.607.5-1.2,1.014-1.823,1.5a.574.574,0,0,1-.477.073c-.214-.082-.476-.162-.444-.515.054-.579.079-1.162.092-1.743a3.228,3.228,0,0,0-1.324-2.731c-.47-.366-.953-.716-1.461-1.1.187-.3.36-.57.549-.87,1.173.7,2.324,1.374,3.454,2.08a1.115,1.115,0,0,0,1.136.1c1.232-.508,2.481-.976,3.733-1.434.164-.059.5-.034.55.061a.5.5,0,0,1-.146.7c-.416.283-.8.607-1.21.9a.536.536,0,0,0-.115.878c1.373,1.78,2.736,3.569,4.124,5.338a.686.686,0,0,0,.592.224,5.627,5.627,0,0,1,3.039-.231,12.471,12.471,0,0,0,1.379.1c.2,0,.408.036.611.052a1.811,1.811,0,0,1,1.7,1.754c.014.453,0,.906,0,1.415h-.546l-3.153,0Q-2029.282,14.108-2031.034,14.1Zm-16.487-1.751c-.179-.037-.32-.257-.48-.393.154-.147.3-.41.464-.421a15.278,15.278,0,0,1,2.07,0,.648.648,0,0,1,.454.412c.053.3-.192.426-.486.423-.221,0-.44,0-.66,0h-.377c-.2,0-.393.016-.588.016A1.975,1.975,0,0,1-2047.521,12.344Zm16.754-2.44c-1.21-1.584-2.428-3.161-3.676-4.782l.991-.749,3.332,5.555a.635.635,0,0,1-.352.161A.378.378,0,0,1-2030.766,9.9Zm-10.872-1.646c-1.18-.679-2.358-1.36-3.595-2.074l1.5-2.379c2.214,1.474,2.689,1.863,2.3,4.544A1.942,1.942,0,0,1-2041.638,8.258Zm-.6-6.823.867-1.436c.849.459,1.681.89,2.492,1.356a.759.759,0,0,1,.315.913c-.092.364-.207.721-.335,1.161Z"
                transform="translate(2048.745 0.667)"
                fill="currentColor"
                stroke="rgba(0,0,0,0)"
                stroke-width="1"
              />
            </svg>
            <p class="dialog-title mb-0">{{ $tr("jump_to") }}</p>
            <v-spacer></v-spacer>
            <CloseBtn width="40px" height="40px" @click="closeMenu" />
          </div>
          <v-text-field
            :class="`form-new-item jumpto-input mx-2`"
            background-color="var(--new-input-bg)"
            outlined
            hide-details
            autofocus
            :placeholder="$tr('search') + '...'"
            @input="onSearchChange"
          >
          </v-text-field>
        </div>

        <div class="pt-2">
          <v-list dense class="my-1 ms-2 jump-to-custom-list">
            <template v-for="(item, i) in menu">
              <div v-if="item.items && item.key" :key="i">
                <v-subheader v-if="item.key">
                  {{ $tr(item.key) }}
                </v-subheader>
                <v-list>
                  <template v-for="(item2, j) in item.items">
                    <JumpToMenuChildren
                      v-if="
                        item2.items &&
                        (isChildInScope(item2.items ? item2.items : []) ||
                          item2.showForAll)
                      "
                      :item="item2"
                      :key="(j + 1) * 100001"
                    />
                    <JumptoMenuItem
                      v-else
                      :key="(j + 1) * 10000"
                      :item="item2"
                    />
                  </template>
                </v-list>
              </div>
              <JumptoMenuItem v-else :key="(i + 3) * 238120398" :item="item" />
            </template>
          </v-list>
          <div class="" style="position: absolute; bottom: 0px">
            <JumToFooter />
          </div>
        </div>
      </v-navigation-drawer>
    </div>
  </client-only>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import Menu from "../../configs/navigation";
import JumptoMenuItem from "./JumptoMenuItem.vue";
import JumpToMenuChildren from "./JumpToMenuChildren.vue";
import JumToFooter from "./JumToFooter.vue";

export default {
  data() {
    return {
      right: false,
      search: "",
      timeout: null,
      menu: JSON.parse(JSON.stringify(Menu.menu)),
      typingTimer: null,
    };
  },
  components: {
    CloseBtn,
    JumptoMenuItem,
    JumpToMenuChildren,
    JumToFooter,
  },

  mounted() {
    this.animate();
  },
  beforeDestroy() {
    if (this.timeout) clearTimeout(this.timeout);
  },
  methods: {
    animate() {
      if (this.timeout) clearTimeout(this.timeout);
      const time = (Math.floor(Math.random() * 10 + 1) + 10) * 1000;
      this.timeout = setTimeout(() => {
        this.$animate(this.$refs.button.$el, "bounce");
        this.animate();
      }, time);
    },
    async closeMenu() {
      this.right = false;
    },
    openMenu() {
      this.right = true;
    },
    isChildInScope(children) {
      for (let child of children) {
        if (this.$isInScope(child.scope)) {
          return this.$isInScope(child.scope);
        }
      }
      return false;
    },
    debounce(callback, wait) {
      clearTimeout(this.typingTimer);
      this.typingTimer = setTimeout(callback, wait);
    },
    onSearchChange(search) {
      const callback = () => this.searchMenu(search);
      this.debounce(callback, 0);
    },

    formatItems(items, search) {
      let temp = [];
      if (items && items?.length > 0) {
        const s = (item, search) =>
          this.$tr(item.key).toLowerCase().indexOf(search) >= 0;
        items.forEach((el) => {
          if (el.items) {
            if (s(el, search)) {
              temp = [...temp, el];
              el.items.forEach((el2) => {
                if (s(el2, search)) {
                  temp = [...temp, el2];
                }
              });
            } else {
              temp = [...temp, ...el.items];
            }
          } else {
            temp = [...temp, el];
          }
        });
      }
      return temp;
    },

    searchMenu(search) {
      search = search.trim().toLowerCase();
      const searchMenu = JSON.parse(JSON.stringify(Menu.menu));
      if (search == "") {
        this.menu = searchMenu;
        return;
      }
      const s = (item, search) =>
        this.$tr(item.key).toLowerCase().indexOf(search) >= 0;
      const cfs = (item) => this.$isInScope(item.scope) || item.showForAll;
      const filter3 = (item3) => {
        return s(item3, search) && cfs(item3);
      };
      const filter2 = (item2) => {
        if (item2.items) {
          if (s(item2, search)) {
            return this.isChildInScope(item2.items);
          } else {
            item2.items = item2.items.filter(filter3);
            return item2.items.length > 0;
          }
        } else {
          return s(item2, search) && cfs(item2);
        }
      };
      const filter1 = (item) => {
        if (item.items) {
          if (s(item, search)) {
            return this.isChildInScope(item.items);
          }
          item.items = item.items.filter(filter2);
          item.items = this.formatItems(item.items, search);
          return item.items.length > 0;
        } else {
          return s(item, search) && cfs(item);
        }
      };
      this.menu = searchMenu.filter(filter1);
    },

    theAction(event) {
      if (event.srcKey == "esc") {
        this.closeMenu();
      }
      if (event.srcKey == "ctrlP") {
        this.openMenu();
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.drawer-button {
  position: fixed;
  bottom: 125px;
  right: -20px;
  z-index: 6;
  box-shadow: 1px 1px 18px #00bc81;

  .v-icon {
    margin-left: -18px;
    font-size: 1.3rem;
  }
}
.v-application--is-rtl {
  .drawer-button {
    right: unset;
    left: -20px;
    .v-icon {
      margin-right: -18px;
      margin-left: unset;
    }
  }
}
.dialog-title {
  font-size: 20px;
  font-weight: 600;
  color: #777;
}
</style>
<style>
.j-menu-icon {
  display: flex;
  align-items: center;
}
.j-menu-icon svg {
  height: 18px;
  width: 18px;
  fill: currentColor;
}
.jumpto .v-subheader {
  font-size: 14px !important;
  font-weight: 400 !important;
}
.jumpto .v-list-item {
  color: #777 !important;
  border-radius: 8px;
}
.jumpto .v-list-item::before {
  border-radius: 8px;
}
.jumpto .jumpto-input .v-input__slot {
  min-height: 36px;
}
</style>

<style>
.jump-to-custom-list {
  max-height: 72vh;
  overflow-y: auto;
}
.jump-to-custom-list::-webkit-scrollbar {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
  border-radius: 10px !important;
  background-color: #f5f5f5 !important;
}
.jump-to-custom-list::-webkit-scrollbar {
  width: 10px;
  background-color: var(--v-surface-base);
}
.jump-to-custom-list::-webkit-scrollbar:hover {
  cursor: alias !important;
}
.jump-to-custom-list::-webkit-scrollbar-thumb:hover {
  cursor: alias !important;
}

.jump-to-custom-list::-webkit-scrollbar-thumb {
  border-radius: 10px;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
  background-color: var(--v-background-darken2);
}
</style>
