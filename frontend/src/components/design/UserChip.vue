<template>
	<div class="`ma-0 d-flex justify-center align-center user-created pe-1">
		<v-avatar class="me-1" :color="color" size="24">
			<img v-if="hasImage" :src="avatar" :alt="name" />
			<span v-else class="white--text">{{ avatar }}</span>
		</v-avatar>
		<p class="ma-0" style="white-space: nowrap">{{ name }}</p>
		<button
			@click="close"
			v-if="showCloseBtn"
			type="button"
			:class="`close-btn v-btn ms-2 
    ${selectedClose ? 'select-close' : ''}`"
			v-bind="$attrs"
			v-on="$listeners"
		>
			<svg
				version="1.1"
				id="Layer_1"
				xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink"
				x="0px"
				y="0px"
				scaleY="-1"
				viewBox="0 0 30.3 30.4"
				style="enable-background: new 0 0 30.3 30.4"
				xml:space="preserve"
				fill="currentColor"
			>
				<g>
					<path
						d="M15.2,0c-3.6,0-7.1,1.3-9.9,3.6C2.6,6,0.8,9.2,0.2,12.8c-0.6,3.6,0.2,7.2,2,10.3l0.4,0.7L4,23l-0.4-0.7
		c-1.7-2.8-2.4-6.2-1.8-9.4c0.6-3.3,2.3-6.2,4.9-8.3c2.6-2.1,5.8-3.1,9.1-3c3.3,0.2,6.4,1.5,8.8,3.8c2.4,2.3,3.8,5.4,4.1,8.7
		c0.3,3.3-0.7,6.6-2.7,9.2s-4.9,4.5-8.1,5.2c-3.2,0.7-6.6,0.1-9.5-1.5l-0.7-0.4L7,28.1l0.7,0.4h0c2.7,1.5,5.8,2.2,8.9,1.9
		c3.1-0.3,6-1.5,8.4-3.5c2.4-2,4.1-4.7,4.9-7.7c0.8-3,0.6-6.2-0.4-9.1c-1.1-2.9-3-5.4-5.5-7.2C21.3,1,18.3,0,15.2,0L15.2,0z"
					/>
					<path
						d="M8.8,22.7l6.4-6.4l6.4,6.4l1.1-1.1l-6.4-6.4l6.4-6.4l-1.1-1.1l-6.4,6.4L8.8,7.7L7.7,8.8l6.4,6.4l-6.4,6.4L8.8,22.7z"
					/>
				</g>
			</svg>
		</button>
	</div>
</template>
<script>
export default {
	props: {
		name: {
			type: String,
			required: true,
		},
		avatar: {
			type: String,
			required: true,
		},
		color: {
			type: String,
			default: "primary",
		},
		showCloseBtn: {
			type: Boolean,
		},
		selectedClose: {
			type: Boolean,
		},
		hasImage: {
			type: Boolean,
			default: false,
		},
	},
	methods: {
		close(event) {
			if (event) {
				event?.preventDefault();
			}
			this.$emit("close");
		},
	},
};
</script>
<style scoped>
.user-created {
	padding: 4px;
	border: 1px solid rgba(144, 144, 144, 0.5);
	border-radius: 16px;
	width: fit-content;
	height: 32px !important;
}
.user-created p {
	text-decoration: none !important;
	font-size: 14px;
	font-weight: 500;
	color: unset !important;
}
.select-close {
	height: 20px;
	width: 20px;
	color: rgb(87, 87, 87) !important;
}
.select-close:hover {
	color: black !important;
}
</style>
