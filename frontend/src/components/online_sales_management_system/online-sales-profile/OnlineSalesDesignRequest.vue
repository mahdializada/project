<template>
  <div>
    <v-dialog v-model="assignUser" width="1000">
      <Dialog
        persistent
        max-width="1000"
        @closeDialog="
          () => {
            selectedItems = [];
            assignUser = false;
          }
        "
      >
        <DesignRequestAssignUser
          :selected="this.selectedItems"
          :tabKey="currentTab"
          v-if="assignUser"
          @close="assignUser = false"
          @fetchNewData="fetchDesignRequests()"
        />
      </Dialog>
    </v-dialog>

    <div
      class="white ps-4 d-flex py-2 align-center justify-space-between"
      style="border-radius: 30px 0 0 0; width: 100%"
    >
      <div class="d-flex align-center">
        <v-btn fab x-small class="me-1 primary" @click="$emit('closeDialog')">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-btn fab x-small class="primary" @click="$emit('fullScreen')">
          <v-icon>mdi-fullscreen</v-icon>
        </v-btn>
        <div class="grey rounded mx-3" style="height: 40px; width: 1px"></div>
        <span>
          <v-avatar size="40" class="grey lighten-3 me-1">
            <v-avatar size="35" color="white">
              <v-avatar size="30" color="white">
                <v-avatar size="30" class="text-h6 primary white--text">
                  {{
                    selected_product?.product_code
                      ? selected_product?.product_code[0]
                      : selected_product?.pcode[0]
                  }}
                </v-avatar>
              </v-avatar>
            </v-avatar>
          </v-avatar>
          <span class="dialog-title d-inline-block me-2">
            {{
              selected_product?.product_code || selected_product?.pcode
            }}</span
          >
          <span v-if="selected_product?.product_name"
            >({{ selected_product.product_name }})</span
          >
        </span>
      </div>
      <div class="d-flex align-center pe-3">
        <FilterDateRange
          ref="filterDateRange"
          :customSelectDate="{ type: 'lifetime', index: 5 }"
          :dateRangeProp="date_range"
          :hide-title="true"
          @dateChanged="FilterByDateRange($event)"
          :custom_design="true"
          :nudge_left="300"
        />
        <!-- <AdProfileSvg class="ms-2" /> -->
      </div>
    </div>
    <div class="pa-2">
      <DesignRequestPageAction
        :showDelete="false"
        :showSelect="false"
        :showApply="false"
        :showAssign="true"
        :showUnAssign="true"
        :selectedItems.sync="selectedItems"
        :tab-key.sync="currentTab"
        @onUnAssign="onUnAssign"
        @onAssign="onAssign"
        @fetchNewData="() => {}"
        :routeName="'design-request'"
        :statusItems="statusItems"
        :subSystemName="'Design Request'"
        :noReasonSubmitOperations="'in storyboard, storyboard review, in design, design review, in programming, completed, approve in design'"
      />
      <TableTabs
        :minWidth="'280px'"
        @getSelectedTabRecords="onTabChange"
        :tabData="tabItems"
        :extraData="extraData"
      />
      <DataTable
        :key="tableKey"
        :elevation="1"
        ref="tableRef"
        :headers="headers"
        :items="tableRecords"
        :ItemsLength="total_record"
        v-model="selectedItems"
        :loading="apiCalling"
        @onTablePaginate="onTableChanges"
        @click:row="onRowClicked"
      >
        <template v-slot:[`item.company`]="{ item }">
          {{ item.company ? item.company.name : "" }}
        </template>
        <template v-slot:[`item.content_type`]="{ item }">
          <div
            class="text-center rounded-lg px-1"
            :style="
              item.type == 'landing page design'
                ? 'background:#00B411BF'
                : item.type == 'advertisement content'
                ? 'background:#00B41180'
                : item.type == 'translation'
                ? 'background:#00B7C180'
                : item.type == 'social media design'
                ? 'background:#00B411'
                : item.type == 'texts and writings'
                ? 'background:#A4CA11'
                : 'background:#CDB711'
            "
          >
            {{ item.type && $tr(item.type.replaceAll(" ", "_")) }}
          </div>
        </template>

        <template v-slot:[`item.template`]="{ item }">
          {{ item.template && item.template.name }}
        </template>

        <template v-slot:[`item.project_url`]="{ item }">
          <div
            class="success status rounded-lg text-center mx-auto"
            style="width: 100px"
            v-if="
              item.design_request_order &&
              item.design_request_order.project_url &&
              item.design_request_order.project_url != 'null'
                ? item.design_request_order.project_url != ''
                  ? true
                  : false
                : false
            "
          >
            <a
              :href="
                item.design_request_order
                  ? item.design_request_order.project_url
                  : ''
              "
              target="blank"
              class="white--text ma-0 pa-0"
              style="cursor: pointer"
            >
              <span>{{ $tr("link") }}</span>
              <v-icon class="ms-1" color="white">mdi-link</v-icon>
            </a>
          </div>
          <div
            class="secondary status rounded-lg text-center mx-auto"
            style="width: 100px"
            v-else
          >
            <span class="white--text ma-0 pa-0" style="cursor: pointer">
              <span>{{ $tr("no") }} {{ $tr("link") }}</span>
              <v-icon class="ms-1" color="white">mdi-link-off</v-icon>
            </span>
          </div>
        </template>
        <template v-slot:[`item.video`]="{ item }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <span
                v-bind="attrs"
                v-on="on"
                style="white-space: nowrap"
                class="primary--text"
              >
                <span class="mx-auto d-flex justify-center">
                  <v-icon
                    color="primary"
                    v-if="
                      item?.design_request_order?.project_url &&
                      item?.design_request_order?.project_url != '' &&
                      item?.design_request_order?.project_url != null &&
                      item?.design_request_order?.project_url != 'null'
                    "
                    @click="
                      viewCanvaVideo(item?.design_request_order?.project_url)
                    "
                    >mdi-eye</v-icon
                  >
                  <v-icon color="secondary" v-else>mdi-eye-off</v-icon>
                </span>
              </span>
            </template>
            {{ $tr("canva_video") }}
          </v-tooltip>
        </template>
        <template v-slot:[`item.percentage`]="{ item }">
          <v-avatar color="primary" size="30" class="text-center">
            <span
              class="white--text"
              style="font-size: 10px; text-align: center; line-height: 2px"
            >
              {{ currentTab == "completed" ? 100 : item.percentage }}%</span
            >
          </v-avatar>
        </template>

        <template v-slot:[`item.total_time_spent`]="{ item }">
          <div v-for="total_time in total_times_spent" :key="total_time.index">
            <span v-if="total_time.code == item.code">
              {{ total_time.total_time }}
            </span>
          </div>
        </template>
        <template v-slot:[`item.priority`]="{ item }">
          <div
            class="text-center rounded-lg px-1"
            :style="
              item.priority == 'medium'
                ? 'background:#00B411BF'
                : item.priority == 'low'
                ? 'background:#00B41180'
                : item.priority == 'high'
                ? 'background:#00B411'
                : ''
            "
          >
            {{ $tr(item.priority) }}
          </div>
        </template>

        <template v-slot:[`item.requestType`]="{ item }">
          <div v-if="item.status == 'in storyboard'">
            <div
              :class="`white--text rounded-lg d-flex justify-center px-1 ${
                item.storyboard_reject_count > 0 ? 'primary' : ' success'
              }`"
              style="width: 90px; padding: 1px"
            >
              {{
                item.storyboard_reject_count > 0 ? $tr("repeate") : $tr("new")
              }}
            </div>
          </div>
          <div v-else-if="item.status == 'in design'">
            <div
              :class="`white--text  rounded-lg justify-center d-flex px-1 ${
                item.design_reject_count > 0 ? 'primary' : ' success'
              }`"
              style="width: 90px; padding: 1px"
            >
              {{ item.design_reject_count > 0 ? $tr("repeate") : $tr("new") }}
            </div>
          </div>
          <div v-else>
            <div
              class="white--text rounded-lg d-flex justify-center px-1 success"
              style="width: 90px; padding: 1px"
            >
              {{ $tr("new") }}
            </div>
          </div>
        </template>
        <template v-slot:[`item.assignedUser`]="{ item }">
          <div v-if="item.design_request_order != null">
            <div
              v-if="
                item.design_request_order.design_request_order_user.length > 0
              "
            >
              <v-tooltip bottom color="white primary--text">
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-for="(user, key) in item.design_request_order
                      .design_request_order_user"
                    :key="key"
                  >
                    <v-avatar
                      v-bind="attrs"
                      v-on="on"
                      v-if="key < 3"
                      color="white"
                      size="24"
                      class="ml-n1"
                      outlined
                    >
                      <img :src="user.user.image" alt="user" />
                    </v-avatar>
                  </span>
                </template>
                <span
                  v-for="(user, key) in item.design_request_order
                    .design_request_order_user"
                  :key="key"
                >
                  <v-avatar size="20" outlined>
                    <img :src="user.user.image" alt="user" />
                  </v-avatar>
                  {{ user.user.firstname }} {{ user.user.lastname }} <br />
                </span>
              </v-tooltip>
              <span
                class="primary--text"
                v-if="
                  item.design_request_order.design_request_order_user.length > 3
                "
                >+{{
                  item.design_request_order.design_request_order_user.length - 3
                }}</span
              >
            </div>
          </div>
          <div v-else class=""></div>
        </template>

        <template v-slot:[`item.order_type`]="{ item }">
          <div
            class="text-center text-capitalize rounded-lg px-1"
            :style="
              getOrderType(item) == 'copy'
                ? 'background:#00B411BF'
                : getOrderType(item) == 'scratch'
                ? 'background:#00B41180'
                : getOrderType(item) == 'mix'
                ? 'background:#00B411'
                : ''
            "
          >
            {{ getOrderType(item) }}
          </div>
        </template>

        <template v-slot:[`item.landing_page_link`]="{ item }">
          <div
            class="info status rounded-lg text-center mx-auto"
            style="width: 100px"
            v-if="
              item.design_request_order &&
              item.design_request_order.landing_page_link != 'null' &&
              item.type == 'landing page design'
                ? item.design_request_order.landing_page_link != ''
                  ? true
                  : false
                : false
            "
          >
            <a
              :href="
                item.design_request_order
                  ? item.design_request_order.landing_page_link
                  : ''
              "
              target="blank"
              class="white--text ma-0 pa-0"
              style="cursor: pointer"
            >
              <span>{{ $tr("link") }}</span>
              <v-icon class="ms-1" color="white">mdi-link</v-icon>
            </a>
          </div>
          <div
            class="secondary status rounded-lg text-center mx-auto"
            style="width: 100px"
            v-else
          >
            <span class="white--text ma-0 pa-0" style="cursor: pointer">
              <span>{{ $tr("no") }} {{ $tr("link") }}</span>
              <v-icon class="ms-1" color="white">mdi-link-off</v-icon>
            </span>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
  
  <script>
import FilterDateRange from "../../advertisement/FilterDateRange.vue";
import AdProfileSvg from "../../advertisement/video-ad-profile/AdProfileSvg.vue";
import moment from "moment";
import DataTable from "../../design/DataTable.vue";
import design_requests from "~/configs/pages/design_requests";
import TableTabs from "../../common/TableTabs.vue";
import axios from "axios";
import DesignRequestPageAction from "../../landing/DesignRequestPageAction.vue";
import Dialog from "../../design/Dialog/Dialog.vue";
import DesignRequestAssignUser from "../../landing/DesignRequestAssignUser.vue";

export default {
  components: {
    AdProfileSvg,
    FilterDateRange,
    DataTable,
    TableTabs,
    DesignRequestPageAction,
    Dialog,
    DesignRequestAssignUser,
  },
  props: {
    selected_product: {
      default: () => {
        return { product_code: "PR40", product_name: "dffddfdfd" };
      },
    },
  },
  data() {
    return {
      date_range: {
        start_date: "2023-01-12",
        end_date: moment().format("YYYY-MM-DD"),
      },
      apiCalling: false,
      tableRecords: [],
      total_record: 0,
      selectedItems: [],
      headers: design_requests(this).headers,
      tabItems: design_requests(this).tabItems,
      token:
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMDZkMGVkNmNhMzQ1ZmY4MDUxMzcyMWUwNzkwYTk2MDk2MDcwYmQ4N2ExNzk1MjI0NzZhNzU3YjA4NDdlMWI5N2ZmMjU1NDViNWNhZDYwODYiLCJpYXQiOjE2NzczMjA3ODcuMTcxMzM2LCJuYmYiOjE2NzczMjA3ODcuMTcxMzQ0LCJleHAiOjE3MDg4NTY3ODcuMDM2NjExLCJzdWIiOiI1Y2M1NjJmYi0zOTdhLTQyMGUtODg1NC05OGNkYjExNjAzMDgiLCJzY29wZXMiOlsidWMiLCJ1ZCIsInVlIiwidXUiLCJ1diIsInRjIiwidGQiLCJ0ZSIsInR1IiwidHYiLCJyYyIsInJkIiwicmUiLCJydSIsInJ2IiwidWFjIiwidWFkIiwidWFlIiwidWF1IiwidWF2IiwibHVjIiwibHVkIiwibHVlIiwibHV1IiwibHV2Iiwic2V1YyIsInNldWQiLCJzZXVlIiwic2V1dSIsInNldXYiLCJybnVjIiwicm51ZCIsInJudWUiLCJybnV1Iiwicm51diIsImJsYyIsImJsZCIsImJsZSIsImJsdSIsImJsdiIsInNtYyIsInNtZCIsInNtZSIsInNtdSIsInNtdiIsImNuYyIsImNuZCIsImNuZSIsImNudSIsImNudiIsImNtYyIsImNtZCIsImNtZSIsImNtdSIsImNtdiIsImRwYyIsImRwZCIsImRwZSIsImRwdSIsImRwdiIsImxuYyIsImxuZCIsImxuZSIsImxudSIsImxudiIsImxtYyIsImxtZCIsImxtZSIsImxtdSIsImxtdiIsInNlbWMiLCJzZW1kIiwic2VtZSIsInNlbXUiLCJzZW12Iiwicm5tYyIsInJubWQiLCJybm1lIiwicm5tdSIsInJubXYiLCJzeWMiLCJzeWQiLCJzeWUiLCJzeXUiLCJzeXYiLCJtZGMiLCJtZGQiLCJtZGUiLCJtZHUiLCJtZHYiLCJ1cmMiLCJ1cmQiLCJ1cmUiLCJ1cnUiLCJ1cnYiLCJwZmMiLCJwZmQiLCJwZmUiLCJwZnUiLCJwZnYiLCJyZmMiLCJyZmQiLCJyZmUiLCJyZnUiLCJyZnYiLCJmYyIsImZkIiwiZmUiLCJmdSIsImZ2Iiwic2ZjIiwic2ZkIiwic2ZlIiwic2Z1Iiwic2Z2IiwidGZjIiwidGZkIiwidGZlIiwidGZ1IiwidGZ2Iiwic2V0YyIsInNldGQiLCJzZXRlIiwic2V0dSIsInNldHYiLCJkcmMiLCJkcmQiLCJkcmUiLCJkcnUiLCJkcnYiLCJkcmEiLCJkcmlzYiIsImRyaXNiciIsImRyaWQiLCJkcmlkciIsImRyaXAiLCJkcmFwcmoiLCJkcnZhIiwiZHJvYyIsImRyb2QiLCJkcm9lIiwiZHJvdSIsImRyb3YiLCJkcm9pc2IiLCJkcm9pc2JyIiwiZHJvaWQiLCJkcm9pZHIiLCJkcm9pcCIsImxwYWMiLCJscGFkIiwibHBhZSIsImxwYXUiLCJscGF2Iiwic2VjYyIsInNlY2QiLCJzZWNlIiwic2VjdSIsInNlY3YiLCJybmNjIiwicm5jZCIsInJuY2UiLCJybmN1Iiwicm5jdiIsImxjYyIsImxjZCIsImxjZSIsImxjdSIsImxjdiIsImFmYyIsImFmZCIsImFmZSIsImFmdSIsImFmdiIsImxmYyIsImxmZCIsImxmZSIsImxmdSIsImxmdiJdfQ.aAowABW86-sKUsl9fc2S3isKJnEBYSCuFT8n417hP1BtE6oasFoA7PciMThFiOpqQw3lAsH6leVMxX6fa3NmKAIAwpSC1gYDpSToT-9xPykbtbWVTcP9HkcatxqjfPLyJAXgEeMSwwCAAXhfweDfl6TGmGbjio3oocsMrvilfcO79aGIFIgcef-pkpgC03UDD7o-33gk8Yj8py7XSEcrMNMd1kXy9R2GqgpcWvZ54p6R7VEnd6fjlL84Sjp-kbct3-QoaAdmiktQVJg9r9KSUGClqmEUCsdlDOowYoIvZPMLlpbm_pK3WH2aAQoZkP7YdWMhYTNRTnUXfJJYeAKLU9RBwQ9By0ZXdoShKSyT4BKZ9D_aWpi7Qt1YcXr_H4eqWURlotMzBSuaGv-K5dHZzicQfduIB2TQgqO9EGOdcySREhxceDQU1yo_Gbec0Gf97MWJONbr2FMcFhjjj6PNsEbb8Qsj7mIK3_q1XqeNyGMp1Vb_B-VRyl20GCucW33-IsWRyI86FPy9WXaD9uEFQXXlFQJLq7g7NSbTVX9FWz4H_zUm_YT462CsWOLr2I01yX3kLxo5vcbl13y5CNHHvEVCL0eKOlBEsCxWvU4EDzhlgeQyF9DLYVXBKZl11rAO1qgyOtMaiu6R5JKsschNUhH_ViU_TPOIA9iBvAAlM0w",

      extraData: [],
      options: {
        itemsPerPage: 10,
        page: 1,
      },
      currentTab: "all",
      total_times_spent: [],
      tableKey: 0,
      assignUser: false,
      statusItems: [],
    };
  },
  watch: {
    selectedItems: function (_) {
      this.setStatusItems(this.currentTab);
    },
  },
  created() {},
  methods: {
    async onUnAssign() {
      if (this.selectedItems.length) {
        let orderIds = this.selectedItems.map(
          (item) => item.design_request_order.id
        );
        try {
          await this.$axios2.post("design-request/assign-user?type=unassign", {
            orderIds: orderIds,
          });
          this.fetchDesignRequests();
        } catch (error) {
          this.$toastr.e(this.$tr("something_went_wrong"));
        }
      }
    },
    onAssign() {
      this.assignUser = !this.assignUser;
    },
    viewCanvaVideo(item) {},
    FilterByDateRange(date) {
      this.options.page = 1;
      this.tableKey++;
      this.date_range = date;
      this.fetchDesignRequests();
    },
    onTableChanges(options) {
      this.options = structuredClone(options);
      this.fetchDesignRequests();
      console.log(options);
    },
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },
    onTabChange(tab) {
      this.currentTab = tab;
      this.fetchDesignRequests();
    },

    async fetchDesignRequests() {
      if (this.ColumnAxiosSource)
        this.ColumnAxiosSource.cancel("Request-Cancelled");
      this.ColumnAxiosSource = axios.CancelToken.source();

      const options = {
        params: {
          ...this.options,
          ...this.date_range,
          key: this.currentTab,
          product_code:
            this.selected_product?.product_code || this.selected_product?.pcode,
        },
        cancelToken: this.ColumnAxiosSource.token,
      };

      try {
        this.apiCalling = true;
        const { data, status } = await this.$axios2.get(
          `get-design-requests`,
          options
        );
        if (status == 200) {
          this.tableRecords = data.data;
          this.total_record = data.total;
          this.extraData = data;
          this.limitedInterval = window.setInterval(() => {
            this.setTotalTimeSpent();
          }, 5000);
        } else {
        }
      } catch (error) {
        if (error?.response?.status === 401) {
          this.$emit("closeDialog");
        }
        console.log("error", error);
      }
      this.apiCalling = false;
    },

    getOrderType(order) {
      return (
        order.design_request_order.order_type ??
        order.design_request_order.order_type
      );
    },

    checkSpentTime(diff) {
      let total_time_spent = "";
      let total_year = "";
      let total_month = "";
      let total_day = "";
      let total_hour = "";
      let total_minute = "";

      total_year = Math.floor(diff / 31536000);
      if (total_year > 0) {
        diff -= total_year * 31536000;
        total_time_spent = total_time_spent.concat(total_year, "Y");
      }

      total_month = Math.floor(diff / (86400 * 30));
      if (total_month > 0) {
        diff -= total_month * (86400 * 30);
        total_time_spent = total_time_spent.concat(" ", total_month, "MO");
      }

      total_day = Math.floor(diff / 86400);
      if (total_day > 0) {
        diff -= total_day * 86400;
        total_time_spent = total_time_spent.concat(" ", total_day, "D");
      }

      total_hour = Math.floor(diff / 3600);
      if (total_hour > 0) {
        diff -= total_hour * 3600;
        total_time_spent = total_time_spent.concat(" ", total_hour, "H");
      }

      total_minute = Math.floor(diff / 60);
      if (total_minute > 0) {
        diff -= total_minute * 60;
        total_time_spent = total_time_spent.concat(" ", total_minute, "M");
      }
      if (diff > 0) {
        total_time_spent = total_time_spent.concat(" ", Math.round(diff), "S");
      }
      return total_time_spent;
    },

    setTotalTimeSpent() {
      var d = new Date();
      d.toString();
      this.total_times_spent = [];
      this.tableRecords.forEach((element) => {
        if (element?.status == "completed") {
          d = new Date(element?.completed_date + " UTC");
        }
        let cancelledTotalSecond = 0;
        if (element.status_times?.length > 0) {
          element.status_times.forEach((e) => {
            if (e.status == "cancelled") {
              let cancelledDate = new Date(e.created_at + " UTC");
              cancelledDate.toString();
              if (e.end_date) {
                let endDate = new Date(e.end_date + " UTC");
                endDate.toString();
                let diff = Math.abs(
                  cancelledDate.getTime() / 1000 - endDate.getTime() / 1000
                );
                cancelledTotalSecond += diff;
              } else {
                d = new Date(e?.created_at + " UTC");
                d.toString();
              }
            } else if (e.status == "removed") {
              let removedDate = new Date(e.created_at);
              removedDate.toString();
              if (e.end_date) {
                let endDate = new Date(e.end_date);
                endDate.toString();
                let diff = Math.abs(
                  removedDate.getTime() / 1000 - endDate.getTime() / 1000
                );
                cancelledTotalSecond += diff;
              } else {
                d = new Date(e?.created_at);
                d.toString();
              }
            }
          });
        }
        let created_date = element.created_at;
        let f = new Date(created_date);
        f.toString();
        let diff = Math.abs(d.getTime() / 1000 - f.getTime() / 1000);
        diff = diff - cancelledTotalSecond;
        let total_time_spent = this.checkSpentTime(diff);

        this.total_times_spent.push({
          code: element.code,
          diff: diff,
          total_time: total_time_spent,
        });
      });
    },
    setStatusItems(status) {
      let hasInStoryboardCount = 0;
      let hasNotInStoryboardCount = 0;
      this.selectedItems.forEach(({ has_in_storyboard }) => {
        has_in_storyboard == true
          ? hasInStoryboardCount++
          : hasNotInStoryboardCount++;
      });

      this.showRestore = false;
      switch (status) {
        case "waiting":
        case "storyboard rejected":
          let items = [{ id: "cancelled", name: this.$tr("cancel") }];
          if (hasInStoryboardCount > 0) {
            items.unshift({
              id: "in storyboard",
              name: this.$tr("in_storyboard"),
            });
          }

          if (hasNotInStoryboardCount) {
            items.unshift({
              id: "in design",
              name: this.$tr("in_design"),
            });
          }

          this.statusItems = items;
          break;
        case "in storyboard":
          this.statusItems = [
            {
              id: "storyboard review",
              name: this.$tr("storyboard_review"),
            },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;

        case "storyboard review":
          this.statusItems = [
            {
              id: "approve in design",
              name: this.$tr("approve"),
            },
            {
              id: "storyboard rejected",
              name: this.$tr("reject"),
            },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;

        case "design rejected":
          this.statusItems = [
            { id: "approve in design", name: this.$tr("in_design") },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;
        case "in design":
          this.statusItems = [
            {
              id: "design review",
              name: this.$tr("design_review"),
            },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;
        case "design review":
          this.statusItems = [
            {
              id: "in programming",
              name: this.$tr("approve"),
            },
            {
              id: "design rejected",
              name: this.$tr("reject"),
            },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;
        case "in programming":
          this.statusItems = [
            { id: "completed", name: this.$tr("completed") },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;
        case "removed":
          this.statusItems = [];
          this.showRestore = true;
        case "cancelled":
          let allowedItems = [];
          if (hasInStoryboardCount > 0) {
            allowedItems.unshift({
              id: "in storyboard",
              name: this.$tr("in_storyboard"),
            });
          }

          if (hasNotInStoryboardCount) {
            allowedItems.unshift({
              id: "in design",
              name: this.$tr("in_design"),
            });
          }

          this.statusItems = allowedItems;
          break;
        default:
          this.statusItems = [];
          break;
      }
    },
  },
  mounted() {
    // console.log("mounted called");
    // if (process.client) {
    //   this.limitedInterval = window.setInterval(() => {
    //     this.setTotalTimeSpent();
    //   }, 60000);
    // }
  },
  beforeDestroy() {
    clearInterval(this.limitedInterval);
  },
};
</script>
  
  <style>
</style>