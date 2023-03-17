<template>
	<div
		style="height: 90%"
		class="d-flex justify-center align-center justify-space-between mx-2 position-relative"
	>
		<div class="me-1 prev-btn">
			<v-btn
				@click="onPrev"
				v-shortkey="['arrowleft']"
				@shortkey.native="onPrev"
				:disabled="!hasPrev"
				icon
				text
			>
				<v-icon x-large>mdi-chevron-left</v-icon>
			</v-btn>
		</div>

		<div
			v-if="isLoading"
			class="d-flex align-center justify-center mx-auto w-full"
		>
			<Loader
				style="position: unset; left: unset; transform: unset; top: unset"
			/>
		</div>

		<div
			style="width: calc(100% - 140px)"
			v-else
			:class="`mx-auto d-flex ${
				details.extension in officeTypes ||
				details.mime_type === 'application/pdf'
					? 'h-full'
					: ''
			}`"
		>
			<img
				:src="details.path"
				v-if="checkFileType('image')"
				contain
				class="rounded mx-auto"
				style="max-height: 80vh; max-width: 100%"
				:lazy-src="details.thumbnail"
			/>
			<video
				v-else-if="checkFileType('video')"
				controls
				:autoplay="false"
				ref="videoRef"
				style="display: block; border-radius: 4px; max-height: 80vh"
				width="100%"
				class="w-full"
				@enterpictureinpicture="onEnterPip"
				@leavepictureinpicture="onLeavePip"
				@timeupdate="onVideoTimeUpdate"
			>
				<source ref="sourceRef" :type="details.mime_type" :src="details.path" />
				Your browser does not support the video tag.
			</video>

			<!-- Office package types goes here -->
			<!-- <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=https://api.oredoh.org/storage/file-management/personal/463178d9-d161-4bb4-b891-263fe7e5dcbd/languages.xlsx' :width='clientWidth' :height='clientHeight' frameborder='0'> </iframe> -->

			<iframe
				v-else-if="details.extension in officeTypes"
				:src="`https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(
					details.path
				)}`"
				class="w-full h-full"
				frameborder="0"
			>
				This is an embedded
				<a target="_blank" href="http://office.com">Microsoft Office</a>
				document, powered by
				<a target="_blank" href="http://office.com/webapps">Office Online</a>
				.
			</iframe>
			<!-- <iframe v-if="details.extension in officeTypes" :src='`https://view.officeapps.live.com/op/embed.aspx?src=${details.path}`' :width='clientWidth' :height='clientHeight' frameborder='0'>This is an embedded <a target='_blank' href='http://office.com'>Microsoft Office</a> document, powered by <a target='_blank' href='http://office.com/webapps'>Office Online</a>.</iframe> -->

			<!-- pdf file sample  -->
			<!-- <object
        v-if="details.mime_type === 'application/pdf'"
        :width="clientWidth"
        :height="clientHeight"
        :data="details.path"
        type="application/pdf"
      ></object> -->
			<!-- <object
        v-else-if="details.mime_type === 'application/pdf'"
        :width="clientWidth"
        :height="clientHeight"
        :data="details.path"
        type="application/pdf"
      ></object> -->

			<iframe
				v-else-if="details.mime_type === 'application/pdf'"
				:src="`https://docs.google.com/gview?url=${encodeURIComponent(
					details.path
				)}&embedded=true`"
				class="w-full h-full"
				frameborder="0"
				embedded="true"
			></iframe>

			<div v-else-if="checkFileType('audio')" style="min-width: 100%">
				<!-- <div
          :style="`height: ${
            clientHeight > 300 ? clientHeight - 200 : clientHeight
          }px !important; max-height: 500px;`"
        >
          <canvas
            class="rounded"
            :style="`height: 100%; width: 100%;`"
            id="audiocanvas"
          ></canvas>
        </div> -->
				<div
					:style="`height: ${
						clientHeight > 300 ? clientHeight - 200 : clientHeight
					}px !important; max-height: 500px;     display: flex;
    align-items: center;
    justify-content: center;`"
				>
					<svg
						width="300px"
						xmlns="http://www.w3.org/2000/svg"
						xmlns:xlink="http://www.w3.org/1999/xlink"
						version="1.1"
						id="Icons"
						x="0px"
						y="0px"
						viewBox="0 0 24 24"
						style="enable-background: new 0 0 24 24"
						xml:space="preserve"
					>
						<path
							style="fill: var(--v-primary-base)"
							d="M13,19c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1c0.4,0,0.7,0.2,0.9,0.5c0,0,2.8,4.5,7.1,4.5c0.6,0,1,0.4,1,1  s-0.4,1-1,1c-2.7-0.1-5.2-1.2-7-3.2V18C14,18.6,13.6,19,13,19z"
						/>
						<circle style="fill: var(--v-primary-base)" cx="9" cy="18" r="5" />
					</svg>
				</div>
				<div class="w-full mt-1" id="preview-audio-id">
					<audio
						preload="auto"
						class="w-full"
						:src="details.path"
						autoplay
						id="myaudio"
						controls
						style="border-radius: 20px"
					></audio>
				</div>
				<!-- <span v-if="playPauseAudio()"></span> -->
			</div>
			<div v-else class="d-flex align-center justify-center w-full">
				<FileNotSupportPreview />
			</div>
		</div>
		<div class="ms-1 next-btn">
			<v-btn
				@click="onNext"
				v-shortkey="['arrowright']"
				@shortkey.native="onNext"
				:disabled="!hasNext"
				icon
				text
			>
				<v-icon x-large>mdi-chevron-right</v-icon></v-btn
			>
		</div>
	</div>
</template>
<script>
import Loader from "../../../design/Loader.vue";
import FileNotSupportPreview from "../FileNotSupportPreview.vue";

export default {
	name: "main-body",
	components: { Loader, FileNotSupportPreview },
	props: {
		details: Object,
		isLoading: Boolean,
		hasNext: Boolean,
		hasPrev: Boolean,
	},

	data() {
		return {
			isInPipMode: false,
			clientHeight: document.documentElement.clientHeight - 100,
			clientWidth: document.documentElement.clientWidth - 540,
			officeTypes: {
				doc: "",
				docm: "",
				docx: "",
				dot: "",
				dotx: "",
				xlsx: "",
				xlsm: "",
				xltx: "",
				xltm: "",
				xls: "",
				xlt: "",
				xls: "",
				xla: "",
				xlw: "",
				xlr: "",
				adp: "",
				adn: "",
				accdb: "",
				laccdb: "",
				accdw: "",
				accdc: "",
				accda: "",
				accdr: "",
				accdt: "",
				mdb: "",
				mda: "",
				mdw: "",
				mdf: "",
				mde: "",
				accde: "",
				mam: "",
				mad: "",
				maq: "",
				mar: "",
				mat: "",
				maf: "",
				pptx: "",
				pptm: "",
				ppt: "",
				potx: "",
				potm: "",
				pot: "",
				ppsx: "",
				ppsm: "",
				pps: "",
				ppam: "",
				ppa: "",
			},
		};
	},

	computed: {
		checkFileType: function () {
			this.loadVideo();
			return (type) => (this.details?.mime_type?.includes(type) ? true : false);
		},
	},

	mounted() {
		const isPip = this.$route.query?.isPip;
		if (isPip) {
			const vm = this;
			setTimeout(() => {
				const video = vm.$refs?.videoRef;
				const currentTime = JSON.parse(localStorage.getItem("currentTime"));
				video && (video.currentTime = currentTime);
			}, 5000);
		}
	},

	methods: {
		playPauseAudio() {
			const vm = this;
			setTimeout(() => {
				initailizeAudio(vm.details.path);
				// initailizeAudio("/sounds/1.mp3");
			}, 1000);
			return true;
		},

		stopMedia() {
			if (this.details?.mime_type?.includes("video") && !this.isInPipMode) {
				const video = this.$refs?.videoRef;
				video.pause();
			}
			return this.isInPipMode;
		},

		onNext() {
			this.hasNext && this.$emit("on-next");
		},
		onPrev() {
			this.hasPrev && this.$emit("on-prev");
		},

		loadVideo() {
			if (this.details?.mime_type?.includes("video")) {
				var video = this.$refs.videoRef;
				var sourceRef = this.$refs.sourceRef;
				if (sourceRef && video) {
					sourceRef.src = this.details.path;
					video.load();
					// video.play();
				}
			}
		},

		updateUrl(id) {
			const path = JSON.parse(localStorage.getItem("pipPath"));
			const url = path + "?preview=true&preview_id=" + id + "&isPip=true";
			window.location = url;
		},

		onVideoTimeUpdate(event) {
			const currentTime = event.path[0]?.currentTime;
			localStorage.setItem("currentTime", currentTime);
		},
		onEnterPip() {
			this.isInPipMode = true;
			localStorage.setItem("pipPath", JSON.stringify(this.$route.path));
		},
		onLeavePip() {
			this.isInPipMode = false;
			const path = JSON.parse(localStorage.getItem("pipPath"));
			if (path === this.$route.path) {
				this.$emit("on-leave-pip");
				return 0;
			}
			this.updateUrl(this.details.id);
		},

		handleResize(clientHeightWidth) {
			this.clientHeight = clientHeightWidth.clientHeight - 100;
			this.clientWidth = clientHeightWidth.clientWidth - 540;
		},
	},
};
</script>
<style>
#thefile {
	position: fixed;
	top: 10px;
	left: 10px;
	z-index: 100;
}
.prev-btn,
.next-btn {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
}
.prev-btn {
	left: 0%;
}
.next-btn {
	right: 0%;
}

/* #canvas {
  width: 600px;
  height: 350px;
} */

/* audio {
  position: fixed;
  left: 10px;
  bottom: 10px;
  width: calc(100% - 20px);
} */
</style>
