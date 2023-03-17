<template>
	<div>
		<CTitle :text="'checking_ads'" />
		<v-data-table
			:headers="headers"
			:items="items"
			:key="key"
			disable-sort
			height="800px"
			:items-per-page="15"
		>
			<template
				v-slot:item="{ item }"
				v-if="this.form.check_type.$model != 'order'"
			>
				<tr>
					<td class="red--text font-weight-bold">{{ item.id }}</td>
					<td class="red--text font-weight-bold">{{ item.ad_id }}</td>
				</tr>
			</template>
			<template v-slot:[`item.number`]="{ index }">
				<div>{{ index }}</div>
			</template>
			<template v-slot:[`item.exist`]="{ item }">
				<div :class="item.exist == 'exist' ? 'primary--text' : 'pink--text'">
					{{ item.exist }}
				</div>
			</template>
		</v-data-table>
	</div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CustomButton from "../../design/components/CustomButton.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";

export default {
	props: {
		form: Object,
	},
	watch: {
		"form.file.$model": function (data) {
			if (Array.isArray(data))
				if (this.form.check_type.$model == "order") {
					this.headers = [
						{ text: this.$tr("#"), value: "number" },
						{ text: this.$tr("exist"), value: "exist" },
						{ text: this.$tr("product"), value: "pcode" },
						{ text: this.$tr("name"), value: "name" },
						{ text: this.$tr("phone"), value: "phone" },
						{ text: this.$tr("qty"), value: "qty" },
						{ text: this.$tr("price"), value: "price" },
						{ text: this.$tr("city"), value: "city" },
						{ text: this.$tr("address"), value: "address" },
					];
				} else {
					this.headers = [
						{ text: this.$tr("in_line"), value: "id" },
						{ text: this.$tr("ad_id"), value: "ad_id" },
					];
				}
		},
	},
	data() {
		return {
			isFileDownloaded: false,
			key: 0,
			items: [],
			headers: [
				{ text: this.$tr("in_line"), value: "id" },
				{ text: this.$tr("ad_id"), value: "ad_id" },
			],
			validateRule: GlobalRules.validate,
		};
	},
	methods: {
		async validate() {
			return true;
		},
		async loaded() {
			this.key++;
			let data = this.form.$model.file;

			this.items =
				this.form.check_type.$model == "order" ? data : this.form.$model.file;
		},
	},
	components: { CustomButton, CTitle },
};
</script>

<style scoped></style>
