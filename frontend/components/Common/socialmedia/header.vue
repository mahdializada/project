<template>
  <div>
    <v-card class="flex-grow-1 d-flex v-sheet pa-0 mb-2" style="height: 100px;">
      <div class="d-flex flex-grow align-center">
        <div class="d-flex flex-column align-center" style="width: 100px;">
          <v-avatar
            size="60"
            style="
            margin-left: 20px;
            background-color:#d0e7fd;
            "
          >
            <span>
              <i class="fa fa-thumbs-up" style="font-size: 30px;"></i>
            </span>
          </v-avatar>
        </div>
      </div>
      <div class="d-flex" style="width: 500px;">
        <span>
          <h2 class="ml-4 mt-5" style="height: 20px;">Social Medias</h2>
          <span style="font-size: 15px;">
            <i
              class="fa fa-gear ml-4 mt-5"
              style="font-size: 15px; margin-right: 4px;"
            ></i>
            Master Management System >>
            <i
              class="fa fa-thumbs-up"
              style="font-size: 15px; margin-right: 4px;"
            ></i>
            Social Media
          </span>
        </span>
      </div>
    </v-card>

    <v-card class="mb-5" style="margin-top:0px">
      <strong class="ml-4" style="margin-top:0px">Search & Filter</strong>
      <v-row class="ml-5" style="margin-top:0px">
        <v-col cols="6" md="1" sm="3" style="margin-top:-30px">
          <v-radio-group v-model="searchtype" mandatory style="display: inline-block;margin-bottom:0px;">
            <template v-slot:label>
              <h4>Search Type</h4>
              <!-- <div style="font-size:15px"><strong >Search Type</strong></div> -->
            </template>
            <v-radio label="id" value="se_id" style="display: inline;">
            </v-radio>
            <v-radio label="Content" value="se_content">
            </v-radio>
          </v-radio-group>
        </v-col>
        <v-col cols="6" md="3" sm="8">
          <v-text-field
            small
            style="border-radius: 30px;"
            label="search"
            v-model="searchData"
            outlined
            dense
            append-icon="mdi-magnify"
            class="mt-10"
            @keyup="searches"
          ></v-text-field>
        </v-col>

        <v-col cols="6" md="4" sm="6">
          <!-- filter -->
          <v-btn small color="white" class="mt-10" @click="openfilter">
            <div>
              <i class="fa fa-plus" style="color: blue;"></i>
              <h5 style="color: blue;">filter</h5>
            </div>
          </v-btn>
          <v-dialog v-model="filtermodel" persistent a width="1200">
            <v-card>
              <v-card-title>
                <i class="fa fa-filter"></i>
                &nbsp;&nbsp;&nbsp;
                <h3>
                  Tabs & Filters
                </h3>
                <v-spacer></v-spacer>
                <v-btn fab x-small @click="closeFilter" style="float: right;">
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
              </v-card-title>
              <v-card-text>
                <v-row>
                  <v-col cols="6" md="4" sm="6">
                    <v-card height="400" elevation="4">
                      <h3 class="text-center pt-4"><strong>Date</strong></h3>
                      <v-col>
                        <h4>Name</h4>
                        <v-autocomplete
                          name="socialname"
                          :items="selectsocial"
                          item-text="name"
                          item-value="id"
                          @click="getData"
                          v-model="name_id"
                          clearable
                          dense
                          solo-inverted
                          background-color="indigo accent-1"
                        ></v-autocomplete>
                      </v-col>
                      <br />
                      <v-col>
                        <h4>Website</h4>
                        <v-select
                          dense
                          :items="selectsocial"
                          @click="getData"
                          item-text="website"
                          item-value="id"
                          v-model="website_id"
                          solo-inverted
                          background-color="indigo accent-1"
                        ></v-select>
                      </v-col>
                      <br />
                      <v-col>
                        <h4>Status</h4>
                        <v-text-field
                          v-model="statusfilter"
                          solo-inverted
                          background-color="indigo accent-1"
                          dense
                        ></v-text-field>
                      </v-col>
                    </v-card>
                  </v-col>
                  <v-col cols="6" md="4" sm="6">
                    <v-card height="400" elevation="4">
                      <h3 class="text-center pt-4">
                        <strong>Date Range</strong>
                      </h3>

                      <p style="margin-bottom: -10px;">
                        <strong class="pl-2">Created At</strong>
                      </p>
                      <v-col>
                        <v-menu
                          ref="menu"
                          v-model="menu"
                          :close-on-content-click="false"
                          :return-value.sync="date"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="created_at"
                              label="created at"
                              solo-inverted
                              background-color="indigo accent-1"
                              prepend-icon="mdi-calendar"
                              dense
                              readonly
                              v-bind="attrs"
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="created_at"
                            no-title
                            scrollable
                          >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menu = false">
                              Cancel
                            </v-btn>
                            <v-btn
                              text
                              color="primary"
                              @click="$refs.menu.save(date)"
                            >
                              OK
                            </v-btn>
                          </v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-spacer></v-spacer>
                      <p style="margin-bottom: -10px; margin-top: 20px;">
                        <strong class="pl-2">Updated At</strong>
                      </p>
                      <v-col>
                        <v-menu
                          ref="menu1"
                          v-model="menu1"
                          :close-on-content-click="false"
                          :return-value.sync="date"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="updated_at"
                              solo-inverted
                              dense
                              background-color="indigo accent-1"
                              label="Updated_AT"
                              prepend-icon="mdi-calendar"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="updated_at"
                            no-title
                            scrollable
                          >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menu1 = false">
                              Cancel
                            </v-btn>
                            <v-btn
                              text
                              color="primary"
                              @click="$refs.menu1.save(date)"
                            >
                              OK
                            </v-btn>
                          </v-date-picker>
                        </v-menu>
                      </v-col>
                    </v-card>
                  </v-col>
                  <v-col cols="6" md="4" sm="6">
                    <v-card height="400" elevation="4">
                      <h3 class="text-center pt-4">
                        <strong>ID Filter</strong>
                      </h3>
                      <v-col>
                        <v-col style="margin-bottom: -20px;">
                          <label for="filterId"><strong>ID</strong></label>
                          <v-text-field
                            :class="showById ? '' : 'disableId'"
                            style="display: inline;"
                            v-model="filterId"
                            label="Ex:10010"
                            solo-inverted
                            background-color="indigo accent-1"
                            dense
                          ></v-text-field>
                        </v-col>
                        <v-col align="right">
                          <v-btn-toggle v-model="toggle_exclusive">
                            <v-btn
                              x-small
                              rounded
                              dense
                              :class="include ? '' : 'disableId'"
                              @click="showId('include')"
                            >
                              <p class="pt-4">Include</p>
                            </v-btn>

                            <v-btn
                              x-small
                              rounded
                              dense
                              :class="exclude ? '' : 'disableId'"
                              @click="showId('exclude')"
                            >
                              <p class="pt-4">Exclude</p>
                            </v-btn>
                          </v-btn-toggle>
                        </v-col>
                      </v-col>
                    </v-card>
                  </v-col>
                  <v-col align="right">
                    <v-btn
                      @click="clearfilter"
                      style="background-color: red; color: white;"
                      dense
                      small
                    >
                      Clear Filter
                    </v-btn>
                    <v-btn
                      color="primary"
                      dense
                      small
                      @click="submitfilter('filter')"
                    >
                      submit
                    </v-btn>
                    <v-btn
                      style="background-color: #757575; color: white;"
                      @click="canclefilter"
                      dense
                      small
                    >
                      cancle
                    </v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>
          <!-- columns -->
          <v-btn small color="white" class="mt-10" @click="openModel">
            <div class="block">
              <i class="fa-solid fa-table-columns" style="color: blue;"></i>
              <h5 style="color: blue;">columns</h5>
            </div>
          </v-btn>

          <v-dialog v-model="opencolumn" persistent width="1200">
            <v-card class="pa-3">
              <v-card-title class="pa-0 ma-0">
                <v-row>
                  <v-col cols="10">
                    <h3
                      class="pa-5"
                      style="display: inline-block; color: #757575;"
                    >
                      Tabs & Columns
                    </h3>
                  </v-col>
                  <v-col cols="2" class="pa-5">
                    <v-btn
                      fab
                      x-small
                      @click="closeModel"
                      style="float: right;"
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
                  </v-col>
                </v-row>
              </v-card-title>
              <v-card-text>
                <v-row>
                  <v-col cols="4" sm="4" md="4">
                    <v-card elevation="5" height="400">
                      <h3 style="color: #757575;" class="pa-4 ma-0">
                        Categories
                      </h3>
                      <template>
                        <v-list
                          class="pa-1 ma-3"
                          style="border: 0.5px solid #0091ea;"
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
                                <v-list-item-title
                                  v-text="item.title"
                                ></v-list-item-title>
                              </v-list-item-content>
                              <v-chip
                                content="6"
                                style="color: #0091ea;"
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
                        <h3 style="color: #757575; display: inline-block;">
                          Columns
                        </h3>
                        <span class="float-right d-flex mt-2">
                          <a
                            @click="selectAll"
                            class="mr-5"
                            style="font-size: 15px;"
                          >
                            {{
                              allList.length == list.length
                                ? 'Unselect All'
                                : 'Select All'
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
                            style="position: absolute; right: 5px;"
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
                            <div id="d1" style="color: grey;">
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
                                        ></v-select>
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
                                        >
                                          Save
                                        </v-btn>
                                      </v-form>
                                    </div>
                                  </v-list-item-content>
                                </v-card>
                              </v-menu>
                              <v-btn icon small id="d3">
                                <v-tooltip bottom>
                                  <template v-slot:activator="{ on, attrs }">
                                    <v-icon dark small v-bind="attrs" v-on="on">
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
                      <h3 class="pa-4" style="color: #757575;">
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
                            style="cursor: pointer;"
                          >
                            <v-card
                              class="list-group-item pa-3 ma-2 d-flex"
                              v-for="element in list"
                              :key="element.id"
                            >
                              <v-row>
                                <v-col cols="10 d-flex">
                                  <v-icon style="cursor: pointer;">
                                    mdi-pan-vertical
                                  </v-icon>
                                  <h5 style="color: grey; font-size: 13px;">
                                    {{ element.text }}
                                  </h5>
                                </v-col>
                                <v-col cols="2" class="pr-1">
                                  <v-icon
                                    class="pr-1"
                                    @click="closeCol(element.text)"
                                    style="cursor: pointer;"
                                  >
                                    mdi-close-circle-outline
                                  </v-icon>
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
                      <!-- <v-col cols="6" sm="4" md="2"></v-col> -->
                      <v-col cols="5" md="4" sm="4">
                        <span class="float-right mt-2">
                          <v-btn
                            small
                            class="pa-4"
                            color="primary"
                            outlined
                            style="margin-top: 5px;"
                            @click="closeModel"
                          >
                            Cancel
                          </v-btn>
                          <v-btn
                            small
                            outlined
                            color="white"
                            class="primary pa-4"
                            style="margin-top: 5px;"
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

          <v-btn small color="white" class="mt-10" @click="opendownload">
            <div class="block">
              <i class="fa-sharp fa-solid fa-download" style="color: blue;"></i>
              <h5 style="color: blue;">downloads</h5>
            </div>
          </v-btn>
          <v-dialog v-model="download_dialog" persistent max-width="600">
            <v-card>
              <v-card-title class="font-weight-bold" style="font-size: medium;">
                Select File Type
                <br />
                Choose one or multiple type you wnat to download
                <v-spacer></v-spacer>
                <v-btn
                  fab
                  x-small
                  @click="cancel_download"
                  style="float: right;"
                >
                  <svg
                    width="50"
                    height="60"
                    viewBox="0 0 700 560"
                    fill="currentColor"
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
              </v-card-title>

              <v-card-text>
                <v-row>
                  <v-col cols="3">
                    <v-card
                      width="130px"
                      height="130px"
                      class="elevation-3"
                      style="border-radius: 15px 15px 15px 15px;"
                    >
                      <v-card-title
                        class="pa-0 mt-0 float-right"
                        style="height: 10px;"
                      >
                        <v-checkbox
                          v-model="json"
                          class="pa-0 ma-0"
                        ></v-checkbox>
                      </v-card-title>
                      <v-card-text>
                        <v-avatar
                          width="80px"
                          height="80px"
                          class="grey lighten-4 mt-4"
                        >
                          <span style="width: 40px; height: 45px;">
                            <img src="icons/json.png" alt="" />
                          </span>
                        </v-avatar>
                        <h4 class="text-center font-weight-black">Json File</h4>
                      </v-card-text>
                    </v-card>
                  </v-col>
                  <v-col cols="3">
                    <v-card
                      width="130px"
                      height="130px"
                      class="elevation-3"
                      style="border-radius: 15px 15px 15px 15px;"
                    >
                      <v-card-title
                        class="pa-0 mt-0 float-right"
                        style="height: 10px;"
                      >
                        <v-checkbox
                          v-model="csv"
                          id="download-csv"
                          class="pa-0 ma-0"
                        ></v-checkbox>
                      </v-card-title>
                      <v-card-text>
                        <v-avatar
                          width="80px"
                          height="80px"
                          class="grey lighten-4 mt-4"
                        >
                          <span style="width: 40px; height: 45px;">
                            <img src="icons/csv.png" alt="" />
                          </span>
                        </v-avatar>
                        <h4 class="text-center font-weight-black">CSV File</h4>
                      </v-card-text>
                    </v-card>
                  </v-col>

                  <v-col cols="3">
                    <v-card
                      width="130px"
                      height="130px"
                      class="elevation-3"
                      style="border-radius: 15px 15px 15px 15px;"
                    >
                      <v-card-title
                        class="pa-0 mt-0 float-right"
                        style="height: 10px;"
                      >
                        <v-checkbox
                          v-model="txt"
                          class="pa-0 ma-0"
                        ></v-checkbox>
                      </v-card-title>
                      <v-card-text>
                        <v-avatar
                          width="80px"
                          height="80px"
                          class="grey lighten-4 mt-4"
                        >
                          <span style="width: 40px; height: 45px;">
                            <img src="icons/txt.png" alt="" />
                          </span>
                        </v-avatar>
                        <h4 class="text-center font-weight-black">TXT File</h4>
                      </v-card-text>
                    </v-card>
                  </v-col>
                  <v-col cols="3">
                    <v-card
                      width="130px"
                      height="130px"
                      class="elevation-3"
                      style="border-radius: 15px 15px 15px 15px;"
                    >
                      <v-card-title
                        class="pa-0 mt-0 float-right"
                        style="height: 10px;"
                      >
                        <v-checkbox
                          v-model="excel"
                          class="pa-0 ma-0"
                        ></v-checkbox>
                      </v-card-title>
                      <v-card-text>
                        <v-avatar
                          width="80px"
                          height="80px"
                          class="grey lighten-4 mt-4"
                        >
                          <span style="width: 40px; height: 45px;">
                            <img src="icons/excel.png" alt="" />
                          </span>
                        </v-avatar>
                        <h4 class="text-center font-weight-black">
                          Excel File
                        </h4>
                      </v-card-text>
                    </v-card>
                  </v-col>
                  <v-col cols="3">
                    <v-card
                      width="130px"
                      height="130px"
                      class="elevation-3"
                      style="border-radius: 15px 15px 15px 15px;"
                    >
                      <v-card-title
                        class="pa-0 mt-0 float-right"
                        style="height: 10px;"
                      >
                        <v-checkbox
                          v-model="pdf"
                          class="pa-0 ma-0"
                        ></v-checkbox>
                      </v-card-title>
                      <v-card-text>
                        <v-avatar
                          width="80px"
                          height="80px"
                          class="grey lighten-4 mt-4"
                        >
                          <span style="width: 40px; height: 45px;">
                            <img src="icons/pdf.png" alt="" />
                          </span>
                        </v-avatar>
                        <h4 class="text-center font-weight-black">PDF File</h4>
                      </v-card-text>
                    </v-card>
                  </v-col>
                </v-row>
                <div class="d-flex">
                  <h4 class="mt-4">Show who download this file</h4>
                  <v-spacer></v-spacer>
                  <v-switch input-value="true" inset class="pa-0"></v-switch>
                </div>
                <div class="d-flex">
                  <h4 class="mt-5">
                    Show the current time & date of the downloaded file
                  </h4>
                  <v-spacer></v-spacer>
                  <v-switch
                    input-value="true"
                    inset
                    class="pa-0"
                    style=""
                  ></v-switch>
                </div>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                  color="primary darken-1"
                  depressed
                  style="height: 35px; font-weight: 900; width: 90px;"
                  class="elevation-2"
                  dense
                  small
                  solo
                  outlined
                  @click="cancel_download"
                >
                  cancel
                </v-btn>

                <v-btn
                  class="elevation-2 primary"
                  style="height: 35px; font-weight: 900; width: 110px;"
                  depressed
                  dense
                  small
                  color="white darken-1"
                  solo
                  text
                  @click="downloadPDF"
                >
                  Download
                </v-btn>
                <vue-csv-downloader
                  :data="download_data"
                  id="dwnCsv"
                  style="display: none;"
                >
                  download
                </vue-csv-downloader>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-col>
        <v-col cols="6" md="3" sm="6" class="mt-10">
          <v-btn small color="primary" dense @click="toggleDialog">
            <div>
              <i class="fa fa-plus"></i>
              <h6>Create social media</h6>
            </div>
          </v-btn>
          <v-btn small color="primary" dense>
            <div>
              <i class="fa fa-plus"></i>
              <h6>Create</h6>
            </div>
          </v-btn>
        </v-col>
      </v-row>
    </v-card>
  </div>
</template>
<script>
import draggable from 'vuedraggable'

import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'
import { saveExcel } from '@progress/kendo-vue-excel-export'
import { saveAs } from 'file-saver'
import VueCsvDownloader from 'vue-csv-downloader'
export default {
  props: {
    download_data: {
      type: Array,
      default: function () {
        return []
      },
    },
    allList: {
      type: Array,
      default: null,
    },
    search_tab: String,
  },
  name: 'simple',
  display: 'Simple',
  order: 0,
  data() {
    return {
      // name select
      selectsocial: [],
      name_id: '',
      statusfilter: '',
      website_id: '',
      created_at: '',
      updated_at: '',
      filterId: '',
      showById: false,
      include: true,
      exclude: false,

      // search by ID or Content
      searchtype: '',
      searchData:'',
      // date
      date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
      menu: false,
      menu1: false,
      updated_at: '',
      created_at: '',
      download_dialog: false,
      enabled: true,
      // id exclude and include
      text: 'center',
      toggle_exclusive: 2,
      filterId: '',
      // view:'',
      contentClose: false,
      expand: false,
      category_id: '',

      tooltip: '',
      txtTooltip: 'id',
      searchColumn: '',
      social: [],
      list: [],
      data: [],
      dragging: false,
      ditems: [
        { id: 1, name: 'hoge', children: [{ id: 11, name: 'hoge-child1' }] },
      ],
      categoryColumn: { name: 'all' },
      opencolumn: false,
      offsetTop: 0,
      category: [
        {
          active: true,
          items: [],
          title: 'All',
        },
      ],
      posts: [],
      showMessages: '',
      dialog: false,
      json: false,
      csv: false,
      txt: false,
      excel: false,
      pdf: false,
      // download_data: [],
      filtermodel: false,
    }
  },
  computed: {
    filteredList() {
      let v = this.searchColumn
      return this.allList.filter((post) => {
        return post.text.toLowerCase().includes(v.toLowerCase())
      })
    },
    draggingInfo() {
      return this.dragging ? 'under drag' : ''
    },
  },
  created() {
    if (typeof window !== 'undefined') {
      this.social = JSON.parse(localStorage.getItem('columns') || '[]')
    }
  },
  watch: {
    social: function () {
      if (this.social == null) {
        this.social == this.allList
      }
      this.list = this.social.map((item) => {
        return item
      })

      return this.social
    },
  },
  methods: {
    // select
    async getData() {
      const sh = await this.$axios.get('socialmedia');
      this.selectsocial = sh.data.data;
      console.log(this.selectsocial);
    },
    async submitfilter(filter) {
      let filterData = {
        name_id: this.name_id,
        website_id: this.website_id,
        created_at: this.created_at,
        updated_at: this.updated_at,
        statusfilter: this.statusfilter,
        filterId: this.filterId,
        type: filter,
      }
      const filteredData = await this.$axios.get('socialmedia', {
        params: filterData,
      })
      console.log(filteredData)
      this.$emit('getfilter', filteredData.data)
      this.filtermodel = false
    },
    clearfilter() {
      this.name_id = ''
      this.website_id = ''
      this.statusfilter = ''
      this.updated_at = ''
      this.created_at = ''
      this.filterId = ''
    },
    showId(type) {
      if (type == 'exclude') {
        this.include = true
        this.exclude = false
        this.showById = !this.showById
      } else {
        this.include = false
        this.exclude = true
        this.showById = !this.showById
      }
    },
    canclefilter() {
      this.filtermodel = false
      this.filterId = ''
      this.name_id = ''
      this.website_id = ''
      this.statusfilter = ''
      this.updated_at = ''
      this.created_at = ''
    },
    // search
    async searches(searchData) {
      if (searchData.key == "Enter"){
      // console.log(searchData)
      if (this.searchtype == 'se_id'){
        let se_data = {
          search_tab: this.search_tab,
          searchValue: this.searchData,
          type: 'idsearch',
        };
        const result = await this.$axios.get('socialmedia', { params: se_data });
        this.$emit('searchresult', result.data);
      } else {
        let se_data = {
          search_tab: this.search_tab,
          searchValue: this.searchData,
          type: 'contentsearch',
        };
        const result = await this.$axios.get('socialmedia', { params: se_data });
        this.$emit('searchresult', result.data);
      }
    }
    },
    async tooltipSave(params) {
      let data1 = {
        tooltip: this.tooltip,
        category: this.category_id,
        value: this.data,
      }
      const update = await this.$axios.put(`column/${params.id}`, data1)
      console.log(this.data.data[params.id].value)
      this.contentClose = true
    },
    closeToggle(item) {
      this.data.item
      this.id = this.contentClose = false
      this.tooltip = null
    },
    // search_datatable() {
    //   this.$emit('search_data', this.searchData)
    // },
    opendownload() {
      this.download_dialog = true
    },
    toggleExpand() {
      this.expand = !this.expand
      if (this.expand != '') {
        return
      }
      if (this.expand == true) {
      }
    },
    closeCol(id) {
      this.social = this.social.filter((item) => item.text != id)
    },
    selectAll() {
      if (this.allList.length == this.list.length) {
        this.social = []
      } else {
        this.social = this.allList
      }
    },
    saveChanges() {
      localStorage.setItem('columns', JSON.stringify(this.list))
      this.$emit('saveChanges')
      this.closeModel()
    },
    toggleDialog() {
      this.$emit('openD')
    },
    checkMove: function (e) {
      window.console.log('Future index: ' + e.draggedContext.futureIndex)
    },
    async openModel() {
      this.opencolumn = true
      // let re = await this.$axios.get("column");
      // this.data = re;

      // console.log(this.data.data[0].value);
    },
    closeModel() {
      this.opencolumn = false
      // this.social=this.allList;
    },
    cancle() {
      this.social = []
    },
    cancel_download() {
      ;(this.download_dialog = false),
        (this.json = null),
        (this.csv = null),
        (this.txt = null),
        (this.excel = null),
        (this.pdf = null)
    },
    // download
    downloadPDF() {
      if (this.pdf == true) {
        const doc = new jsPDF('1', 'mm', [297, 210])

        autoTable(doc, {
          theme: 'grid',
          styles: {
            fontSize: 10,

            cellPadding: { top: 1, right: 0.5, bottom: 1, left: 0.5 },
            fillColor: [255, 255, 255],
            textColor: [0, 0, 0],
          },
          columnStyles: { 0: { halign: 'center' } },
          margin: { top: 20, right: 5, bottom: 15, left: 5 },

          head: [['ID', 'Name', 'Status']],
          body: this.download_data,
          columns: [
            {
              text: 'ID',
              dataKey: 'id',
            },
            {
              text: 'NAME',
              dataKey: 'name',
            },
            {
              text: 'STATUS',
              dataKey: 'status',
            },
          ],
        })

        doc.save('socialmedia.pdf')
        ;(this.dialog = false), (this.pdf = null)
      }
      if (this.excel == true) {
        saveExcel({
          data: this.download_data,
          fileName: 'socialmedia',
          columns: [
            {
              field: 'id',
              title: 'ID',
            },
            {
              field: 'name',
              title: 'NAME',
            },

            {
              field: 'status',
              title: 'STATUS',
            },
          ],
        })
        ;(this.dialog = false), (this.excel = null)
      }
      if (this.txt == true) {
        const FileSaver = require('file-saver')
        const data = this.download_data
        const json = JSON.stringify(data)
        const file = new Blob([json], {
          type: 'text/plain;charset=utf-8',
        })
        saveAs(file, 'socialmedia.txt')
        ;(this.dialog = false), (this.txt = null)
      }
      if (this.json == true) {
        const FileSaver = require('file-saver')
        const data = this.download_data
        const json = JSON.stringify(data)
        const file = new Blob([json], {
          type: 'text/plain;charset=utf-8',
        })
        saveAs(file, 'socialmedia.json')
        ;(this.dialog = false), (this.json = null)
      }
      if (this.csv == true) {
        this.exportCsv()
        ;(this.dialog = false), (this.csv = null)
      }
    },
    exportCsv() {
      var link = document.getElementById('dwnCsv')
      link.click()
    },
    // filter
    openfilter() {
      this.filtermodel = true
    },
    closeFilter() {
      this.filtermodel = false
    },
  },
  components: { draggable, VueCsvDownloader },
}
</script>
<style scoped>
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
.search-txt {
  border-radius: 25px;
  width: 50px;
  height: 20px !important;
  margin-top: 5px;
  align-items: center;
  transition: all 0.3s;
  max-width: 360px !important;
  border: 2px solid transparent;
}
.expand {
  width: 200px;
  min-height: 20px !important;
  height: 20px !important;
  padding-left: 16px;
}
.v-text-field.v-text-field--solo {
  min-height: auto !important;
  height: 10px !important;
}
.v-input__slot {
  min-height: 10px !important;
}
div.v-input__append-inner {
  margin-right: 10px !important;
}
.disableId {
  opacity: 0.5 !important;
  pointer-events: none !important;
  cursor: not-allowed !important;
}
</style>
