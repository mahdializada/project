<template>
	<InputCard pbNone v-bind="$attrs" v-on="$listeners">
		<template slot-scope="{ attrs, listeners }">
			<v-file-input
				:class="`form-new-item form-custom-file-input`"
				background-color="var(--new-input-bg)"
				prepend-icon=""
				outlined
				rounded
				v-bind="attrs"
				v-on="listeners"
				:clearable="false"
				ref="fileInput"
				@change="fileInputChange"
			>
				<template slot="append">
					<v-btn
						rounded
						class="file-input-btn"
						color="primary"
						large
						dark
						@click="
							() => {
								value == null ? btnUpload() : btnDelete();
							}
						"
					>
						{{ value == null ? $tr("upload") : $tr("delete") }}
					</v-btn>
				</template>
			</v-file-input>
		</template>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
	props: {
		items: Array,
		value: null,
	},
	components: {
		InputCard,
	},
	methods: {
		btnUpload() {
			this.$refs.fileInput.$refs.input.click();
		},
		btnDelete() {
			this.$refs.fileInput.reset();
		},
		fileInputChange(file) {
			this.$emit("input", file);
		},
	},
};
</script>

<style>
.form-custom-file-input .v-input__prepend-inner {
	margin-top: 16px;
	margin-right: 8px !important;
}
.form-custom-file-input.has-append .v-input__slot {
	padding-right: 8px !important;
}
.v-application--is-rtl .form-custom-file-input.has-append .v-input__slot {
	padding-right: 24px !important;
	padding-left: 8px;
}
.form-custom-file-input.has-append .v-input__append-inner {
	margin-top: 8px !important;
}
.form-custom-file-input .v-input__append-inner {
	margin-top: 8px;
}
.form-custom-file-input .file-input-btn {
	height: 40px !important;
	font-size: 14px;
	font-weight: 500;
}
.form-custom-file-input .v-input__slot {
	padding-right: 8px !important;
}
.v-application--is-rtl .form-custom-file-input .v-input__slot {
	padding-right: unset !important;
	padding-left: 8px !important;
}
</style>
