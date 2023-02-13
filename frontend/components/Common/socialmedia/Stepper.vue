<template>
  <v-row>
    <v-col class="overflow-y-auto" id="scroll-target">
      <v-dialog
        v-model="dialog"
        width="1100"
        style="overflow-y: hidden !important;"
        persistent
      >
        <template>
          <div>
            <v-stepper v-model="e1" style="padding: 0;">
              <v-card style="background-color:#80808012">
              <v-btn
                fab
                x-small
                @click="closeModel"
                style="float: right; margin: 15px;"
              >
                <svg
                  width="50px"
                  height="50px"
                  viewBox="0 0 700 560"
                  fill="currentColor'"
                  style="transform: scaleY(-1);"
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
              </v-btn>
              <!-- <v-btn
                    outlined
                    dismissible
                    fab
                    color="indigo"
                    small
                    style="float:right !important"
                    @click="closeModel"
                    class="ma-4"
                    >
                    <v-icon>mdi-close</v-icon>
                  </v-btn> -->
              <v-stepper-header
                style="
                  margin-right: 200px;
                  margin-left: 200px;
                  box-shadow: none;
                "
              >
                <v-stepper-step
                  :complete="e1 > 1"
                  step="1"
                  style="
                    display: inline-block !important;
                    text-align: center;
                    padding-top: 30px;
                  "
                >
                  General
                </v-stepper-step>
                <v-divider></v-divider>
                <v-stepper-step
                  :complete="e1 > 2"
                  step="2"
                  icon
                  style="
                    display: inline-block;
                    text-align: center;
                    padding-top: 30px;
                  "
                >
                  Done
                </v-stepper-step>
              </v-stepper-header>
            </v-card>
              <v-stepper-items>
                <v-stepper-content step="1">
                  <v-card
                    height="400px"
                    style="overflow-y: scroll; padding: 30px;"
                  >
                    <v-form ref="form">
                      <label for="">Social Media Name *</label>
                      <v-text-field
                        :counter="10"
                        v-model="name"
                        :rules="nameRules"
                        label="Name"
                        required
                        dense
                        outlined
                        lable="Social Media Name"
                      ></v-text-field>
                      <br />
                      <template>
                        <div>
                          <!-- @change="onChange"
                      :rules="nameRules"
                      required
                      v-model="image"
                      @vdropzone-error="onError"
                      @vdropzone-success="onSuccess"
                      @vdropzone-file-added="onFileAdded"
                      type="file"
                      label="image"
                      :previewElement="true"
                       -->
                          <dropzone
                            style="border: 1px dashed darkgray;"
                            @vdropzone-complete="onComplete"
                            @vdropzone-sending-multiple="sending"
                            ref="myDropzone"
                            id="dropzone"
                            :options="dropzoneOptions"
                          ></dropzone>

                          <!-- <dropzone v-model="fileSingle"></dropzone> -->

                          <!-- <dropzone id="bar" ref="bar" :options="options" :destroyDropzone="true" :useCustomSlot="true">
                    <div class="dropzone-custom-content">
                      <h3 class="dropzone-custom-title">Custom slot</h3>
                      <p class="subtitle">Subtitle</p>
                      </div>
                    </dropzone> -->
                        </div>
                      </template>
                      <label for="">Website*</label>
                      <v-text-field
                        v-model="website"
                        dense
                        :rules="nameRules"
                        required
                        outlined
                        label="website"
                      ></v-text-field>
                      <br />
                      <label for="">
                        A sample link to user accounts or pages of this social
                        media
                      </label>
                      <v-text-field
                        dense
                        outlined
                        label="a sample link to user accounts or pages of this social media"
                      ></v-text-field>
                      <v-btn
                        class="my-2 mr-16"
                        color="primary"
                        @click="save"
                        style="float: right;"
                      >
                        save
                      </v-btn>
                    </v-form>
                  </v-card>
                </v-stepper-content>
                <v-stepper-content step="2">
                  <v-card></v-card>
                </v-stepper-content>
              </v-stepper-items>
            </v-stepper>
          </div>
        </template>
      </v-dialog>
    </v-col>
  </v-row>
</template>
<script>
import Dropzone from 'nuxt-dropzone'
import 'nuxt-dropzone/dropzone.css'

export default {
  data() {
    return {
      offsetTop: 0,
      e1: 1,
      website: '',
      name: '',
      valid: true,
      nameRules: [
        (v) => !!v || 'Name is required',
        (v) => (v && v.length <= 10) || 'Name must be less than 10 characters',
      ],
      dialog: false,
      // headers: { "My-Awesome-Header": "header value" }
      dropzoneOptions: {
        url: 'http://localhost:8000/api/socialmedia',
        thumbnailWidth: 150,
        maxFilesize: 5,
        parallelUploads: 5,
        maxFiles: 5,
        autoProcessQueue: false,
        uploadMultiples: true,
      },
    }
  },
  methods: {
    openDialog() {
      this.dialog = !this.dialog
      this.name = ''
      this.image = ''
      this.website = ''
    },
    closeModel() {
      this.dialog = false
    },

    // onFileAdded(e) {
    //   console.log(e);

    // },
    // onError(e) {
    // },
    // onSuccess(e) {

    // },
    async onComplete(response) {
      if (response.status == 'success') {
        console.log('upload success')
      } else {
        console.log('not uploaded')
      }
    },
    async save() {
      this.$refs.myDropzone.processQueue()
    },
    // async sending(files, xhr){
    //    let form=new FormData();
    //   form.append('name',this.name);
    //   //  console.log(formData.append('name',this.name));
    //   form.append('website',this.website);
    //   const insert=await this.$axios.post('socialmedia',formData);
    //   console.log(insert);
    //   if(insert.status==201){
    //     console.log(insert.status);
    //     // this.datas.unshift(insert.data);
    //   }
    // },
    sending: async function (files, xhr, formData) {
      formData.append('name', this.name)
      formData.append('website', this.website)
    },
  },
  components: {
    Dropzone,
  },
}
</script>
