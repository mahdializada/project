<template>
  <div class="d-flex profile my-1 customLogoInput">
    <div>
      <div
        class="avatar d-flex align-center me-4 justify-center rounded-circle"
      >
        <img v-if="filePath" :src="filePath" alt="" />
        <v-icon v-if="filePath === ''">fa-user</v-icon>
      </div>
    </div>
    <div class="w-full">
      <div class="images d-flex justify-space-between align-center custom-file">
        <a @click="prev"><v-icon>fa-chevron-circle-left</v-icon></a>
        <div>
          <span v-for="startIndex in endIndex" :key="startIndex">
            <img
              width="80"
              v-if="isIndexInRange(startIndex)"
              class="svgStyle mx-1"
              :class="startIndex == activeIcone ? 'primaryBackground' : ''"
              :src="`/icons/SVG BLACK/Icons-${startIndex}.svg`"
              @click="selectIcon(startIndex)"
            />
          </span>
        </div>
        <a @click="next"><v-icon>fa-chevron-circle-right</v-icon></a>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    defaultIcon: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      filePath: this.defaultIcon,
      iconTotal: 398,
      activeIcone: 0,
      back: false,
      startIndex: 1,
      endIndex: 7,
      changeItem: 7,
    };
  },
  created() {
    window.addEventListener("resize", this.myEventHandler);
    window.dispatchEvent(new Event("resize"));
  },
  destroyed() {
    window.removeEventListener("resize", this.myEventHandler);
  },
  methods: {
    isIndexInRange(index) {
      return index >= this.startIndex && index <= this.endIndex;
    },
    prev() {
      this.back = true;
      if (this.startIndex == 1) {
        this.endIndex = this.iconTotal;
        this.startIndex = this.iconTotal - (this.changeItem - 1);
      } else {
        this.startIndex -= this.changeItem;
        this.endIndex -= this.changeItem;
      }
    },
    next() {
      this.back = false;
      if (this.endIndex == this.iconTotal) {
        this.startIndex = 1;
        this.endIndex = this.startIndex + (this.changeItem - 1);
      } else {
        this.startIndex += this.changeItem;
        this.endIndex += this.changeItem;
      }
    },

    selectIcon(icon) {
      this.activeIcone = icon;
      this.filePath = "/icons/SVG BLUE/Icons-" + icon + ".svg";
      this.$emit("onLogoChanged", this.filePath);
    },
    getIndex(slide) {
      this.start = (slide - 1) * this.iconPerPage;
      return (slide - 1) * this.iconPerPage;
    },
    myEventHandler(e) {
      var w = e.target.innerWidth;
      var w = window.innerWidth;
      if (w < 500) {
        this.changeItem = 1;
        this.endIndex = this.startIndex + (this.changeItem - 1);
      } else if (w < 600) {
        this.changeItem = 2;
        this.endIndex = this.startIndex + (this.changeItem - 1);
      } else if (w < 768) {
        this.changeItem = 3;

        this.endIndex = this.startIndex + (this.changeItem - 1);
      } else if (w < 900) {
        this.changeItem = 4;
        this.endIndex = this.startIndex + (this.changeItem - 1);
      } else if (w < 1030) {
        this.changeItem = 6;
        this.endIndex = this.startIndex + (this.changeItem - 1);
      } else if (w < 1200) {
        this.changeItem = 7;
        this.endIndex = this.startIndex + (this.changeItem - 1);
      }
    },
  },
};
</script>
<style scoped>
.avatar {
  height: 120px;
  width: 120px;
  background-color: var(--gray-cyan);
  border: 2px solid var(--gray-cyan);
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
}

.avatar .v-icon {
  color: var(--color-white);
  font-size: 60px;
}

.custom-file {
  height: 120px;
  background-color: #f8fafd;
  border: 3px solid #cdd5e1;
  border-radius: 4px;
}
.svgStyle {
  border: 4px solid #cdd5e1;
  border-radius: 50%;
}
.style {
  color: #cdd5e1 !important;
}
.primaryBackground {
  color: white !important;
  background-color: var(--v-primary-base) !important;
}
/*
.custom-file .v-input__slot fieldset {
  border-style: dashed !important;
}

.custom-file .v-input__slot {
  display: flex;
  justify-content: center;
  align-items: center;
}

.upload-first-p {
  font-size: 18px;
  color: var(--upload-input-first-p);
  letter-spacing: 0.5px;
}

.upload-second-p {
  color: var(--input-border-color);
  line-height: 1.5;
}

.custom-file .v-input__prepend-inner {
  position: absolute;
}

.dublicate-name {
  top: -32% !important;
  padding: 0rem !important;
  position: relative !important;
  color: red;
}

.v-progress-circular {
  margin-top: 2rem;
  margin-left: 0rem;
  margin-right: 2rem;
} */

.slide-leave-active {
  transition: 0.3s;
  position: absolute;
  display: none;
}
.slide-enter-active {
  transition: 0.3s;
}
.slide-enter {
  transform: translate(100%, 0);
}
.slide-leave-to {
  transform: translate(-100%, 0);
  position: absolute;
}
.slideback-leave-active {
  transition: 0.3s;
  display: none;
}
.slideback-enter {
  transform: translate(-100%, 0%);
  /* position: absolute; */
}
.slideback-enter-active {
  transition: 0.3s;
  /* position: absolute; */
}
</style>
