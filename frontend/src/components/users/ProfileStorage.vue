<template>
  <v-card
    outlined
    style="background-color: inherit; height: 35vh"
    elevation="0"
    class="mt-2 overflow-y-auto overflow-x-hidden"
  >
    <v-row class="mt-1">
      <v-col :key="storage.limi" cols="12" sm="6" class="py-0">
        <v-card-title class="justify-center mb-2">
          <v-progress-circular
            :rotate="-90"
            :size="180"
            :width="15"
            :value="storagePercentage()"
            :color="progressColor"
          >
            <span class="d-flex flex-column align-center">
              <p class="pa-0 ma-0">
                {{ storage.used_size | getFileSize(2) }}
              </p>
              <p
                class="pa-0 ma-0 surface--text text--darken-2"
                style="font-size: 14px !important"
              >
                {{ $tr("storage_used_text", [fullSize()]) }}
              </p>
            </span>
          </v-progress-circular>
          <span class="ms-2">
            <div>
              <v-avatar :color="progressColor" size="10"></v-avatar>
              {{ $tr("used_storage") }}
            </div>
            <div>
              <v-avatar color="surface darken-2" size="10"></v-avatar>
              {{ $tr("free_storage") }}
            </div>
          </span>
        </v-card-title>
      </v-col>
      <v-col cols="12" sm="6" class="py-0">
        <v-radio-group
          style="margin-top: 0 !important"
          v-model="requestStorage.type"
          hide-details
          row
          dense
        >
          <v-radio value="limited" :label="$tr('limited')"></v-radio>
          <v-radio value="unlimited" :label="$tr('unlimited')"></v-radio>
        </v-radio-group>

        <div class="mt-1 d-lg-flex flex-column flex-md-row">
          <v-text-field
            :disabled="requestStorage.type !== 'limited'"
            hide-details
            dense
            outlined
            rounded
            class="rounded-pill w-lg-5-12 me-md-1 my-1"
            placeholder="Size"
            type="number"
            v-model="requestStorage.amount"
            min="0"
          ></v-text-field>
          <v-select
            :disabled="requestStorage.type !== 'limited'"
            append-icon="mdi-chevron-down"
            class="w-lg-3-12 me-md-1 my-1"
            :items="['MB', 'GB']"
            rounded
            hide-details
            outlined
            placeholder=""
            dense
            v-model="requestStorage.size"
          ></v-select>
          <v-btn
            class="w-2-12 me-1 font-weight-medium my-1"
            hide-details
            dense
            rounded
            color="primary"
            :loading="isLoading"
            @click="submitStorage"
            >{{ $tr("change") }}</v-btn
          >
        </div>
      </v-col>
    </v-row>
    <v-divider></v-divider>
    <div class="storage__container">
      <SettingsCard
        v-for="item in cardItems"
        :key="item.name"
        :name="item.name"
        :FileSize="storage[item.name + 's_used']"
        :FileAmount="storage[item.id]"
        :colorIcon="item.color"
        :icon="item.icon"
      ></SettingsCard>
    </div>
  </v-card>
</template>

<script>
import SettingsCard from "../files/personal/SettingsCard.vue";
import HandleException from "~/helpers/handle-exception";
export default {
  components: {
    SettingsCard,
  },
  props: {
    storage: {
      type: Object,
      required: true,
    },
  },

  watch: {
    "requestStorage.size": function (size) {
      const { amount } = this.requestStorage;
      if (size == "MB") {
        this.requestStorage.amount = (amount * 1024).toFixed(0);
      } else if (size == "GB") {
        this.requestStorage.amount = (amount / 1024).toFixed(0);
      }
    },
  },
  data() {
    return {
      isLoading: false,
      requestStorage: {
        user_id: this.storage.user_id,
        id: this.storage.id,
        amount:
          this.storage.limited_size != "unlimited"
            ? this.bytesToMb(this.storage.limited_size)
            : 0,
        size: "MB",
        type:
          this.storage.limited_size == "unlimited" ? "unlimited" : "limited",
      },
      progressColor: "primary",

      cardItems: [
        {
          name: "document",
          color: "primary",
          icon: "mdi-file",
          id: "documentFiles",
        },
        { name: "image", color: "red", icon: "mdi-image", id: "imageFile" },
        {
          name: "video",
          color: "purple",
          icon: "mdi-filmstrip-box",
          id: "videoFiles",
        },
        { name: "audio", color: "green", icon: "mdi-music", id: "audioFiles" },
        { name: "other", color: "orange", icon: "mdi-file", id: "otherFiles" },
        {
          name: "share_file",
          color: "teal",
          icon: "mdi-folder-download",
          id: "sharesFiles",
        },
      ],
    };
  },

  methods: {
    async submitStorage() {
      if (this.requestStorage.type == "limited") {
        let { amount, size } = this.requestStorage;
        if (!amount && amount < 0) {
          // this.$toastr.i(this.$tr("enter_storage_size"));
							this.$toasterNA("primary",this.$tr('enter_storage_size'));

          return;
        }
        amount = parseInt(amount);
        if (amount < 0) {
          // this.$toastr.i(this.$tr("max_value", this.$tr("storage_size"), 0));
							this.$toasterNA("primary",this.$tr("max_value", this.$tr("storage_size"), 0));

          return;
        }
        if (size == "MB") {
          amount = Math.round(amount * 1024 * 1024);
        } else if (size == "GB") {
          amount = Math.round(amount * 1024 * 1024 * 1024);
        }

        if (amount < parseInt(this.storage.used_size)) {
          // this.$toastr.i(this.$tr("storage_must_greater_then_used_storage"));
							this.$toasterNA("primary",this.$tr("storage_must_greater_then_used_storage"));

          return;
        }
      }
      try {
        this.isLoading = true;
        const url = "files/storage/personal/update";
        const { data } = await this.$axios.post(url, this.requestStorage);

        if (data.result) {
          const { type } = this.requestStorage;
          if (type == "limited") {
            this.storage.limited_size = data.amount;
          } else {
            this.storage.limited_size = type;
          }
        } else if (data.greater) {
          // this.$toastr.i(this.$tr("storage_must_greater_then_used_storage"));
							this.$toasterNA("primary",this.$tr("storage_must_greater_then_used_storage"));

        } else if (data.not_found) {
          // this.$toastr.i(this.$tr("record_not_found"));
							this.$toasterNA("orange",this.$tr("record_not_found"));

        } else {
          // this.$toastr.i(this.$tr("something_went_wrong"));
							this.$toasterNA("red",this.$tr("something_went_wrong"));
          
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
      this.isLoading = false;
    },

    storagePercentage() {
      let grandTotal = 1;
      if (this.storage.limited_size != "unlimited") {
        const total = this.storage.limited_size;
        const used = this.storage.used_size;
        grandTotal = (used * 100) / total;
      }
      if (grandTotal > 96) {
        this.progressColor = "error";
      } else {
        this.progressColor = "primary";
      }
      return grandTotal;
    },

    fullSize() {
      return this.storage.limited_size == "unlimited"
        ? "unlimited"
        : this.$options.filters.getFileSize(this.storage.limited_size);
    },

    bytesToMb(bytes) {
      return (bytes / 1024 ** 2).toFixed(0);
    },
  },
};
</script>

<style>
.storage__container p {
  margin: 2px;
}
</style>
