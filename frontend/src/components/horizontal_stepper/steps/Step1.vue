<template>
	<div class="w-full h-full d-flex align-center-justify-center mt-5">
		<div class="w-full h-full">
			<!-- category product start -->
			<div class="mb-3">
				<InputCard
					:title="
						path.length == 1
							? 'Find your product'
							: history.current.parent
							? history.current.parent.name
							: ''
					"
					:hasSearch="true"
					:hasPagination="true"
					:showBack="path.length > 1"
					:page="history[searchActive ? 'search' : 'current'].page"
					:pages="history[searchActive ? 'search' : 'current'].pages"
					:path="path"
					@back="back"
					@search="search"
					@disableSearch="disableSearch"
					@updatePage="
						(newpage) => {
							history[searchActive ? 'search' : 'current'].page = newpage;
						}
					"
					@changeTo="changeTo"
				>
					<ItemsContainer
						v-model="form.selectItems.$model"
						:items="
							history[searchActive ? 'search' : 'current'].items.slice(
								history[searchActive ? 'search' : 'current'].page * 21 - 21,
								history[searchActive ? 'search' : 'current'].page * 21
							)
						"
						:loading="loading"
						@category_clicked="category_clicked"
						:no_data="
							history[searchActive ? 'search' : 'current'].no_data
								? true
								: false
						"
						:multi="true"
					></ItemsContainer>
				</InputCard>
			</div>
			<div class="mb-3">
				<ItemsSelectionLg
					v-model="itemsSelectionSelected"
					:items="ItemsSelectionItems"
				/>
			</div>

			<div class="mb-3">
				<CFileUpload v-model="file" title="Upload Image" />
			</div>

			<div class="mb-3">
				<CTextField
					v-model.trim="sellingGoal"
					:items="sellingGoals"
					title="Text Field With add"
					placeholder="Text Field Place Holder"
					prepend-inner-icon="mdi-bullseye-arrow"
					:hasItems="true"
					@add="addItem"
					@removeItem="removeItem"
				/>
			</div>
			<div class="mb-3">
				<CTextArea
					v-model.trim="sellingGoal"
					:items="sellingGoals"
					title="Text Area With add"
					placeholder="Text Field Place Holder"
					prepend-inner-icon="mdi-bullseye-arrow"
					:hasItems="true"
					@add="addItem"
					@removeItem="removeItem"
				/>
			</div>

			<!-- category product end -->
			<div class="mb-3">
				<CTextArea
					title="Text Area"
					placeholder="Text Area Place Holder"
					prepend-inner-icon="mdi-information"
				/>
			</div>
			<div class="mb-3 d-flex flex-column flex-md-row">
				<div class="me-md-2 w-full">
					<CTextField
						title="Text Field"
						placeholder="Text Field Place Holder"
						prepend-inner-icon="mdi-view-grid"
					/>
				</div>

				<div class="ms-md-2 w-full">
					<CSelect
						title="Select"
						placeholder="Select Place Holder"
						prepend-inner-icon="mdi-view-grid"
						:items="selectItems"
					/>
				</div>
			</div>
			<div class="mb-3 d-flex flex-column flex-md-row">
				<div class="me-md-2 w-full">
					<CAutoComplete
						title="Auto complete"
						placeholder="Auto complete Place Holder"
						prepend-inner-icon="mdi-view-grid"
						:items="selectItems"
					/>
				</div>
				<div class="ms-md-2 w-full">
					<CNumber
						title="Number"
						placeholder="Number Place Holder"
						v-model="number"
					/>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import ItemCard from "../../new_form_components/components/ItemCard.vue";
import CTextArea from "../../new_form_components/Inputs/CTextArea.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CSelect from "../../new_form_components/Inputs/CSelect.vue";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import CNumber from "../../new_form_components/Inputs/CNumber.vue";
import CFileUpload from "../../new_form_components/Inputs/CFileUpload.vue";
import ItemsSelectionLg from "../../new_form_components/Inputs/ItemsSelectionLg.vue";

export default {
	props: {
		form: Object,
	},
	data() {
		return {
			number: 0,
			validateRule: GlobalRules.validate,

			selectItems: ["Foo", "Bar", "Fizz", "Buzz"],
			// product selection
			selectedItems: null,
			history: {
				current: {
					items: [],
					pages: 0,
					page: 1,
					parent: null,
				},
				search: {
					items: [],
					pages: 0,
					page: 1,
					parent: null,
				},
			},
			path: ["categories"], //stack
			loading: false,
			searchActive: false,
			// textfield with add
			sellingGoal: "",
			sellingGoals: [],

			// Items Selection
			ItemsSelectionItems: [
				{
					id: 1,
					title: "Individual Selling Product Processing (ISPP)",
					icon: `
						<svg viewBox="0 0 73.582 74.799">
							<g id="Group_465" data-name="Group 465">
								<path
									id="Path_328"
									data-name="Path 328"
									d="M66.023,74.8a9.71,9.71,0,0,1-1.434-1.384c-1.432-2.085-2.8-4.216-4.187-6.329-.158-.24-.325-.473-.538-.783-.575.714-1.111,1.361-1.628,2.025a1.919,1.919,0,0,1-2.2.729,1.961,1.961,0,0,1-1.4-1.858c-.24-2.1-.516-4.2-.735-6.3-.059-.563-.359-.57-.786-.569q-6.466.014-12.932.006c-.195,0-.39,0-.584,0a1.487,1.487,0,0,1-1.619-1.491,1.472,1.472,0,0,1,1.61-1.422q5.809-.008,11.617,0h2.238c-.265-2.178-.525-4.293-.78-6.408-.084-.7-.2-1.4-.224-2.1a1.872,1.872,0,0,1,2.809-1.792c.981.478,1.9,1.077,2.85,1.625.859.5,1.717,1,2.665,1.547V13.172H3.023c-.012.231-.037.486-.037.741q0,20.307,0,40.614c0,1.961.945,2.888,2.925,2.888q10.265,0,20.531,0c1.243,0,2,.959,1.567,1.991a1.42,1.42,0,0,1-1.432.926c-1.047-.008-2.095,0-3.142,0q-8.877,0-17.754,0A5.417,5.417,0,0,1,.007,54.7Q-.007,30.154.008,5.61A5.415,5.415,0,0,1,5.7,0Q31.9,0,58.089,0a5.429,5.429,0,0,1,5.717,5.735q.007,22.718-.013,45.435a1.342,1.342,0,0,0,.767,1.366c2.2,1.241,4.364,2.555,6.556,3.815a2,2,0,0,1-.486,3.795c-.774.2-1.544.418-2.4.651.614.942,1.191,1.832,1.772,2.718,1.014,1.546,2.033,3.089,3.044,4.637.866,1.327.686,2.315-.613,3.168-1.787,1.173-3.592,2.32-5.39,3.478ZM3.034,10.068H60.787c0-1.733.06-3.411-.023-5.082a2.107,2.107,0,0,0-1.656-1.919,4.241,4.241,0,0,0-1.222-.145Q31.924,2.912,5.96,2.917c-.219,0-.439-.009-.657.011A2.333,2.333,0,0,0,3.059,4.877c-.092,1.715-.025,3.439-.025,5.19M55.6,50.682l1.725,14.056c.373-.458.644-.786.91-1.119a1.991,1.991,0,0,1,3.528.163c.713,1.072,1.412,2.154,2.12,3.23.973,1.479,1.948,2.956,2.946,4.47l3.4-2.24c-.178-.282-.314-.507-.458-.726-1.56-2.376-3.132-4.745-4.679-7.13a1.944,1.944,0,0,1,1.156-3.1c.464-.139.927-.281,1.537-.467L55.6,50.682"
									transform="translate(0 0)"
									fill="#2e7be6"
								/>
								<path
									id="Path_329"
									data-name="Path 329"
									d="M79.19,192.537H97.454c.195,0,.39-.008.584,0a1.544,1.544,0,0,1,1.588,1.483,1.517,1.517,0,0,1-1.508,1.479c-.193.018-.389.011-.584.011q-18.41,0-36.82,0a4.619,4.619,0,0,1-.871-.064,1.4,1.4,0,0,1-1.177-1.381,1.359,1.359,0,0,1,1.033-1.418,3.873,3.873,0,0,1,1.007-.107q9.241-.009,18.483,0"
									transform="translate(-47.219 -154.965)"
									fill="#2e7be6"
								/>
								<path
									id="Path_330"
									data-name="Path 330"
									d="M64.882,110.783c-1.557,0-3.113,0-4.67,0a1.5,1.5,0,0,1-1.7-1.641q-.034-4.7,0-9.41A1.489,1.489,0,0,1,60.2,98.087q4.706-.007,9.413,0a1.5,1.5,0,0,1,1.669,1.67q.018,4.669,0,9.338a1.526,1.526,0,0,1-1.732,1.687c-1.557.005-3.113,0-4.67,0m-3.366-2.963h6.747v-6.754H61.516Z"
									transform="translate(-47.079 -78.944)"
									fill="#2e7be6"
								/>
								<path
									id="Path_331"
									data-name="Path 331"
									d="M75.871,239.9H91.063c1.37,0,2.1.517,2.095,1.483s-.747,1.493-2.1,1.493H60.749c-.122,0-.243,0-.365,0-1.09-.026-1.783-.63-1.758-1.532.024-.879.684-1.439,1.761-1.44q7.742-.011,15.484,0"
									transform="translate(-47.186 -193.088)"
									fill="#2e7be6"
								/>
								<path
									id="Path_332"
									data-name="Path 332"
									d="M166.744,148.137c-3.068,0-6.137.02-9.2-.021a1.959,1.959,0,0,1-1.355-.486,1.724,1.724,0,0,1-.283-1.448c.152-.409.73-.682,1.16-.953.159-.1.428-.03.647-.03H175.9c.146,0,.293-.005.438,0a1.53,1.53,0,0,1,1.5,1.488,1.454,1.454,0,0,1-1.524,1.437c-3.19.016-6.38.006-9.57.006Z"
									transform="translate(-125.431 -116.847)"
									fill="#2e7be6"
								/>
								<path
									id="Path_333"
									data-name="Path 333"
									d="M166.392,101.007c-3.117,0-6.234.007-9.351,0A1.434,1.434,0,0,1,155.5,99.26a1.454,1.454,0,0,1,1.6-1.173q4.493-.007,8.986,0,4.931,0,9.862,0a1.456,1.456,0,0,1,1.478.965,1.225,1.225,0,0,1-.36,1.518,2.235,2.235,0,0,1-1.257.423c-3.141.034-6.282.019-9.424.018"
									transform="translate(-125.138 -78.944)"
									fill="#2e7be6"
								/>
								<path
									id="Path_334"
									data-name="Path 334"
									d="M162.3,295.708a1.419,1.419,0,0,1,1.409-1.456,1.456,1.456,0,1,1,0,2.911,1.421,1.421,0,0,1-1.41-1.456"
									transform="translate(-130.629 -236.834)"
									fill="#2e7be6"
								/>
								<path
									id="Path_335"
									data-name="Path 335"
									d="M59.839,28.172a1.472,1.472,0,0,1-1.479,1.483,1.539,1.539,0,0,1-1.448-1.467,1.478,1.478,0,0,1,1.5-1.463,1.429,1.429,0,0,1,1.424,1.446"
									transform="translate(-45.807 -21.511)"
									fill="#2e7be6"
								/>
								<path
									id="Path_336"
									data-name="Path 336"
									d="M87.426,28.168a1.456,1.456,0,1,1-2.912.039,1.456,1.456,0,0,1,2.912-.039"
									transform="translate(-68.023 -21.519)"
									fill="#2e7be6"
								/>
								<path
									id="Path_337"
									data-name="Path 337"
									d="M32.52,28.292a1.468,1.468,0,1,1-2.936-.058,1.414,1.414,0,0,1,1.528-1.4,1.433,1.433,0,0,1,1.408,1.462"
									transform="translate(-23.811 -21.594)"
									fill="#2e7be6"
								/>
							</g>
						</svg>
					`,
				},
				{
					id: 2,
					title: "Individual Product Advertisement (IPA)",
					icon: `
						<svg
							viewBox="0 0 75.557 53.703"
						>
							<g
								id="Group_461"
								data-name="Group 461"
							>
								<path
									id="Path_321"
									data-name="Path 321"
									d="M2.407,18.238h1.96q14.343,0,28.686,0a3.183,3.183,0,0,1,1.312.064c.424.211,1.027.718.992,1.034a2.057,2.057,0,0,1-.971,1.24c-.324.2-.863.065-1.306.065q-14.417,0-28.834,0H2.407v1.5q0,15.748,0,31.5c0,2.268.508,2.775,2.768,2.775q32.531,0,65.062,0c2.351,0,2.8-.461,2.8-2.871q0-13.012,0-26.025c0-.443-.138-.979.061-1.307a2.035,2.035,0,0,1,1.221-.979c.322-.037.816.557,1.055.973.172.3.059.77.059,1.164q0,13.308,0,26.616c0,3.342-1.505,4.821-4.889,4.822q-32.827,0-65.653,0C1.542,58.807,0,57.278,0,53.958Q0,33.035,0,12.112C0,8.853,1.529,7.291,4.758,7.29q25.655-.01,51.31,0c.345,0,.772-.111,1.016.05.437.289,1.061.749,1.057,1.131,0,.406-.584.917-1.033,1.171-.339.192-.866.057-1.309.057H5.229c-2.383,0-2.817.438-2.821,2.843,0,1.817,0,3.634,0,5.7"
									transform="translate(0 -5.106)"
									fill="#2e7be6"
								/>
								<path
									id="Path_322"
									data-name="Path 322"
									d="M133.58,25.848l-1.152,4.326.22.159a16,16,0,0,1,2.069-2.114c.431-.311,1.177-.185,1.781-.256-.005.612.245,1.439-.058,1.8q-2.706,3.224-5.643,6.253c-1.3,1.341-2.667.887-3.105-.926-.8-3.294-1.59-6.588-2.355-9.89-.193-.835-.484-1.327-1.478-1.482a63.54,63.54,0,0,1-6.771-1.43c-.677-.183-1.6-.886-1.677-1.451A2.423,2.423,0,0,1,116.641,19q19-9.474,38.079-18.794a2.007,2.007,0,0,1,1.73-.015,1.878,1.878,0,0,1,.314,1.665c-2.824,8.889-5.712,17.758-8.573,26.636-.441,1.369-1.27,1.7-2.614,1.253-3.946-1.316-7.911-2.573-12-3.894M152.869,6.054l-.26-.226L135.2,23.795l10.807,3.489,6.856-21.23m-4.151-.189L119.851,20.094a25.637,25.637,0,0,0,5.221,1.341,2.184,2.184,0,0,0,1.447-.455c2.172-1.614,4.279-3.315,6.454-4.926.83-.615,1.808-2.01,2.823-.622.98,1.339-.593,1.928-1.41,2.583-2.073,1.663-4.206,3.25-6.284,4.907a1.376,1.376,0,0,0-.545,1.021c.532,2.451,1.15,4.883,1.743,7.321a14.883,14.883,0,0,0,1.347-4.07A12.19,12.19,0,0,1,134.59,20.2c4.746-4.575,9.272-9.38,14.127-14.337"
									transform="translate(-81.275 0)"
									fill="#2e7be6"
								/>
								<path
									id="Path_323"
									data-name="Path 323"
									d="M68.124,116.478a11.961,11.961,0,0,1-9.781-5.679c-1.108-1.786-1.832-3.806-2.888-5.627a61.627,61.627,0,0,0-3.484-5.239c-.487-.671-.87-1.238-.221-1.931a1.167,1.167,0,0,1,1.918.161,46.9,46.9,0,0,1,3.492,4.891c.992,1.686,1.618,3.583,2.547,5.31,3.251,6.043,9.689,7.4,15.042,3.1,1.551-1.247,2.737-2.941,4.192-4.323.387-.367,1.152-.336,1.743-.487.086.611.471,1.434.211,1.8-2.89,4.1-6.452,7.29-11.7,7.962-.292.037-.59.033-1.077.058"
									transform="translate(-36.203 -68.714)"
									fill="#2e7be6"
								/>
								<path
									id="Path_324"
									data-name="Path 324"
									d="M21.967,85.665c2.07.53,3.8.882,5.448,1.454a1.842,1.842,0,0,1,.958,1.48c-.1.881-.906.989-1.726.785-1.515-.376-3.029-.77-4.563-1.046-.844-.151-1.538-.477-1.34-1.346.119-.522.817-.912,1.223-1.328"
									transform="translate(-14.586 -60.331)"
									fill="#2e7be6"
								/>
								<path
									id="Path_325"
									data-name="Path 325"
									d="M43.958,28.106c-.407-.562-1.167-1.154-1.124-1.679A2.154,2.154,0,0,1,44.172,24.9a1.285,1.285,0,0,1,1.554,1.464c-.07.611-.735,1.154-1.134,1.727l-.635.01"
									transform="translate(-30.166 -17.514)"
									fill="#2e7be6"
								/>
								<path
									id="Path_326"
									data-name="Path 326"
									d="M25.409,26.752c-.546.414-1.046,1.076-1.647,1.186a1.24,1.24,0,0,1-1.533-1.447,2.195,2.195,0,0,1,1.437-1.443c.5-.081,1.139.654,1.716,1.029l.027.675"
									transform="translate(-15.637 -17.636)"
									fill="#2e7be6"
								/>
								<path
									id="Path_327"
									data-name="Path 327"
									d="M66.22,26.727c-.559.394-1.151,1.137-1.669,1.088a2.161,2.161,0,0,1-1.48-1.342A1.24,1.24,0,0,1,64.5,24.93c.6.065,1.138.737,1.7,1.137q.006.33.012.66"
									transform="translate(-44.385 -17.551)"
									fill="#2e7be6"
								/>
							</g>
						</svg>
					`,
				},
			],
			itemsSelectionSelected: "",

			file: [],
		};
	},
	methods: {
		async loaded() {
			this.fetchRootItems();
		},
		async validate() {
			return true;
		},

		// product selection
		async category_clicked(item) {
			this.selectedItems = [];
			await this.fetchChildItems(item);
			if (this.path[this.path.length - 1].id !== item.id) {
				this.path.push(item);
			}
		},
		async fetchRootItems() {
			this.loading = true;
			try {
				let res = await this.$axios.get("/single-sales/categories", {
					params: {
						fetch_for_form: true,
					},
				});
				let obj = {};
				if (res.data.length > 0) {
					obj = {
						items: res.data.sort(this.sortByName),
						parent: null,
						pages: Math.ceil(res.data.length / 21),
						page: 1,
					};
				} else {
					obj = {
						no_data: true,
						items: [],
						parent: null,
						pages: 0,
						page: 1,
					};
				}
				this.history.current = obj;
				this.history.parent = obj;
			} catch (error) {}
			this.loading = false;
		},
		async fetchChildItems(category) {
			this.loading = true;
			if (this.history[category.id]) {
				this.history.current = this.history[category.id];
			} else {
				let res1 = await this.$axios.get("/single-sales/categories", {
					params: {
						fetch_for_form: true,
						category_id: category.id,
					},
				});
				let res2 = await this.$axios.get("/single-sales/products-ssp", {
					params: {
						fetch_for_form: true,
						category_id: category.id,
					},
				});
				let data = [...res1.data, ...res2.data];
				var obj = {};
				if (data.length > 0) {
					obj = {
						items: data.sort(this.sortByName),
						parent: category,
						pages: Math.ceil(data.length / 21),
						page: 1,
					};
				} else {
					obj = {
						no_data: true,
						items: [],
						parent: category,
						pages: 0,
						page: 1,
					};
				}
				this.history[category.id] = obj;
				this.history.current = obj;
			}
			this.loading = false;
		},
		back() {
			this.loading = true;
			if (this.path.length > 1) {
				this.path.pop();
				if (this.path.length == 1) {
					if (this.history.parent) {
						this.history.current = this.history.parent;
					} else {
						this.fetchRootItems();
					}
				} else {
					if (this.history[this.path[this.path.length - 1].id]) {
						this.history.current =
							this.history[this.path[this.path.length - 1].id];
					} else {
						this.fetchChildItems(this.path[this.path.length - 1].id);
					}
				}
			}
			this.loading = false;
		},
		changeTo(item) {
			if (item == "parent") {
				this.path = this.path.slice(0, 1);
				if (this.history.parent) {
					this.history.current = this.history.parent;
				} else {
					this.fetchRootItems();
				}
			} else {
				let index = this.path.findIndex((item2) => item2?.id == item.id);
				this.path = this.path.slice(0, index + 1);
				if (this.history[this.path[this.path.length - 1].id]) {
					this.history.current =
						this.history[this.path[this.path.length - 1].id];
				} else {
					this.fetchChildItems(this.path[this.path.length - 1].id);
				}
			}
		},
		async search(search) {
			// search_for_from
			this.loading = true;
			var data = [];
			var obj = {
				no_data: true,
				items: [],
				parent: null,
				pages: 0,
				page: 1,
			};
			try {
				if (search) {
					this.searchActive = true;
					let res = await this.$axios.get("/single-sales/categories", {
						params: {
							search_for_from: true,
							search,
							category_id: this.history.current.parent
								? this.history.current.parent.id
								: null,
						},
					});
					data = [...res.data.products, ...res.data.categories];
				}
				if (data.length > 0) {
					obj = {
						items: data.sort(this.sortByName),
						parent: null,
						pages: Math.ceil(data.length / 21),
						page: 1,
					};
				}
				this.history.search = obj;
			} catch (err) {}
			this.loading = false;
		},
		sortByName(itemA, itemB) {
			const nameA = itemA.name.toLowerCase();
			const nameB = itemB.name.toLowerCase();
			return nameA.localeCompare(nameB);
		},
		disableSearch() {
			this.searchActive = false;
		},
		// end

		// textfield wit add
		addItem() {
			if (this.sellingGoal?.length > 0) {
				this.sellingGoals.push(this.sellingGoal);
				this.sellingGoal = "";
			}
		},
		removeItem(index) {
			this.sellingGoals = this.sellingGoals.filter((item, i) => i !== index);
		},
	},
	components: {
		InputCard,
		ItemsContainer,
		ItemCard,
		CTextArea,
		CTextField,
		CSelect,
		CAutoComplete,
		CNumber,
		CFileUpload,
		ItemsSelectionLg,
	},
};
</script>
