<template>
  <div>
    <v-hover v-slot="{ hover }">
      <div
        class="media_container on_hover"
        :class="itemCheck ? 'selected_item' : ''"
      >
        <div class="media_title" style="cursor: pointer">
          <div class="d-flex align-center">
            <div class="media_title_text">
              <span class="media_text font-weight-medium" style="color: black"
                >Media Name</span
              >
            </div>
          </div>
        </div>

        <div class="media_img">
          <div v-if="hover" class="media_details">
            <div class="d-flex" style="width: 100%">
              <div class="d-flex">
                <div class="media_title_icon">
                  <v-icon style="font-size: medium">mdi-video</v-icon>
                </div>
              </div>
              <div class="d-flex ml-auto">
                <v-checkbox
                  v-if="hover || itemCheck"
                  class="item_check"
                  v-model="itemCheck"
                  label=""
                ></v-checkbox>
              </div>
            </div>
            <div class="iconic_details d-flex justify-space-between">
              <div></div>
            </div>
          </div>

          <img width="100%" :src="imgPath" alt="media" />
        </div>
      </div>
    </v-hover>
  </div>
</template>

<script>
export default {
  props: ["imgPath", "id"],
  emits: ["showProfile"],
  data() {
    return {
      itemCheck: false,
      addToFavorite: false,
    };
  },

  methods: {
    addFavorite() {
      this.addToFavorite = !this.addToFavorite;
    },
  },
};
</script>

<style scoped>
.media_container {
  background-color: #f3f3f3;
  padding: 10px;
  border-radius: 15px;
  transition: 0.2s;
  border: 1px solid transparent;
  /* margin-top   : 10px; */
}
.media_container.on_hover:hover {
  background-color: white !important;
}

.on_hover:hover {
  box-shadow: 2px 10px 25px -2px rgba(0, 0, 0, 0.21);
  -webkit-box-shadow: 2px 10px 25px -2px rgba(0, 0, 0, 0.21);
  -moz-box-shadow: 2px 10px 25px -2px rgba(0, 0, 0, 0.21);
}

.selected_item {
  border: 1px solid #2e7be6;
  background-color: white;
}

.media_img {
  overflow: hidden;
  border-radius: 10px;
  position: relative;
}

.media_title {
  display: flex;
  padding: 5px 0 10px 0;
  justify-content: start;
  align-items: center;
}

.media_title_text {
  display: flex;
  flex-direction: column;
}

.media_title_text .media_content {
  margin-top: 20px;
  font-size: 0.8rem;
}

.media_title_icon {
  margin-right: 10px;
  background-color: #b2b2b2;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white !important;
}

.media_details {
  position: absolute;
  width: 100%;
  height: 100%;
  background-image: linear-gradient(
    180deg,
    rgba(0, 0, 0, 0.5),
    rgba(0, 0, 0, 0.1),
    rgba(0, 0, 0, 0.5)
  );
  top: 0;
  left: 0;
  /* opacity: 0.4; */
  display: flex;
  flex-direction: column;
  /* align-items: flex-end; */
  justify-content: space-between;
  padding: 10px;
}

.iconic_details {
  height: 70%;
  /* display: flex;
    flex-direction: column;
    justify-content: start;
    align-items: flex-start; */
}

.icon_holder {
  background: rgba(0, 0, 0, 0.3);
  padding: 3px;
  border-radius: 5px;
  display: inline-block;
  /* position: relative; */
}

.iconic_details_tooltip {
  color: black !important;
  position: absolute;
  top: 0;
  left: 40px;
  padding: 15px;
  background-color: white;
  border-radius: 5px;
  width: max-content;
  z-index: 10;
}

.iconic_details_tooltip p {
  word-break: keep-all !important;
  word-wrap: normal !important;
}

.iconic_details_tooltip::after {
  content: " ";
  position: absolute;
  top: 30%;
  right: 100%;
  /* To the left of the tooltip */
  margin-top: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent white transparent transparent;
}

.iconic_details_tooltip p {
  margin-bottom: 0;
}

.iconic_details_tooltip p:first-child {
  margin-bottom: 20px;
}

.iconic_details_tooltip > .hover_title {
  font-size: 0.8rem;
  opacity: 0.6;
  /* margin-bottom: 15px; */
}
</style>

<style>
.media_title_icon i {
  color: white !important;
}

.iconic_details i {
  color: white !important;
}

.icon_container {
  position: relative;
}

.item_check {
  padding: 0 !important;
  margin-top: 0 !important;
  margin-left: auto;
  margin-right: 0px;
}

.item_check .v-messages.theme--light {
  display: none;
}

.item_check .v-input__slot,
.item_check .v-input--selection-controls__input {
  margin: 0 !important;
}

/* .v-tooltip .v-tooltip--right{
    background-color: red !important;
} */
</style>