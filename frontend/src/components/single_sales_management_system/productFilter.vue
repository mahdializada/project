<template>
	<v-dialog v-model="model" persistent max-width="1300" width="1300">
		<CustomFilter
			@onClose="changeModel"
			@onSubmit="onSubmit"
			@onClear="onClear"
			:cardTitle="$tr('filter_item', $tr('products'))"
		>
			<template v-slot:data>
				<FilterInput
					v-model="form.category_id"
					:label="$tr('category')"
					:type="'autocomplete'"
					:items="category"
					:hasAvatar="true"
				/>
				<FilterInput
					v-model="form.brand_id"
					:label="$tr('brand')"
					:type="'autocomplete'"
					:items="brand"
					:hasAvatar="true"
				/>
				<FilterInput v-model="form.name" :label="$tr('name')" />
				<FilterInput
					v-model="form.number_of_sales"
					:label="$tr('number_of_sales')"
				/>
				<FilterInput v-model="form.unit" :label="$tr('unit')" />
				<FilterInput v-model="form.pcode" :label="$tr('pcode')" />
				<FilterInput v-model="form.parent_sku" :label="$tr('parent_sku')" />
				<FilterInput v-model="form.description" :label="$tr('description ')" />
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
	</v-dialog>
</template>

<script>
import FilterInput from "~/components/design/components/FilterInput.vue";
import CustomFilter from "~/components/common/CustomFilter.vue";
import HandleException from "../../helpers/handle-exception";

export default {
	components: {
		FilterInput,
		CustomFilter,
	},
	data() {
		return {
			model: false,
			form: {
				ids: [],
				unit: "",
				name: "",
				pcode: "",
				include: 1,
				parent_sku: "",
				description: "",
				created_date: null,
				updated_date: null,
				number_of_sales: "",
				brand_id: "",
				category_id: "",
			},
			category: [],
			brand: [],
			sortedData: {},
			clearInput: false,
		};
	},

	async created() {
	},
	
	methods: {
		changeModel() {
			this.model = !this.model;
			 this.getRecord();
			
		},

		async getRecord() {
			try {
				const response = await this.$axios.get(
					"single-sales/categories-ssp?key=all"
				);
				const result = await this.$axios.get("single-sales/brand-ssp?key=all");
				if (response.status === 200) {
					this.category = response.data?.data;
				}
				if (result.status === 200) {
					this.brand = result.data?.data;
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
		// fetch all users
		getDate(date, selected) {
			if (selected.toLowerCase().includes("created"))
				this.form.created_date = date;
			else if (selected.toLowerCase().includes("updated"))
				this.form.updated_date = date;
		},

		sortData() {
			this.form = JSON.parse(JSON.stringify(this.form)); // Add this line to prevent reference.
			this.sortedData = {};
			if (this.form.category_id)
				this.sortedData.category_id = [
					"whereHas",
					"category",
					this.form.category_id,
				];

			if (this.form.brand_id)
				this.sortedData.brand_id = ["whereHas", "brand", this.form.brand_id];

			if (this.form.name) this.sortedData.name = "like@@" + this.form.name;

			if (this.form.number_of_sales)
				this.sortedData.number_of_sales = "like@@" + this.form.number_of_sales;

			if (this.form.unit) this.sortedData.unit = "like@@" + this.form.unit;

			if (this.form.pcode) this.sortedData.pcode = "like@@" + this.form.pcode;

			if (this.form.parent_sku)
				this.sortedData.parent_sku = "like@@" + this.form.parent_sku;

			if (this.form.description)
				this.sortedData.description = "like@@" + this.form.description;

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
				
				this.changeModel();
			}
		},
		onClear() {
			this.form = {
				description: "",
				name: "",
				created_date: null,
				updated_date: null,
				include: 1,
				ids: [],
			};
			this.clearInput = true;
			setTimeout(() => {
				this.clearInput = false;
			}, 2000);

			if (!this.isAlreadySubmited()) {
				this.$emit("filterRecords", this.sortedData);
				this.changeModel();
			}
			this.sortedData = {};
		},

		isAlreadySubmited() {
			const obj1 = this.sortedData;
			this.sortData();
			const obj2 = this.sortedData;
			console.log(this.sortedData);
			return JSON.stringify(obj1) === JSON.stringify(obj2);
		},
	},
};
</script>
