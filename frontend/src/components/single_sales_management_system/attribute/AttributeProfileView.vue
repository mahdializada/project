<template>
    <div class="text-center">
      <v-dialog v-model="dialog" width="700">
        <v-card>
          <v-card-title>
            <div class="d-flex">
              <h4 class="">Attribute View</h4>
            </div>
            <div class="ml-auto"> 
              <CloseBtn @click="dialog = false" />
            </div>
          </v-card-title>
          <v-divider></v-divider>
  
          <v-card-text >
            <v-card class="mt-2">
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
                  <h5 class="ml-2" style="font-size: 1rem">{{ data.created_by?.firstname }}</h5>
                </div>
              </v-card-title>
              <v-card-text class="pa-0">
                <v-col cols="12" class="pa-0 ml-3 mt-1">
                  <h4 style="font-size: 1rem">Attribute Information</h4>
                </v-col>
                <v-container style="">
                  <v-row>
                    <v-col cols="5" class="ml-1 pa-2">
                      <h4 style="color: #707070; font-size: 1rem"
                      >{{ $tr('attribute') }}</h4
                      >
                    </v-col>
                    <v-col cols="5" class="pa-2" id="span_right_side_styles">
                      <strong style="color: #777; font-size: 1rem" >{{ data.name }}</strong>
                    </v-col>
                  </v-row>
                  <v-row style="background-color: #eeeeee">
                    <v-col cols="5" class="ml-1 pa-2">
                      <h4 style="color: #707070; font-size: 1rem; margin-top:5px">{{ $tr('value') }}</h4>
                    </v-col>
                    <v-col cols="6" class="pa-2" id="span_right_side_styles">
                      <div class="d-flex flex-wrap">
                        <span
                        v-for="item in data.values"
                        :key="item"
                          class="px-2 mr-1 font-weight-black"
                          style="
                            font-size: 1rem;  
                            background-color: white;      
                            border-radius: 20px;    
                            border: 1px solid #e0e0e0;   
                            padding-top:4px;
                            padding-bottom:4px;
                          "
                        >
                        {{ item }}
                        </span>
                      </div>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="5" class="ml-1 pa-2">
                      <h4 style="color: #707070; font-size: 1rem">{{ $tr('status')}}</h4>
                    </v-col>
                    <v-col cols="5" class="pa-2" id="span_right_side_styles">
                      <strong style="color: #777; font-size: 1rem">{{ data.status }}</strong>
                    </v-col>
                  </v-row>
                  <v-row style="background-color: #eeeeee">
                    <v-col cols="5" class="ml-1 pa-2">
                      <h4 style=" font-size: 1rem"
                        >{{ $tr('created_by')}}</h4
                      >
                    </v-col>
                    <v-col cols="5" class="pa-2" id="span_right_side_styles">
                      <strong style="color: #777; font-size: 1rem"
                        >{{ data.created_by?.firstname }}</strong
                      >
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
  import CloseBtn from '../../design/Dialog/CloseBtn.vue';
  
  export default {
      props: {
          headerShow: {
              type: String
          },
          contentRow: {
              type: String
          }
      },
      data() {
          return {
              dialog: false,
              tags: ["red", "yellow", "purpol"],
              data: [],
          };
      },
      methods: {
          showDialog(item) {
              item = this.$_.cloneDeep(item);
              item.values = item.values;
              this.dialog = !this.dialog;
              this.data = item;
          },
      },
      components: { CloseBtn }
  };
  </script>
  
  <style>
  #span_right_side_styles {
      font-size: 18px;
  }
  </style>
  