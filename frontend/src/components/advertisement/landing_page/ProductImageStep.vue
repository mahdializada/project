<template>
  <div>
    <InputCard
      :title="$tr('upload_slider_1_images')"
      v-bind="$attrs"
      v-on="$listeners"
    >
      <template slot-scope="{ attrs, listeners }">
        <v-file-input
          class="custom-file form-custom-file"
          outlined
          accept="image/jpeg,image/jpg,image/png"
          prepend-icon=""
          dense
          v-bind="attrs"
          v-on="listeners"
          @change="onChange"
          :rules="
            validateRule(
              form.landingPage_image, // validate objec
              $root, // context
              $tr('upload_slide_1_image') // lable for feedback
            )
          "
          :invalid="form.landingPage_image.$invalid"
          @blur="form.landingPage_image.$touch()"
          :multiple="true"
        >
          <template slot="prepend-inner">
            <div
              name="prepend-inner"
              style="max-width: 260px; text-align: center"
            >
              <svg
                id="upload-icon"
                width="40.152"
                height="50.415"
                class="mt-2 mb-2"
              >
                <g
                  id="Group_136"
                  data-name="Group 136"
                  transform="translate(0)"
                >
                  <path
                    id="Path_135"
                    data-name="Path 135"
                    d="M27.011,0l12.04,12.578c.2.214.41.428.612.645a1.744,1.744,0,0,1,.488,1.247q-.01,14.576-.009,29.153a6.693,6.693,0,0,1-5.937,6.763,6.59,6.59,0,0,1-.888.027q-13.248,0-26.5,0A6.679,6.679,0,0,1,.047,44.5a8.08,8.08,0,0,1-.025-1.035c0-12.2.033-24.4-.022-36.6A6.823,6.823,0,0,1,5.141.083C5.185.074,5.22.029,5.26,0ZM25.205,2.359h-.614q-8.8,0-17.6,0a4.225,4.225,0,0,0-4.512,4.5q0,18.3,0,36.6a4.224,4.224,0,0,0,4.511,4.5q13.076,0,26.151,0a4.254,4.254,0,0,0,4.539-4.535q0-13.738,0-27.476V15.4h-.611c-2.257,0-4.515.016-6.772-.012a5.249,5.249,0,0,1-5.057-4.922c-.066-2.481-.029-4.965-.037-7.448,0-.194,0-.389,0-.654m2.47,1.885c0,2-.013,3.832,0,5.669a2.865,2.865,0,0,0,3.083,3.025c1.564.007,3.128,0,4.692,0,.136,0,.272-.021.482-.038l-8.26-8.656"
                    transform="translate(0 -0.001)"
                    fill="#2e7be6"
                    opacity="0.29"
                  />
                  <path
                    id="Path_136"
                    data-name="Path 136"
                    d="M54.67,72.885v.3q0,3.493,0,6.987a1.316,1.316,0,0,1-.061.466.687.687,0,0,1-.693.424.717.717,0,0,1-.619-.567,1.768,1.768,0,0,1-.027-.362q0-3.465,0-6.931v-.307l-.094-.08a1.456,1.456,0,0,1-.152.25q-1.239,1.34-2.485,2.675a.834.834,0,0,1-.676.338.647.647,0,0,1-.6-.4.661.661,0,0,1,.1-.736c.338-.376.683-.745,1.028-1.115q1.465-1.571,2.933-3.141a.744.744,0,0,1,1.28,0q1.949,2.08,3.891,4.167a.715.715,0,0,1,.021,1.048.692.692,0,0,1-1.042-.1q-1.278-1.362-2.548-2.731c-.063-.068-.118-.144-.176-.216l-.08.038"
                    transform="translate(-33.9 -48.171)"
                    fill="#2e7be6"
                  />
                  <path
                    id="Path_137"
                    data-name="Path 137"
                    d="M43.447,166.687q2.841,0,5.682,0a.7.7,0,1,1,.128,1.389,2.133,2.133,0,0,1-.281.007H37.949c-.075,0-.15,0-.225,0a.721.721,0,0,1-.679-.721.7.7,0,0,1,.719-.67q2.841-.005,5.682,0"
                    transform="translate(-23.39 -131.525)"
                    fill="#2e7be6"
                  />
                </g>
              </svg>
              <p class="ma-0 prepend-text mb-1">
                {{ $tr("drag_and_drop_images_to_upload") }}
              </p>
              <p class="ma-0 prepend-text2">
                {{ $tr("or_browse_files_on_your_computer") }}
              </p>
            </div>
          </template>
        </v-file-input>
        <div class="items d-flex flex-wrap">
          <CFileItemsImage
            v-for="(file, i) in form.landingPage_image.$model"
            :key="i"
            :file="file"
            @removeFile="removeFile"
            @retry="retry"
            @cancelUpload="cancelUpload"
          />
        </div>
      </template>
    </InputCard>
    <InputCard
      class="mt-3"
      :title="$tr('upload_slider_2_images')"
      v-bind="$attrs"
      v-on="$listeners"
    >
      <template slot-scope="{ attrs, listeners }">
        <v-file-input
          class="custom-file form-custom-file"
          outlined
          accept="image/jpeg,image/jpg,image/png"
          prepend-icon=""
          dense
          v-bind="attrs"
          v-on="listeners"
          @change="onChange2"
          :rules="
            validateRule(
              form.landingPage_image2, // validate objec
              $root, // context
              $tr('upload_slider_2_images') // lable for feedback
            )
          "
          :invalid="form.landingPage_image2.$invalid"
          @blur="form.landingPage_image2.$touch()"
          :multiple="true"
        >
          <template slot="prepend-inner">
            <div
              name="prepend-inner"
              style="max-width: 260px; text-align: center"
            >
              <svg
                id="upload-icon"
                width="40.152"
                height="50.415"
                class="mt-2 mb-2"
              >
                <g
                  id="Group_136"
                  data-name="Group 136"
                  transform="translate(0)"
                >
                  <path
                    id="Path_135"
                    data-name="Path 135"
                    d="M27.011,0l12.04,12.578c.2.214.41.428.612.645a1.744,1.744,0,0,1,.488,1.247q-.01,14.576-.009,29.153a6.693,6.693,0,0,1-5.937,6.763,6.59,6.59,0,0,1-.888.027q-13.248,0-26.5,0A6.679,6.679,0,0,1,.047,44.5a8.08,8.08,0,0,1-.025-1.035c0-12.2.033-24.4-.022-36.6A6.823,6.823,0,0,1,5.141.083C5.185.074,5.22.029,5.26,0ZM25.205,2.359h-.614q-8.8,0-17.6,0a4.225,4.225,0,0,0-4.512,4.5q0,18.3,0,36.6a4.224,4.224,0,0,0,4.511,4.5q13.076,0,26.151,0a4.254,4.254,0,0,0,4.539-4.535q0-13.738,0-27.476V15.4h-.611c-2.257,0-4.515.016-6.772-.012a5.249,5.249,0,0,1-5.057-4.922c-.066-2.481-.029-4.965-.037-7.448,0-.194,0-.389,0-.654m2.47,1.885c0,2-.013,3.832,0,5.669a2.865,2.865,0,0,0,3.083,3.025c1.564.007,3.128,0,4.692,0,.136,0,.272-.021.482-.038l-8.26-8.656"
                    transform="translate(0 -0.001)"
                    fill="#2e7be6"
                    opacity="0.29"
                  />
                  <path
                    id="Path_136"
                    data-name="Path 136"
                    d="M54.67,72.885v.3q0,3.493,0,6.987a1.316,1.316,0,0,1-.061.466.687.687,0,0,1-.693.424.717.717,0,0,1-.619-.567,1.768,1.768,0,0,1-.027-.362q0-3.465,0-6.931v-.307l-.094-.08a1.456,1.456,0,0,1-.152.25q-1.239,1.34-2.485,2.675a.834.834,0,0,1-.676.338.647.647,0,0,1-.6-.4.661.661,0,0,1,.1-.736c.338-.376.683-.745,1.028-1.115q1.465-1.571,2.933-3.141a.744.744,0,0,1,1.28,0q1.949,2.08,3.891,4.167a.715.715,0,0,1,.021,1.048.692.692,0,0,1-1.042-.1q-1.278-1.362-2.548-2.731c-.063-.068-.118-.144-.176-.216l-.08.038"
                    transform="translate(-33.9 -48.171)"
                    fill="#2e7be6"
                  />
                  <path
                    id="Path_137"
                    data-name="Path 137"
                    d="M43.447,166.687q2.841,0,5.682,0a.7.7,0,1,1,.128,1.389,2.133,2.133,0,0,1-.281.007H37.949c-.075,0-.15,0-.225,0a.721.721,0,0,1-.679-.721.7.7,0,0,1,.719-.67q2.841-.005,5.682,0"
                    transform="translate(-23.39 -131.525)"
                    fill="#2e7be6"
                  />
                </g>
              </svg>
              <p class="ma-0 prepend-text mb-1">
                {{ $tr("drag_and_drop_images_to_upload") }}
              </p>
              <p class="ma-0 prepend-text2">
                {{ $tr("or_browse_files_on_your_computer") }}
              </p>
            </div>
          </template>
        </v-file-input>
        <div class="items d-flex flex-wrap">
          <CFileItemsImage
            v-for="(file, i) in form.landingPage_image2.$model"
            :key="i"
            :file="file"
            @removeFile="removeFile2"
            @retry="retry2"
            @cancelUpload="cancelUpload2"
          />
        </div>
      </template>
    </InputCard>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CFileUpload from "../../new_form_components/Inputs/CFileUploadTemp";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CFileItemsImage from "../../new_form_components/Inputs/CFileItemsImage.vue";

export default {
  props: {
    form: Object,
  },
  components: { CFileUpload, InputCard, CFileItemsImage },
  data() {
    return {
      key: 0,
      validateRule: GlobalRules.validate,
    };
  },
  methods: {
    async validate() {
      this.form.landingPage_image.$touch();
      this.form.landingPage_image2.$touch();
      console.log("image", this.form);
      let isValid =
        !this.form.landingPage_image.$invalid &&
        !this.form.landingPage_image2.$invalid;
      return isValid;
    },
    onChange(files) {
      let temp = [];
      for (let i = 0; i < files?.length; i++) {
        const file = files[i];
        temp.push({
          id: this.generateID(),
          file: file,
          name: file.name,
          progress: 0,
          retry: false,
          preview: this.getImage(file),
        });
      }
      this.form.landingPage_image.$model = [
        ...this.form.landingPage_image.$model,
        ...temp,
      ];
      temp.forEach((image) => {
        this.upload(image);
      });
    },
    onChange2(files) {
      let temp = [];
      for (let i = 0; i < files?.length; i++) {
        const file = files[i];
        temp.push({
          id: this.generateID(),
          file: file,
          name: file.name,
          progress: 0,
          retry: false,
          preview: this.getImage(file),
        });
      }
      this.form.landingPage_image2.$model = [
        ...this.form.landingPage_image2.$model,
        ...temp,
      ];
      temp.forEach((image) => {
        this.upload2(image);
      });
    },
    async upload(item) {
      try {
        const app = this;
        const request = new Promise(async function (resolve, reject) {
          const formData = new FormData();
          formData.append("image", item.file);
          let source = app.$axios.CancelToken.source();
          item.source = source;
          const url = `product-management/pr-products/images`;
          app.$axios
            .post(url, formData, {
              cancelToken: source.token,
              onUploadProgress: function (progressEvent) {
                let uploadProgress =
                  parseInt(
                    Math.round(
                      (progressEvent.loaded / progressEvent.total) * 100
                    )
                  ) - 5;

                app.updateProgress(item.id, uploadProgress);
              }.bind(app),
            })
            .then((response) => {
              if (response.data.result == true) {
                app.updateProgress(item.id, 100, response.data.path);
              }
            })
            .catch(({ response }) => {
              app.addtoRetry(item);
            });
        });
        return await request;
      } catch (e) {}
    },
    async upload2(item) {
      try {
        const app = this;
        const request = new Promise(async function (resolve, reject) {
          const formData = new FormData();
          formData.append("image", item.file);
          let source = app.$axios.CancelToken.source();
          item.source = source;
          const url = `product-management/pr-products/images`;
          app.$axios
            .post(url, formData, {
              cancelToken: source.token,
              onUploadProgress: function (progressEvent) {
                let uploadProgress =
                  parseInt(
                    Math.round(
                      (progressEvent.loaded / progressEvent.total) * 100
                    )
                  ) - 5;

                app.updateProgress2(item.id, uploadProgress);
              }.bind(app),
            })
            .then((response) => {
              if (response.data.result == true) {
                app.updateProgress2(item.id, 100, response.data.path);
              }
            })
            .catch(({ response }) => {
              app.addtoRetry2(item);
            });
        });
        return await request;
      } catch (e) {}
    },
    removeFile(file) {
      this.cancelUpload(file);
      this.form.landingPage_image.$model =
        this.form.landingPage_image.$model.filter(
          (image) => image.id != file.id
        );
      if (file.progress == 100) {
        this.deleteFileBackend(file);
      }
    },
    removeFile2(file) {
      this.cancelUpload(file);
      this.form.landingPage_image2.$model =
        this.form.landingPage_image2.$model.filter(
          (image) => image.id != file.id
        );
      if (file.progress == 100) {
        this.deleteFileBackend2(file);
      }
    },
    retry(passedItem) {
      let item = this.form.landingPage_image.$model.find(
        (item) => item.id == passedItem.id
      );
      item.retry = false;
      this.upload(item);
    },
    retry2(passedItem) {
      let item = this.form.landingPage_image2.$model.find(
        (item) => item.id == passedItem.id
      );
      item.retry = false;
      this.upload2(item);
    },
    cancelUpload(passedItem) {
      let item = this.form.landingPage_image.$model.find(
        (item) => item.id == passedItem.id
      );
      if (item && item.source) {
        item.source.cancel("file-cancled");
        delete item.source;
        this.addtoRetry(item);
      }
    },
    cancelUpload2(passedItem) {
      let item = this.form.landingPage_image2.$model.find(
        (item) => item.id == passedItem.id
      );
      if (item && item.source) {
        item.source.cancel("file-cancled");
        delete item.source;
        this.addtoRetry2(item);
      }
    },
    updateProgress(id, progress, path = "") {
      this.form.landingPage_image.$model =
        this.form.landingPage_image.$model.map((item) => {
          if (item.id == id) {
            item.progress = progress;
            if (path !== "") {
              item.path = path;
            }
          }
          return item;
        });
    },
    updateProgress2(id, progress, path = "") {
      this.form.landingPage_image2.$model =
        this.form.landingPage_image2.$model.map((item) => {
          if (item.id == id) {
            item.progress = progress;
            if (path !== "") {
              item.path = path;
            }
          }
          return item;
        });
    },
    addtoRetry(item) {
      this.form.landingPage_image.$model =
        this.form.landingPage_image.$model.map((item2) => {
          if (item.id == item2.id) {
            item.retry = true;
          }
          return item2;
        });
    },
    addtoRetry2(item) {
      this.form.landingPage_image2.$model =
        this.form.landingPage_image2.$model.map((item2) => {
          if (item.id == item2.id) {
            item.retry = true;
          }
          return item2;
        });
    },
    async deleteFileBackend(item) {
      try {
        let res = await this.$axios.post(
          "product-management/pr-products/images",
          {
            file_path: item.path,
          }
        );
      } catch (e) {}
    },
    async deleteFileBackend2(item) {
      try {
        let res = await this.$axios.post(
          "product-management/pr-products/images",
          {
            file_path: item.path,
          }
        );
      } catch (e) {}
    },
    generateID() {
      return (
        "Id" +
        Math.floor(
          (Date.now() *
            Math.floor(
              Math.random() * Math.floor(Math.random() * Date.now())
            )) /
            (Math.random() * 1000000000)
        )
      );
    },
    generateID2() {
      return (
        "Id" +
        Math.floor(
          (Date.now() *
            Math.floor(
              Math.random() * Math.floor(Math.random() * Date.now())
            )) /
            (Math.random() * 1000000000)
        )
      );
    },
    getImage(file) {
      return URL.createObjectURL(file);
    },
    getImage2(file) {
      return URL.createObjectURL(file);
    },
  },
};
</script>

<style>
</style>