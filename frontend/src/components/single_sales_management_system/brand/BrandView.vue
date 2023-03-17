<template>
  <div>
    <v-dialog v-model="dialog" width="700" persistent>
      <v-card>
        <v-card-title style="color: #777; border-bottom: 1px solid lightgray">
          <h3>Brand View</h3>
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

        <v-card-text
          style="max-height: 480px; overflow-y: auto; background-color: #eeeeee"
        >
          <br />
          <v-card style="height: 375px">
            <v-row style="padding-left: 20px">
              <v-col cols="1">
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
              </v-col>
              <v-col cols="11" style="padding-left: 40px">
                <h2>Brand Name</h2>
                <h3 style="color: #777">
                  {{ firstname }}
                </h3>
              </v-col>
            </v-row>

            <v-col
              cols="12"
              style="margin-bottom: -5.5rem !important; padding-left: 35px"
            >
              <h3 style="color: #777">
                {{ $tr("brand_Information ") }}
              </h3>
            </v-col>
            <v-container style="padding: 16px; padding-bottom: 0px">
              <v-container style="padding:16px  padding-bottom: 0px;">
                <br />
                <v-row style="">
                  <v-col cols="5">{{ $tr("Name") }} </v-col>
                  <v-col cols="5" id="span_right_side_styles">
                    <strong style="color: #757575">
                      {{ data.name }}
                    </strong>
                  </v-col>
                  <v-col cols="2"></v-col>
                </v-row>
                <v-row style="background-color: #faf8faff">
                  <v-col cols="5" style="background-color: #faf8faff">
                    {{ $tr("Status") }}
                  </v-col>
                  <v-col cols="5" id="span_right_side_styles"
                    ><strong style="color: #757575">{{
                      data.status
                    }}</strong></v-col
                  >
                  <v-col cols="2"></v-col>
                </v-row>
                <v-row style="">
                  <v-col cols="5">{{ $tr("Created By") }} </v-col>
                  <v-col cols="5" id="span_right_side_styles"
                    ><strong style="color: #757575">{{
                      firstname
                    }}</strong></v-col
                  >
                  <v-col cols="2"></v-col>
                </v-row>
              </v-container>
            </v-container>
          </v-card>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" primary @click="closeDialog()">close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  components: {},
  data() {
    return {
      dialog: false,
      data: "",
      logo: "",
      firstname: "",
    };
  },
  methods: {
    toggleViewDialog(item) {
      this.dialog = true;
      console.log("item:", item);
      this.data = item;
      if (item) {
        this.logo = item.attachment?.path;
        this.firstname = item.user.firstname;
      }
    },
    closeDialog() {
      this.dialog = false;
    },
  },
};
</script>

<style>
</style>