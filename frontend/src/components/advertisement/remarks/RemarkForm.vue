<template>
  <v-dialog v-if="showDialog" v-model="showDialog" width="800" persistent>
    <v-card>
      <v-card-title class="px-2 pt-2 pb-2" style="color: #777">
        <div class="dialog-title d-flex align-center">
          {{ $tr("remarks") }}
          <div
            class="remarks-number ms-1"
            :style="`background: ${$vuetify.theme.defaults.light.primary}33`"
          >
            {{ remarks.length }}
          </div>
        </div>
        <v-spacer></v-spacer>
        <div class="d-flex align-center">
          <CustomInput
            v-model="comment_category"
            :items="
              this.comment_categories.map((item) => {
                item.text = this.$tr(item.text);
                return item;
              })
            "
            :type="'select'"
            :multiple="false"
            hide-details
            item-text="text"
            item-value="value"
            class="me-2 remarks-select"
          />
          <svg
            @click="toggleDialog()"
            width="42px"
            height="42px"
            viewBox="0 0 700 560"
            fill="currentColor"
            style="transform: scaleY(-1); cursor: pointer"
          >
            <g>
              <path
                d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
              />
              <path
                d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
              />
            </g>
          </svg>
        </div>
      </v-card-title>
      <v-card-text>
        <div
          class="remarks-container"
          ref="remarksContainer"
          style="position: relative"
        >
          <div v-if="!api_loading">
            <div v-if="sortRemarks().length > 0">
              <SingleRemark
                v-for="(remark, i) in sortRemarks()"
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
              <NoRemark />
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
        <div class="mt-3">
          <v-form @submit.prevent="submit">
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
                      {{
                        $tr("please_wait_30_seconds_before_your_next_remark")
                      }}
                    </div>
                  </template>
                </PopOver>
              </template>
            </v-text-field>
          </v-form>
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import CustomInput from "../../design/components/CustomInput.vue";
import SingleRemark from "./SingleRemark.vue";
import moment from "moment-timezone";
import PopOver from "../../design/PopOver.vue";
import NoRemark from "./NoRemark.vue";

export default {
  props: {
    selected_items: Array,
    tab: String,
    sub_system: {
      type: String,
      default: "advertisement",
    },
  },
  data() {
    return {
      showDialog: false,
      remark: "",
      remarks: [],
      attrs_sk: {
        style: "margin-bottom:0.2px",
        boilerplate: true,
        elevation: 0,
      },
      api_loading: false,
      comment_categories: [
        {
          text: "latest",
          value: "latest",
        },
        {
          text: "top_comments",
          value: "top_comments",
        },
      ],
      comment_category: "latest",
      showSubmitErr: false,
    };
  },
  methods: {
    async toggleDialog() {
      this.showDialog = !this.showDialog;
      if (this.showDialog) {
        this.api_loading = true;
        await this.fetchAllData();
        this.api_loading = false;
        this.scrollDown();
      } else {
        this.remark = "";
      }
    },
    async fetchAllData() {
      try {
        let data = {
          model: this.tab,
          id: this.getId(),
          filter: this.comment_category,
          sub_system: this.sub_system,
        };
        let res = await this.$axios.get("/remarks", {
          params: data,
        });
        this.remarks = res?.data?.data;
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    async submit() {
      console.log(this.selected_items);
      let canComment = this.checkDate();
      if (canComment < 2) {
        try {
          if (this.remark != "") {
            let reqData = {
              model: this.tab,
              id: this.getId(),
              remark: this.remark,
              sub_system: this.sub_system,
            };
            let { data } = await this.$axios.post("/remarks", reqData);
            if (data.result) {
              this.remarks.push(data.data);
              this.$emit("addRemark", this.selected_items[0]?.id);
              this.remark = "";
              this.scrollDown();
            }
          }
        } catch (error) {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
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
      this.remarks.map((item) => {
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
    async deleteHandler(id) {
      this.remarks = this.remarks.filter((item) => item.id != id);
    },
    getId() {
      let target_id = "id";
      if (this.sub_system == "advertisement") {
        switch (this.tab) {
          case "ad":
            target_id = "ad_id";
            break;

          case "ad_set":
            target_id = "adset_id";
            break;

          case "campaign":
            target_id = "campaign_id";
            break;
          case "item_code":
            target_id = "pcode";
            break;
        }
      } else if (this.sub_system == "online_sales") {
        switch (this.tab) {
          case "country":
            target_id = "country_id";
            break;
          case "company":
            target_id = "company_id";
            break;
          case "project":
            target_id = "project_id";
            break;
          case "sales_type":
            target_id = "sales_type";
            break;
          case "item_code":
            target_id = "product_code";
            break;
          default:
            target_id = "id";
            break;
        }
      }
      return this.selected_items[0][target_id];
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
    sortRemarks() {
      if (this.comment_category == "latest") {
        return this.remarks;
      } else {
        let data = JSON.parse(JSON.stringify(this.remarks));

        data.sort((a, b) => {
          return b.reactions.length - a.reactions.length;
        });
        return data;
      }
    },
  },
  components: { CustomInput, SingleRemark, PopOver, NoRemark },
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
