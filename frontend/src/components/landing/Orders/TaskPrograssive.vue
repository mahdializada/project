<template>
  <div>
    <v-app-bar color="primary" dense class="rounded-sm rounded-b-0">
      <v-toolbar-title
        class="subtitle-1 font-weight-medium text-capitalize white--text"
      >
        {{ $tr("design_request") + ` ` + $tr("task_prograssive") }}
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <CloseBtn
        style="height: 31px"
        :logoColor="'white'"
        @click="closeDialog"
        class="py-0 my-0"
        small
      />
    </v-app-bar>
    <v-card class="DesignRequest rounded-t-0">
      <v-card-text class="py-5">
        <h3 class="pb-5 px-4">
          {{ $tr("range_task_prograssive") }}
        </h3>
        <div class="d-flex justify-space-between align-center mx-4">
          <span v-for="step in value" :key="step.key">
            <v-avatar
              size="70"
              :class="
                steps == 100
                  ? 'completed'
                  : step.key == steps
                  ? 'inPrograss'
                  : step.key < steps
                  ? 'completed'
                  : 'uncomplete'
              "
            >
              <v-avatar size="60" class="inner px-3">
                <span class="text-caption text-capitalize">{{
                  step.text
                }}</span></v-avatar
              >
            </v-avatar>
          </span>
        </div>
        <v-slider
          :readonly="
            readOnly || this.steps == this.validPercentage ? true : false
          "
          class="pt-5 custom px-3"
          v-model="steps"
          track-color="blue lighten-3"
          :max="100"
          step="25"
          ticks="always"
          :tick-size="14"
          :thumb-size="30"
          thumb-label="always"
          loader-height="5"
          append-icon="mdi-plus"
          prepend-icon="mdi-minus"
          @click:append="zoomIn"
          @click:prepend="zoomOut"
        >
          <template v-slot:thumb-label="{ value }"> {{ value }}% </template>
        </v-slider>
        <div class="d-flex justify-center align-center pt-5">
          <v-chip class="px-3 py-2" color="grey lighten-4 ">
            <h4 class="grey--text">
              {{ $tr("you_are") }}
              <strong class="primary--text mx-1">{{ steps }}%</strong>
              {{ $tr("done") }}
            </h4>
          </v-chip>
        </div>
      </v-card-text>
      <div class="d-flex justify-end pa-2">
        <TextButton
          type="secondary"
          class="me-1"
          :text="$tr('cancel')"
          @click="closeDialog"
        >
        </TextButton>
        <TextButton
          type="primary"
          :text="$tr('save')"
          :loading="loading"
          @click="savePrograss"
        >
        </TextButton>
      </div>
    </v-card>
  </div>
</template>
<script>
import TextButton from "../../common/buttons/TextButton.vue";
import CloseBtn from "~/components/design/Dialog/CloseBtn";
import CustomButton from "~/components/design/components/CustomButton.vue";
import { mapGetters, mapMutations } from "vuex";
export default {
  name: "CompanyFilter",
  components: { CloseBtn, CustomButton, TextButton },
  props: {
    profileData: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      value: [
        { key: 0, text: this.$tr("started") },
        { key: 25, text: this.$tr("working") },
        { key: 50, text: this.$tr("half_done") },
        { key: 75, text: this.$tr("almost_done") },
        { key: 100, text: this.$tr("done") },
      ],

      steps: 0,
      id: 0,
      loading: false,
      userPrograss: null,
      readOnly: false,
      validPercentage: 100,
      notelength: 0,
    };
  },
  computed: {
    ...mapGetters({
      hasFile: "design_requests/hasFile",
    }),
  },
  methods: {
    ...mapMutations({
      updateHasFile: "design_requests/UPDATE_HAS_FILE",
    }),
    closeDialog() {
      this.$emit("close");
    },

    zoomOut() {
      if (!this.readOnly) {
        this.steps = this.steps - 25 || 0;
      }
    },
    zoomIn() {
      if (!this.readOnly) {
        if (this.steps < this.validPercentage) {
          this.steps = this.steps + 25 || 100;
        } else if (this.validPercentage != 100) {
          if (this.notelength == -1) {
            this.toastFunction("Design link");
          } else if (!this.hasFile) {
            this.toastFunction("Design file");
          } else {
            // this.$toastr.w(
            //   this.$tr("note_lenght_is_not_enough", this.notelength)
            // );
						this.$toasterNA("orange",this.$tr("note_lenght_is_not_enough", this.notelength));

          }
        }
      }
    },
    async savePrograss() {
      if (this.steps != this.userPrograss) {
        this.loading = true;
        const data = {
          id: this.id,
          task_prograss: this.steps,
        };
        const response = await this.$axios.post(`updateTaskPrograss`, data);

        if (response?.status == 200) {
          // this.$toastr.s(this.$tr("successfult_updated"));
				this.$toasterNA("green", this.$tr('successfully_updated'));

          await this.$emit("fetch");
          this.closeDialog();
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
        this.loading = false;
      } else {
        // this.$toastr.e(`not_changed`);
			this.$toasterNA("red", this.$tr('not_changed'));

      }
    },
    /**
     * this function for toasts
     */
    toastFunction(param_warning = false, param_info = false) {
      var message = param_warning;
      var info_message = param_info;
      if (info_message != false) {
        info_message = this.$tr(info_message);
        this.$toastr.i(info_message);
							this.$toasterNA("primary",this.$tr(info_message));

      }
      if (message != false) {
        message = this.$tr("feild_is_empty", message);
        // this.$toastr.w(message);
						this.$toasterNA("orange",message);

      }
    },
  },
  watch: {
    hasFile: function (value) {
      this.profileData.has_files = value;
    },
  },
  created() {
    const userId = this.$auth.user.id;
    const profile =
      this.profileData?.design_request_order?.design_request_order_user;
    const status = this.profileData.status;

    profile.forEach((element) => {
      if (element.user_id == userId) {
        (this.userPrograss = element.task_prograss),
          (this.steps = element.task_prograss);
        this.id = element.id;
      }
    });

    if (status == "storyboard review" || status == "design review") {
      this.readOnly = true;
      this.toastFunction(false, "read_only");
    } else if (status == "in design") {
      let noteSize = 0;
      if (this.profileData?.design_request_order?.design_note != null)
        noteSize = this.profileData?.design_request_order?.design_note?.length;
      if (noteSize == 0) {
        this.readOnly = true;
        this.toastFunction("Design note", "read_only");
      } else if (!this.profileData?.has_files) {
        this.updateHasFile(false);
        this.validPercentage = 75;
      } else if (noteSize < 10) {
        this.toastFunction("Please upload files", "read_only");
        this.validPercentage = 75;
      }
    } else if (status == "in storyboard") {
      let noteSize = 0;
      if (this.profileData?.design_request_order?.storyboard_note != null)
        noteSize =
          this.profileData?.design_request_order?.storyboard_note?.length;

      if (noteSize == 0) {
        this.readOnly = true;
        this.toastFunction("storyboard Note", "read_only");
      } else if (noteSize < 25) {
        this.notelength = 25;
        this.validPercentage = 75;
      }
    }
  },
};
</script>
<style>
.inPrograss {
  border: 1px solid var(--v-primary-base) !important;
}
.inPrograss .inner {
  color: var(--v-primary-base);
}
.completed {
  border: 1px solid var(--v-primary-base) !important;
}
.completed .inner {
  color: white;
  background-color: var(--v-primary-base);
}
.uncomplete {
  border: 1px solid rgb(190, 187, 187) !important;
}
.uncomplete .inner {
  color: rgb(190, 187, 187);
}
.custom .v-slider--horizontal .v-slider__track-container {
  height: 9px !important;
}
.custom .v-slider__tick {
  border-radius: 50px !important;
  background-color: white;
  border: 1px solid #90caf9;
}
.custom .v-slider__tick:hover {
  cursor: pointer;
}
.custom .v-slider__tick--filled {
  background: var(--v-primary-base) !important;
  border: var(--v-primary-base) !important;
}
.custom .v-slider__thumb {
  width: 13px !important;
  height: 13px !important;
  left: -7px !important;
}
.custom .v-slider__thumb-label.primary {
  transform: none !important;
  border-radius: 50px !important ;
  width: 35px !important;
  height: 18px !important;
  top: -9px;
  left: -19px;
  background-color: white !important;
  color: var(--v-primary-base) !important ;
  border: 1px solid var(--v-primary-base) !important;
}
.custom .v-slider--horizontal .v-slider__thumb-label > * {
  transform: none !important;
  margin-top: 2px;
}
.custom .v-icon.notranslate.v-icon--link.mdi.mdi-minus.theme--light {
  background-color: var(--v-primary-lighten3) !important;
  margin-right: 20px;
  margin-left: 10px;
  padding: 6px !important;
  border-radius: 50px;
}
.custom .v-icon.notranslate.v-icon--link.mdi.mdi-plus.theme--light {
  background-color: var(--v-primary-lighten3) !important;
  margin-right: 10px;
  margin-left: 20px;
  padding: 6px !important;
  border-radius: 50px;
}
.custom .v-slider--horizontal {
  margin-left: 17px;
  margin-right: 17px;
}
</style>
