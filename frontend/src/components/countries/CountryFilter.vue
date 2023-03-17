<template>
	<div class="countryFilter">
		<CustomFilter
			@onClose="$emit('close')"
			@onSubmit="onSubmit"
			@onClear="onClear"
			:cardTitle="$tr('filter_item', $tr('countries'))"
		>
			<template v-slot:data>
				<FilterInput v-model="form.name" :label="$tr('country_name')" />
				<FilterInput v-model="form.capital" :label="$tr('capital')" />
				<FilterInput v-model="form.national" :label="$tr('national')" />
				<FilterInput v-model="form.currency_name" :label="$tr('currency')" />
				<FilterInput v-model="form.region" :label="$tr('region')" />
				<FilterInput v-model="form.subregion" :label="$tr('sub_region')" />
			</template>
			<template v-slot:date_range>
				<FilterInput
					v-model="form.created_date"
					@getDate="getDate"
					:label="$tr('created_at')"
					:type="'date-range'"
				/>
				<FilterInput
					:clearInput.sync="clearInput"
					v-model="form.updated_date"
					@getDate="getDate"
					:label="$tr('updated_at')"
					:type="'date-range'"
				/>
			</template>
			<template v-slot:id_filtering>
				<FilterInput
					:clearInput.sync="clearInput"
					@isInclude="(isInclude) => (form.include = isInclude)"
					@getIds="(ids) => (form.ids = ids)"
					:label="$tr('id')"
					:type="'id_filtering'"
				/>
			</template>
		</CustomFilter>
	</div>
</template>

<script>
import FilterInput from "../design/components/FilterInput.vue";
import CustomFilter from "../common/CustomFilter.vue";
export default {
	components: {
		FilterInput,
		CustomFilter,
	},
	data() {
		return {
			form: {
				name: "",
				currency_name: "",
				region: "",
				subregion: "",
				capital: "",
				national: "",
				created_date: null,
				updated_date: null,
				include: 1,
				ids: [],
			},
			sortedData: {},
			clearInput: false,
		};
	},

	methods: {
		getDate(date, selected) {
			if (selected.toLowerCase().includes("created"))
				this.form.created_date = date;
			else if (selected.toLowerCase().includes("updated"))
				this.form.updated_date = date;
		},

		sortData() {
			this.form = JSON.parse(JSON.stringify(this.form));		// Add this line to prevent reference.
			this.sortedData = {};
			if (this.form.name) this.sortedData.name = "like@@" + this.form.name;

			if (this.form.currency_name)
				this.sortedData.currency_name = "like@@" + this.form.currency_name;

			if (this.form.region)
				this.sortedData.region = "like@@" + this.form.region;

			if (this.form.subregion)
				this.sortedData.subregion = "like@@" + this.form.subregion;

			if (this.form.capital)
				this.sortedData.capital = "like@@" + this.form.capital;

			if (this.form.national)
				this.sortedData.national = "like@@" + this.form.national;

			if (this.form.updated_date)
				this.sortedData.updated_at = ["date", "range"].concat(
					this.form.updated_date
				);

			if (this.form.created_date)
				this.sortedData.created_at = ["date", "range"].concat(
					this.form.created_date
				);

			if (this.form.ids.length > 0) {
				this.sortedData.ids = this.form.ids;
				this.sortedData.include = this.form.include;
			}
		},

		onSubmit() {
			if (!this.isAlreadySubmited()) {
				this.$emit("filterRecords", this.sortedData);
				this.$emit("close");
			}
		},
		onClear() {
			this.form = {
				name: "",
				currency_name: "",
				region: "",
				subregion: "",
				capital: "",
				national: "",
				created_date: null,
				updated_date: null,
				include: 1,
				ids: [],
			};
			this.clearInput = true;
			setTimeout(() => {
				this.clearInput = false;
			}, 2000);

			if (!this.isAlreadySubmited())
				this.$emit("filterRecords", this.sortedData);
			this.sortedData = {};
		},

		isAlreadySubmited() {
			const obj1 = this.sortedData;
			this.sortData();
			const obj2 = this.sortedData;
			return JSON.stringify(obj1) === JSON.stringify(obj2);
		},
	},
};
</script>
