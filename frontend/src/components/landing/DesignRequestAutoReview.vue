<template>
  <v-card style="height: 90vh" class="overflow-x-hidden">
    <!-- <v-card class="overflow-x-hidden"> -->
    <div>
      <ReasonDialog
        @closeDialog="showReasonDialog = false"
        @onSubmit="onSubmit"
        :title="reasonTitle"
        :isMultiple="true"
        :allReasons="allReasons"
        :showReasonDialog.sync="showReasonDialog"
        :isFetchingReasons.sync="isFetchingReasons"
      />
    </div>
    <div class="primary" style="height: 160px">
      <v-card-title>
        <span class="custom-dialog-title rounded-0 white--text">{{
          $tr("profile")
        }}</span>
        <v-spacer />
        <CloseBtn @click="$emit('closeDialog')" class="white--text" />
      </v-card-title>
    </div>
    <v-card-text>
      <v-card class="move-top pa-3">
        <div
          class="
            d-flex
            align-center
            justify-md-space-between
            flex-wrap
            justify-center
          "
        >
          <div class="d-flex align-center">
            <v-avatar size="120" color="blue lighten-4">
              <v-avatar size="100" color="primary" class="white--text text-h3">
                A</v-avatar
              >
            </v-avatar>
            <div class="ps-2">
              <p class="mb-1 text-h5 font-weight-bold primary--text">
                {{ request_title }}
              </p>
              <v-chip
                class="text-subtitle-2 font-weight-bold"
                outlined
                color="primary"
              >
                {{ $tr(status) }}
              </v-chip>
            </div>
          </div>
          <div>
            <div class="priority mt-1 mt-md-0 rounded-xl" style="">
              <div class="d-flex">
                <span
                  class="
                    py-2
                    w-full
                    d-inline-block
                    text-subtitle-h6
                    font-weight-bold
                    text-center
                  "
                  :class="priority == 'low' ? 'primary white--text' : ''"
                  >{{ $tr("low") }}</span
                >

                <span
                  class="
                    w-full
                    d-inline-block
                    text-center
                    py-2
                    text-subtitle-h6
                  "
                  :class="priority == 'medium' ? 'primary white--text' : ''"
                  >{{ $tr("medium") }}
                </span>

                <span
                  class="
                    d-inline-block
                    w-full
                    py-2
                    text-center text-subtitle-h6
                  "
                  :class="priority == 'high' ? 'primary white--text' : ''"
                >
                  {{ $tr("high") }}</span
                >
              </div>
            </div>
          </div>
        </div>
      </v-card>
      <div style="transform: translateY(-50px)">
        <div
          class="d-flex justify-end align-center mb-2"
          v-if="!done"
          :key="card_key"
        >
          <span
            class="primary--text d-inline-block text-h6 font-weight-bold me-3"
            >{{ $tr("approved") + " " }}:{{
              " " + filterRequest("noReason").length
            }}</span
          >
          <span class="error--text d-inline-block text-h6 font-weight-bold"
            >{{ $tr("rejected") }}:{{
              " " + filterRequest("hasReason").length
            }}</span
          >
        </div>
        <transition
          :name="
            $vuetify.rtl
              ? back
                ? 'slide'
                : 'slideback'
              : back
              ? 'slideback'
              : 'slide'
          "
        >
          <v-card :key="card_key" v-if="!done">
            <v-card-title class="primary white--text">
              {{ $tr("note") }} {{ current_index + 1 }} {{ $tr("of") }}
              {{ request_data.length }}
            </v-card-title>
            <v-card-text
              class="pa-2"
              style="max-height: 270px; overflow-y: auto; min-height: 270px"
              v-html="note"
            >
            </v-card-text>
          </v-card>
          <div v-else :key="card_key">
            <div class="d-flex align-center">
              <p class="mb-0 text-h5 font-weight-bold">
                {{ $tr("total_notes") }}: {{ request_data.length }}
              </p>
              <v-divider
                class="ms-2 rounded-sm grey"
                style="height: 3px; padding: 0.5px"
              ></v-divider>
            </div>

            <div class="mt-3">
              <template>
                <v-expansion-panels>
                  <div class="ps-1 me-1 primary mb-2"></div>
                  <v-expansion-panel
                    class="mb-2"
                    :class="$vuetify.theme.dark ? 'black' : 'white'"
                  >
                    <v-expansion-panel-header>
                      <div>
                        <p class="primary--text mb-0 text-h6 font-weight-bold">
                          {{ $tr("approved") }}:
                          {{ filterRequest("noReason").length }}
                        </p>
                      </div>
                      <template v-slot:actions>
                        <v-icon color="primary">
                          mdi-chevron-down-circle</v-icon
                        >
                      </template>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                      <v-chip
                        v-for="(item, index) in filterRequest('noReason')"
                        :key="index"
                        outlined
                        color="primary"
                        class="me-1 mb-1"
                        close
                        @click:close="removeDecided(item.item_ids)"
                        style="cursor: pointer"
                      >
                        <p
                          class="mb-0 text-subtitle-2"
                          @click="viewSelected(item.item_ids)"
                        >
                          {{ item.name }}
                        </p>
                      </v-chip>
                    </v-expansion-panel-content>
                  </v-expansion-panel>

                  <v-expansion-panel
                    class="mb-2"
                    :class="$vuetify.theme.dark ? 'black' : 'white'"
                  >
                    <v-expansion-panel-header>
                      <div>
                        <p class="error--text mb-0 text-h6 font-weight-bold">
                          {{ $tr("rejected") }}:
                          {{ filterRequest("hasReason").length }}
                        </p>
                      </div>
                      <template v-slot:actions>
                        <v-icon color="primary">
                          mdi-chevron-down-circle</v-icon
                        >
                      </template>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                      <v-chip
                        v-for="(item, index) in filterRequest('hasReason')"
                        :key="index"
                        outlined
                        color="primary"
                        class="me-1 mb-1"
                        close
                        @click:close="removeDecided(item.item_ids)"
                        style="cursor: pointer"
                      >
                        <p
                          class="mb-0 text-subtitle-2"
                          @click="viewSelected(item.item_ids)"
                        >
                          {{ item.name }}
                        </p>
                      </v-chip>
                    </v-expansion-panel-content>
                  </v-expansion-panel>

                  <v-expansion-panel
                    class="mb-2"
                    :class="$vuetify.theme.dark ? 'black' : ''"
                  >
                    <v-expansion-panel-header>
                      <div>
                        <p class="grey--text mb-0 text-h6 font-weight-bold">
                          {{ $tr("not_decided_yet") }}:
                          {{ filterRequest("not_decided").length }}
                        </p>
                      </div>
                      <template v-slot:actions>
                        <v-icon color="primary">
                          mdi-chevron-down-circle</v-icon
                        >
                      </template>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                      <v-chip
                        v-for="(item, index) in filterRequest('not_decided')"
                        :key="index"
                        outlined
                        color="primary"
                        class="me-1 mb-1"
                        style="cursor: pointer"
                      >
                        <p
                          class="mb-0 text-subtitle-2"
                          @click="viewSelected(item.id)"
                        >
                          {{ item.product_name + " " + item.product_code }}
                        </p>
                      </v-chip>
                    </v-expansion-panel-content>
                  </v-expansion-panel>
                </v-expansion-panels>
              </template>
            </div>
          </div>
        </transition>
      </div>
    </v-card-text>
    <div class="mt-n3">
      <div
        class="d-flex justify-center align-center"
        v-if="request_data.length > 1"
      >
        <v-btn
          color="primary"
          icon
          class="me-1"
          :disabled="current_index == 0"
          @click="setNote(current_index - 1)"
        >
          <v-icon large>mdi-chevron-left</v-icon>
        </v-btn>
        <v-avatar
          size="28"
          v-for="(n, index) in request_data.length"
          :key="index"
          :class="index == current_index ? 'primary white--text' : ''"
          class="me-1"
          style="cursor: pointer"
          @click="setNote(index)"
          >{{ n }}</v-avatar
        >

        <v-btn
          color="primary"
          :disabled="current_index + 1 == request_data.length"
          icon
          @click="setNote(current_index + 1)"
        >
          <v-icon large>mdi-chevron-right</v-icon>
        </v-btn>
      </div>
    </div>
    <v-card-text>
      <div
        class="d-flex"
        :class="done ? 'justify-space-between' : 'justify-end'"
      >
        <div
          class="d-flex align-center"
          style="cursor: pointer"
          @click="setNote(current_index)"
          v-if="done"
        >
          <v-icon color="primary" large>mdi-chevron-left</v-icon>
          <p class="mb-0 ps-1 text-subtitle-2 primary--text">
            {{ $tr("prev") }}
          </p>
        </div>
        <div class="d-flex align-center" style="cursor: pointer" v-if="!done">
          <text-button
            :text="'Approve'"
            @click="confirmApprove"
            type="success"
          />
          <text-button
            :text="'Reject'"
            :type="'danger'"
            @click="confirmReject"
          />
          <text-button
            v-if="current_index + 1 < request_data.length"
            :text="'next'"
            :type="'primary'"
            @click="setNote(current_index + 1)"
          />

          <text-button
            v-else
            :text="'done'"
            :type="'primary'"
            @click="moveToDone"
          />
        </div>
        <div class="d-flex align-center" style="cursor: pointer" v-else>
          <text-button
            :text="'submit'"
            @click="submitChanges"
            :loading.sync="isLoading"
          />
          <text-button
            :text="'cancel'"
            :type="'secondary'"
            @click="$emit('closeDialog')"
          />
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
import TextButton from "../common/buttons/TextButton.vue";
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import Alert from "../../helpers/alert";
import ReasonDialog from "../common/ReasonDialog.vue";

export default {
  components: { CloseBtn, TextButton, ReasonDialog },
  props: {
    request_data: {
      type: Array,
      require: true,
    },
  },
  data() {
    return {
      isLoading: false,
      card_key: 0,
      request_priority: 0,
      back: false,
      current_index: 0,
      status: "",

      priority: "",
      note: "",
      request_title: "",
      approved: [],
      rejected: [],
      allReasons: [],
      reasonTitle: this.$tr("Why you want to Reject?"),
      isFetchingReasons: false,
      showReasonDialog: false,
      showcard: false,
      done: false,
      panel: 0,
      reject_type: "design",
      decided_request: [],
    };
  },
  methods: {
    viewSelected(id) {
      const index = this.request_data.findIndex((row) => {
        return row.id == id;
      });
      this.setNote(index);
    },
    removeDecided(id) {
      this.decided_request = this.decided_request.filter(
        (row) => row.item_ids != id
      );
    },
    setNote(index) {
      if (this.done) {
        this.done = false;
      }
      if (index == this.request_data.length) {
        this.done = true;
        return;
      }
      index > this.current_index ? (this.back = false) : (this.back = true);
      const data = this.request_data[index];
      this.request_title = data.product_name + " " + data.product_code;
      this.status = data.status;
      this.priority = data.priority;

      this.note =
        this.status == "storyboard review"
          ? data.design_request_order.storyboard_note
          : data.design_request_order.design_note;
      this.current_index = index;
      this.card_key++;
    },

    async onSubmit(selectedReasons) {
      this.decided_request.push({
        item_ids: [this.request_data[this.current_index].id],
        type: "hasReason",
        reasons: selectedReasons,
        name: this.request_title,
        status:
          this.status == "storyboard review"
            ? "storyboard rejected"
            : "design rejected",
      });
      this.setNote(this.current_index + 1);
      this.showReasonDialog = false;
    },

    cancelChanges() {},
    async submitChanges() {
      try {
        if (this.decided_request.length == 0) {
          // this.$toastr.w(this.$tr("no_request_selected"));
						this.$toasterNA("orange",this.$tr("no_request_selected"));

          return;
        }
        this.isLoading = true;
        const response = await this.$axios.post(
          "designRequests/autoReview",
          this.decided_request
        );
        this.isLoading = false;

        Alert.successAlert(this, this.$tr("successfully_updated"));
        this.$emit("closeDialog");
      } catch (error) {
        this.isLoading = false;

        if (error?.response?.status === 307 && !error?.response?.data?.result) {
          // this.$toastr.e(this.$tr("user_has_no_permission"));
			this.$toasterNA("red", this.$tr('user_has_no_permission'));

          this.$emit("askToAddPermissions", this.selectedItems[0].id);
        }
        if (
          [1200, 1400, 1600, 1201, 1202, 1203].includes(
            error?.response?.data?.code
          ) &&
          !error?.response?.data?.result
        ) {
          let message = "";
          switch (error?.response?.data?.code) {
            case 1200:
              message = this.$tr(
                "note_is_either_empty_or_not_enough",
                this.$tr("design")
              );
              this.$toasterNA("red", message)
              break;
            case 1203:
              message = this.$tr(
                "note_is_either_empty_or_not_enough",
                this.$tr("storyboard")
              );
              this.$toasterNA("red", message);
              break;
            case 1201:
              this.$toasterNA("red", this.$tr("design_note_and_design_link_are_empty"));
              break;
            case 1202:
              this.$toasterNA("red", this.$tr("design_link_is_empty"));
              break;
            case 1400:
              this.$toasterNA("red", this.$tr("no_employee_has_been_assigned"));
              break;
            case 1600:
              this.$toasterNA("red", this.$tr("employee_task_not_done"));
              break;
            default:
              break;
          }
        }
        // this.$toastr.e(response.data.message);
      }
      // this.$toastr.e();
    },
    // approve
    confirmApprove() {
      let is_decided = this.decided_request.find(
        (row) => row.item_ids == this.request_data[this.current_index].id
      );
      if (is_decided) {
        if (is_decided.type != "hasReason") {
          // this.$toastr.w(this.$tr("already_approved"));
						this.$toasterNA("orange",this.$tr("already_approved"));
          
          return;
        } else {
          const index = this.decided_request.findIndex(
            (row) => row == is_decided
          );
          this.decided_request.splice(index, 1);
        }
      }
      Alert.confirmAlert(this, () => this.Approve());
    },
    confirmReject() {
      let is_decided = this.decided_request.find(
        (row) => row.item_ids == this.request_data[this.current_index].id
      );
      if (is_decided) {
        if (is_decided.type == "hasReason") {
          // this.$toastr.w(this.$tr("already_rejected"));
						this.$toasterNA("orange",this.$tr("already_rejected"));

          return;
        } else {
          const index = this.decided_request.findIndex(
            (row) => row == is_decided
          );
          this.decided_request.splice(index, 1);
        }
      }
      const type =
        this.reject_type == "design"
          ? "design rejected"
          : "storyboard rejected";
      this.fetchAllReasons(type);
    },
    Approve() {
      this.decided_request.push({
        item_ids: [this.request_data[this.current_index].id],
        type: "noReason",
        reasons: [],
        name: this.request_title,
        status:
          this.status == "storyboard review" ? "in design" : "in programming",
      });
      this.setNote(this.current_index + 1);
    },
    moveToDone() {
      this.done = true;
    },
    async fetchAllReasons(type) {
      this.isFetchingReasons = true;
      try {
        const response = await this.$axios.get(
          `reasons/id?type=${type}&sub_system=Design Request`
        );
        this.allReasons = response?.data?.reasons;
        this.showReasonDialog = true;
        this.isFetchingReasons = false;
      } catch (error) {
        this.showReasonDialog = false;
        if (error?.response?.status === 404 && !error?.response?.data?.result) {
          // this.$toastr.w(this.$tr("sub_system_not_found"));
						this.$toasterNA("orange",this.$tr("sub_system_not_found"));

        }
        this.isFetchingReasons = false;
      }
    },
    filterRequest(type) {
      if (type == "not_decided") {
        let data = [];

        data = this.request_data.filter(
          (row) => !this.decided_request.some((col) => col.item_ids == row.id)
        );
        return data;
      } else {
        return this.decided_request.filter((row) => row.type == type);
      }
    },
  },
  created() {
    this.setNote(0);
  },
};
</script>

<style scoped>
.move-top {
  transform: translateY(-70px) !important;
}

.priority {
  overflow: hidden;
  border: 1px solid grey;
  width: 300px;
  cursor: pointer;
}
</style>