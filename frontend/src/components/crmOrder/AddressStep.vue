<template>
	<div class="connection__container">
		<CTitle text="connection_information" />
		<div class="mb-3">
			<InputCard
				:title="this.$tr('city')"
				:hasSearch="true"
				:hasPagination="false"
				@search="(v) => (searchCity = v)"
			>
				<ItemsContainer
					v-model="form.city.$model"
					:items="filterCity"
					item-value="name"
					:loading="isFetchingCity"
					:no_data="filterCity.length < 1 && !isFetchingCity"
					height="150px"
					:itemValue="'name'"
					:nameLogo="'name'"
				></ItemsContainer>
			</InputCard>
		</div>
		<div class="mb-3">
			<CComboBox
				@blur="form.area.$touch()"
				v-model="form.area.$model"
				:placeholder="$tr('choose_item', $tr('area'))"
				:rules="validateRule(form.area, $root, $tr('area'))"
				prepend-inner-icon="fa fa-atom"
				:canAddNewItem="true"
				:items="areas"
				item-value="name"
				item-text="name"
				:title="$tr('label_required', $tr('area'))"
				@onChange="onNewEreaAded"
			/>
		</div>
		<div class="mb-3">
			<CTextArea
				px="px-0"
				py="py-0"
				:title="$tr('label_required', $tr('address'))"
				:placeholder="$tr('address')"
				@blur="form.address.$touch()"
				v-model="form.address.$model"
				:rules="validateRule(form.address, $root, $tr('address'))"
				:invalid="form.address.$invalid"
				prepend-inner-icon="mdi-clipboard-text-outline"
			/>
		</div>
		<div class="mb-3">
			<CTextField
				:title="this.$tr('customer_name')"
				py="py-0"
				v-model="form.name.$model"
				@blur="form.name.$touch()"
				:rules="validateRule(form.name, $root, $tr('name'))"
				:invalid="form.name.$invalid"
				:placeholder="$tr('name')"
				prepend-inner-icon="mdi-account"
			/>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import ItemsContainer from "../new_form_components/cat_product_selection/ItemsContainer.vue";
import CSelect from "../new_form_components/Inputs/CSelect.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";
import CTextArea from "../new_form_components/Inputs/CTextArea.vue";
import CComboBox from "../new_form_components/Inputs/CComboBox.vue";
import allcities from "../../configs/pages/crmOrder/cities";

export default {
	props: {
		form: Object,
		fetchCountry: Boolean,
	},
	mounted() {
		this.$root.$on("onAddressSelect", this.onAddressSelected);
	},

	watch: {
		"form.projectname.$model": function (project) {
			this.cities = allcities(this).Emaratscities;
		},
		"form.city.$model": function (item) {
			this.cities = allcities(this).Emaratscities;
			const city = this.cities.filter(
				(city) => city.name == this.form.city.$model
			);
			this.areas = city.map((row) => row.subcities);
			this.areas = this.areas[0];
			if (this.form.selectedAddress.$model != null) {
				this.form.area.$model = this.areas.find(
					(row) => row.name == this.form.selectedAddress.$model.city
				);
				this.form.name.$model = this.form.selectedAddress.$model.name;
			}
		},
	},

	data() {
		return {
			searchCity: "",
			isFetchingCity: false,
			salesType: [
				{ id: "Single Sale", name: "single_sales" },
				{ id: "WhatsApp", name: "WhatsApp" },
				{ id: "Shopping Cart", name: "shopping_cart" },
			],
			validateRule: GlobalRules.validate,

			cities: [],
			areas: [],
			area: null,
		};
	},

	computed: {
		filterCity: function () {
			if (this.searchCity) {
				const filter = (item) =>
					item?.name?.toLowerCase()?.includes(this.searchCity?.toLowerCase());
				return this.cities.filter(filter);
			}
			return this.cities;
		},
	},

	methods: {
		onAddressSelected(row) {
			this.form.selectedAddress.$model = row[0];
			this.form.city.$model = row[0].emirate_id;
			this.form.address.$model = row[0].address2;
		},
		async loaded() {},

		validate() {
			this.form.city.$touch();
			this.form.area.$touch();
			this.form.name.$touch();
			this.form.address.$touch();

			let isValid =
				!this.form.city.$invalid &&
				!this.form.area.$invalid &&
				!this.form.name.$invalid &&
				!this.form.address.$invalid;
			return isValid;
		},

		async fetchcities() {
			try {
				this.cities = [];
				this.isFetchingCity = true;
				const url = "advertisement/connection/generate_link/country/all";
				const { data } = await this.$axios.get(url);
				this.cities = data.items;
			} catch (error) {}
			this.isFetchingCity = false;
		},

		async fetchItems({ type, id }) {
			try {
				this["isFetching" + type] = true;
				const url = `advertisement/connection/generate_link/${type}/${id}`;
				const { data } = await this.$axios.get(url);
				this[data.type] = data.items;
			} catch (error) {}
			this["isFetching" + type] = false;
		},

		onNewEreaAded(area) {
			if (typeof area != "object") {
				this.form.$model.area = {
					name: area,
					id: "new",
				};
			}
		},
	},
	components: {
		CTitle,
		InputCard,
		ItemsContainer,
		CSelect,
		CTextArea,
		CComboBox,
		CTextField,
	},
};
</script>

<style scoped lang="scss"></style>
