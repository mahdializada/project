<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="700" persistent>
      <v-card>
        <v-card-title>
          <div class="d-flex">
            <h4 class="">Category View</h4>
          </div>
          <div class="ml-auto">
            <CloseBtn @click="dialog = false" />
          </div>
        </v-card-title>
        <v-divider></v-divider>

        <v-card-text>
          <v-card class="mt-2" elevation="2">
            <v-card-title>
              <v-avatar color="primary" size="55">
                <span style="width: 25px; height: 20px">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="13.809"
                    height="10.175"
                    viewBox="0 0 13.809 10.175"
                  >
                    <path
                      id="attribute"
                      d="M15.133,8.11a1.449,1.449,0,0,0-1.185-.61l-7.994.007A1.452,1.452,0,0,0,4.5,8.954v7.268a1.452,1.452,0,0,0,1.454,1.446l7.994.007a1.449,1.449,0,0,0,1.185-.61l3.176-4.477Z"
                      transform="translate(-4.5 -7.5)"
                      fill="#fff"
                    />
                  </svg>
                </span>
              </v-avatar>
              <div class="d-block">
                <h5 class="ml-2" style="font-size: 1.2rem">{{ data.name }}</h5>
                <h5 class="ml-2" style="font-size: 1rem">
                  {{ user }}
                </h5>
              </div>
            </v-card-title>
            <v-card-text class="pa-0">
              <v-col cols="12" class="pa-0 ml-3 mt-1">
                <h4 style="font-size: 1rem">Category Information</h4>
              </v-col>
              <v-container style="">
                <v-row
                  style="
                    margin: 0px !important;
                    padding: 0px !important;
                    height: 4rem !important;
                  "
                >
                  <v-col cols="12" md="3"
                    ><h3 style="margin-left: -20px !important">
                      {{ $tr("name") }}
                    </h3></v-col
                  >
                  <v-col cols="12" md="3">
                    <h3 style="margin-left: -15px !important">
                      {{ data.name }}
                    </h3></v-col
                  >
                </v-row>
                <v-row
                  style="
                    background-color: #faf8faff;
                    margin: 0px !important;
                    padding: 0px !important;
                    height: 4rem !important;
                  "
                >
                  <v-col cols="12" md="3"
                    ><h3
                      style="margin-left: -20px; margin-top: -5px !important"
                    >
                      {{ $tr("status") }}
                    </h3></v-col
                  >
                  <v-col cols="12" md="3">
                    <h3 style="margin-left: -15px; margin-top: -5px !important">
                      {{ data.status }}
                    </h3></v-col
                  >
                </v-row>
                <v-row
                  style="
                    margin: 0px !important;
                    padding: 0px !important;
                    height: 4rem !important;
                  "
                >
                  <v-col cols="12" md="3"
                    ><h3 style="margin-left: -20px !important">
                      {{ $tr("created_by") }}
                    </h3></v-col
                  >
                  <v-col cols="12" md="3">
                    <h3 style="margin-left: -15px !important">
                      {{ user }}
                    </h3></v-col
                  >
                </v-row>
              </v-container>
            </v-card-text>
          </v-card>
        </v-card-text>
        <v-divider></v-divider>
        <v-card style="margin-top: 0px">
          <v-card-title style="color: #777" class="px-2 pt-2 pb-2">
            <v-spacer></v-spacer>
            <v-btn large color="primary" @click="dialog = false" class="mr-1">Close</v-btn>
          </v-card-title>
        </v-card>
      </v-card>
    </v-dialog>
  </div>
</template>
  
  <script>
import CloseBtn from "../../design/Dialog/CloseBtn.vue";

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
      tags: ["red", "yellow", "purpol"],
      data: [],
      user: '',
    };
  },
  methods: {
    showDialog(item) {
        if(item){
            this.user = item.user.firstname;
        }
      item = this.$_.cloneDeep(item);
      this.dialog = !this.dialog;
      this.data = item;
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
  