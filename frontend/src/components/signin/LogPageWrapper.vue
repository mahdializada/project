<template>
  <div :key="signKey">
    <span>
      <video
        :key="videoKey"
        v-if="selectedBack.type == 'video'"
        autoplay
        muted
        loop
        id="myVideo"
      >
        <source :src="selectedBack.icon" type="video/mp4" />
      </video>
      <div
        v-if="selectedBack.type == 'image'"
        id="myVideo"
        :style="`background-image:url(${selectedBack.icon}) ; background-size :cover ;background-position: 100%;`"
      ></div>
      <div id="layout" style="background-color: rgba(0, 0, 0, 0.5)"></div>
    </span>
    <div style="position: absolute; left: 2%; top: 30px">
      <img src="../../static/images/Group 230.png" alt="logo pic" width="100" />
    </div>

    <div style="position: absolute; left: 2%; bottom: 30px">
      <svg
        id="e4POyesRqXz1"
        height="50px"
        xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        viewBox="0 0 320 50"
        shape-rendering="geometricPrecision"
        text-rendering="geometricPrecision"
      >
        <g transform="translate(0 0.000001)">
          <path
            id="e4POyesRqXz3"
            d="M56.2,18.2l-4,15.4h-4.6L45.1,23.4L42.5,33.6h-4.6L34,18.2h4l2.2,11.2L43,18.2h4.1L50,29.5l2.2-11.2h4Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz4"
            d="M60.5,33.1c-1-.5-1.7-1.2-2.3-2.2-.5-1-.8-2.1-.8-3.3c0-1.3.3-2.4.8-3.3.6-1,1.3-1.7,2.3-2.2s2.1-.8,3.3-.8s2.3.3,3.3.8s1.7,1.3,2.3,2.2c.6,1,.8,2.1.8,3.3s-.3,2.4-.8,3.3-1.4,1.7-2.4,2.2-2.1.8-3.3.8c-1.2-.1-2.3-.3-3.2-.8Zm5.1-3.3c.5-.5.8-1.3.8-2.3s-.2-1.7-.7-2.3c-.5-.5-1.1-.8-1.8-.8s-1.3.3-1.8.8-.7,1.3-.7,2.3.2,1.7.7,2.3c.5.5,1.1.8,1.8.8.5,0,1.2-.3,1.7-.8Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz5"
            d="M77.6,21.8c.7-.4,1.4-.6,2.2-.6v4h-1c-.9,0-1.6.2-2.1.6s-.7,1.1-.7,2.1v5.7h-3.8v-12.2h3.8v2c.4-.6.9-1.2,1.6-1.6Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz6"
            d="M85.3,17.4v16.3h-3.8v-16.3h3.8Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz7"
            d="M88,24.2c.5-1,1.1-1.7,1.9-2.2s1.7-.8,2.8-.8c.8,0,1.5.2,2.2.5s1.2.8,1.6,1.4v-5.7h3.8v16.3h-3.8v-1.8c-.4.6-.9,1.1-1.5,1.4-.7.4-1.4.5-2.3.5-1,0-1.9-.3-2.8-.8-.8-.5-1.5-1.3-1.9-2.2-.5-1-.7-2.1-.7-3.3c0-1.3.2-2.4.7-3.3Zm7.7,1.1c-.5-.5-1.2-.8-1.9-.8s-1.4.3-1.9.8-.8,1.3-.8,2.2.3,1.7.8,2.2s1.2.8,1.9.8s1.4-.3,1.9-.8.8-1.3.8-2.2-.3-1.7-.8-2.2Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz8"
            d="M111.4,35.2c0,1.5-.4,2.6-1.1,3.3-.8.7-1.8,1-3.2,1h-1.5v-3.2h.9c.4,0,.7-.1.9-.2.2-.2.3-.4.3-.8v-13.9h3.8v13.8Zm-3.5-15.7c-.4-.4-.6-.9-.6-1.4c0-.6.2-1.1.6-1.5s1-.6,1.6-.6s1.2.2,1.6.6.6.9.6,1.5-.2,1.1-.6,1.4-.9.6-1.6.6c-.6,0-1.2-.2-1.6-.6Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz9"
            d="M126.2,21.4v12.3h-3.8v-1.7c-.4.5-.9,1-1.6,1.3s-1.4.5-2.2.5c-.9,0-1.8-.2-2.5-.6s-1.3-1-1.7-1.8-.6-1.7-.6-2.8v-7.2h3.7v6.7c0,.8.2,1.5.6,1.9.4.5,1,.7,1.7.7s1.3-.2,1.7-.7.6-1.1.6-1.9v-6.7h4.1Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz10"
            d="M131.1,33.3c-.8-.4-1.5-.9-2-1.5s-.8-1.4-.8-2.1h3.7c0,.4.2.8.6,1c.4.3.8.4,1.3.4s.8-.1,1.1-.3.4-.4.4-.7c0-.4-.2-.6-.6-.8s-1-.4-1.8-.6c-.9-.2-1.6-.4-2.2-.6s-1.1-.6-1.5-1.1-.6-1.2-.6-2c0-.7.2-1.3.6-1.9s1-1,1.7-1.4c.8-.3,1.7-.5,2.7-.5c1.5,0,2.8.4,3.6,1.1s1.4,1.8,1.5,3h-3.5c-.1-.4-.2-.8-.6-1-.3-.2-.7-.4-1.2-.4-.4,0-.8.1-1,.3s-.4.4-.4.7c0,.4.2.6.6.8s1,.4,1.7.5c.9.2,1.6.5,2.2.7s1.1.6,1.5,1.1.7,1.2.7,2c0,.7-.2,1.4-.6,1.9-.4.6-1,1-1.7,1.3-.8.3-1.6.5-2.6.5-1,.1-2,0-2.8-.4Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz11"
            d="M148.1,30.5v3.2h-1.9c-1.4,0-2.4-.3-3.2-1s-1.1-1.8-1.1-3.3v-4.9h-1.5v-3.1h1.5v-3h3.8v3h2.5v3.1h-2.5v4.9c0,.4.1.6.3.8s.5.2.9.2h1.2Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz12"
            d="M161.8,21.7c.7.4,1.2.8,1.5,1.4v-1.7h3.8v12.3c0,1.1-.2,2.2-.7,3.1s-1.1,1.6-2,2.2c-.9.5-2.1.8-3.4.8-1.8,0-3.3-.4-4.4-1.3s-1.8-2-1.9-3.5h3.7c.1.5.4.8.8,1.1s1,.4,1.6.4c.8,0,1.4-.2,1.9-.7s.7-1.2.7-2.1v-1.7c-.4.6-.9,1-1.5,1.4s-1.4.5-2.3.5c-1,0-1.9-.3-2.8-.8-.8-.5-1.5-1.3-1.9-2.2-.5-1-.7-2.1-.7-3.3c0-1.3.2-2.4.7-3.3.5-1,1.1-1.7,1.9-2.2s1.7-.8,2.8-.8c.8-.1,1.5.1,2.2.4Zm.7,3.6c-.5-.5-1.2-.8-1.9-.8s-1.4.3-1.9.8-.8,1.3-.8,2.2.3,1.7.8,2.2s1.2.8,1.9.8s1.4-.3,1.9-.8.8-1.3.8-2.2-.3-1.7-.8-2.2Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz13"
            d="M172.1,33.1c-1-.5-1.7-1.2-2.3-2.2-.5-1-.8-2.1-.8-3.3c0-1.3.3-2.4.8-3.3.6-1,1.3-1.7,2.3-2.2s2.1-.8,3.3-.8s2.3.3,3.3.8s1.7,1.3,2.3,2.2c.6,1,.8,2.1.8,3.3s-.3,2.4-.8,3.3-1.3,1.7-2.3,2.2-2.1.8-3.3.8c-1.3-.1-2.3-.3-3.3-.8Zm5.1-3.3c.5-.5.8-1.3.8-2.3s-.2-1.7-.7-2.3c-.5-.5-1.1-.8-1.8-.8s-1.3.3-1.8.8-.7,1.3-.7,2.3.2,1.7.7,2.3c.5.5,1.1.8,1.8.8.6,0,1.2-.3,1.7-.8Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz14"
            d="M190.6,30.5v3.2h-1.9c-1.4,0-2.4-.3-3.2-1s-1.1-1.8-1.1-3.3v-4.9h-1.5v-3.1h1.5v-3h3.8v3h2.5v3.1h-2.5v4.9c0,.4.1.6.3.8s.5.2.9.2h1.2Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz15"
            d="M208.9,28.4h-8.5c.1.8.3,1.3.7,1.7s1,.6,1.6.6c.9,0,1.6-.4,2-1.2h4c-.2.8-.6,1.5-1.1,2.2-.5.6-1.2,1.2-2,1.5-.8.4-1.7.5-2.7.5-1.2,0-2.3-.3-3.2-.8s-1.7-1.2-2.2-2.2-.8-2.1-.8-3.3c0-1.3.3-2.4.8-3.3.5-1,1.3-1.7,2.2-2.2s2-.8,3.2-.8s2.2.3,3.2.7c.9.5,1.6,1.2,2.2,2.1s.8,2,.8,3.2c-.2.6-.2.9-.2,1.3Zm-3.8-2.1c0-.6-.2-1.2-.7-1.5-.4-.4-1-.6-1.6-.6s-1.2.2-1.6.6-.7.9-.8,1.6h4.7Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz16"
            d="M210.9,24.2c.5-1,1.1-1.7,1.9-2.2s1.7-.8,2.8-.8c.9,0,1.6.2,2.3.5.7.4,1.2.8,1.5,1.4v-1.7h3.8v12.3h-3.8v-1.7c-.4.6-.9,1-1.5,1.4-.7.4-1.4.5-2.3.5-1,0-1.9-.3-2.7-.8s-1.5-1.3-1.9-2.2c-.5-1-.7-2.1-.7-3.3-.1-1.4.1-2.5.6-3.4Zm7.7,1.1c-.5-.5-1.2-.8-1.9-.8s-1.4.3-1.9.8-.8,1.3-.8,2.2.3,1.7.8,2.2s1.2.8,1.9.8s1.4-.3,1.9-.8.8-1.3.8-2.2-.3-1.7-.8-2.2Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz17"
            d="M228,33.3c-.8-.4-1.5-.9-2-1.5s-.8-1.4-.8-2.1h3.7c0,.4.2.8.6,1c.4.3.8.4,1.3.4s.8-.1,1.1-.3.4-.4.4-.7c0-.4-.2-.6-.6-.8s-1-.4-1.8-.6c-.9-.2-1.6-.4-2.2-.6s-1.1-.6-1.5-1.1-.6-1.2-.6-2c0-.7.2-1.3.6-1.9s1-1,1.7-1.4c.8-.3,1.7-.5,2.7-.5c1.5,0,2.8.4,3.6,1.1s1.4,1.8,1.5,3h-3.5c-.1-.4-.2-.8-.6-1-.3-.2-.7-.4-1.2-.4-.4,0-.8.1-1,.3s-.4.4-.4.7c0,.4.2.6.6.8s1,.4,1.7.5c.9.2,1.6.5,2.2.7s1.1.6,1.5,1.1.7,1.2.7,2c0,.7-.2,1.4-.6,1.9-.4.6-1,1-1.7,1.3-.8.3-1.6.5-2.6.5-1,.1-2,0-2.8-.4Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz18"
            d="M238.4,19.5c-.4-.4-.6-.9-.6-1.4c0-.6.2-1.1.6-1.5s1-.6,1.6-.6s1.2.2,1.6.6.6.9.6,1.5-.2,1.1-.6,1.4-.9.6-1.6.6c-.6,0-1.2-.2-1.6-.6Zm3.5,1.9v12.3h-3.8v-12.3h3.8Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz19"
            d="M256.1,28.4h-8.5c.1.8.3,1.3.7,1.7s1,.6,1.6.6c.9,0,1.6-.4,2-1.2h4c-.2.8-.6,1.5-1.1,2.2-.5.6-1.2,1.2-2,1.5-.8.4-1.7.5-2.7.5-1.2,0-2.3-.3-3.2-.8s-1.7-1.2-2.2-2.2-.8-2.1-.8-3.3c0-1.3.3-2.4.8-3.3.5-1,1.3-1.7,2.2-2.2s2-.8,3.2-.8s2.2.3,3.2.7c.9.5,1.6,1.2,2.2,2.1s.8,2,.8,3.2c-.1.6-.1.9-.2,1.3Zm-3.7-2.1c0-.6-.2-1.2-.7-1.5-.4-.4-1-.6-1.6-.6s-1.2.2-1.6.6-.7.9-.8,1.6h4.7Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz20"
            d="M263.6,21.8c.7-.4,1.4-.6,2.2-.6v4h-1c-.9,0-1.6.2-2.1.6s-.7,1.1-.7,2.1v5.7h-3.8v-12.2h3.8v2c.4-.6.9-1.2,1.6-1.6Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz21"
            d="M267.7,33.3c-.4-.4-.6-.9-.6-1.4c0-.6.2-1.1.6-1.5s1-.6,1.6-.6s1.2.2,1.6.6.6.9.6,1.5-.2,1.1-.6,1.4c-.4.4-1,.6-1.6.6-.6-.1-1.1-.3-1.6-.6Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz22"
            d="M273.9,33.3c-.4-.4-.6-.9-.6-1.4c0-.6.2-1.1.6-1.5s1-.6,1.6-.6s1.2.2,1.6.6.6.9.6,1.5-.2,1.1-.6,1.4c-.4.4-1,.6-1.6.6-.6-.1-1.1-.3-1.6-.6Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
          <path
            id="e4POyesRqXz23"
            d="M280.1,33.3c-.4-.4-.6-.9-.6-1.4c0-.6.2-1.1.6-1.5s1-.6,1.6-.6s1.2.2,1.6.6.6.9.6,1.5-.2,1.1-.6,1.4c-.4.4-1,.6-1.6.6-.6-.1-1.1-.3-1.6-.6Z"
            transform="translate(-24.993026 0)"
            opacity="0"
            fill="#fff"
          />
        </g>
      </svg>
    </div>

    <div style="position: absolute; right: 2%; bottom: 30px">
      <v-avatar
        v-for="(backGround, index) in backGrounds"
        :key="index"
        size="60"
        color="grey darken-2"
        :style="
          backGround.isSelected ? `border: 2px solid white !important` : ``
        "
        style="margin: 0px 3px"
        @click="chosseBack(backGround)"
        class="backAvatar"
      >
        <v-avatar size="50" color="primary" class="position-relative">
          <img :src="backGround.thumnail" alt="" />
          <div
            v-if="backGround.type == 'video'"
            style="position:absolute; top: 50% right: 50%;"
          >
            <v-avatar
              size="35"
              style="background-color: rgba(255, 255, 255, 0.5) !important"
            >
              <v-icon color="white">mdi-play</v-icon>
            </v-avatar>
          </div>
        </v-avatar>
      </v-avatar>
    </div>
    <slot name="form"></slot>
  </div>
</template>

<script>
export default {
	props: {
		signKey: Number,
	},
	data() {
		return {
			videoKey: 0,
			selectedBack: "",
			backGrounds: [
				{
					id: 1,
					isSelected: false,
					type: "image",
					icon: "/images/1.jpg",
					thumnail: "/images/1.jpg",
				},
				{
					id: 2,
					isSelected: false,
					type: "video",
					icon: "/videos/video1.mp4",
					thumnail: "/videos/video1.png",
				},
				{
					id: 3,
					isSelected: false,
					type: "image",
					icon: "/images/2.jpg",
					thumnail: "/images/2.jpg",
				},
				{
					id: 4,
					isSelected: false,
					type: "video",
					icon: "/videos/video2.mp4",
					thumnail: "/videos/video2.png",
				},
				{
					id: 5,
					isSelected: false,
					type: "video",
					icon: "/videos/video3.mp4",
					thumnail: "/videos/video3.png",
				},
				{
					id: 6,
					isSelected: true,
					type: "image",
					icon: "/images/3.jpg",
					thumnail: "/images/3.jpg",
				},
				{
					id: 7,
					isSelected: false,
					type: "image",
					icon: "/images/4.jpg",
					thumnail: "/images/4.jpg",
				},
				{
					id: 8,
					isSelected: false,
					type: "video",
					icon: "/videos/video4.mp4",
					thumnail: "/videos/video4.png",
				},
			],
		};
	},

  methods: {
    chosseBack(item) {
      this.backGrounds.map((e) =>
        e.id == item.id ? (e.isSelected = true) : (e.isSelected = false)
      );
      this.selectedBack = item;
      this.videoKey++;
      const itemStorage = JSON.stringify(item);
      localStorage.setItem("selected background", itemStorage);
    },
  },
  created() {
    if (process.client) {
      let selectedBack2 = JSON.parse(
        localStorage.getItem("selected background")
      );
      if (selectedBack2) {
        this.selectedBack = selectedBack2;
      } else {
        selectedBack2 = this.backGrounds.filter(
          (row) => row.isSelected == true
        );
        this.selectedBack = selectedBack2[0];
      }
      this.backGrounds.map((e) =>
        e.id == this.selectedBack.id
          ? (e.isSelected = true)
          : (e.isSelected = false)
      );
    }
  },
};
</script>

<style scoped>
.v-sheet.v-card:not(.v-sheet--outlined) {
  box-shadow: unset;
}
.theme--light.v-card {
  background-color: rgba(232, 221, 221, 0.804);
  color: rgba(0, 0, 0, 0.87);
}
#myVideo {
  position: fixed;
  right: 0;
  bottom: 0;
  min-width: 100vw;
  min-height: 100vh;
}
.backAvatar:hover {
  cursor: pointer;
}
#layout {
  position: fixed;
  right: 0;
  bottom: 0;
  min-width: 100vw;
  min-height: 100vh;
}
</style>

<style>
#e4POyesRqXz3 {
  animation: e4POyesRqXz3_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz3_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  4% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz4 {
  animation: e4POyesRqXz4_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz4_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  8% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz5 {
  animation: e4POyesRqXz5_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz5_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  12% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz6 {
  animation: e4POyesRqXz6_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz6_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  16% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz7 {
  animation: e4POyesRqXz7_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz7_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  20% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz8 {
  animation: e4POyesRqXz8_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz8_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  24% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz9 {
  animation: e4POyesRqXz9_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz9_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  28% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz10 {
  animation: e4POyesRqXz10_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz10_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  32% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz11 {
  animation: e4POyesRqXz11_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz11_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  36% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz12 {
  animation: e4POyesRqXz12_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz12_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  40% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz13 {
  animation: e4POyesRqXz13_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz13_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  44% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz14 {
  animation: e4POyesRqXz14_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz14_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  48% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz15 {
  animation: e4POyesRqXz15_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz15_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  52% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz16 {
  animation: e4POyesRqXz16_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz16_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  56% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz17 {
  animation: e4POyesRqXz17_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz17_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  60% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz18 {
  animation: e4POyesRqXz18_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz18_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  64% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz19 {
  animation: e4POyesRqXz19_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz19_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  68% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz20 {
  animation: e4POyesRqXz20_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz20_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  72% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz21 {
  animation: e4POyesRqXz21_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz21_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  76% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz22 {
  animation: e4POyesRqXz22_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz22_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  80% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
#e4POyesRqXz23 {
  animation: e4POyesRqXz23_c_o 2500ms linear infinite normal forwards;
}
@keyframes e4POyesRqXz23_c_o {
  0% {
    opacity: 0;
    animation-timing-function: step-end;
  }
  84% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
</style>