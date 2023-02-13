<template>
  <v-dialog v-model="manualModadl" width="1000" height="300px" persistent>
    <v-card>
      <template>
        <v-stepper  v-model="e1">
           <v-stepper alt-labels>
                <v-stepper-header center>
                <v-stepper-step  :complete="e1 > 1" step="1">
                    Choose File
                </v-stepper-step>

                <v-divider></v-divider>

                <v-stepper-step  :complete="e1 > 2" step="2">
                    File Upload
                </v-stepper-step>
                <v-divider></v-divider>

                <v-stepper-step :complete="e1 > 3" step="3">
                    check
                </v-stepper-step>

                <v-divider></v-divider>

                <v-stepper-step step="4">
                    Done
                </v-stepper-step>
                        <v-btn
                        class="ma-3"
                        outlined
                        fab
                        small
                        color="indigo"
                        @click="colesDialog"
                        >
                        <v-icon>mdi-close</v-icon>
                        </v-btn>
                </v-stepper-header>
            </v-stepper>

                    <v-stepper-items>
                        <v-stepper-content step="1">
                        <v-card
                            class="mb-12"
                            height="200px"
                        >
                          <template>
                            <dropzone 
                            id="foo"
                            ref="el"
                            :options="options" 
                            :destroyDropzone="true"
                            style="border: 1px dashed; height:30px"
                            >
                              <!-- <v-icon dark>
                                mdi-cloud-upload
                              </v-icon> -->
                            </dropzone>
                          </template>
                            <v-btn
                              class="ma-2"
                              color="primary"
                              small
                              dark
                            >
                              Download Format
                              <v-icon dark>
                                mdi-cloud-download
                              </v-icon>
                            </v-btn>
                        </v-card>

                        <v-btn color="primary" @click="e1 = 2" style="float: right"
                            >Next<i class="fa-solid fa-angles-right"></i>
                        </v-btn>
                        </v-stepper-content>

                        <v-stepper-content step="2">
                        <v-card
                            class="mb-12"
                            height="200px"
                        ></v-card>

                        <v-btn @click="e1 = 1">
                            <i class="fa-solid fa-angles-left"></i>Prev
                        </v-btn>
                        <v-btn color="primary" @click="e1 = 3" style="float: right"
                            >Next<i class="fa-solid fa-angles-right"></i>
                        </v-btn>
                        </v-stepper-content>

                        <v-stepper-content step="3">
                        <v-card
                            class="mb-12"
                            height="200px"
                        ></v-card>

                        <v-btn @click="e1 = 2">
                            <i class="fa-solid fa-angles-left"></i>Prev
                        </v-btn>
                        <v-btn color="primary" @click="e1 = 4" style="float: right"
                            >Next<i class="fa-solid fa-angles-right"></i>
                        </v-btn>
                        </v-stepper-content>
                        <v-stepper-content step="4">
                        <v-card
                            class="mb-12"
                            height="200px"
                        ></v-card>
                        <v-btn @click="e1 = 3">
                            <i class="fa-solid fa-angles-left"></i>Prev
                        </v-btn>
                        <v-btn
                            type="submit"
                            color="primary"
                            class="btn btn-primary"
                            style="float: right; margin-left:10"
                            text
                            :loading="load"
                            >Submit
                        </v-btn>
                        </v-stepper-content>
                    </v-stepper-items>
                    </v-stepper>
                </template></v-card
                ></v-dialog >
            </template>
      </v-card>
    </v-dialog>
</template>

<script>
import Dropzone from 'nuxt-dropzone';
import 'nuxt-dropzone/dropzone.css';

export default {
   components: {
    Dropzone
  },
  data() {
    return {
        options: {
        url: "http://httpbin.org/anything"
      },
      e1: 1,
      load: false,
      manualModadl: false,
    };
  },
  methods: {
    async openManual() {
      this.manualModadl = true;
    },
    colesDialog() {
      //   this.dialog2 = false;
      this.manualModadl = false;
      //   this.reset();
      this.step = 1;
    },
    mounted() {
    // Everything is mounted and you can access the dropzone instance
    const instance = this.$refs.el.dropzone
  }
  },
};
</script>

<style scoped>
.v-sheet.v-stepper:not(.v-sheet--outlined) ,.v-sheet.v-card:not(.v-sheet--outlined){
  box-shadow: 0px 0px 0px 0px rgb(0 0 0 / 0%), 0px 0px 0px 0px rgb(0 0 0 / 0%),
    0px 0px 0px 0px rgb(0 0 0 / 0%);
}
</style>