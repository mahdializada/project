<template>
	<v-form @submit.prevent="submit">
		<v-textarea
			:class="`form-new-item form-custom-text-area ${true ? 'has-append' : ''}`"
			background-color="var(--new-input-bg)"
			outlined
			rows="2"
			row-height="15"
			auto-grow
			v-model.trim="remark.remark">
			<template slot="append" class="pe-0">
				<v-btn
					fab
					x-small
					text
					color="primary"
					class="ma-0"
					@click="editRemark()">
					<v-icon dark size="20"> mdi-send </v-icon>
				</v-btn>
			</template>
		</v-textarea>
	</v-form>
</template>

<script>
export default {
	props: {
		remark: {
			type: Object,
			requere: true,
		},
	},
	data() {
		return {
			temp_remark: {},
		};
	},
	methods: {
		async editRemark() {
			try {
				if (this.remark.remark != "") {
					this.$emit("saveEdit", this.remark);
					let { data } = await this.$axios.put("remarks/id", this.remark);
					if (!data.result) {
						this.$emit("reverseEdit", this.temp_remark);
					}
				}
			} catch (error) {
				// this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr("something_went_wrong"));
			}
		},
	},
	created() {
		this.temp_remark = JSON.parse(JSON.stringify(this.remark));
	},
};
</script>

<style></style>
