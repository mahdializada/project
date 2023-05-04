<template>
  <div style="width: 100%; position: relative">
    <ProfileDialog ref="profileDialogRefs" :filter_type="filter_type" />
    <LibraryOperationStepper ref="InsertDialogRef" @addRecord="addRecord" />
    <LabelForm

      ref="labelRef"
      subsystem_name="content_library"
      :model_name="tabKey"
      :noModel="true"
      :selected_item.sync="selectedItems"
      @applyLabelChanges="applyLabelChanges"
    />
    <RemarkForm
      ref="remarkRef"
      :tab="'content_library_media'"
      @addRemark="addRemark"
      :selected_items.sync="selectedItems"
    />
    <div class="headerContainer">
      <div
        style="
          position: fixed;
          top: 64px;
          z-index: 5;
          width: inherit;
          max-width: inherit;
        "
      >
        <ContentLibraryChangeStatus
          ref="changeStatusRef"
          @changeStatus="changeStatus"
        />
        <ContentHeader
          :headerExpand="expandTabs"
          @onTabChanged="onTabChanged"
          @openInsertDialogNow="openInsertDialog"
          @onDelete="onDelete($event)"
          :showItem="showData"
          ref="PropertiseRef"
          @openLabelDialogNow="openLabelDialog"
          @openChangeStatusDialogNew="openChangeStatusDialog"
          @openRemarkDialog="openRemarkDialog"
          @onSelectAll="selectAll"
          @filter="getMedia"
          @closeProp="isProp = false"
          @searchMedia="searchMedia"
          :filter_type="filter_type"
          :selectedItems.sync="selectedItems"
        ></ContentHeader>
      </div>
    </div>
    <div class="library_container">
      <!-- header -->
      <!-- end header -->

      <div class="main_content">
        <div class="library_galary">
          <div
            class="d-flex"
            style="margin-top: 255px"
            v-if="medias.length > 0"
          >
            <div class="media-wrapper">
              <div class="media-item" v-for="i in 4" :key="i">
                <MediaItem
                  @showCamparingDialog="showCamparingDialog(item)"
                  @onFavorite="onDelete($event)"
                  @showProfile="doShowProfile(item)"
                  v-for="(item, index) in getArray(i)"
                  :key="index + i * 1000"
                  class="item"
                  :item="item"
                  :selectedItems.sync="selectedItems"
                  @onItemClicked="onItemClicked(item)"
                />
              </div>
              <div style="width: "></div>
            </div>
            <div
              class="left_panel_space"
              :class="isProp ? 'left_panel_space_active' : ''"
            ></div>
          </div>
          <MediaSkeleton v-if="mediaLoading" />
        </div>
      </div>
    </div>
    <v-card v-intersect="infiniteScrolling" class="red"></v-card>
  </div>
</template>

<script>
import ContentHeader from "../../components/content-library/content-library-page/ContentHeader.vue";
import CustomButton from "../../components/design/components/CustomButton.vue";
import MediaItem from "../../components/content-library/content-library-page/MediaItem.vue";
import HeaderArabicChar from "../../components/content-library/content-library-page/HeaderArabicChar.vue";
import MediaSkeleton from "../../components/content-library/content-library-page/MediaSkeleton.vue";
import LabelForm from "../../components/advertisement/label/LabelForm.vue";
import LibraryOperationStepper from "../../components/content-library/content-library-oprations/LibraryOperationStepper.vue";
import ContentLibraryChangeStatus from "../../components/content-library/ContentLibraryChangeStatus.vue";
import ProfileDialog from "../../components/content-library/content-library-page/ProfileDialog.vue";
import RemarkForm from "../../components/advertisement/remarks/RemarkForm.vue";
export default {
  components: {
    ContentHeader,
    CustomButton,
    HeaderArabicChar,
    MediaItem,
    MediaSkeleton,
    LabelForm,
    LibraryOperationStepper,
    ContentLibraryChangeStatus,
    ProfileDialog,
    RemarkForm,
  },

  data() {
    return {
      filter_type: this.$route.params.filter_type,
      dialog: false,
      expandTabs: "",
      isScrolled: false,
      isProp: false,
      medias: [
        // {
        // 	id: 1,
        // 	src: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRPpgdUTi4lunnaOenCOknQjy1rTR31rvL8LQ&usqp=CAU",
        // },
      ],
      loopingCol1: [],
      loopingCol2: [],
      loopingCol3: [],
      loopingCol4: [],
      text: "Lorem ipsum dolor sit amet, consectetur adi",
      showFilter: false,
      switch1: false,
      selectedItems: [],
      showData: null,
      params: {
        page: 1,
        itemsPerPage: 10,
      },
      mediaLoading: false,
      propHeight: "",
      applyHeader: "",
      page: 1,
      tabKey: "all",
      disableScroll: false,
    };
  },

  // updated() {

  // },

  mounted() {
    document.addEventListener("scroll", this.expandOnScroll);
    this.gallryLayout();
    window.addEventListener("resize", this.setHeaderW);
    this.setHeaderW();
    // this.getWidth(this.setHeaderW);
    this.$root.$on("setWidthHeader", this.setHeaderW);
  },

  watch: {
    medias: function (data) {
      this.gallryLayout();
    },
  },

  created() {
    // this.getMedia();
  },

  methods: {
    onDelete(id) {
      this.selectedItems = [];
      this.medias = this.medias.filter((row) => row.id != id);
    },
    selectAll(value) {
      this.selectedItems = value ? this.medias.map((media) => media) : [];
    },
    onItemClicked(item) {
      if (this.selectedItems.find((i) => i.id == item.id)) {
        this.selectedItems = this.selectedItems.filter((i) => i.id != item.id);
      } else this.selectedItems.push(item);
    },
    getArray(i) {
      const array = this["loopingCol" + i];
      return array;
    },

    async getMedia(data = null) {
      this.medias = [];
      if (data.filter_data) {
        this.page = 1;
        data.page = 1;
        this.disableScroll = false;
      }
      if (data.date) {
        console.log("date", data);
        this.page = 1;
        data.page = 1;
        this.disableScroll = false;
      }
      this.tabKey = data?.tab;
      this.mediaLoading = true;
      try {
        this.makeParams(data);
        const response = await this.$axios.get("library/content-library", {
          params: { ...this.params, filter_type: this.filter_type },
        });
        if (response.status == 200) {
          this.medias = response.data.data;
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
      this.mediaLoading = false;
    },
    openInsertDialog() {
      this.$refs.InsertDialogRef.togglelibrary();
    },
    openLabelDialog() {
      if (this.tabKey == "all") {
        this.$toasterNA("red", this.$tr("not_allowed_to_change_status"));
        return;
      }
      if (this.selectedItems.length != 1) {
        this.$toasterNA("red", this.$tr("select_only_one_item"));
        return;
      }
      this.$refs.labelRef.toggleDialog();
    },
    openChangeStatusDialog() {
      if (this.tabKey == "all" || this.tabKey == "new") {
        this.$toasterNA("red", this.$tr("not_allowed_to_change_status"));
        return;
      }
      if (this.selectedItems.length != 1) {
        this.$toasterNA("red", this.$tr("select_only_one_item"));
        return;
      }

      this.$refs.changeStatusRef.openDialog(this.selectedItems, this.tabKey);
    },
    changeStatus(id) {
      // this.medias = this.medias.filter((item)=>item.id != id);
      this.getMedia(this.tabKey);
      this.selectedItems = [];
    },
    openRemarkDialog() {
      // if (this.tabKey == "all" || this.tabKey == "new") {
      //   this.$toasterNA("red", this.$tr("not_allowed_to_change_status"));
      //   return;
      // }
      if (this.selectedItems.length != 1) {
        this.$toasterNA("red", this.$tr("select_only_one_item"));
        return;
      }

      this.$refs.remarkRef.toggleDialog();
    },
    addRemark(id) {
      console.log(id);
    },

    makeParams(newParam) {
      if (newParam == null) return this.params;
      else {
        if (newParam.tab) this.params.tab = newParam.tab;
        if (newParam.page) this.params.page = newParam.page;
        if (newParam.itemsPerPage)
          this.params.itemsPerPage = newParam.itemsPerPage;

        if (newParam.searchValue) {
          this.params.searchValue =
            newParam.searchValue == "clearSearch" ? null : newParam.searchValue;
        }

        if (newParam.searchValue == "") {
          delete this.params.searchValue;
        }

        if (newParam.filter_data) {
          this.params.filter_data = newParam.filter_data;
        }
        if (newParam.date) {
          this.params.date = newParam.date;
        }
      }

      return this.params;
    },

    gallryLayout() {
      let arrindex = 0;
      let arrindex1 = 1;
      let arrindex2 = 2;
      let arrindex3 = 3;
      this.loopingCol1 = [];
      this.loopingCol2 = [];
      this.loopingCol3 = [];
      this.loopingCol4 = [];
      if (this.medias.length != 0) {
        if (this.medias[0] != undefined) this.loopingCol1.push(this.medias[0]);
        if (this.medias[1] != undefined) this.loopingCol2.push(this.medias[1]);
        if (this.medias[2] != undefined) this.loopingCol3.push(this.medias[2]);
        if (this.medias[3] != undefined) this.loopingCol4.push(this.medias[3]);

        this.medias.forEach((item, index) => {
          arrindex = arrindex + 4;
          arrindex1 = arrindex1 + 4;
          arrindex2 = arrindex2 + 4;
          arrindex3 = arrindex3 + 4;

          if (this.medias[arrindex] != undefined) {
            this.loopingCol1.push(this.medias[arrindex]);
          }

          if (this.medias[arrindex1] != undefined) {
            this.loopingCol2.push(this.medias[arrindex1]);
          }

          if (this.medias[arrindex2] != undefined) {
            this.loopingCol3.push(this.medias[arrindex2]);
          }

          if (this.medias[arrindex3] != undefined) {
            this.loopingCol4.push(this.medias[arrindex3]);
          }
        });
      }
    },

    doShowProfile(data) {
      this.isProp = true;
      this.showData = data;
      this.$refs.PropertiseRef.showProp();
    },
    addRecord(item) {
      if (this.tabKey == "pending" || this.tabKey == "all")
        for (let i = 0; i < item.length; i++) {
          this.medias.push(item[i]);
        }
    },
    expandOnScroll() {
      let ctaScroll = document.querySelector(".cta_scroll");

      if (document.documentElement.scrollTop > 100) {
        this.expandTabs = "collapse_header";
        this.$nextTick(function () {
          this.$refs.PropertiseRef?.onScrollH();
        });
        if (ctaScroll) ctaScroll.style.top = "160px";
      } else {
        this.expandTabs = "";
        this.$nextTick(function () {
          this.$refs.PropertiseRef?.onScrollHRemove();
        });
        if (ctaScroll) ctaScroll.style.top = "270px";
      }
    },
    setHeaderW() {
      let mainW = document.querySelector(".library_galary");
      let headerW = document.querySelector(".headerContainer");
      setTimeout(() => {
        headerW.style.width = mainW.offsetWidth + "px";
      }, 400);
    },

    async infiniteScrolling(entries, observer, isIntersecting) {
      try {
        if (isIntersecting && !this.disableScroll && !this.mediaLoading) {
          this.mediaLoading = true;
          this.page++;
          this.makeParams({ page: this.page });
          const response = await this.$axios.get("library/content-library", {
            params: { ...this.params, filter_type: this.filter_type },
          });
          if (response.data.data.length > 1) {
            this.medias = [...this.medias, ...response.data.data];
          }

          if (response.data.total == this.medias.length)
            this.disableScroll = true;
          this.mediaLoading = false;
        }
      } catch (error) {
        this.mediaLoading = false;
        console.log(error);
      }
    },
    onTabChanged(data) {
      this.page = 1;
      this.disableScroll = false;
      this.selectedItems = [];
      if (this.params.searchValue) {
        delete this.params["searchValue"];
      }
      this.getMedia(data);
    },
    showCamparingDialog(item) {
      console.log("console.log", item);
      this.$refs.profileDialogRefs.showDialog(item);
    },
    searchMedia(item = "") {
      const data = {
        tab: this.tabKey,
        searchValue: item,
        page: 1,
      };

      this.getMedia(data);
    },
    applyLabelChanges(labels) {
      this.medias = this.medias.map((row) => {
        if (row.id == this.selectedItems[0].id) {
          row.labels = labels;
        }
        return row;
      });
      this.selectedItems = [];
      console.log("labels", labels);
    },
  },
};
</script>

<style scoped>
.headerContainer {
  transition: 0.2s;
}
.media_profile_icon::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: var(--v-primary-base);
  opacity: 0.1;
  border-radius: 50%;
  z-index: 1;
}

.media-wrapper {
  line-height: 0;
  display: flex;
  flex-wrap: wrap;
  width: 100%;
}

.media-wrapper .media-item {
  flex: 25%;
  max-width: 25%;
  padding: 0 10px;
}
.left_panel_space {
  width: 0px;
  transition: 0.2s;
}

.left_panel_space_active {
  width: 450px;
  transition: 0.2s;
}

.item {
  margin-bottom: 20px;
}

.library_container {
  width: 100%;
  min-height: 1000px !important;
}

.main_content {
  /* position: absolute; */
  width: 100%;
  /* top: 230px; */
  /* height: 100%; */
  transition: all 0.2s ease-out;
  /* margin-top: 0px; */
  padding-top: 0;
}

.collapse_tabs {
  top: 60px;
  transition: all 0.2s ease-out;
}

.library_galary {
  padding: 15px;
  background: white;
  /* height: 100%; */
}

.page_title {
  height: 100%;
}
</style>

<style>
.media_profile_icon i {
  color: var(--v-primary-base) !important;
  font-size: 1.1rem !important;
}

.filter_container {
  box-shadow: none !important;
  border: 1px solid rgb(232, 232, 232) !important;
  padding: 10px;
}

.media_profile_dialog .v-dialog--persistent {
  max-width: 1100px !important;
}
</style>
