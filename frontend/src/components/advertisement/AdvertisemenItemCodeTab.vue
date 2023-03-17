<template>
  <v-tabs
    v-model="currentTabIndex"
    height="40"
    background-color="tabBackground"
    active-class="activeClass"
    show-arrows
    :hide-slider="true"
    class=" mt-1"
    center-active
    id="tabs"
  >
    <client-only>
      <v-tab
        v-for="(item, i) in itemCodeTab"
        :key="i"
        :id="i"
        :class="` me-1 customTab ${!$vuetify.rtl ? 'ltrTab' : 'rtlTab'}
        ${item.isSelected ? '' : item.status}
        `"
        :style="`z-index: ${
          item.isSelected ? itemCodeTab.length + 1 : itemCodeTab.length - i
        };background-color:${item.isSelected ? '' : item.color}`"
        @click="onChange(item.status)"
      >
        <!-- :style="'color:' + " -->

        <span
          class="d-flex align-center"
          :style="`color:  ${item.isSelected ? item.color : 'white'}`"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16.707"
            height="22.028"
            viewBox="0 0 11.707 17.028"
          >
            <path
              id="status"
              fill="currentColor"
              d="M-6488.871-3470.972A2.131,2.131,0,0,1-6491-3473.1v-10.855a2.12,2.12,0,0,1,.294-1.079,2.136,2.136,0,0,1,.771-.765v.034a2.237,2.237,0,0,0,2.235,2.235h5.108a2.237,2.237,0,0,0,2.234-2.235v-.034a2.143,2.143,0,0,1,.771.764,2.13,2.13,0,0,1,.293,1.08v10.855a2.131,2.131,0,0,1-2.128,2.128Zm2.927-9.7a3.627,3.627,0,0,0-1.06.424,3.627,3.627,0,0,0-.894.755,3.616,3.616,0,0,0-.606,1,3.623,3.623,0,0,0-.254,1.114,3.62,3.62,0,0,0,.1,1.136,3.618,3.618,0,0,0,.47,1.071,3.609,3.609,0,0,0,.787.865,3.616,3.616,0,0,0,1,.56,3.621,3.621,0,0,0,1.12.221l.135,0a3.612,3.612,0,0,0,1.025-.149,3.6,3.6,0,0,0,1.053-.508,3.6,3.6,0,0,0,.819-.8,3.624,3.624,0,0,0,.531-1.01,3.6,3.6,0,0,0,.189-1.155v-.294a.426.426,0,0,0-.425-.426.426.426,0,0,0-.426.426v.293a2.782,2.782,0,0,1-1.982,2.652,2.714,2.714,0,0,1-.775.112,2.8,2.8,0,0,1-1.317-.33,2.761,2.761,0,0,1-1.014-.929,2.784,2.784,0,0,1,.218-3.3,2.771,2.771,0,0,1,2.112-.967,2.731,2.731,0,0,1,1.117.237.421.421,0,0,0,.173.037.425.425,0,0,0,.389-.252.426.426,0,0,0,.009-.326.424.424,0,0,0-.225-.236,3.614,3.614,0,0,0-1.131-.3c-.114-.01-.229-.016-.342-.016A3.634,3.634,0,0,0-6485.944-3480.674Zm-.461,2.914a.423.423,0,0,0-.125.3.422.422,0,0,0,.125.3l.958.958a.428.428,0,0,0,.3.125.422.422,0,0,0,.3-.125l3.193-3.2a.42.42,0,0,0,.125-.3.422.422,0,0,0-.125-.3.423.423,0,0,0-.3-.125.421.421,0,0,0-.3.125l-2.892,2.895-.657-.657a.423.423,0,0,0-.3-.125A.423.423,0,0,0-6486.405-3477.76Zm-1.3-6.622a1.383,1.383,0,0,1-1.383-1.383v-.037a.094.094,0,0,0,0-.012.027.027,0,0,0,0-.006.126.126,0,0,1,0-.013.012.012,0,0,1,0,0c0-.005,0-.01,0-.016h0a1.429,1.429,0,0,1,.022-.172s0,0,0,0l0-.011a1.384,1.384,0,0,1,1.356-1.108h.54a.768.768,0,0,1-.007-.107.744.744,0,0,1,.745-.745h2.554a.745.745,0,0,1,.745.745.7.7,0,0,1-.008.107h.54a1.383,1.383,0,0,1,1.378,1.265h0v.021c0,.005,0,.01,0,.015a.016.016,0,0,1,0,0s0,.01,0,.014,0,0,0,.005a.13.13,0,0,0,0,.014v.005s0,.01,0,.015v.023a1.383,1.383,0,0,1-1.383,1.383Z"
              transform="translate(6491 3487.999)"
            />
          </svg>
        </span>
        <p
          :class="` ${
            item.isSelected ? 'selected' : 'not-selected'
          }  mb-0 mx-2`"
          :style="`white-space: nowrap;color:${
            item.isSelected ? item.color : 'white'
          }`"
        >
          {{ $tr(item.status) }}
        </p>
        <v-chip
          class="tab-chip"
          :color="item.isSelected ? item.color : 'white'"
          :text-color="item.isSelected ? 'white' : item.color"
          small
          v-text="item.count??'0'"
        >
        </v-chip>
      </v-tab>
    </client-only>
  </v-tabs>
</template>
<script>
export default {
  props: {
    tabs: {
      required: true,
    },
  },
  watch: {
    currentTabIndex: function (index) {
      this.itemCodeTab = this.itemCodeTab.map((row) => {
        row.isSelected = 0;
        return row;
      });
      this.itemCodeTab[index].isSelected = 1;
    },
    tabs: function () {
      this.getCount();
    },
  },

  data() {
    return {
      currentTabIndex: 0,
      isTabChange: false,
      addItem: [],
      itemCodeTab: [
              {
                status: "all",
                count: 0,
                isSelected: 1,
                color: "#2E7BE6 !important",
              },
              // {
              //   status: "new_item",
              //   count: 0,
              //   isSelected: 0,
              //   color: "#007f35 !important",
              // },
              // {
              //   status: "in_study",
              //   count: 0,
              //   isSelected: 0,
              //   color: "#37383a !important",
              // },
              // {
              //   status: "create_content",
              //   count: 0,
              //   isSelected: 0,
              //   color: "#fa8059 !important",
              // },
              // {
              //   status: "content_review",
              //   count: 0,
              //   isSelected: 0,
              //   color: "#8a53fd !important",
              // },
              {
                status: "ready_to_sale",
                count: 0,
                isSelected: 0,
                color: "#0049b0 !important",
              },
              {
                status: "in_creation",
                count: 0,
                isSelected: 0,
                color: "#767171 !important",
              },
              {
                status: "in_testing",
                count: 0,
                isSelected: 0,
                color: "#5267b6 !important",
              },
              {
                status: "in_sales",
                count: 0,
                isSelected: 0,
                color: "#09d5a2 !important",
              },
              {
                status: "retesting",
                count: 0,
                isSelected: 0,
                color: "#450e50 !important",
              },
              {
                status: "waiting_for_md",
                count: 0,
                isSelected: 0,
                color: "#e08ea5 !important",
              },
              {
                status: "short_stop",
                count: 0,
                isSelected: 0,
                color: "#fc9b24 !important",
              },
              {
                status: "final_stop",
                count: 0,
                isSelected: 0,
                color: "#fe1619 !important",
              },
              {
                status: "cancel",
                count: 0,
                isSelected: 0,
                color: "#fe58bc !important",
              },
              {
                status: "remove",
                count: 0,
                isSelected: 0,
                color: "#bb732b !important",
              },
            ],
    };
  },

  methods: {
    onChange(status) {
      this.$emit("onChange", status);
    },
    getCount() {
      try {
        const counts1 = this.tabs[4]?.count?.item_code_tabs;
        this.itemCodeTab = this.addItem.length>0?this.addItem:this.itemCodeTab.map((item) => {
          if (counts1) {
            item.count = counts1[item.status];
          }
          return item;
        });
      } catch (error) {
        return "0";
      }
    },
    changeCount(item) {
      this.addItem = [];
      this.itemCodeTab = this.itemCodeTab.map((i,index) => {
        if (i.status == item.prev_status) {
          i.count -= i.count > 0 ? 1 : 0;
        }
        if (i.status == item.item_status.status) {
          i.count++;
        }
        this.addItem.push(i);
        return i;
      });
    },
    resetItem(){
      this.addItem = [];
    }
  },
};
</script>

<style lang="scss" scoped>
.customTab.v-tab {
  width: fit-content !important;
}
.customTab {
  display: flex;
  justify-content: space-between;
  border-top-right-radius: 8px;
  border-top-left-radius: 8px;
  position: relative;
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

.all:after {
  background: #2e7be6;
}
.new_item:after {
  background: #007f35;
}
.in_study:after {
  background: #37383a;
}
.create_content:after {
  background: #fa8059;
}
.content_review:after {
  background: #8a53fd;
}
.ready_to_sale:after {
  background: #0049b0;
}
.in_creation:after {
  background: #767171;
}
.in_testing:after {
  background: #5267b6;
}
.in_sales:after {
  background: #09d5a2;
}
.waiting_for_md:after {
  background: #e08ea5;
}
.short_stop:after {
  background: #fc9b24;
}
.final_stop:after {
  background: #fe1619;
}
.retesting:after {
  background: #450e50;
}
.cancel:after {
  background: #fe58bc;
}
.remove:after {
  background: #bb732b;
}
</style>
