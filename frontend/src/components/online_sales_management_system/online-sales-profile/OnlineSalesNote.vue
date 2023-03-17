<template>
  <div>
    <div
      style="border-radius: 30px 0 0 0; width: 100%"
      class="white ps-4 d-flex align-center justify-space-between"
    >
      <div class="d-flex align-center">
        <v-btn fab x-small class="me-1 primary" @click="$emit('closeDialog')">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-btn fab x-small class="primary" @click="$emit('fullScreen')">
          <v-icon>mdi-fullscreen</v-icon>
        </v-btn>
        <div class="grey rounded mx-3" style="height: 40px; width: 1px"></div>
        <span>
          <v-avatar size="40" class="grey lighten-3 me-1">
            <v-avatar size="35" color="white">
              <v-avatar size="30" color="white">
                <v-avatar size="30" class="text-h6 primary white--text">
                  {{ selected_item?.product_code?selected_item?.product_code[0]:selected_item?.pcode[0] }}
                </v-avatar>
              </v-avatar>
            </v-avatar>
          </v-avatar>
          <span class="dialog-title"> {{ selected_item?.product_code??selected_item?.pcode }}</span>
        </span>
      </div>

      <div class="d-flex align-center">
        <v-icon
          @click="toggleCreateNote"
          color="white"
          class="rounded-circle pa-1 me-2"
          style="background: #2e7be6"
        >
          mdi-plus
        </v-icon>
        <slot name="date_slot">
          <FilterDateRange
            :customSelectDate="{ type: 'lifetime', index: 5 }"
            ref="filterDateRange"
            :dateRangeProp="date_range"
            :hide-title="true"
            @dateChanged="FilterByDateRange($event)"
            :custom_design="true"
            :nudge_left="130"
          />
        </slot>

        <v-img
          src="/new_landing/product_profile_header.svg"
          height="60px"
        ></v-img>
      </div>
    </div>
    <div class="d-flex">
      <div
        :class="`ma-3  ${showNote ? 'NoteView' : 'active_note'}`"
        style="height: 90vh; overflow-y: auto"
      >
        <div>
          <div v-if="!isFetchingNotes" class="d-flex flex-wrap">
            <v-card
              :class="` mb-4 ${showNote ? 'mx-3' : 'mx-2'}`"
              style="border-radius: 15px; width: 295px !important"
              elevation="0"
              v-for="(item, index) in allNotes"
              :key="index"
            >
              <v-card-title class="pa-1 d-flex mx-1">
                <div class="d-flex">
                  <span style="font-size: 1rem">{{ item.name }}</span>
                </div>
                <div class="d-flex ml-auto">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16.708"
                    height="16.066"
                    viewBox="0 0 14.708 14.066"
                    @click="EditNote(item)"
                  >
                    <path
                      id="edit-btn"
                      d="M-9510.646-13702.935a.751.751,0,0,1-.75-.75.752.752,0,0,1,.75-.751h6.6a.751.751,0,0,1,.751.751.75.75,0,0,1-.751.75Zm-7.136-.221a.752.752,0,0,1-.2-.712l.733-2.937a.781.781,0,0,1,.2-.348l9.174-9.175a2.3,2.3,0,0,1,1.632-.675,2.3,2.3,0,0,1,1.632.675,2.313,2.313,0,0,1,0,3.264l-9.175,9.175a.772.772,0,0,1-.348.2l-2.937.732a.732.732,0,0,1-.183.023A.751.751,0,0,1-9517.781-13703.155Zm10.968-12.106-9.026,9.025-.379,1.519,1.521-.379,9.026-9.026a.807.807,0,0,0,0-1.139.8.8,0,0,0-.571-.238A.794.794,0,0,0-9506.813-13715.262Z"
                      transform="translate(9518 13717.001)"
                      fill="#2E7BE6"
                    />
                  </svg>
                </div>
                <div class="d-flex" style="padding-left: 10px">
                  <div
                    style="
                      border-left: 1px solid lightgray;
                      height: 20px;
                      padding-right: 7px;
                      margin-top: 2px;
                    "
                  ></div>
                  <v-icon color="red" size="15"  @click="onDelete(item)">
                    fa fa-trash
                  </v-icon>
                </div>
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text
                class="onHverToScroll"
                style="padding: 12px; padding-right: 0px"
              >
                <div>
                  <span
                    class="font-weight-bold"
                    style="font-size: 0.9rem"
                    v-html="item.note"
                  >
                  </span>
                </div>
                <div class="d-flex position-relative" v-if="item.attachments.length > 0">
                  <v-divider></v-divider>
                  <span class="position-absolute " style="top:-10px; width:70px; background-color: white; font-size: 0.9rem;"><i style="transform:rotate(30deg)" class="fa fa-thumbtack"></i>&nbsp; images</span>
                </div>
                <div
                  class="d-flex flex-wrap mb-2"
                  style="width: 100%; padding: 4px; height: fit-content"
                >
                <span v-for="(image,j) in item.attachments" :key="j">
                  <div class="d-flex mt-1 uploaded-img rounded" v-if="image.file_type=='image'">
                      <v-img
                      class="rounded cursor-pointer d-flex align-center justify-center"
                      :src="image.path"
                      />
                  </div>
                </span>
                </div>
                <div class="d-flex position-relative" v-if="item.attachments.length > 0 ">
                  <v-divider></v-divider>
                  <span class="position-absolute " style="top:-10px; width:70px; background-color: white; font-size: 0.9rem;"><i style="transform:rotate(30deg)" class="fa fa-thumbtack"></i>&nbsp; voices</span>
                </div>
                <div class="mt-2" style="padding-right: 10px">
                  <span  v-for="(voice, i) in item.attachments" :key="i">
                    <AudioPlayer
                    v-if="voice.file_type=='raw'"
                    :source="voice.path"
                    :dur="parseInt(voice.name)"
                    />
                  </span>
                </div>
              </v-card-text>
              <v-divider></v-divider>
              <v-card-actions class="pa-1" style="background-color: #e5e5e5">
                <v-avatar color="primary" size="35" style="color: white">
                  <img
                    v-if="item.user.image"
                    :src="item.user.image"
                    lazy-src="https://picsum.photos/id/11/10/6"
                  />
                  <span v-else>{{
                    item.user ? item.user.firstname[0].toUpperCase() : ""
                  }}</span>
                </v-avatar>
                <div class="d-flex flex-column ms-1">
                  <span style="font-size: 11px">{{
                    item.user.firstname + " " + item.user.lastname
                  }}</span>
                  <span style="font-size: 9px">{{ item.created_at }}</span>
                </div>
              </v-card-actions>
            </v-card>
          </div>
          <div v-else class="d-flex flex-wrap">
            <v-card
              class="mx-2 mb-4 rounded-3"
              style="border-radius: 15px; width: 295px !important"
              elevation="0"
              v-for="(item, index) in 6"
              :key="index"
            >
              <v-card-text height="300px" class="pa-0">
                <v-skeleton-loader
                  class="rounded-3"
                  height="300px"
                  type="card"
                  width="100%"
                ></v-skeleton-loader>
              </v-card-text>
            </v-card>
          </div>
        </div>
      </div>
      <div
        :class="`ml-auto filter_holder ${
          showDrawer ? 'active_properties' : ''
        }`"
      >
        <OnlineSalesInsertion
          ref="onlineSalesInsertionRef"
          :productCode="selected_item?.product_code??selected_item?.pcode"
          @pushNote="pushNote"
          @updateNote="updateNote"
          @closeInsertNote="closeInsertNote"
        />
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import FilterDateRange from "../../advertisement/FilterDateRange.vue";
import AudioPlayer from "../../new_form_components/components/AudioPlayer.vue";
import OnlineSalesInsertion from "./OnlineSalesInsertion.vue";
export default {
  props: {
    selected_item: Object,
  },
  data() {
    return {
      date_range: {
        start_date: "2023-01-12",
        end_date: moment().format("YYYY-MM-DD"),
      },
      showNote: true,
      isDelete:false,
      showDrawer: false,
      isFetchingNotes: false,
      allNotes: [],
    };
  },
  methods: {
    async fetchNotes() {
      try {
        this.isFetchingNotes = true;
        const res = await this.$axios.get(
          `online-sales/fetch-note/${this.selected_item?.product_code??this.selected_item?.pcode}`,
          { params: this.date_range }
        );
        if (res.status == 200) {
          this.allNotes = res.data;
        }
        this.isFetchingNotes = false;
      } catch (error) {
        console.log(error);
      }
    },
    FilterByDateRange() {
      this.fetchNotes();
    },
    pushNote(item) {
      this.showNote = true;
      this.showDrawer = false;
      this.isFetchingNotes = true;
      setTimeout(() => {
        this.allNotes.unshift(item);
        this.isFetchingNotes = false;
      }, 500);
    },
    updateNote(item) {
      this.showNote = true;
      this.showDrawer = false;
      this.isFetchingNotes = true;
      setTimeout(() => {
        this.allNotes = this.allNotes.map((i) => {
          if (i.id == item.id) return item;
          return i;
        });
        this.isFetchingNotes = false;
      }, 500);
    },
    toggleCreateNote() {
      this.showDrawer = true;
      this.showNote = false;
      this.$refs.onlineSalesInsertionRef.reset();
    },
    closeInsertNote() {
      this.$refs.onlineSalesInsertionRef.reset();
      this.showDrawer = false;
      this.showNote = true;
    },
    EditNote(item) {
      this.$refs.onlineSalesInsertionRef.editNote(item);
      this.showDrawer = true;
      this.showNote = false;
    },
    onDelete(item) {
      this.$TalkhAlertNA(
        "Are you sure?",
        "delete",
        () => this.DeleteNote(item),
        "delete"
      );
    },
    async DeleteNote(item) {
      try {
        var path = [];
        this.isFetchingNotes = true;
        this.allNotes = this.allNotes.map((i)=>{
          if(i.id == item.id)
          i.isDelete = true;
          return i;
        })
        if (item.attachments.length > 0) {
          path = item.attachments.map((i) => {
            return i.path;
          });
        }
        const result = await this.$axios.delete(
          `online-sales/delete-note/${item.id}`,
          { params: { path: path } }
          );
          if (result.data == true) {
            this.allNotes = this.allNotes.filter((i) => i.id != item.id);
            this.$toasterNA("green", this.$tr("successfully_deleted"));
            this.$refs.onlineSalesInsertionRef.checkEditOnDelete(item.id);
          } else {
            this.$toasterNA("red", this.$tr("someting_went_wrong"));
          }
          this.isFetchingNotes = false;
      } catch (error) {
        console.log(error);
        this.isFetchingNotes = false;
      }
    },
  },
  components: { FilterDateRange, OnlineSalesInsertion, AudioPlayer },
};
</script>

<style>
.onHverToScroll {
  height: 300px;
  overflow-y: hidden;
}
.onHverToScroll:hover {
  overflow-y: auto !important;
}
/* Hide scrollbar for Chrome, Safari and Opera */
.onHverToScroll::-webkit-scrollbar {
  width: 2px;
}

/* Hide scrollbar for IE, Edge and Firefox */
.onHverToScroll {
  scrollbar-width: 2px; /* Firefox */
}
.filter_holder {
  width: 0;
  transition: 0.2s;
  position: absolute;
  right: 0;
  overflow: auto;
  background: white;
  min-height: 62vh;
}
.active_properties {
  width: 30%;
  transition: 0.3s;
  border: 1px solid rgb(232, 232, 232);
}
.active_note {
  width: 70%;
  transition: 0.3s;
}
.NoteView {
  width: 100%;
  transition: 0.2s;
  position: absolute;
  overflow: auto;
}
.uploaded-img {
  border: 2px solid #d9e0f4;
  width: 85px;
  height: 85px;
  margin-left: 3px;
  margin-right: 3px;
}
</style>
