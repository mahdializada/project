<template>
	<InputCard pbNone v-bind="$attrs" v-on="$listeners">
		<template slot-scope="{ attrs, listeners }">
			<v-text-field
				:class="`form-new-item form-custom-text-field ${
					hasItems || hasCustomBtn ? 'has-append' : ''
				} ${small ? 'small' : ''}`"
				background-color="var(--new-input-bg)"
				outlined
				:rounded="rounded"
				v-on="listeners"
				v-bind="attrs"
				@click="menu2 = true"
			>
				<template v-slot:append>
					<v-menu
						v-model="menu2"
						nudge-bottom="50"
						:close-on-content-click="closeClickContent"
					>
						<template v-slot:activator="{ on }">
							<div :style="swatchStyle" v-on="on" />
						</template>
						<v-card>
							<v-card-text>
								<v-color-picker
									mode="hexa"
									v-bind="attrs"
									v-on="listeners"
									:bottomBar="false"
									:swatches="swatches"
									show-swatches
								/>
							</v-card-text>
						</v-card>
					</v-menu>
				</template>
			</v-text-field>
		</template>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
import CFeildItems from "./CFeildItems.vue";
export default {
	props: {
		hasItems: Boolean,
		small: Boolean,
		hasCustomBtn: Boolean,
		btnIcon: String,
		currentColor: {
			type: String,
			default: "#FF0000FF",
		},
		rounded: {
			type: Boolean,
			default: true,
		},
		closeClickContent: Boolean,
	},
	data() {
		return {
			i: 0,
			menu2: false,
			swatches: [
				["#2e7be6", "#31944f"],
				["#EE4f12", "#46BBB1"],
				["#ee44aa", "#55BB46"],
			],
		};
	},
	computed: {
		swatchStyle() {
			return {
				backgroundColor: this.currentColor,
				cursor: "pointer",
				height: "30px",
				width: "30px",
				position: "relative",
				bottom: "5px",
				borderRadius: "50%",
				top: "0px",
				transition: "border-radius 200ms ease-in-out",
			};
		},
	},
	components: {
		InputCard,
		CFeildItems,
	},
};
</script>

<style>
.form-custom-text-field .v-input__prepend-inner {
	margin-top: 16px;
	margin-right: 8px !important;
}
.form-custom-text-field.has-append .v-input__slot {
	padding-right: 8px !important;
}
.v-application--is-rtl .form-custom-text-field.has-append .v-input__slot {
	padding-right: 24px !important;
	padding-left: 8px;
}
.form-custom-text-field.has-append .v-input__append-inner {
	margin-top: 8px !important;
}
.form-custom-text-field.small .v-btn {
	height: 28px;
	width: 28px;
}
.form-custom-text-field.has-append.small .v-input__append-inner {
	margin-top: 6px !important;
}
.form-custom-text-field.has-append.small .v-input__slot {
	padding-right: 6px !important;
	padding-left: 24px !important;
}
.v-application--is-rtl .form-custom-text-field.has-append.small .v-input__slot {
	padding-right: 24px !important;
	padding-left: 6px !important;
}
.single-text-filed-values {
	max-height: 78px;
	overflow-y: auto;
}
.single-text-filed-value {
	border: 1px solid rgba(0, 0, 0, 0.05);
	border-radius: 4px;
	color: #777;
	font-size: 12px;
	margin: 4px;
}
.single-text-filed-value .v-btn {
	height: 28px;
	width: 28px;
}
</style>
