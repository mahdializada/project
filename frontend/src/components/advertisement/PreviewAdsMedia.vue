<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="auto" persistent scrollable>
      <div class="container show-controls" id="container">
        <CloseBtn @click="onClose" type="button" id="CloseBtn" />
        <div class="wrapper" id="wrapper" @mousemove="showControls">
          <div
            class="video-timeline"
            @click="prograssarea"
            @mousemove="mouseMove"
            id="videoTimeline"
          >
            <div class="progress-area">
              <span id="timer">{{ timerPrograss }}</span>
              <div class="progress-bar" id="bar"></div>
            </div>
          </div>
          <ul class="video-controls">
            <li class="options left">
              <button class="volume" @click="mute">
                <v-icon>{{
                  volume
                    ? high
                      ? "mdi-volume-high"
                      : "mdi-volume-medium"
                    : "mdi-volume-off"
                }}</v-icon>
              </button>
              <input
                type="range"
                min="0"
                max="1"
                step="any"
                id="value"
                orient="vertical"
                @change="value"
              />
              <div class="video-timer">
                <p class="current-time">{{ timer }}</p>
              </div>
            </li>
            <li class="options center">
              <button class="skip-backward" @click="backward">
                <v-icon>{{ "mdi-fast-forward" }}</v-icon>
              </button>
              <button class="play-pause" @click="playPause">
                <v-icon>{{
                  playOrPause ? "mdi-play-circle" : "mdi-pause-circle"
                }}</v-icon>
              </button>
              <button class="skip-forward" @click="forward">
                <v-icon>{{ "mdi-fast-forward" }}</v-icon>
              </button>
            </li>
            <li class="options right">
              <p class="video-duration">{{ duration }}</p>
            </li>
          </ul>
        </div>
        <video
          style="background: black; width: 1000px !important; height: 500px"
          @click="playPause"
          preload="metadata"
          @mousemove="showControls"
          @timeupdate="timeUpdate"
        >
          <source :src="path" />
        </video>
        <iframe
          loading="lazy"
          style="
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            border: none;
            padding: 0;
            margin: 0;
            background: black;
          "
          @click="playPause"
          preload="metadata"
          @mousemove="showControls"
          @timeupdate="timeUpdate"
          src="https://www.canva.com/design/DAFTrW3rOis/watch?embed"
        >
        </iframe>
      </div>
    </v-dialog>
  </div>
</template>
<script src="script.js"></script>
<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
export default {
  props: {
    path: {
      typs: String,
      default: "/videos/video3.mp4",
    },
  },
  components: {
    CloseBtn,
  },
  data() {
    return {
      poster: "/videos/video3.png",
      canPlay: true,
      dialog: false,
      duration: "00:00",
      timerPrograss: "00:00",
      timer: "00:00",
      playOrPause: true,
      high: true,
      volume: true,
      timing: 0,
    };
  },
  methods: {
    prograssarea(e) {
      const mainVideo = document.querySelector("video");
      if (!mainVideo.duration) return;
      let pos = e.pageX - e.target.getBoundingClientRect().left;
      let percent =
        (pos * 100) / document.getElementById("container").clientWidth;
      document.getElementById("bar").style.width = `${percent}%`;
      document.getElementById("timer").style.left = `${percent}%`;
      this.timerPrograss = this.formatTime(
        (percent * mainVideo.duration) / 100
      );
      mainVideo.currentTime = (percent * mainVideo.duration) / 100;
    },
    mouseMove(e) {
      const mainVideo = document.querySelector("video");
      let fullWirdth = document.getElementById("container").clientWidth;
      let pos = e.pageX - e.target.getBoundingClientRect().left;
      let percent = (pos * 100) / fullWirdth;
      // document.getElementById("bar").style.width = `${percent}%`;
      document.getElementById("timer").style.left = `${percent}%`;
      this.timerPrograss = this.formatTime(
        (percent * mainVideo.duration) / 100
      );
    },
    timeUpdate(e) {
      const mainVideo = e.target;
      if (mainVideo.currentTime == mainVideo.duration) {
        this.playOrPause = true;
        mainVideo.pause();
        mainVideo.currentTime = 0;
      }
      let percent = (mainVideo.currentTime / mainVideo.duration) * 100;
      document.getElementById("bar").style.width = `${percent}%`;
      document.getElementById("timer").style.left = `${percent}%`;
      this.timerPrograss = this.formatTime(mainVideo.currentTime);
      this.timer = this.timerPrograss;
      this.hideControls(5000);
    },
    hideControls(time = 3000) {
      const fullWirdth = document.getElementById("container");
      this.timing = setTimeout(() => {
        fullWirdth.classList.remove("show-controls");
      }, time);
    },
    showControls() {
      const fullWirdth = document.getElementById("container");
      fullWirdth.classList.add("show-controls");
      clearTimeout(this.timing);
      // this.hideControls();
    },
    openDialog() {
      const mainVideo = document.querySelector("video");
      this.canPlay = true;
      if (mainVideo?.duration) {
        mainVideo.volume = 0.5;
        this.duration = this.formatTime(mainVideo?.duration);
        mainVideo.currentTime = 0;
        document.getElementById("value").value = mainVideo.volume;
      } else {
        this.canPlay = false;
      }
      this.dialog = !this.dialog;
    },
    onClose() {
      this.dialog = !this.dialog;
      const mainVideo = document.querySelector("video");
      this.playOrPause = true;
      mainVideo.currentTime = 0;
      mainVideo.pause();
    },
    value(e) {
      const mainVideo = document.querySelector("video");
      mainVideo.volume = e.target.value;
      if (mainVideo.volume == 0) {
        this.volume = false;
      } else {
        this.volume = true;
        if (mainVideo.volume > 0.5) {
          this.high = true;
        } else {
          this.high = false;
        }
      }
    },
    mute() {
      const mainVideo = document.querySelector("video");
      const input = document.getElementById("value");
      if (mainVideo.volume > 0) {
        mainVideo.volume = 0;
        this.volume = false;
      } else {
        this.volume = true;
        if (input.value == 0) {
          this.high = false;
          input.value = 0.1;
          mainVideo.volume = input.value;
        } else mainVideo.volume = input.value;
        if (mainVideo.volume == 0) {
          this.high = false;
        } else {
          this.high = true;
        }
      }
    },
    async playPause() {
      if (!this.dialog) return;
      const mainVideo = document.querySelector("video");
      if (mainVideo.duration) {
        this.duration = mainVideo?.duration
          ? this.formatTime(mainVideo?.duration)
          : "00:00";
        if (mainVideo.paused) {
          this.playOrPause = false;
          this.hideControls();
          mainVideo.play();
        } else {
          this.showControls();
          this.playOrPause = true;
          mainVideo.pause();
        }
      } else {
        return;
      }
    },
    backward() {
      const mainVideo = document.querySelector("video");
      mainVideo.currentTime -= 0.5;
    },
    forward() {
      const mainVideo = document.querySelector("video");
      mainVideo.currentTime += 0.5;
    },
    formatTime(time) {
      let seconds = Math.floor(time % 60),
        minutes = Math.floor(time / 60) % 60,
        hours = Math.floor(time / 3600);

      seconds = seconds < 10 ? `0${seconds}` : seconds;
      minutes = minutes < 10 ? `0${minutes}` : minutes;
      hours = hours < 10 ? `0${hours}` : hours;

      if (hours == 0) {
        return `${minutes}:${seconds}`;
      }
      return `${hours}:${minutes}:${seconds}`;
    },
  },
};
</script>
<style scoped>
/* Import Google font - Poppins */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap");
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}
.video-controls,
.video-timer,
.options {
  display: flex;
  align-items: center;
  justify-content: center;
}
#CloseBtn {
  position: relative !important;
  top: 50px !important;
  left: 10px !important;
  color: #fff !important;
  z-index: 1000;
}
.container {
  cursor: pointer;
  /* max-width: 1000px; */
  position: relative;
  border-radius: 5px;
  overflow: hidden;
}
.wrapper {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1;
  visibility: hidden;
  transition: all 0.13s ease;
}
.container.show-controls .wrapper {
  background: #0000007d;
  visibility: visible;
  transition: all 0.13s ease;
}
.wrapper::before {
  content: "";
  bottom: 0;
  widows: 100%;
  z-index: -1;
  position: absolute;
  height: calc(100% + 35px);
  background: linear-gradient(to top, rgb(0, 0, 0, 0.7), transparent);
}
.video-controls {
  padding: 0px 20px 6px;
}
.video-controls .options {
  width: 100%;
}
.video-controls .options:last-child {
  justify-content: flex-end;
}
.video-controls .options:first-child {
  justify-content: flex-start;
}
.video-controls .left {
  position: relative;
}
.options button :where(.mdi, span) {
  width: 100%;
  height: 100%;
  line-height: 40px;
}
.video-controls .right {
  position: relative;
}
.options input {
  height: 6px;
  max-width: 50px;
}
.video-timeline {
  height: 7px;
  width: 100%;
  cursor: pointer;
}
.video-timeline .progress-area {
  height: 3px;
  position: relative;
  background: rgb(255, 255, 255, 0.6);
}
.progress-area span {
  position: absolute;
  top: -25px;
  left: 0%;
  color: #fff;
  font-size: 13px;
  transform: translateX(-50%);
}
.video-duration {
  position: absolute;
  top: -10px;
  color: #fff;
  font-size: 13px;
  transform: translateX(-50%);
}
.progress-area .progress-bar::before {
  content: "";
  top: 50%;
  right: 0;
  height: 12px;
  width: 12px;
  background: inherit;
  position: absolute;
  border-radius: 50%;
  transform: translateY(-50%);
}
.progress-area span,
.progress-area .progress-bar::before {
  display: none;
}
.video-timeline:hover .progress-area span,
.video-timeline:hover .progress-area .progress-bar::before {
  display: block;
}
.progress-area .progress-bar {
  width: 0%;
  height: 100%;
  position: relative;
  background: #2289ff;
}
.video-controls .video-timer {
  font-size: 14px;
  color: #efefef;
  position: relative;
  bottom: -9px;
  left: 5px;
}
.video-timer .separator {
  font-size: 16px;
  margin-left: 3px;
  margin-right: 3px;
  font-family: "Open sans";
}
.options button {
  width: 40px;
  height: 40px;
  border: none;
  font-size: 19px;
  background: none;
}
button .mdi {
  cursor: pointer;
  color: #fff;
}
.skip-backward {
  transform: rotate(180deg);
}
.playback-content {
  position: relative;
}
.playback-content .speed-options {
  position: absolute;
  background: #fff;
  bottom: 40px;
  left: -40px;
  opacity: 0;
  pointer-events: none;
  text-align: left;
  padding-left: 0;
  list-style-type: none;
  border-radius: 4px;
  transition: opacity 0.13s ease;
}
.playback-content button:hover ~ .speed-options {
  opacity: 1;
  pointer-events: auto;
}
.speed-options li {
  cursor: pointer;
  font-size: 14px;
  padding: 3px 15px 3px 5px;
}
.speed-options li.active {
  color: #fff;
  background: #2289ff;
}
.container video {
  width: 100%;
  border-radius: 5px;
}
</style>
