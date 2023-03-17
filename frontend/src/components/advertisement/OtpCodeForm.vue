<template>
  <v-dialog v-model="showDialog" persistent width="460" scrollable>
    <v-card :color="$vuetify.theme.dark ? 'black' : '#f2f5fa'">
      <v-card-text
        class="pa-3 overflow-auto"
        style="min-height: 310px"
        v-if="!submiting"
      >
        <div
          class="d-flex primary--text align-center"
          style="font-size: 18px; font-weight: 500"
        >
          <v-icon class="me-1" color="primary" size="30"> mdi-dialpad </v-icon>
          OTP Code
        </div>
        <div class="d-flex align-center flex-column pt-3">
          <p
            class="text-center grey--text mb-1"
            style="width: 280px; font-size: 17px; font-weight: 400"
          >
            please enter the OTP code we have just send you.
          </p>
          <div class="px-8">
            <v-otp-input
              dense
              length="6"
              type="number"
              v-model="otp_code"
            ></v-otp-input>
          </div>

          <p
            class="text-center grey--text"
            style="width: 280px; font-size: 14px; font-weight: 400"
            v-if="expire_time > current_time"
          >
            The Code Will Expire on
            <span class="primary--text">{{
              timer.minutes + ":" + timer.seconds
            }}</span>
          </p>
          <p
            class="text-center grey--text"
            style="width: 280px; font-size: 14px; font-weight: 400"
            v-else
          >
            OTP Code Expired!
          </p>
          <v-btn
            color="primary"
            plain
            icon
            style="font-size: 16px; font-weight: 400"
            :loading="sending_otp_code"
            @click="sendOtpCode()"
          >
            <v-icon>mdi-refresh</v-icon> Resend the code
          </v-btn>
        </div>
      </v-card-text>
      <v-card-text
        v-if="submiting"
        class="pa-3 overflow-auto d-flex align-center justify-center"
        style="min-height: 310px"
      >
        <lottie-player
          src="/assets/man-on-rocket.json"
          background="transparent"
          speed="3"
          style="width: 200px; height: 200px"
          loop
          autoplay
        >
        </lottie-player>
      </v-card-text>
      <v-card-actions class="d-flex justify-end white py-2">
        <v-btn
          plain
          color="primary"
          @click="toggleDialog"
          style="font-size: 16px; font-weight: 500"
          >{{ $tr("cancel") }}</v-btn
        >
        <v-btn
          class="primary"
          @click="onSubmit"
          style="font-size: 16px; font-weight: 400"
          >{{ $tr("cofirm") }}</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import CloseBtn from "./../../components/design/Dialog/CloseBtn.vue";

export default {
  props: {
    submit_url: {
      type: String,
      default: "advertisement/delete-itemcode",
    },
    subsystem: {
      type: String,
      default: "advertisement",
    },
  },
  components: { CloseBtn },
  data() {
    return {
      showDialog: false,
      selectedItem: { pcode: "" },
      sending_otp_code: false,
      otp_code: null,
      current_time: new Date().getTime(),
      expire_time: new Date().getTime(),
      submiting: false,
    };
  },

  computed: {
    timer() {
      let timeleft = this.expire_time - this.current_time;
      let minutes = Math.floor((timeleft % (1000 * 60 * 60)) / (1000 * 60));
      let seconds = Math.floor((timeleft % (1000 * 60)) / 1000);
      return { minutes: minutes, seconds: seconds };
    },
  },
  mounted() {
    setInterval(() => {
      this.current_time = new Date().getTime();
    }, 1000);
  },

  methods: {
    toggleDialog(data = null) {
      if (!this.showDialog) {
        if (
          this.current_time > this.expire_time ||
          this.selectedItem.pcode != data.pcode
          ) {
          this.selectedItem = data;
          this.sendOtpCode();
        }
        this.selectedItem = data;
      }
      this.otp_code = null;
      this.showDialog = !this.showDialog;
    },
    async onSubmit() {
      try {
        if (this.current_time > this.expire_time) {
          this.$toasterNA("red", this.$tr("otp_code_expired"));
          return;
        }
        if (this.otp_code < 100000) {
          this.$toasterNA("red", this.$tr("invalid_otp_code"));
          return;
        }
        const data= {
            pcode: this.selectedItem.pcode,
            otp_code: this.otp_code,
            record_id: this.selectedItem.id,
          }
        this.submiting = true;
        const response = await this.$axios.delete(this.submit_url, {params:data});
        if (this.submit_url == "advertisement") {
          if (response.status == 200) {
            const result = response?.data?.final_result;
            if (result?.length == 0) {
              this.$toasterNA("primary", this.$tr("item_deleted_successfully"));
              this.toggleDialog();
              this.submiting = false;
              this.$emit("onSuccess");
              this.expire_time = 0;
              return;
            }
            result.forEach((row) => {
              this.$toasterNA("red", row);
            });
          }
        } else {
          this.$emit("onSuccess");
          this.toggleDialog();
          this.$toasterNA("primary", this.$tr("item_deleted_successfully"));
        }
      } catch (error) {
        console.error("error", error?.response?.data);
        this.$toasterNA("red", error?.response?.data);
      }
      this.submiting = false;
    },
    async sendOtpCode() {
      try {
        this.otp_code = null;
        this.sending_otp_code = true;
        const response = await this.$axios.post("otp-code/" + this.subsystem, {
          pcode: this.selectedItem.pcode,
          record_id: this.selectedItem.id,
        });
        if (response.status != 200) {
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
        this.setTimer();
      } catch (error) {
        console.error("error", error);
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
      this.sending_otp_code = false;
    },

    setTimer() {
      let date = new Date();
      date.setMinutes(date.getMinutes() + 5);
      this.expire_time = date.getTime();
    },
  },
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
