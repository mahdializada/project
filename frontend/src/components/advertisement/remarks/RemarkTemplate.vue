<template>
  <div class="mb-1 px-2">
    <div
      class="dialog-title mb-1 py-auto d-flex align-center"
      style="height: 40px; background-color: #f7f8fc"
    >
      Remarks
    </div>
    <div v-if="!api_loading">
      <div
        id="graph-table-scroll"
        ref="remarksContainer"
        style="
          position: relative;
          max-height: 520px;
          min-height: 520px;
          width: 100%;
          overflow: auto;
        "
      >
        <div v-if="remark_data.length > 0">
          <single-remark
            v-for="(remark, i) in remark_data"
            :key="i"
            :remark="remark"
            @deleteHandler="deleteHandler"
            @changeReactionCount="changeReactionCount(i, $event)"
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
          <no-remark width="170" height="170" />
        </div>
      </div>
    </div>

    <div
      v-else
      class=""
      style="max-height: 520px; min-height: 520px; width: 100%"
    >
      <v-skeleton-loader
        v-for="i in 4"
        :key="i"
        v-bind="attrs_sk"
        type=" list-item-avatar,list-item-two-line"
      ></v-skeleton-loader>
    </div>
    <div class="mt-3">
      <v-form @submit.prevent="submit()">
        <v-text-field
          :class="`form-new-item remarks-input`"
          background-color="var(--new-input-bg)"
          outlined
          dense
          hide-details
          :placeholder="$tr('type_here') + '...'"
          v-model.trim="remark"
        >
          <template slot="append" class="pe-0">
            <PopOver v-model="showSubmitErr" :right="true" :indicator="10">
              <template v-slot:activator>
                <v-btn
                  fab
                  x-small
                  text
                  color="primary"
                  class="ma-0"
                  @click="submit"
                >
                  <v-icon dark size="20"> mdi-send </v-icon>
                </v-btn>
              </template>
              <template v-slot:body>
                <div>
                  {{ $tr("please_wait_30_seconds_before_your_next_remark") }}
                </div>
              </template>
            </PopOver>
          </template>
        </v-text-field>
      </v-form>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import PopOver from "../../design/PopOver.vue";
import NoRemark from "./NoRemark.vue";
import SingleRemark from "./SingleRemark.vue";
export default {
  components: { SingleRemark, NoRemark, PopOver },
  props: {
    selected_items: {
      type: Object,
      default: () => {
        return { id: "11" };
      },
    },
    model: {
      type: String,
      default: "",
    },
    comment_category: {
      type: String,
      default: "latest",
    },
  },
  data() {
    return {
      api_loading: false,
      remark_data: [],
      remark: "",
      showSubmitErr: false,
      attrs_sk: {
        style: "margin-bottom:0.2px",
        boilerplate: true,
        elevation: 0,
      },
    };
  },
  created() {
    this.fetchRemarks();
  },
  methods: {
    getId() {
      switch (this.model) {
        case "ad":
          return this.selected_items?.ad_id;
        case "ad_set":
          return this.selected_items?.adset_id;
        case "campaign":
          return this.selected_items?.campaign_id;
        case "item_code":
          return this.selected_items?.pcode;
        default:
          return this.selected_items?.id;
      }
    },
    async deleteHandler(id) {
      this.remark_data = this.remark_data.filter((item) => item.id != id);
    },

    changeReactionCount(i, data = null) {
      this.remark_data[i].reactions = this.remark_data[i].reactions.filter(
        (row) => row.user_id != this.$auth.user.id
      );
      if (data) {
        this.remark_data[i].reactions.push(data);
      }
      this.remark_data = [...this.remark_data];
    },

    async submit() {
      let canComment = this.checkDate();
      if (canComment < 2) {
        try {
          if (this.remark != "") {
            let reqData = {
              model: "content_library_media",
              id: this.getId(),
              remark: this.remark,
            };
            let { data } = await this.$axios.post("/remarks", reqData);
            if (data.result) {
              this.remark_data.push(data.data);
              this.remark = "";
              this.scrollDown();
            }
          }
        } catch (error) {
          console.log("remark error", error);
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
        this.showSubmitErr = false;
      } else {
        this.showSubmitErr = true;
      }
    },
    checkDate() {
      let now = moment().format("DD/MM/YYYY HH:mm:ss");
      let comment_count = 0;
      this.remark_data.map((item) => {
        if (item.user_id == this.$auth.user.id) {
          let ms = moment(now, "DD/MM/YYYY HH:mm:ss").diff(
            moment(item.created_at)
          );
          if (ms < 30000) {
            comment_count++;
          }
        }
      });
      return comment_count;
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

    async fetchRemarks() {
      try {
        this.api_loading = true;
        let data = {
          model: this.model,
          id: this.getId(),
          filter: this.comment_category,
        };
        let res = await this.$axios.get("/remarks", {
          params: data,
        });
        this.remark_data = res?.data?.data;
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
      this.api_loading = false;
    },
  },
};
</script>

<style>
</style>