<template>
  <div>
    <v-card class="flex-grow-1 d-flex  v-sheet pa-0 ma-0">
      <div class="d-flex pa-0 ma-0">
        <div class="d-flex flex-column" style="width: 150px">
          <h4 class="ml-2">Search & Filter</h4>
          <h5 class="ml-10">Search Type</h5>
          <div class="d-flex">
            <v-radio-group
              v-model="column"
              column
              class="pt-0 ml-12 mt-0 mr-12"
              style="width: 100px"
            >
              <v-radio
                label="ID"
                value="id"
                class="mb-0"
                style="width: 50px"
              ></v-radio>
              <v-radio
                label="Content"
                value="content"
                style="width: 50px"
              ></v-radio>
            </v-radio-group>
          </div>
        </div>
        
        <div class="d-flex flex-column " style="width: 300px">
          <span>
            <v-text-field
              class="mt-10"
              label="Search..." 
              append-icon="mdi-magnify"
              v-model="searchData"
              @keyup="search"
              solo
              dense
              depressed
              style="
                border: 1px solid lightgrey;
                border-radius: 25px 25px 25px 25px;
                height: 40px;
              "
            ></v-text-field>
          </span>
        </div>

        <div class="flex-column ml-15 mt-4">
          <v-btn
            style="height: 40px; font-weight: 1000; width: 100px"
            outlined
            color="primary "
            class="mt-9 elevation-1"
            depressed
            dense
            small
            solo
            text
            @click="openModel"
            ><div class="d-block">
              <i class="fa fa-filter"></i>
              <div>Filter</div>
            </div></v-btn
          >

          <v-btn
            style="height: 40px; font-weight: 1000; width: 100px"
            outlined
            color="primary"
            class="mt-9 elevation-2"
            depressed
            dense
            small
            solo
            text
            ><div class="d-block">
              <i class="fa fa-table-columns"></i>
              <div>Column</div>
            </div></v-btn
          >

          <v-btn
            style="height: 40px; font-weight: 1000; width: 130px"
            outlined
            color="primary"
            class="mt-9 elevation-2"
            depressed
            dense
            small
            solo
            text
            @click="OpenDownloadModel"
            ><div class="d-block">
              <i class="fa fa-download"></i>
              <div>Download</div>
            </div></v-btn
          >
          <Download ref="downloadModelRef" />
        </div>
      </div>
    </v-card>
    <v-dialog
      v-model="dialog"
      max-width="1200px"
      style="transform-origin: center center; margin: 0px"
    >
      <v-card class="v-sheet card1">
        <v-card-title class="text-h5">
          <span class="mr-3"><i class="fa fa-filter"></i></span>Filter Country
          <v-spacer></v-spacer>
          <v-btn fab x-small @click="dialog = false" style="float: right">
            <svg
              width="50"
              height="60"
              viewBox="0 0 700 560"
              fill="currentColor"
              style="transform: scaleY(-1)"
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
        </v-card-title>
        <v-card-text class="pa-0">
          <v-row justify="space-around">
            <v-col cols="4" sm="8" md="4" class="pr-0">
              <v-card height="470px" class="overflow-y-auto">
                <v-card-title>
                  <h3 class="font-weight-black" style="margin-left: 40%">
                    Data
                  </h3>
                </v-card-title>
                <v-card-text>
                  <h4 class="text-left font-weight-black">Country Name</h4>
                  <v-text-field solo dense outlined placeholder="Country Name">
                  </v-text-field>
                  <h4 class="text-left font-weight-black">Capital</h4>
                  <v-text-field solo dense outlined placeholder="Capital">
                  </v-text-field>
                  <h4 class="text-left font-weight-black">National</h4>
                  <v-text-field solo dense outlined placeholder="National">
                  </v-text-field>
                  <h4 class="text-left font-weight-black">Currency</h4>
                  <v-text-field solo dense outlined placeholder="Currency">
                  </v-text-field>
                  <h4 class="text-left font-weight-black">Region</h4>
                  <v-text-field solo dense outlined placeholder="Region">
                  </v-text-field>
                  <h4 class="text-left font-weight-black">Sub Region</h4>
                  <v-text-field solo dense outlined placeholder="Sub Region">
                  </v-text-field>
                  <h4 class="text-left font-weight-black">Country Name</h4>
                  <v-text-field solo dense outlined placeholder="Country Name">
                  </v-text-field>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="4" sm="8" md="4" class="pr-0 pl-0">
              <v-card height="470px">
                <v-card-title>
                  <h3 class="font-weight-black" style="margin-left: 40%">
                    Data Range
                  </h3>
                </v-card-title>
                <v-card-text>
                  <h4 class="text-left font-weight-black">Created At</h4>
                  <v-text-field
                    solo
                    dense
                    outlined
                    append-icon="mdi-calendar"
                    placeholder="Created At"
                  >
                  </v-text-field>
                  <h4 class="text-left font-weight-black">Updated At</h4>
                  <v-text-field
                    append-icon="mdi-calendar"
                    solo
                    dense
                    outlined
                    placeholder="Updated At"
                  >
                  </v-text-field>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="4" sm="8" md="4">
              <v-card height="470px">
                <v-card-title>
                  <h3 class="font-weight-black" style="margin-left: 40%">
                    ID Filtering
                  </h3>
                </v-card-title>
                <v-card-text class="pb-2">
                  <h4 class="text-left font-weight-black">ID</h4>
                  <v-text-field
                    solo
                    dense
                    outlined
                    append-icon="mdi-calendar"
                    placeholder="1000100"
                  >
                  </v-text-field>
                  <v-text-field class="mt-2" dense outlined> </v-text-field>
                </v-card-text>
                <v-card-action>
                  <v-btn-toggle
                    rounded
                    dense
                    solo
                    small
                    class="p-0 mr-3 mb-5"
                    style="float: right"
                  >
                    <v-btn style="height: 20px; font-size: 10px">Exclode</v-btn>
                    <v-btn style="height: 20px; font-size: 10px">Inclode</v-btn>
                  </v-btn-toggle>
                </v-card-action>
              </v-card>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions >
          <span>
            <v-btn
              class="mt-2"
              color="warning"
              style="float: right"
              solo
              depressed
              >Submit</v-btn
            >
          </span>
          <span>
            <v-btn
              class="mt-2"
              color="warning"
              style="float: right"
              solo
              depressed
              >Submit</v-btn
            >
          </span>
          <span>
            <v-btn
              class="mt-2"
              color="warning"
              style="float: right"
              solo
              depressed
              >Submit</v-btn
            >
          </span>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import Download from "./Download.vue";

export default {
  props: {
    download_data: {
      type: Array,
      default: function () {
        return [];
      },
    },
  },
  data() {
    return {
      column: "id",
      loading: false,
      items: [],
      select: null,
      dialog: false,
      searchData: "",
    };
  },
  methods: {
    openModel() {
      this.dialog = true;
    },
    OpenDownloadModel() {
      this.$refs.downloadModelRef.openDialog(this.download_data);
    },
    search(){
      this.$emit('search',this.searchData)
    }
  },
  components: { Download },
};
</script>

<style scoped>
.icon1 {
  cursor: pointer;
}

.v-dialog:not(.v-dialog--fullscreen) {
  max-height: 100%;
}
.te {
  text-align: center;
}
.v-messages theme--light {
  display: none;
}
</style>
