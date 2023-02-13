<template>
  <v-dialog v-model="dialog" width="1100">
    <v-card class="pa-3">
      <v-card-title class="pa-0 ma-0">
        <v-row>
          <v-col cols="10">
            <h3 class="pa-5" style="display: inline-block; color: #757575">
              Tabs & Columns
            </h3>
          </v-col>
          <v-col cols="2" class="pa-5">
            <v-btn fab x-small @click="closeModel" style="float: right">
              <svg
                width="50px"
                height="50px"
                viewBox="0 0 700 560"
                fill="currentColor'"
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
          </v-col>
        </v-row>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="4" sm="4" md="4">
            <v-card elevation="5" height="400">
              <h3 style="color: #757575" class="pa-4 ma-0">Categories</h3>
              <template>
                <v-list
                  :items="catg1"
                  class="pa-1 ma-3"
                  style="border: 0.5px solid #0091ea"
                  shaped
                  primary
                  tile
                  dense
                >
                  <v-list-group
                    v-for="item in items"
                    :key="item.title"
                    v-model="item.active"
                    :prepend-icon="item.action"
                    no-action
                  >
                    <template v-slot:activator>
                      <v-list-item-content>
                        <v-list-item-title
                          v-text="item.title"
                        ></v-list-item-title>
                      </v-list-item-content>
                      <v-chip
                        content="6"
                        style="color: #0091ea"
                        label
                        outlined
                        pill
                        small
                        class="float-right"
                      >
                        <h5>2</h5>
                      </v-chip>
                    </template>

                    <v-list-item v-for="child in item.items" :key="child.title">
                      <v-list-item-content>
                        <v-list-item-title
                          v-text="child.title"
                        ></v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list-group>
                </v-list>
              </template>
            </v-card>
          </v-col>

          <v-col cols="4" sm="4" md="4">
            <v-card elevation="5" height="400">
              <div class="pa-4">
                <h3 style="color: #757575; display: inline-block">Columns</h3>
                <span class="float-right mr-3">
                  <a class="mr-5" style="font-size: 15px">
                    <!-- {{
                              allList.length == list.length
                                ? "Unselect All"
                                : "Select All"
                            }} -->
                  </a>
                    <v-btn fab depressed x-small class="search-btn" @click="show">
                      <i class="fa-solid fa-magnifying-glass"></i>
                    </v-btn>
                  </span>
                </div>
                <v-text-field
                      label="Search..."
                      append-icon="mdi-magnify"
                      class="search-txt"
                      v-model="searchData"
                      solo
                      dense
                      depressed
                      style="
                        border-radius: 25px 25px 25px 25px;
                        border: 1px solid lightgrey;
                        height: 40px;
                      "
                    ></v-text-field>
                <div
                class="pa-2 pb-0 ml-5"
                v-for="(column, index) in allList"
                :key="index"
              >
                <v-row>
                  <v-col cols="12" md="12">
                    <div id="d1" style="color: grey">
                      {{ column.text }}
                    </div>
                    <div class="float-right d-flex-end" id="d2">
                      <v-btn icon small>
                        <i id="d3" class="fa-solid fa-gear"></i>
                      </v-btn>
                      <v-btn icon small class="search-btn">
                        <i id="d3" class="fa-sharp fa-solid fa-circle-info"></i>
                      </v-btn>
                      <v-checkbox
                        dense
                        v-model="social"
                        :value="column"
                        class="d4"
                      ></v-checkbox>
                    </div>
                    <v-divider class="mt-2"></v-divider>
                  </v-col>
                </v-row>
              </div>
            </v-card>
          </v-col>
          <v-col cols="4" md="4" sm="6">
            <v-card elevation="5" height="400">
              <h3 class="pa-4" style="color: #757575">Columns Arrangement</h3>
              <v-card
                color="grey lighten-5"
                height="300"
                class="ma-5 mt-0 overflow-y-auto"
              >
                <v-col cols="12">
                  <draggable
                    :list="list"
                    :disabled="!enabled"
                    class="list-group"
                    ghost-class="ghost"
                    :move="checkMove"
                    @start="dragging = true"
                    @end="dragging = false"
                  >
                    <v-card
                      class="list-group-item pa-3 ma-2 d-flex"
                      v-for="element in list"
                      :key="element.id"
                    >
                      <v-row>
                        <v-col cols="10 d-flex">
                          <v-icon style="cursor: pointer"
                            >mdi-pan-vertical</v-icon
                          >
                          <h5 style="color: grey; font-size: 13px">
                            {{ element.text }}
                          </h5>
                        </v-col>
                        <v-col cols="2" class="pr-1">
                          <v-icon class="pr-1" style="cursor: pointer"
                            >mdi-close-circle-outline</v-icon
                          >
                        </v-col>
                      </v-row>
                    </v-card>
                    <!-- {{social}} -->
                  </draggable>
                </v-col>
              </v-card>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
      <template>
        <div>
          <v-col cols="12">
            <v-row>
              <v-col cols="10" md="8" sm="8" class="d-flex">
                <v-select
                  :items="items"
                  item-text="team_name"
                  filled
                  dense
                  rounded
                  class="mr-3"
                  label="Shared Views"
                ></v-select>

                <v-text-field
                  filled
                  rounded
                  dense
                  label="view Name"
                  class="mr-3"
                ></v-text-field>
                <div class="d-flex pa-0">
                  <label for="public" class="mr-4 d-flex mt-4"> public </label>
                  <v-switch
                    id="public"
                    inset
                    dense
                    v-model="showMessages"
                  ></v-switch>
                </div>
                <v-btn color="primary" class="pa-4 mt-3" small> submit </v-btn>
              </v-col>
              <!-- <v-col cols="6" sm="4" md="2"></v-col> -->
              <v-col cols="5" md="4" sm="4">
                <span class="float-right mt-2">
                  <v-btn
                    small
                    class="pa-4"
                    style="
                              margin-top: 5px,
                              color: #0091ea,
                              border: 0.5px solid #0091ea;
                            "
                    @click="cancle"
                  >
                    Cancel
                  </v-btn>
                  <v-btn
                    small
                    class="primary pa-4"
                    style="margin-top: 5px"
                    @click="saveChanges"
                  >
                    Save
                  </v-btn>
                </span>
              </v-col>
            </v-row>
          </v-col>
        </div>
      </template>
    </v-card>
  </v-dialog>
</template>

<script>
import draggable from "vuedraggable";
export default {
  // props: {
  //   allList: {
  //     type: Array,
  //     default: null,
  //   },
  // },
  order: 0,
  data() {
    return {
      enabled: true,
      social: [],
      list: [],
      allList: [],
      dragging: false,
      items: ["Foo", "Bar", "Fizz", "Buzz"],
      ditems: [
        { id: 1, name: "hoge", children: [{ id: 11, name: "hoge-child1" }] },
      ],
      catg1: ["all", "cat1", "cat2"],
      dialog: false,
      offsetTop: 0,
      search: false,
      items: [
        {
          active: true,
          items: [],
          title: "All",
        },
      ],
      showMessages: "",
      radios: "id",
      posts: [],
    };
  },
  computed: {
    draggingInfo() {
      return this.dragging ? "under drag" : "";
    },
  },
  // created(){
  //   this.social = this.header;

  // },
  created() {
    if (typeof window !== "undefined") {
      this.social = JSON.parse(localStorage.getItem(this.$route?.name) || "[]");
    }
  },
  watch: {
    social: function () {
      if (this.social == null) {
        this.social == this.allList;
      }
      this.list = this.social.map((item) => {
        return item;
      });
      return this.social;
    },
  },
  methods: {
    show(){
      this.$attrs('.search-txt').show();
      this.$attrs('.search-btn').hide();
    },
    get() {
      all = this.allList;
      this.$emit("get", all);
    },
    openDialog() {
      this.dialog = true;
    },
    // selectAll() {
    //   if (this.allList.length == this.list.length) {
    //     this.social = [];
    //   } else {
    //     this.social = this.allList;
    //   }
    // },
    saveChanges() {
      localStorage.setItem(this.$route?.name, JSON.stringify(this.list));
      this.$emit("saveChanges");
      this.closeModel();
    },
    toggleDialog() {
      this.$emit("openD");
    },
    checkMove: function (e) {
      window.console.log("Future index: " + e.draggedContext.futureIndex);
    },
    closeModel() {
      this.dialog = false;
      // this.social=this.allList;
    },
    cancle() {
      this.social = [];
    },
  },
  components: { draggable },
};
</script>

<style>
.bb::-webkit-scrollbar {
  width: 15px;
}

.bb::-webkit-scrollbar-track {
  background: #e6e6e6;
  border-left: 1px solid #dadada;
}

.bb::-webkit-scrollbar-thumb {
  background: #b0b0b0;
  border: solid 3px #e6e6e6;
  border-radius: 7px;
}

#d1 {
  display: inline-block;
  font-size: 15px;
  color: #757575;
}
#d2 {
  margin: -8px;
}
#d3 {
  font-size: 13px;
  margin-bottom: 5px;
}
.d4 {
  display: inline-block;
}
#d5 {
  margin-bottom: -15px;
}
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
.v-list-group
  .v-list-group__header
  .v-list-item__icon.v-list-group__header__append-icon {
  min-width: 0px !important;
}
 .search-txt{
  width:50px;
  padding: 5px;
}
</style>