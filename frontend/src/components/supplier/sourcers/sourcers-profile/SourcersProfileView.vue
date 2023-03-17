<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="700">
      <v-card>
        <v-card-title>
          <div class="d-flex">
            <h4 class="">{{ $tr("sourcer_view") }}</h4>
          </div>
          <div class="ml-auto">
            <CloseBtn @click="dialog = false" />
          </div>
        </v-card-title>
        <v-divider></v-divider>

        <v-card-text>
          <v-card class="mt-2">
            <v-card-title>
              <v-avatar color="primary" size="55">
                <span style="width: 25px; height: 20px">
                  <svg
                    viewBox="0 0 12.393 16.68"
                    width="13.809"
                    height="10.175"
                  >
                    <path
                    id="sourcer"
                      d="M2320.2-3079.32h-.036a1.494,1.494,0,0,1-.693-.189h0a1.505,1.505,0,0,1-.351-.271l-4.677-4.676a1.5,1.5,0,0,1,0-2.121,1.5,1.5,0,0,1,2.121,0l2.136,2.137V-3094.5a1.5,1.5,0,0,1,1.5-1.5,1.5,1.5,0,0,1,1.5,1.5v10.058l2.136-2.136a1.5,1.5,0,0,1,2.121,0,1.5,1.5,0,0,1,0,2.121l-4.679,4.679a1.5,1.5,0,0,1-1.042.457h-.036Z"
                      transform="translate(-2314 3096)"
                      fill="#fff"
                    />
                  </svg>
                </span>
              </v-avatar>
              <div class="d-block">
                <h5 class="ml-2" style="font-size: 1.2rem">{{ item?.name }}</h5>
                <h5 class="ml-2" style="font-size: 1rem">
                  {{ item?.last_name }}
                </h5>
              </div>
            </v-card-title>
            <v-card-text class="pa-0">
              <v-col cols="12" class="pa-0 ml-3 mt-1">
                <h4 style="font-size: 1rem">
                  {{ $tr("sourcer_information") }}
                </h4>
              </v-col>
              <v-container style="">
                <v-row>
                  <v-col cols="5" class="ml-1 pa-2">
                    <h4 style="color: #707070; font-size: 1rem">
                      {{ $tr("email") }}
                    </h4>
                  </v-col>
                  <v-col cols="5" class="pa-2" id="span_right_side_styles">
                    <strong style="color: #777; font-size: 1rem">{{
                      item?.email
                    }}</strong>
                  </v-col>
                </v-row>
                <v-row style="background-color: #eeeeee">
                  <v-col cols="5" class="ml-1 pa-2">
                    <h4
                      style="color: #707070; font-size: 1rem; margin-top: 5px"
                    >
                      {{ $tr("phone_number") }}
                    </h4>
                  </v-col>
                  <v-col cols="5" class="pa-2" id="span_right_side_styles">
                    <strong style="color: #777; font-size: 1rem">{{
                      item?.phone_number
                    }}</strong>
                  </v-col>
                </v-row>
                <v-row style="">
                  <v-col cols="5" class="ml-1 pa-2">
                    <h4
                      style="color: #707070; font-size: 1rem; margin-top: 5px"
                    >
                      {{ $tr("status") }}
                    </h4>
                  </v-col>
                  <v-col cols="5" class="pa-2" id="span_right_side_styles">
                    <strong style="color: #777; font-size: 1rem">{{
                      item?.status
                    }}</strong>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="5" class="ml-1 pa-2">
                    <h4 style="color: #707070; font-size: 1rem">
                      {{ $tr("country") }}
                    </h4>
                  </v-col>
                  <v-col cols="5" class="pa-2" id="span_right_side_styles">
                    <strong style="color: #777; font-size: 1rem">{{
                      item?.country?.name
                    }}</strong>
                  </v-col>
                </v-row>
                <v-row style="background-color: #eeeeee">
                  <v-col cols="5" class="ml-1 pa-2">
                    <h4 style="font-size: 1rem">{{ $tr("company") }}</h4>
                  </v-col>
                  <v-col cols="5" class="pa-2" id="span_right_side_styles">
                    <strong style="color: #777; font-size: 1rem">{{
                      item?.company?.name
                    }}</strong>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
          </v-card>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import CloseBtn from "../../../design/Dialog/CloseBtn.vue";

export default {
  props: {
    headerShow: {
      type: String,
    },
    contentRow: {
      type: String,
    },
  },
  data() {
    return {
      dialog: false,
      item: [],
    };
  },
  methods: {
    showViewDialog(items) {
      this.dialog = !this.dialog;
      this.item = items;
    },
  },
  components: { CloseBtn },
};
</script>

<style>
#span_right_side_styles {
  font-size: 18px;
}
</style>
