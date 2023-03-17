<template>
	<div
		class="single-attribute"
		:class="selected ? 'borderColorPrimary' : 'borderColor'"
		@click="Atclick()"
	>
		<svg
			id="Group_677"
			data-name="Group 677"
			xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink"
			width="15"
			height="21"
			viewBox="0 0 12.031 18.229"
		>
			<defs>
				<clipPath id="clip-path">
					<rect
						id="Rectangle_296"
						data-name="Rectangle 296"
						width="12.031"
						height="18.229"
						fill="#2e7be6"
					/>
				</clipPath>
			</defs>
			<g id="Group_676" data-name="Group 676" clip-path="url(#clip-path)">
				<path
					id="Path_389"
					data-name="Path 389"
					d="M11.615.523a5.385,5.385,0,0,1,.4,1.394c.029,4.8.02,9.608.013,14.413a1.779,1.779,0,0,1-1.1,1.709,1.763,1.763,0,0,1-2.006-.3c-.845-.719-1.7-1.432-2.533-2.16a.482.482,0,0,0-.737.006Q4.369,16.7,3.072,17.777a1.746,1.746,0,0,1-1.929.281A1.823,1.823,0,0,1,.008,16.464c-.014-.14,0-.281,0-.422Q0,9.116,0,2.192A1.923,1.923,0,0,1,2.2,0c2.582,0,5.164,0,7.746,0a2.01,2.01,0,0,1,1.537.568l.136-.045m-7.222,6.3a2.255,2.255,0,1,0,.044-3.172,2.261,2.261,0,0,0-.044,3.172"
					transform="translate(0 0)"
					fill="#2e7be6"
				/>
				<path
					id="Path_390"
					data-name="Path 390"
					d="M57.4,48.358a.75.75,0,0,1,0,1.05.738.738,0,0,1-1.079-.01.752.752,0,0,1,.023-1.05.763.763,0,0,1,1.053.01"
					transform="translate(-50.85 -43.622)"
					fill="#2e7be6"
				/>
			</g>
		</svg>

		<p class="single-attribute-title ms-1" :class="selected ? 'primary--text' : 'grey--text'">
			{{ titleItem }}
		</p>
	</div>
</template>
<script>
export default {
	props: {
		titleItem: String,
		selected: Boolean,
	},
	methods: {
		Atclick() {
			this.$emit("click");
		},
	},
};
</script>
<style scoped>
.single-attribute {
	border-radius: 5px;
	padding: 5.5px 8px;
	margin: 5px 7px;
	display: flex;
	align-items: center;
	justify-content: center;
}
.borderColor {
	border: 1px solid var(--gray);
}
.borderColorPrimary {
	border: 1px solid var(--v-primary-base);
}
.single-attribute-title {
	margin-bottom: 0px;
	font-size: 17px;
}
.single-attribute:hover {
	background-color: var(--v-surface-darken1);
	cursor: pointer;
}
</style>
