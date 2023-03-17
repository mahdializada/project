<template>
	<div class="connection__container">
		<CTitle text="new_order" />

		<div class="mb-3">
			<InputCard
				:title="this.$tr('project')"
				:hasSearch="true"
				:hasPagination="true"
				@search="(v) => (searchProject = v)"
			>
				<NoCheckboxItemContainer
					v-model="form.project_id.$model"
					:items="filterProject"
					:loading="isFetchingprojects"
					:no_data="filterProject.length < 1 && !isFetchingprojects"
					height="220px"
					:customIcon="'project'"
				></NoCheckboxItemContainer>
			</InputCard>
		</div>
		<div class="mb-3">
			<InputCard :title="this.$tr('source')">
				<CheckBoxHorizontalItem
					v-model="form.source.$model"
					:items="sources"
					:loading="isFetchingSources"
					:no_data="sources.length < 1 && !isFetchingSources"
					height="150px"
					:hasIcon="true"
				></CheckBoxHorizontalItem>
			</InputCard>
		</div>
		<div class="mb-3">
			<InputCard :title="this.$tr('solution')" v-show="complaignSource">
				<CheckBoxHorizontalItem
					v-model="form.solution.$model"
					:items="solutions"
					:loading="isFetchingSolution"
					:no_data="solutions.length < 1 && !isFetchingSolution"
					height="90px"
				></CheckBoxHorizontalItem>
			</InputCard>

			<CTextField
				v-show="whatsappSource"
				:title="this.$tr('landing_link')"
				py="py-0"
				v-model="form.landing_link.$model"
				@blur="form.landing_link.$touch()"
				:rules="validateRule(form.landing_link, $root, $tr('landing_link'))"
				:invalid="form.landing_link.$invalid"
				:placeholder="$tr('landing_link')"
				prepend-inner-icon="mdi-currency-usd"
				class="custom-number"
				:hasCustomBtn="true"
				btnIcon="mdi-content-paste"
				@add="copyLinkFromClipboard"
			/>

			<CTextField
				v-show="celebritySource"
				:title="this.$tr('celebrity_code')"
				py="py-0"
				v-model="form.celebrity_code.$model"
				@blur="form.celebrity_code.$touch()"
				:rules="validateRule(form.celebrity_code, $root, $tr('celebrity_code'))"
				:invalid="form.celebrity_code.$invalid"
				:placeholder="$tr('celebrity_code')"
				prepend-inner-icon="mdi-currency-usd"
				class="custom-number"
			/>
		</div>

		<div class="mb-3">
			<CPhoneNumber
				@blur="form.phone_number.$touch()"
				v-model="form.phone_number.$model"
				:rules="validateRule(form.phone_number, $root, $tr('phone'))"
				:invalid="form.phone_number.$invalid"
				:title="$tr('label_required', $tr('phone'))"
				:loading="isCheckingPhone"
				:placeholder="$tr('number')"
				prepend-inner-icon="fa fa-globe"
				type="number"
			/>
		</div>

		<div class="mb-3">
			<InputCard
				:title="this.$tr('resource_id') + '#10'"
				v-if="addresses.length > 0 || isFetchingaddresses"
			>
				<AddressTable
					:headers="headers"
					:items="addresses"
					:apiCalling="isFetchingaddresses"
				/>
			</InputCard>
		</div>
	</div>
</template>
<script>
import AddressTable from "./AddressTable.vue";
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import CheckBoxHorizontalItem from "../new_form_components/cat_product_selection/CheckBoxHorizontalItem.vue";
import NoCheckboxItemContainer from "../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import CPhoneNumber from "../new_form_components/Inputs/CPhoneNumber";
import CTextField from "../new_form_components/Inputs/CTextField.vue";

export default {
	props: {
		form: Object,
		fetchCountry: Boolean,
	},

	watch: {
		"form.project_id.$model": function (id) {
			this.form.projectname.$model = this.projects.filter(
				(project) => project.id == id
			);
		},
		"form.phone_number.$model": function (phone) {
			if (!this.form.phone_number.$invalid) {
				this.fetchItems({
					type: "addresses",
					id: phone,
				});
			}
		},
		"form.source.$model": function (sourse) {
			this.whatsappSource = false;
			this.celebritySource = false;
			this.complaignSource = false;
			if (sourse == 3) this.complaignSource = true;
			else if (sourse == 7) this.whatsappSource = true;
			else if (sourse == 6) this.celebritySource = true;
		},
	},

	data() {
		return {
			value: null,
			whatsappSource: false,
			celebritySource: false,
			complaignSource: false,
			searchProject: "",
			solutions: [
				{ id: 1, name: "exchange", icon: "mdi-star" },
				{ id: 2, name: "return", icon: "mdi-share" },
				{
					id: 3,
					name: "send_an_extra_kit",
					icon: "mdi-whatsapp",
				},
				{ id: 4, name: "send_money", icon: "mdi-share" },
			],
			sources: [
				{
					id: 1,
					name: "customer_services",
					icon: "mdi-headset",
				},
				{ id: 2, name: "social_media", icon: "mdi-share-variant" },
				{ id: 3, name: "complain", icon: "mdi-message-alert" },
				{ id: 4, name: "datasheet", icon: "mdi-table" },
				{ id: 5, name: "main_bumber", icon: "mdi-phone" },
				{ id: 6, name: "celebrity", icon: "mdi-star-shooting" },
				{ id: 7, name: "whatsapp_sales", icon: "mdi-whatsapp" },
			],
			validateRule: GlobalRules.validate,

			isFetchingprojects: false,
			ColumnAxiosSource: null,
			isCheckingPhone: false,
			isFetchingSources: false,
			isFetchingSolution: false,
			headers: [
				{ value: "name", text: "customer_name" },
				{ value: "emirate_id", text: "city" },
				{ value: "city", text: "area" },
				{ value: "address2", text: "address" },
			],
			projects: [],
			isFetchingaddresses: false,
			addresses: [],
			addresses2: [
				{
					id: "04330ed6-cfe8-4252-a9d1-ab74d46c0586",
					name: "Mrs. Gabrielle Ortiz MD",
					code: "25950997",
				},

				{
					id: "04330ed6-cfe8-4252-a9d1-ab74d46c0583",
					name: "Mrs. Gabrielle Ortiz MD",
					code: "25950997",
				},
				{
					id: "0444ec05-dd39-442d-8c68-c7bffe8db69d",
					name: "Ms. Burdette Kub II",
					code: "84045503",
				},
				{
					id: "0647f8f9-9a13-4300-aabb-bc1f753cc413",
					name: "Katelynn Balistreri",
					code: "50888536",
				},
				{
					id: "08f32f63-e151-4f91-b5fa-9e29bd0ee3fb",
					name: "Miss Rahsaan Corkery",
					code: "99885710",
				},
				{
					id: "0c2be37f-fba7-4a9e-ac4a-8806be1b2fcc",
					name: "Mattie Brekke",
					code: "96885410",
				},
				{
					id: "2ef9185f-045f-4654-88ea-f983921e6af7",
					name: "Dax Bailey",
					code: "84820780",
				},
				{
					id: "2f342395-90ae-409e-beff-4eb50affcf40",
					name: "Dr. Elliot Collier",
					code: "8436455",
				},
				{
					id: "2f5aae2b-9df6-4993-ad9f-b50ae83ecead",
					name: "Madelynn Christiansen V",
					code: "27589672",
				},
				{
					id: "30bf2ce3-e079-40c7-bb2b-603f0cf96962",
					name: "Travis Schiller",
					code: "22282094",
				},
				{
					id: "3b5cbdd4-6f57-4647-a9f2-d642ff0f0921",
					name: "Flavie Cole",
					code: "8950289",
				},
			],
		};
	},

	computed: {
		filterProject() {
			if (this.searchProject) {
				const filter = (item) =>
					item?.name
						?.toLowerCase()
						?.includes(this.searchProject?.toLowerCase());
				return this.projects.filter(filter);
			}

			return this.projects;
		},
	},

	methods: {
		async loaded() {
			if (this.projects.length < 1)
				this.fetchItems({
					type: "projects",
					id: 1,
				});
		},

		validate() {
			this.form.phone_number.$touch();
			this.form.celebrity_code.$touch();
			this.form.landing_link.$touch();
			let isValid =
				!this.form.project_id.$invalid &&
				!this.form.source.$invalid &&
				!this.form.solution.$invalid &&
				!this.form.celebrity_code.$invalid &&
				!this.form.landing_link.$invalid &&
				!this.form.phone_number.$invalid;

			return isValid;
		},

		async fetchItems({ type, id }) {
			try {
				if (this.ColumnAxiosSource)
					this.ColumnAxiosSource.cancel("Request-Cancelled");
				this.ColumnAxiosSource = this.$axios.CancelToken.source();
				this["isFetching" + type] = true;
				const url = `orders/fetch-items/${type}/${id}`;
				const { data } = await this.$axios.get(url, {
					params: {},
					cancelToken: this.ColumnAxiosSource.token,
				});
				this[data.type] = data.items;
			} catch (error) {}
			this["isFetching" + type] = false;
		},

		onProjectSelected(item) {
			if (this.value == item.id) {
				this.$emit("input", "");
			} else {
				this.$emit("input", item.id);
			}
		},
		async copyLinkFromClipboard() {
			try {
				const clipboard = navigator.clipboard;
				const link = await clipboard.readText();
				if (link && this.isURL(link)) {
					this.form.landing_link.$model = link;
				}
			} catch (error) {}
		},
	},
	components: {
		CTitle,
		InputCard,
		NoCheckboxItemContainer,
		CheckBoxHorizontalItem,
		CPhoneNumber,
		AddressTable,
		CTextField,
	},
};
</script>

<style scoped lang="scss">
.sales__type__container {
	display: flex;
	justify-content: space-between;
}
</style>
