<template>
  <div>
    <div class="d-flex justify-space-between mb-2 align-center image-upload">
      <div class="ml-3">
        <span class="text-style"> Upload Image </span>
      </div>
      <div class="mr-2">
        <input
          ref="uploader"
          class="d-none"
          type="file"
          @change="onFileChanged"
        />
        <BackgroundButton
          :color="'blue'"
          :icon="'fa-upload'"
          @click="uploadImage()"
        />
      </div>
    </div>
    <div
      class="d-flex flex-wrap mb-2"
      style="width: 100%; padding: 4px; max-height: 200px; overflow-y: auto"
    >
      <div
        v-for="(item, index) in uploadedFiles"
        :key="index + 'i'"
        class="d-flex mt-1 uploaded-img rounded"
        style=""
      >
        <v-img
          class="rounded cursor-pointer d-flex align-center justify-center"
          :src="item.path"
        >
          <div class="d-flex justify-center">
            <v-progress-circular
              :size="35"
              :value="item?.progress"
              :color="item?.isUploading ? 'primary' : 'blue-grey lighten-3'"
              :rotate="-90"
              width="4"
            >
              <v-btn
                class="blue-grey lighten-3"
                style="height: 28px; width: 28px"
                fab
                x-small
                @click="deleteFileBackend(item)"
              >
                <v-icon dark color="white" size="20"> mdi-close </v-icon>
              </v-btn>
            </v-progress-circular>
          </div>
        </v-img>
      </div>
    </div>
  </div>
</template>

<script>
import BackgroundButton from "../../common/buttons/BackgroundButton.vue";

export default {
  components: { BackgroundButton },
  props:{
    system_name:{
      type:String,
      default:'test'
    }
  },
  data() {
    return {
      uploadedFiles: [],
    };
  },
  methods: {
    onEdit(item) {
      this.uploadedFiles = item;
    },
    async deleteFileBackend(item) {
      try {
        if (item.new_path) {
          this.uploadedFiles = this.uploadedFiles.filter(
            (i) => i.new_path != item.new_path
          );
          let res = await this.$axios.delete("delete-upload-to-cloud", {
            params: { path: item.new_path },
          });
          if (res.data.result) {
          } else {
            console.log("error");
          }
        } else {
          this.uploadedFiles = this.uploadedFiles.filter(
            (i) => i.path != item.path
          );
          this.$emit("onDeleteImg", item.path);
        }
      } catch (e) {
        console.log("errrror", e);
      }
    },
    uploadImage() {
      this.$refs.uploader.click();
    },
    onFileChanged(e) {
      const files = e.target.files;
      let temp = [];
      for (let i = 0; i < files?.length; i++) {
        const file = files[i];
        const data = {
          file: file,
          progress: 0,
          isUploading: true,
          path: URL.createObjectURL(file),
        };
        this.uploadedFiles.unshift(data);
        temp.push(data);
      }
      temp.forEach((file) => {
        this.upload(file);
      });
    },
    async upload(item) {
      try {
        this.uploading = true;
        const formData = new FormData();
        formData.append("file", item.file);
        formData.append("system_name", this.system_name);
        let source = this.$axios.CancelToken.source();
        item.source = source;
        let config = {
          cancelToken: source.token,
          onUploadProgress: function (progressEvent) {
            let uploadProgress =
              parseInt(
                Math.round((progressEvent.loaded / progressEvent.total) * 100)
              ) - 5;

            this.updateProgress(item.path, uploadProgress);
          }.bind(this),
        };

        const response = await this.$axios.post(
          "/upload-to-cloud",
          formData,
          config
        );

        if (response.status == 200) {
          this.updateProgress(item.path, 100, response.data.path);
          this.$emit("onFileChange", response.data.path);
        } else {
          console.log("error");
        }
      } catch (e) {
        console.error("error", e);
      }
      this.uploading = false;
    },
    updateProgress(prev_path, progress, new_path = "") {
      this.uploadedFiles = this.uploadedFiles.map((item) => {
        if (item.path == prev_path) {
          item.progress = progress;
          if (new_path != "") {
            item.new_path = new_path;
            item.isUploading = false;
          }
        }
        return item;
      });
    },
  },
};
</script>

<style>
.image-upload {
  background-color: #f7f8fc;
  width: 100%;
  height: 60px;
}
.text-style {
  font-size: 1.1rem;
  font-weight: bold;
}
.uploaded-img {
  border: 2px solid #d9e0f4;
  width: 98px;
  height: 98px;
  margin-left: 3px;
  margin-right: 3px;
}
</style>
