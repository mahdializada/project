<template>
  <div>
    <v-dialog v-model="dialog" width="700" persistent>
      <v-card>
        <v-card-title style="color: #777; border-bottom: 1px solid lightgray">
          <h3>Edit Images</h3>
          <svg
            @click="closeDialog()"
            width="44px"
            height="44px"
            viewBox="0 0 700 560"
            fill="currentColor"
            style="transform: scaleY(-1); cursor: pointer; margin-left: 483px"
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
        </v-card-title>

        <v-card-text style="max-height: 450px; overflow-y: auto">
          <br />
          <CFileUploadCloud
            :key="key"
            py="py-3"
            :prev_file.sync="prev_file"
            v-model="file"
            :system_name="'advertisement'"
          />
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" outlined @click="closeDialog()">close</v-btn>
          <v-btn color="primary" @click="submit()" :loading="loading"
            >Submit</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import CFileUploadCloud from "../../new_form_components/Inputs/CFileUploadCloud.vue";

export default {
  components: {
    CFileUploadCloud,
  },
  data() {
    return {
      key: 0,
      dialog: false,
      file: [],
      data: "",
      loading: false,
      prev_file: [],
    };
  },
  methods: {
    openImageModal(item) {
      this.dialog = true;
      this.data = item;
      this.file = item.attachments;
      this.prev_file = this.$_.cloneDeep(item.attachments);
      this.key++;
    },
    closeDialog() {
      this.dialog = false;
      this.file = [];
      this.prev_file = [];
    },
    async submit() {
      try {
        this.loading = true;
        const data = {
          item_code: this.data.item_code,
          file: this.file,
        };
        const update = await this.$axios.post(
          "advertisement/profile-info/",
          data
        );
        this.loading = false;

        if (update.status == 200) {
          this.$emit("updateFile",update.data);
          this.closeDialog();
          this.$toasterNA(
            "green",
            this.$tr("updated_successfully_", this.$tr("images"))
          );
        } else {
          this.$toasterNA("error", this.$tr("something_went_wrong"));
        }
        this.loading = false;
      } catch (error) {
        this.loading = false;
        this.$toasterNA("red", this.$tr("someting_went_wrong"));
      }
    },
  },
};
</script>