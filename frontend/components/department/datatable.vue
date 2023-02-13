<template>
  <div>
    <template>
      <filtering ref="filterRef" @filterData="filterData" :title="'akldjfklajdflk'"/>
    </template>
    <template>
      <div class="text-center">
        <v-dialog v-model="dialog" width="1150" persistent>
          <v-stepper v-model="step" alt-labels non-linear>
            <v-stepper-header style="background-color: aliceblue">
              <v-stepper-step step="1"> Choose File </v-stepper-step>
              <v-divider></v-divider>
              <v-stepper-step step="2"> File Upload </v-stepper-step>
              <v-divider></v-divider>
              <v-stepper-step step="3"> Check </v-stepper-step>
              <v-divider></v-divider>
              <v-stepper-step step="4"> Done </v-stepper-step>
              <v-btn
                class="ma-3"
                outlined
                fab
                small
                color="indigo"
                @click="closeDialog"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-stepper-header>
            <v-stepper-items>
              <v-steppper-content>
                <template>
                  <v-row class="ma-10">
                    <v-col cols="12">
                      <dropzone
                        style="border: 1px dashed darkgrey; border-radius: 5px"
                        type="file"
                        v-model="image"
                        @vdropzone-file-added="onFileAdded"
                        @vdropzone-error="onError"
                        @vdropzone-success="onSuccess"
                        @vdropzone-complete="onComplete"
                        :previewElement="false"
                        ref="myDropzone"
                        id="dropzone"
                        :options="dropzoneOptions"
                      >
                      </dropzone>
                      <v-btn color="primary" class="mt-5"
                        >Download Format
                        <span class="fa fa-download ml-1"></span
                      ></v-btn>
                    </v-col>
                  </v-row>
                  <v-row class="ma-10" style="background-color: aliceblue">
                    <v-col cols="12">
                      <v-btn
                        color="primary"
                        @click="step = 2"
                        style="float: right"
                      >
                        Next <i class="fa-solid fa-angles-right"></i
                      ></v-btn>
                    </v-col>
                  </v-row>
                </template>
              </v-steppper-content>
            </v-stepper-items>
          </v-stepper>
        </v-dialog>
      </div>
    </template>

    <template>
      <v-card class="mt-8 pa-0">
        <h4 class="ml-4">Search & Filter</h4>
        <h5 class="ml-6">Search Type</h5>
        <v-row class="ml-5">
          <v-col cols="6" md="2" sm="3" style="margin-bottom: 0px">
            <v-radio-group
              column
              style="margin-top: 6px; padding-top: 0px"
              v-model="radios"
              mandatory
            >
              <v-radio
                active-class
                label="ID"
                color="primary"
                value="id"
                style="margin-bottom: 2px"
              ></v-radio>
              <v-radio
                label="Content"
                color="primary"
                value="content"
              ></v-radio>
            </v-radio-group>
          </v-col>
          <v-col cols="6" md="4" sm="8">
            <v-text-field
              style="display: inline-block; margin-top: 17px"
              outlined
              dense
              rounded
              clearable
              append-icon="mdi-magnify"
              label="Search..."
              v-model="searchData"
              @keyup="search"
            >
            </v-text-field>
          </v-col>
          <v-col cols="6" md="3" sm="6">
            <v-btn color="white" class="mt-5" @click="openFilter">
              <div class="d-block">
                <i
                  class="mdi mdi-filter"
                  aria-hidden="true"
                  style="color: blue"
                ></i>
                <h6 style="color: blue">Filter</h6>
              </div>
            </v-btn>
            <!-- columns -->
            <v-btn color="white" class="mt-5" @click="columnDialog">
              <div class="d-block">
                <i
                  class="fa-solid fa-table-columns"
                  aria-hidden="true"
                  style="color: blue"
                ></i>
                <h6 style="color: blue">Column</h6>
              </div>
            </v-btn>

            <v-dialog v-model="opencolumn" width="1200">
              <v-card class="pa-3">
                <v-card-title class="pa-0 ma-0">
                  <v-row style="background-color:red">
                    <v-col cols="10" style="background-color:green">
                      <h3
                        class="pa-5"
                        style="color: #757575; display: inline-block;"
                      >Tabs & Columns</h3>
                    </v-col>
                    <v-col cols="2" class="pa-5" style="background-color:purple">
                      <v-btn fab x-small @click="opencolumn= false" style="float: right">
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
                      <v-card height="400" elevation="5">
                        <h3 style="color: #757575" class="pa-4 ma-0">Categories</h3>
                        <template>
                          <v-list
                            class="pa-1 ma-3"
                            style="border: 0.5px solid #0091ea"
                            shaped
                            primary
                            tile
                            dense
                          >
                          <!-- remain -->
                        </v-list>
                        </template>
                      </v-card>
                    </v-col>
                    <v-col cols="4" sm="4" md="4">
                      <v-card height="400" elevation="5">
                        <div class="pa-4">
                          <h3 style="color: #757575; display: inline-block;">Columns</h3>
                          <span class="d-flex mt-2 float-right">
                            <a
                              @click="selectAll"
                              class="mr-5"
                              style="font-size: 15px"
                            >
                              <!-- remain -->
                            </a>
                            <v-text-field
                              label="Search..."
                              placeholder="Search..."
                              v-model="searchColumn"
                              class="search_txt mt-3 pa-0"
                              x-small
                              dense
                              solo
                              :class="expand ? 'expand' : ''"
                              style="position:absolute;right:5px"
                            >
                            <template slot="append">
                              <v-btn
                                fab
                                text
                                x-small
                                pr-5
                                mr-5
                                @click="toggleExpand()"
                              >
                                <v-icon class="mr-2">mdi-magnify</v-icon>
                              </v-btn>
                            </template>
                            </v-text-field>
                          </span>
                        </div>
                        <div class="pa-2 pb-0 ml-5 mr-3 mt-3">
                          <v-row>
                            <v-col cols></v-col>
                          </v-row>

                        </div>

                      </v-card>
                    </v-col>
                    <v-col cols="4" sm="4" md="4">
                      <v-card height="400" elevation="5">
                        <h3 style="color: #757575" class="pa-4 ma-0">Columns</h3>
                        <template>
                          <v-list
                            class="pa-1 ma-3"
                            style="border: 0.5px solid #0091ea"
                            shaped
                            primary
                            tile
                            dense
                          >
                          All
                        </v-list>
                        </template>
                      </v-card>
                    </v-col>
                  </v-row>


                </v-card-text>
              </v-card>

            </v-dialog>

            <!-- <v-dialog v-model="opencolumn" width="1200">
             <v-card class="pa-3">
                <v-card-title class="pa-0 ma-0">
                  <v-row>
                    <v-col cols="10">
                      <h3
                        class="pa-5"
                        style="display: inline-block; color: #757575"
                      >
                        Tabs & Columns
                      </h3>
                    </v-col>
                    <v-col cols="2" class="pa-5">
                      <v-btn
                        fab
                        x-small
                        @click="opencolumn = false"
                        style="float: right"
                      >
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
                        <h3 style="color: #757575" class="pa-4 ma-0">
                          Categories
                        </h3>
                        <template>
                          <v-list
                            class="pa-1 ma-3"
                            style="border: 0.5px solid #0091ea"
                            shaped
                            primary
                            tile
                            dense
                          >
                            <v-list-group
                              v-for="item in category"
                              :key="item.title"
                              v-model="item.active"
                              no-action
                            >
                              <template v-slot:activator>
                                <v-list-item-content>
                                  <v-list-item-title v-text="item.title">
                                  </v-list-item-title>
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
                                  <h5>{{ social.length }}</h5>
                                </v-chip>
                              </template>
                            </v-list-group>
                          </v-list>
                        </template>
                      </v-card>
                    </v-col>
                    <v-col cols="4" sm="4" md="4">
                      <v-card elevation="5" height="400">
                        <div class="pa-4">
                          <h3 style="color: #757575; display: inline-block">
                            Columns
                          </h3>
                          <span class="float-right d-flex mt-2">
                            <a
                              @click="selectAll"
                              class="mr-5"
                              style="font-size: 15px"
                            >
                              {{
                                allList.length == list.length
                                  ? "Unselect All"
                                  : "Select All"
                              }}
                            </a>
                            <v-text-field
                              label="Search..."
                              placeholder="Search..."
                              v-model="searchColumn"
                              class="search-txt mt-3 pa-0"
                              x-small
                              dense
                              solo
                              :class="expand ? 'expand' : ''"
                              style="position: absolute; right: 5px"
                            >
                              <template slot="append">
                                <v-btn
                                  fab
                                  text
                                  x-small
                                  pr-5
                                  mr-5
                                  @click="toggleExpand()"
                                >
                                  <v-icon class="mr-2">mdi-magnify</v-icon>
                                </v-btn>
                              </template>
                            </v-text-field>
                          </span>
                        </div>
                        <div class="pa-2 pb-0 ml-5 mr-3 mt-3">
                          <v-row>
                            <v-col
                              cols="12"
                              md="12"
                              v-for="(column, index) in filteredList"
                              :key="index"
                            >
                              <div id="d1" style="color: grey">
                                {{ column.text }}
                              </div>
                              <div class="float-right d-flex-end" id="d2">
                                <v-menu
                                  bottom
                                  min-width="200px"
                                  rounded
                                  offset-y
                                  :close-on-content-click="contentClose"
                                >
                                  <template v-slot:activator="{ on }">
                                    <v-btn
                                      icon
                                      small
                                      :key="index"
                                      v-on="on"
                                      @click="closeToggle(column.id)"
                                    >
                                      <i id="d3" class="fa-solid fa-gear"></i>
                                    </v-btn>
                                  </template>
                                  <v-card width="270px">
                                    <v-list-item-content class="justify-center">
                                      <div class="pa-3">
                                        <v-form>
                                          <h4 color="grey" class="pl-4">
                                            Category
                                          </h4>
                                          <v-select
                                            placeholder="select category"
                                            :items="categoryColumn.name"
                                            v-model="category_id"
                                            rounded
                                            filled
                                            dense
                                            class="pa-3"
                                          >
                                          </v-select>
                                          <h4 class="pl-4">Tooltip Text</h4>
                                          <v-textarea
                                            class="pa-3"
                                            filled
                                            v-model="tooltip"
                                            auto-grow
                                            rounded
                                            rows="4"
                                            row-height="30"
                                          ></v-textarea>
                                          <v-btn
                                            class="primary float-right pa-3"
                                            @click="tooltipSave(column)"
                                            >Save</v-btn
                                          >
                                        </v-form>
                                      </div>
                                    </v-list-item-content>
                                  </v-card>
                                </v-menu>
                                <v-btn icon small id="d3">
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <v-icon
                                        dark
                                        small
                                        v-bind="attrs"
                                        v-on="on"
                                      >
                                        mdi-information
                                      </v-icon>
                                    </template>
                                    <span>{{ txtTooltip }}</span>
                                  </v-tooltip>
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
                        <h3 class="pa-4" style="color: #757575">
                          Columns Arrangement
                        </h3>
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
                              style="cursor: pointer"
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
                                    <v-icon
                                      class="pr-1"
                                      @click="closeCol(element.text)"
                                      style="cursor: pointer"
                                      >mdi-close-circle-outline</v-icon
                                    >
                                  </v-col>
                                </v-row>
                              </v-card>
                              {{social}}
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
                            :items="category.title"
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
                            <label for="public" class="mr-4 d-flex mt-4">
                              public
                            </label>
                            <v-switch
                              id="public"
                              inset
                              dense
                              v-model="showMessages"
                            ></v-switch>
                          </div>
                          <v-btn color="primary" class="pa-4 mt-3" small>
                            submit
                          </v-btn>
                        </v-col>
                        <v-col cols="6" sm="4" md="2"></v-col>
                        <v-col cols="5" md="4" sm="4">
                          <span class="float-right mt-2">
                            <v-btn
                              small
                              class="pa-4"
                              color="primary"
                              outlined
                              style="margin-top: 5px"
                              @click="closeModel"
                            >
                              Cancel
                            </v-btn>
                            <v-btn
                              small
                              outlined
                              color="white"
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
            </v-dialog> -->
            <!-- end of columns -->
            <v-btn color="white" class="mt-5" @click="downloadDialog">
              <div class="d-block">
                <i
                  class="fa-sharp fa-solid fa-download"
                  aria-hidden="true"
                  style="color: blue"
                ></i>
                <h6 style="color: blue">Download</h6>
              </div>
            </v-btn>
            <download-file ref="downloadDialogRef" />
          </v-col>
          <v-col cols="12" md="3" sm="6" style="display: inline-block">
            <v-btn color="primary" class="mt-5" @click="uploadModel">
              <div class="d-block">
                <i class="fa fa-light fa-file-arrow-up"></i>
                <h6>Manual Upload</h6>
              </div>
            </v-btn>
            <v-btn color="primary" class="mt-5" @click="openModel2">
              <div class="d-block">
                <i class="fa fa-user-plus" aria-hidden="true"></i>
                <h6>Create Department</h6>
              </div>
            </v-btn>
          </v-col>
        </v-row>
      </v-card>
    </template>
    <template>
      <v-card class="mb-4" style="margin-top: 2em">
        <v-col cols="2" style="font-weight: 500"> Actions </v-col>
        <v-col cols="12" class="pa-0">
          <div class="text-center">
            <v-btn
              class="btn black"
              :class="selected.length > 0 ? '' : 'customDisabled'"
              @click="viewDialog"
            >
              <div class="d-block">
                <i class="fa fa-light fa-eye" style="color: white"></i>
                <h6 style="color: white">View</h6>
              </div>
            </v-btn>
            <v-btn
              class="btn cyan"
              :class="selected.length > 0 ? '' : 'customDisabled'"
            >
              <div class="d-block">
                <i class="mdi mdi-account-edit" style="color: white"></i>
                <h6 style="color: white">Edit</h6>
              </div>
            </v-btn>
            <v-btn
              class="btn primary"
              :class="selected.length > 0 ? '' : 'customDisabled'"
            >
              <div class="d-block">
                <i class="fa-sharp fa-solid fa-gear" style="color: white"></i>
                <h6 style="color: white">AutoEdit</h6>
              </div>
            </v-btn>
            <v-btn
              class="btn pink"
              :class="selected.length > 0 ? '' : 'customDisabled'"
            >
              <div class="d-block">
                <i class="fa-solid fa-circle-xmark" style="color: white"></i>
                <h6 style="color: white">Block</h6>
              </div>
            </v-btn>
            <v-btn
              class="btn warning lighten-2"
              v-if="tabkey == 'removed'"
              :class="selected.length > 0 ? '' : 'customDisabled'"
              @click="DelItem('restore')"
            >
              <div class="d-block">
                <i class="mdi mdi-restore" style="color: white"></i>
                <h6 style="color: white">Restore</h6>
              </div>
            </v-btn>
            <v-btn
              class="btn error lighten-2"
              :class="selected.length > 0 ? '' : 'customDisabled'"
              @click="DelItem('deleted')"
            >
              <div class="d-block">
                <i class="mdi mdi-delete" style="color: white"></i>
                <h6 style="color: white">Delete</h6>
              </div>
            </v-btn>

            <v-select
              :class="selected.length > 0 ? '' : 'customDisabled'"
              depressed
              outlined
              v-model="status"
              :items="actions"
              label="Select Status"
              style="width: 140px; height: 40px; display: inline-block"
              justify="center"
              dense
            ></v-select>

            <v-btn
              class="btn cyan"
              @click="changeStatus('change_status')"
              :class="selected.length > 0 ? '' : 'customDisabled'"
              depressed
            >
              <div class="d-block">
                <i class="fa fa-check" style="color: white"></i>
                <h6 style="color: white">Apply</h6>
              </div>
            </v-btn>
          </div>
        </v-col>
        <v-col cols="2" style="padding: 3px"></v-col>
      </v-card>
      <v-tabs background-color="primary" dark show-arrows>
        <v-tabs-slider color="blue lighten-3"></v-tabs-slider>
        <v-tab
          class="tab font-weight-bold"
          v-for="item in items"
          :key="item.tab"
          @click="tabChange(item.value)"
        >
          <v-icon>{{ item.icon }}</v-icon>
          <v-spacer></v-spacer>
          {{ item.tab }}
          <v-spacer></v-spacer>
          <v-badge
            class="mt-3 mr-2"
            color="green"
            :content="item.total"
          ></v-badge>
        </v-tab>
      </v-tabs>

      <v-data-table
        v-model="selected"
        @click:row="selectRow"
        :headers="fields"
        :items="contents.data"
        :loading="load"
        item-key="id"
        show-select
        class="elevation-1"
      >
        <template v-slot:item.logo="{ item }">
          <v-avatar>
            <img :src="`http://localhost:8000/${item.logo}`" alt="" />
          </v-avatar>
        </template>
        <template v-slot:item.status="{ item }">
          <v-chip :color="getColor(item.status)" dark>
            {{ item.status }}
          </v-chip>
        </template>
      </v-data-table>
    </template>
  </div>
</template>

<script>
import Dropzone from "nuxt-dropzone";
import "nuxt-dropzone/dropzone.css";
import DownloadFile from "./DownloadFile.vue";
import Filtering from '../company/Filtering.vue';

export default {
  components: { Dropzone, DownloadFile, Filtering },
  props: {
    fields: Array,
    load: Boolean,
    contents: Array,
    downloadData: {
      type: Array,
      default: function () {
        return [];
      },
    },
  },
  data() {
    return {
      flag: false,
      opencolumn: false,
      radios: null,
      actions: ["pending", "active", "inactive", "blocked", "removed"],
      status: "",
      image: "",
      id: "",
      step: 1,
      dialog: false,
      tabkey: "all",
      searchData: "",
      selected: [],
      items: [
        { tab: "All", value: "all", icon: "mdi-table" },
        { tab: "Active", value: "active", icon: "mdi-thumb-up" },
        { tab: "Inactive", value: "inactive", icon: "mdi-cancel" },
        { tab: "Blocked", value: "blocked", icon: "mdi-close-circle" },
        { tab: "Removed", value: "removed", icon: "mdi-delete" },
        { tab: "Pending", value: "pending", icon: "mdi-account-off" },
      ],
      dropzoneOptions: {
        url: "department",
        thumbnailWidth: 150,
        maxFilesize: 0.5,
        headers: { "My-Awesome-Header": "header value" },
      },
    };
  },
  watch: {
    contents: function () {
      this.items = [
        {
          tab: "All",
          value: "all",
          icon: "mdi-table",
          total: this.contents?.extraData?.allTotal,
        },
        {
          tab: "Active",
          value: "active",
          icon: "mdi-thumb-up",
          total: this.contents?.extraData?.activeTotal,
        },
        {
          tab: "Inactive",
          value: "inactive",
          icon: "mdi-cancel",
          total: this.contents?.extraData?.inactiveTotal,
        },
        {
          tab: "Blocked",
          value: "blocked",
          icon: "mdi-close-circle",
          total: "0",
        },
        {
          tab: "Removed",
          value: "removed",
          icon: "mdi-delete",
          total: this.contents?.extraData?.removedTotal,
        },
        {
          tab: "Pending",
          value: "pending",
          icon: "mdi-account-off",
          total: this.contents?.extraData?.pendingTotal,
        },
      ];
    },
  },
  methods: {
    filterData(data) {
      this.$emit("filterData", data);
    },
    openFilter() {
      this.$refs.filterRef.openFilter();
    },
    columnDialog() {
      this.opencolumn = true;
    },
    selectAll() {
      if (this.allList.length == this.list.length) {
        this.social = [];
      } else {
        this.social = this.allList;
      }
    },
    toggleExpand() {
      this.expand = !this.expand;
      if (this.expand != "") {
        return;
      }
      if (this.expand == true) {
      }
    },
    getColor(status) {
      if (status == "active") return "light-blue";
      else if (status == "inactive") return "#2962ff";
      else if (status == "pending") return "pink";
      else return "red";
    },
    downloadDialog() {
      this.$refs.downloadDialogRef.openDownloadDialog(this.downloadData);
    },
    search(searchData) {
      if (searchData.key == "Enter" && this.searchData) this.flag = true;
      if(
        (searchData.key == "Enter" && this.searchData) ||
        (searchData.key == "Backspace" && !this.searchData && this.flag)
      ){
        if (searchData.key == "Backspace" && !this.searchData && this.flag)
        this.flag = false;
        if (this.radios == "id") {
        let filter = {
          searchData: this.searchData,
          tabkey: this.tabkey,
          type: "SearchById"
        };
        this.$emit("search", filter);
      }else{
        let filter = {
          searchData: this.searchData,
          tabkey: this.tabkey,
          type: "SearchByContent"
        };
        this.$emit("search", filter);
      }

      }

    },
    tabChange(item) {
      this.tabkey = item;
      this.$emit("getRecord", item);
    },
    viewDialog() {
      this.$emit("viewModel", this.selected[0]);
      this.selected = [];
    },
    // openDialog() {
    //   this.$emit("openModel");
    // },
    DelItem(type) {
      if (this.tabkey == "all") {
        alert("imposible from tab all");
      } else {
        let DelId = this.selected.map((item) => item.id);
        let data = {
          ids: DelId,
          tabkey: this.tabkey,
          type: type,
        };
        this.$emit("DeleteRecord", data);
        this.selected = [];
      }
    },
    changeStatus(type = null) {
      let item = {
        id: this.selected[0].id,
        status: this.status,
        type: type,
      };
      if (this.status == this.selected[0].status) {
        alert("Status are the same");
      } else {
        this.$emit("sendRecord", item, this.tabkey);
        this.selected = [];
        this.status = [];
      }
    },
    selectRow(item) {
      if (this.selected.find((i) => i.id == item.id)) {
        this.selected = this.selected.filter((i) => i.id != item.id);
      } else {
        this.selected.push(item);
      }
    },
    openModel2() {
      this.$emit("dialogStepper");
    },
    uploadModel() {
      this.dialog = true;
    },
    closeDialog() {
      this.dialog = false;
      this.step = 1;
    },
    onFileAdded(e) {
      console.log(e);
    },
    onError(e) {},
    onSuccess(e) {},
    onComplete(e) {},
  },
};
</script>

<style scoped>
.demo {
  margin-bottom: -21px;
}
.tab {
  width: 250px;
}
.customDisabled {
  opacity: 0.6;
  pointer-events: none;
  cursor: not-allowed !important;
}
</style>
