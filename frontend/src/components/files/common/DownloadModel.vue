<template>
  <div class="download__container elevation-1">
    <v-slide-y-reverse-transition>
      <div v-if="zippingFileModel == 'maximize'">
        <div
          class="download__header d-flex align-center justify-space-between rounded-t-sm"
        >
          <div class="left ms-2">
            {{ getZippingInfo(this.$tr) }}
          </div>
          <div class="right me-1 d-flex align-center">
            <v-icon
              @click="changeZippingModel('minimize')"
              class="me-1"
              size="20"
              color="white"
              >mdi-minus</v-icon
            >
            <v-icon size="20" color="white" @click="onCancelAll"
              >mdi-close</v-icon
            >
          </div>
        </div>
        <div class="download__body py-1 ps-1">
          <v-virtual-scroll height="250" item-height="40" :items="zippingFiles">
            <template v-slot:default="{ item }">
              <div
                class="d-flex align-center justify-space-between my-1 w-full cursor-pointer"
              >
                <div class="download__item-left d-flex align-center">
                  <div class="me-1">
                    <v-btn loading icon color="primary  lighten-4">
                      <template v-slot:loader>
                        <v-progress-circular
                          :rotate="-96"
                          :size="30"
                          :width="2"
                          :value="item.progress"
                          color="primary"
                        >
                          <div style="font-size: 0.5rem !important">
                            {{ item.progress }}%
                          </div>
                        </v-progress-circular>
                      </template>
                    </v-btn>
                  </div>
                  <div
                    style="
                      white-space: nowrap;
                      text-overflow: ellipsis;
                      overflow: hidden;
                    "
                  >
                    {{ item.name }}
                  </div>
                </div>
                <div class="me-1">
                  <v-icon size="20" @click="onCancelItem(item)" color="primary">
                    {{
                      item.object && item.object.isCompleted
                        ? " mdi-check-circle"
                        : "mdi-close"
                    }}
                  </v-icon>
                </div>
              </div>
              <v-divider class="custom-devider ma-0"></v-divider>
            </template>
          </v-virtual-scroll>
        </div>
      </div>
    </v-slide-y-reverse-transition>

    <v-slide-x-reverse-transition>
      <v-btn
        v-if="zippingFileModel == 'minimize'"
        @click="changeZippingModel('maximize')"
        color="primary"
        class="fixed elevation-2"
        :class="{ make__up: uploadingDialog }"
      >
        <v-progress-circular
          :value="getZippingTotal"
          color="white"
          :rotate="-90"
          class="progress"
          size="26"
          :width="2"
        >
          <div style="font-size: 0.5rem !important">{{ getZippingTotal }}%</div>
        </v-progress-circular>
      </v-btn>
    </v-slide-x-reverse-transition>
  </div>
</template>

<script>
import { mapState, mapMutations, mapGetters } from "vuex";
import Alert from "~/helpers/alert";

export default {
  computed: {
    ...mapState("files", [
      "zippingFiles",
      "zippingFileModel",
      "uploadingDialog",
    ]),
    ...mapGetters("files", ["getZippingTotal", "getZippingInfo"]),
  },

  methods: {
    ...mapMutations("files", [
      "changeZippingModel",
      "removeZippingFile",
      "clearZippingFiles",
    ]),

    onCancelItem(item) {
      const callback = () => this.removeZippingFile(item.id);
      if (item.object.isCompleted) {
        callback();
        return;
      }
      Alert.removeDialogAlert(this, callback, "", "yes_cancel");
    },

    onCancelAll() {
      const callback = () => this.clearZippingFiles("hide");
      const every = ({ object: { isCompleted } }) => isCompleted;
      const allDone = this.zippingFiles.every(every);
      if (allDone) {
        callback();
        return;
      }
      Alert.removeDialogAlert(this, callback, "", "yes_cancel_all");
    },
  },
};
</script>

<style lang="scss" scoped>
.make__up {
  bottom: 170px;
}
.download__container {
  position: fixed;
  z-index: 10;
  right: 0;
  bottom: 130px;
  width: 245px;
  max-width: 245px;
  background-color: var(--v-surface-base);
  border-radius: 5px;
  .download__header {
    background: var(--v-primary-base);
    color: var(--color-white);
    padding: 6px 4px;
    .left {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      width: calc(237px - 45px);
    }
    .right {
      cursor: pointer;
    }
  }
}
</style>
