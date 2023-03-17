<template>
    <v-dialog v-model="showReasonDialog" persistent width="800" scrollable>
      <v-card :color="$vuetify.theme.dark ? 'black' : '#f9fafd'">
        <v-card-title class="pt-1 pb-0">
          <v-app-bar-title>
            <span class="custom-dialog-title">{{ $tr("reasons") }}</span>
          </v-app-bar-title>
          <v-spacer />
          <CloseBtn @click="closeDialog" />
        </v-card-title>
        <div v-if="allReasons.length > 0" class="ps-4 grey--text">
          {{ $tr("please_select_reasons", $tr("active")) }}
        </div>
        <v-card-text class="px-4 my-2 overflow-auto" style="height: 50vh">
          <div
            style="height: 400px"
            v-if="submiting"
            class="d-flex align-center justify-center"
          >
            <div
              class="
                connection__container
                pa-5
                d-flex
                align-center
                flex-column
                justify-center
              "
              v-if="!successMessage"
            >
              <lottie-player
                src="/assets/man-on-rocket.json"
                background="transparent"
                speed="3"
                style="width: 250px; height: 250px"
                loop
                autoplay
              >
              </lottie-player>
              <h2 class="pa-2 primary--text">
                {{
                  // $tr("pulish_ads", published, form.$model.ads.length) +
                  "Processing ..."
                }}
              </h2>
            </div>
  
            <div v-show="successMessage">
              <div class="d-flex align-center justify-center flex-column">
                <lottie-player
                  src="/assets/done_animation.json"
                  background="transparent"
                  speed="1"
                  loop
                  autoplay
                  style="width: 250px; height: 250px"
                >
                </lottie-player>
  
                <div
                  class="friqiBase--text"
                  style="font-size: 30px; font-weight: 600"
                >
                  {{ $tr("successfully_inserted") }}
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <v-skeleton-loader
              v-if="isFetchingReasons"
              v-bind="attrs"
              type="list-item-two-line"
            ></v-skeleton-loader>
  
            <span v-else>
              <div
                class="d-flex justify-center h-full align-center flex-column"
                v-if="allReasons.length == 0"
              >
                <ReasonSvg />
                <p class="mb-0 mt-2">{{ $tr("there_is_no_reason") }}</p>
              </div>
  
              <v-checkbox
                v-else
                v-for="(reason, index) in allReasons"
                :key="index"
                class="reason borderDe rounded pt-0 ps-2"
                hide-details
                v-model="selectedReasons"
                :class="{
                  'blue lighten-5': selectedReasons.includes(reason.reason_id),
                }"
                :value="reason.reason_id"
              >
                <template v-slot:label>
                  <div class="py-2 px-1">
                    <p class="mb-0">
                      {{ reason.title }}
                    </p>
                  </div>
                </template>
              </v-checkbox>
            </span>
          </div>
        </v-card-text>
        <v-card-actions class="d-flex justify-end">
          <v-btn outlined color="primary" @click="closeDialog" v-if="isSubmiting">{{
            $tr("cancel")
          }}</v-btn>
          <v-btn class="primary" @click="onSubmit" v-if="isSubmiting">{{ $tr("submit") }}</v-btn>
          <v-btn class="primary" @click="DoneReasonDialog" v-if="done">{{ $tr("done") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </template>
  
  <script>
import ReasonSvg from '../../common/SvgIcons/ReasonSvg.vue';
import CloseBtn from '../../design/Dialog/CloseBtn.vue';

  
  export default {
    props: {
        tabName: {
            type: String,
            default: "",
        },
        status_type: {
            type: String,
            require: true,
        },
    },
    data() {
        return {
            isSubmiting: true,
            done: false,
            submiting: false,
            showReasonDialog: false,
            successMessage: false,
            selectedReasons: [],
            isFetchingReasons: true,
            allReasons: [],
            select: false,
            attrs: {
                class: "mb-1",
                boilerplate: true,
                elevation: 1,
            },
        };
    },
    methods: {
        onSubmit() {
            if (this.submiting)
                return;
            if (this.selectedReasons.length > 0) {
                this.submiting = true;
                this.$emit("onSubmit", this.selectedReasons);
                this.resetForm();
            }
            else {
                this.$toasterNA("orange", this.$tr("please_select_one_option"));
            }
        },
        resetForm() {
            this.selectedReasons = [];
            this.allReasons = [];
        },
        closeDialog() {
            this.resetForm();
            this.showReasonDialog = false;
            this.isSubmiting = true;
            this.done = false;
            this.successMessage = false;
            this.submiting = false;
            this.$emit("closeDialog");
        },
        DoneReasonDialog() {
            this.showReasonDialog = false;
            this.isSubmiting = true;
            this.done = false;
            this.successMessage = false;
            this.submiting = false;
        },
        submitResult(result) {
            if (result) {
                this.successAnimation();
            }
            else {
            }
        },
        async fetchAllReasons(statusTo = null, status) {
            try {
                this.showReasonDialog = true;
                this.isFetchingReasons = true;
                const response = await this.$axios.get(`reasons/id?type=${statusTo == null ? status : (this.selectedStatus = statusTo)}&sub_system=Online Sales&tab_name=${this.tabName}`);
                this.allReasons = response?.data?.reasons;
                this.isFetchingReasons = false;
            }
            catch (error) {
                if (error?.response?.status === 404 && !error?.response?.data?.result)
                    // this.$toastr.w(this.$tr("sub_system_not_found"));
                    this.$toasterNA("orange", this.$tr("sub_system_not_found"));
            }
            this.$emit("fetchAllReasons");
        },
        successAnimation() {
            this.successMessage = true;
            this.isSubmiting = false,
                this.done = true;
            setTimeout(() => {
                this.submiting = false;
                this.showReasonDialog = false;
                this.successMessage = false;
                this.isSubmiting = true;
                this.done = false;
            }, 2000);
        },
    },
    components: { ReasonSvg, CloseBtn }
};
  </script>
  <style>
  .reason:hover {
    cursor: pointer;
  }
  .borderDe {
    border: 1px solid var(--v-surface-darken2);
  }
  .reason.blue.lighten-5 {
    border-color: var(--v-primary-base) !important;
  }
  </style>
  