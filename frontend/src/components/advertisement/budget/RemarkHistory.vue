<template>
  <v-card-text>
    <div
      class="remarks-container"
      ref="remarksContainer"
      style="position: relative"
    >
      <div v-if="!api_loading">
        <div v-if="remarks.length > 0">
          <SingleRemark
            v-for="(remark, i) in remarks"
            :key="i"
            :remark="remark"
            @changeReactionCount="changeReactionCount(i, $event)"
            @deleteHandler="deleteHandler"
          />
        </div>
        <div
          v-else
          class="text-center"
          style="
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            left: 50%;
          "
        >
          <NoRemark />
          <p
            class="mb-0 text-h6"
            style="font-size: 20px; font-weight: 500; color: #777"
          >
            {{ $tr("no_remark_activity") }}
          </p>
        </div>
      </div>
      <div v-else class="">
        <v-skeleton-loader
          v-for="i in 2"
          :key="i"
          v-bind="attrs_sk"
          type=" list-item-avatar, list-item-three-line"
        ></v-skeleton-loader>
      </div>
    </div>
  </v-card-text>
</template>

<script>
import NoRemark from "../remarks/NoRemark.vue";
import SingleRemark from "../remarks/SingleRemark.vue";
export default {
  data() {
    return {
      api_loading: false,
      attrs_sk: {
        style: "margin-bottom:0.2px",
        boilerplate: true,
        elevation: 0,
      },
      remarks: [],
    };
  },
  props: {
    selected_items: Object,
  },
  async created() {
    this.api_loading = true;
    await this.fetchAllData(this.selected_items.dateRange);
    this.api_loading = false;
    this.scrollDown();
  },
  methods: {
    async toggleDialog() {
      this.api_loading = true;
      await this.fetchAllData();
      this.api_loading = false;
      this.scrollDown();
    },
    async fetchAllData(DateRange = {}) {
      try {
        let data = {
          model: this.selected_items.model,
          id: this.getId(),
          filter: this.comment_category,
        };
        data = { ...data, ...DateRange };
        let res = await this.$axios.get("/remarks", {
          params: data,
        });
        this.remarks = res?.data?.data;
        let totals = {
          totals: [
            { name: this.$tr("total_remarks"), total: this.remarks.length },
          ],
        };
        this.$emit("setTitleData", totals);
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    getId() {
      switch (this.selected_items.model) {
        case "ad":
          return this.selected_items.item?.ad_id;
        case "ad_set":
          return this.selected_items.item?.adset_id;
        case "campaign":
          return this.selected_items.item?.campaign_id;
        case "item_code":
          return this.selected_items.item?.pcode;
        default:
          return this.selected_items.item?.id;
      }
    },
    scrollDown() {
      setTimeout(() => {
        this.$refs.remarksContainer.scrollTo(
          0,
          this.$refs.remarksContainer.scrollHeight
        ),
          20;
      });
    },
    changeReactionCount(i, data = null) {
      this.remarks[i].reactions = this.remarks[i].reactions.filter(
        (row) => row.user_id != this.$auth.user.id
      );
      if (data) {
        this.remarks[i].reactions.push(data);
      }
      this.remarks = [...this.remarks];
    },
    onDateRangeSubmit(range) {
      const timeRange = {
        start_date: range.start_date,
        end_date: range.end_date,
      };
      this.fetchAllData(timeRange);
    },
    async deleteHandler(id) {
      this.remarks = this.remarks.filter((item) => item.id != id);
    },
  },
  components: { NoRemark, SingleRemark },
};
</script>

<style scoped>
.remarks-container {
  max-height: 600px;
  min-height: 400px;
  overflow-y: auto;
  scroll-behavior: smooth;
}

.dialog-title {
  font-size: 20px;
  font-weight: 600;
  color: #777;
}

.remarks-number {
  text-align: center;
  height: 24px;
  min-width: 24px;
  font-size: 12px;
  line-height: 24px;
  /* padding: 0 4px; */
  border-radius: 13px;
  background: var(--v-primary-lighten4);
  color: var(--v-primary-base);
}

.remarks-container::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

/* Track */
.remarks-container::-webkit-scrollbar-track {
  background: transparent;
}

/* Handle */
.remarks-container::-webkit-scrollbar-thumb {
  background: #777;
  border-radius: 5px;
  position: absolute;
}

/* Handle on hover */
.remarks-container::-webkit-scrollbar-thumb:hover {
  background: #777;
}
</style>
<style>
.remarks-select {
  width: 156px;
  font-weight: 500;
  font-size: 14px;
}

.remarks-select .v-select__selection {
  font-size: 12px !important;
}

.remarks-select .v-input__slot {
  min-height: 32px !important;
}

.remarks-select .custom-input .v-input__append-inner {
  margin-top: 3px !important;
}

.remarks-input .v-input__slot {
  min-height: 32px;
}

.remarks-input .v-input__append-inner {
  margin-top: 3px !important;
}
</style>
