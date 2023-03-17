<template>
	<master-profile
		:tabitem="items"
		titleProfile="label"
		@click="$emit('update:dialog')"
		:profileData="profileData"
		name_selector="label"
		mt="mt-0"
	>
		<template slot="btn">
			<div class="d-flex justify-end align-center my-1">
				<v-btn
					@click="onSave"
					class="primary me-1"
					elevation="0"
					v-if="!editable"
					>{{ $tr('save') }}</v-btn
				>
				<v-avatar
					size="40"
					class="primary"
					@click.prevent="editableFun"
				>
					<v-icon color="white" v-if="editable"> mdi-pencil </v-icon>
					<CloseBtn
						style="widht: 30px !important; height: 30px !important"
						class="white--text"
						v-else
					/>
				</v-avatar>
			</div>
		</template>

		<template slot="slot_general">
			<span v-if="editable">
				<ProfileCustomeRow
					headerRow="title"
					:contentRow="isUpdated ? label.title : profileData.title"
					:contentRowTooltip="[]"
					headerRow2="label"
					:contentRow2="isUpdated ? label.label : profileData.label"
					:contentRow2Tooltip="[]"
				></ProfileCustomeRow>
				<ProfileCustomeRow
					bgColor
					headerRow="status"
					:contentRow="profileData.status"
					:contentRowTooltip="[]"
					headerRow2="color"
					:contentRow2="isUpdated ? label.color : profileData.color"
					:contentRow2Tooltip="[]"
				></ProfileCustomeRow>
				<ProfileCustomeRow
					headerRow="label_category"
					:contentRow="
						isUpdated
							? updated_category.title
							: profileData.label_category.title
					"
					:contentRowTooltip="[]"
					headerRow2="code_id"
					:contentRow2="profileData.code"
					:contentRow2Tooltip="[]"
				>
				</ProfileCustomeRow>

				<ProfileCustomeRow
					singleRow
					headerRow="sub_systems"
					:listItem="
						isUpdated
							? label.selectedSubSystems
							: profileData.sub_systems
					"
					:contentRowTooltip="[]"
					bgColor
				></ProfileCustomeRow>
			</span>

			<span v-else>
				<v-row>
					<v-col
						cols="12"
						md="6"
						class="py-1 ps-6 d-flex align-center"
					>
						<div style="width: 30%" class="font-weight-black">
							{{ $tr('title') }}
						</div>
						<div style="width: 70%">
							<CustomInput
								type="textarea"
								v-model.trim="$v.label.title.$model"
								dense
								:rules="
									validate(
										$v.label.title,
										$root,
										$tr('title')
									)
								"
								rows="1"
							/>
						</div>
					</v-col>
					<v-divider vertical></v-divider>
					<v-col cols="12" md="6" class="py-1 d-flex align-center">
						<div style="width: 30%" class="font-weight-black">
							{{ $tr('label') }}
						</div>
						<div style="width: 70%">
							<CustomInput
								:rules="
									validate(
										$v.label.label,
										$root,
										$tr('label')
									)
								"
								dense
								type="textfield"
								v-model.trim="$v.label.label.$model"
							/>
						</div>
					</v-col>
				</v-row>
				<v-row style="background-color: var(--v-surface-darken1)">
					<v-col
						cols="12"
						md="6"
						class="py-1 ps-6 d-flex align-center"
					>
						<div style="width: 50%" class="font-weight-black">
							{{ $tr('status') }}
						</div>
						<div style="width: 50%">
							{{ profileData.status }}
						</div>
					</v-col>
					<v-divider vertical></v-divider>

					<v-col cols="12" md="6" class="py-1 d-flex align-center">
						<div style="width: 30%" class="font-weight-black">
							{{ $tr('color') }}
						</div>
						<div style="width: 70%">
							<v-text-field
								dense
								style="
									border: 2px solid var(--input-border-color);
								"
								v-model.trim="$v.label.color.$model"
								hide-details
								class="ma-0 pa-0"
								solo
							>
								<template v-slot:append>
									<v-menu
										top
										nudge-bottom="105"
										nudge-left="16"
										:close-on-content-click="false"
									>
										<template v-slot:activator="{ on }">
											<div
												v-on="on"
												v-for="(color, index) in colors"
												:key="index"
												@click="
													() => {
														indexKey = index;
														label.color = color;
													}
												"
												:style="`cursor: pointer;
                            height: 26px;
                            width: 26px;
                            margin: 0 0.3rem;
                            borderRadius: 4px;
                            transition: border-radius 200ms ease-in-out; background-color: ${colors[index]};`"
											>
												<v-icon
													v-if="
														colors[index] ===
														label.color
													"
													style="padding: 0 0.1rem"
													color="white"
												>
													mdi-circle-medium</v-icon
												>
											</div>
										</template>
										<v-card elevation="0">
											<v-card-text class="pa-0">
												<v-color-picker
													@input="checkColor"
													v-model.trim="
														$v.label.color.$model
													"
													flat
												/>
											</v-card-text>
										</v-card>
									</v-menu>
								</template>
							</v-text-field>
						</div>
					</v-col>
				</v-row>
				<v-row>
					<v-col
						cols="12"
						md="6"
						class="py-1 ps-6 d-flex align-center"
					>
						<div style="width: 30%" class="font-weight-black">
							{{ $tr('label_category') }}
						</div>
						<div style="width: 70%">
							<CustomInput
								v-if="categoryEdit"
								:items="label.categories"
								v-model.trim="$v.label.label_category.$model"
								type="autocomplete"
								item-text="title"
								item-value="id"
								append-outer-icon="mdi-pencil"
								@click:append-outer="categoryEditFun"
							/>
							<v-text-field
								v-if="!categoryEdit"
								dense
								outlined
								v-model.trim="$v.selected_category.title.$model"
								:rules="
									validate(
										$v.selected_category.title,
										$root,
										$tr('label_category')
									)
								"
							>
								<template v-slot:append>
									<v-btn
										class="primary me-1"
										elevation="0"
										small
										@click="saveCategory"
										>{{ $tr('save') }}</v-btn
									>
									<v-avatar
										size="30"
										class="primary"
										@click="categoryEdit = true"
									>
										<CloseBtn
											style="
												widht: 20px !important;
												height: 20px !important;
											"
											class="white--text"
										/>
									</v-avatar>
								</template>
							</v-text-field>
						</div>
					</v-col>
					<v-divider vertical></v-divider>
					<v-col cols="12" md="6" class="py-1 d-flex align-center">
						<div style="width: 50%" class="font-weight-black">
							{{ $tr('code_id') }}
						</div>
						<div style="width: 50%">
							{{ profileData.code }}
						</div>
					</v-col>
				</v-row>
				<v-row style="background-color: var(--v-surface-darken1)">
					<v-col cols="12" class="py-1 ps-6 d-flex">
						<div style="width: 20%" class="font-weight-black">
							{{ $tr('sub_system') }}
						</div>
						<div style="width: 80%">
							<div class="d-flex flex-column flex-md-row">
								<div class="w-full">
									<CustomInput
										:items="label.subSystems"
										v-model="label.subSystem"
										:error-messages="subSystemErrors"
										@blur="touchSubSystem"
										type="autocomplete"
										item-text="name"
										item-value="id"
										class="me-md-4 mb-md-2 mb-0"
									/>
								</div>
								<div class="w-100">
									<FormBtn
										@click="addSubSystem"
										title="add_plus"
										class="mt-md-4 mb-2"
									/>
								</div>
							</div>
							<div class="selected d-flex flex-wrap">
								<template
									v-for="(
										sub, index
									) in label.selectedSubSystems"
								>
									<SelectedItem
										@close="removeSubSystem(index)"
										:key="index"
										:title="$tr(sub.name)"
									/>
								</template>
							</div>
						</div>
					</v-col>
				</v-row>
			</span>
		</template>
		<template slot="slot_status_transformation">
			<StatusTransformationTab
				:tableItems="profileData.status_transformations"
			/>
		</template>
	</master-profile>
</template>
<script>
	import { mapActions } from 'vuex';

	import { minLength, required } from 'vuelidate/lib/validators';
	import SelectedItem from '../design/components/SelectedItem';
	import HandleException from '../../helpers/handle-exception';
	import moment from 'moment-timezone';
	import CloseBtn from '../design/Dialog/CloseBtn.vue';
	import MasterProfile from '../masters/MasterProfile.vue';
	import ProfileCustomeRow from '../masters/ProfileCustomeRow.vue';
	import CustomInput from '../design/components/CustomInput.vue';
	import FormBtn from '../design/components/FormBtn.vue';
	import StatusTransformationTab from '../../components/common/StatusTransformationTab.vue';
	import GlobalRules from '../../helpers/vuelidate';
	export default {
		components: {
			StatusTransformationTab,
			CloseBtn,
			MasterProfile,
			ProfileCustomeRow,
			CustomInput,
			FormBtn,
			SelectedItem,
		},
		props: {
			dialog: {
				type: Boolean,
				required: true,
			},
			profileData: {
				type: Object,
				required: true,
			},
			labelCategory: {
				type: Array,
				required: true,
			},
			subSystems: {
				type: Array,
				required: true,
			},
			systemName: {
				type: String,
				required: true,
			},
		},

		validations: {
			label: {
				title: { required, minLength: minLength(5) },
				label: { required, minLength: minLength(5) },
				label_category: { required },
				status: {},
				color: { required },
			},
			selected_category: { id: { required }, title: { required } },
		},

		methods: {
			...mapActions({
				updateItem: 'labels/updateItem',
			}),

			checkColor() {
				this.colors[this.indexKey] = this.label.color;
			},

			editableFun() {
				
				let data = JSON.parse(JSON.stringify(this.isUpdated ? this.updatedLable : this.profileData));
				this.editable = !this.editable;
				
				this.label.code = data?.code;
				this.label.id = data?.id;
				this.label.title = data?.title;
				this.label.status = data?.status === 'archive' ? true : false;
				this.label.color = data?.color;
				this.updated_category = JSON.parse(
					JSON.stringify(data?.label_category)
				);
				this.label.label = data?.label;
				this.label.label_category = data?.label_category;
				this.label.selectedSubSystems = data?.sub_systems.map((row) => {
					const item = this.label.subSystems.find(
						(sub) => sub.id === row.id
					);
					return item;
				});
			},
			checkColor() {
				this.colors[this.indexKey] = this.color;
			},
			categoryEditFun() {
				this.categoryEdit = !this.categoryEdit;
				this.labelCategory.forEach((ele) => {
					if (
						ele.id === this.label?.label_category ||
						ele.id === this.label?.label_category?.id
					) {
						this.selected_category.title = ele.title;
						this.selected_category.id = ele.id;
					}
				});
			},
			addSubSystem() {
				if (this.label.subSystem == '') {
					this.touchSubSystem();
					return;
				} else {
					this.label.selectedSubSystems.push(
						this.label.subSystems.find(
							(ele) => ele.id === this.label.subSystem
						)
					);
					var set = new Set(this.label.selectedSubSystems);
					this.label.selectedSubSystems = Array.from(set);
					this.label.subSystem = '';
					this.touchSubSystem();
				}
			},
			removeCategory(index) {
				this.label.selectedSubSystems.splice(index, 1);
				this.touchSubSystem();
			},

			touchSubSystem() {
				this.subSystemErrors =
					this.label.selectedSubSystems.length === 0
						? this.$tr('item_is_required', this.$tr('sub_system'))
						: '';
			},

			removeSubSystem(index) {
				this.label.selectedSubSystems.splice(index, 1);
				this.touchSubSystem();
			},

			labelCategoryRule(title) {
				return [
					(_) =>
						title.required ||
						this.$tr(
							'item_is_required',
							this.$tr('label_category')
						),
				];
			},

			categoryRule(label_category) {
				return [
					(_) =>
						label_category.required ||
						this.$tr(
							'item_is_required',
							this.$tr('label_category')
						),
				];
			},

			async onSave() {
				try {
					if (!this.$v.label.$invalid) {
						const data = {
							id: this.label.id,
							color: this.label.color,
							title: this.label.title,
							status: this.label.status ? 'archive' : 'live',
							system_name: this.systemName,
							label: this.label.label,
							subSystems: this.label.selectedSubSystems.map(
								(row) => {
									return row.id;
								}
							),
							label_category:
								typeof this.label.label_category === 'string'
									? this.label.label_category
									: this.label.label_category.id,
						};
						const response = await this.$axios.put(
							'labels/id',
							data
						);
						if (
							response?.status === 200 &&
							response?.data?.result
						) {
							this.isUpdated = true;
							this.updatedLable = response?.data?.data;
							this.updateItem(response?.data?.data);
							this.editableFun();
							// this.$toastr.s(this.$tr('successfully_updated'));
				this.$toasterNA("green", this.$tr('successfully_updated'));

						} else {
							// this.$toastr.w(this.$tr('something_went_wrong'));
				this.$toasterNA("orange", this.$tr('something_went_wrong'));

						}
					}
				} catch (error) {
					HandleException.handleApiException(this, error);
				}
			},
			async saveCategory() {
				try {
					if (!this.$v.selected_category.$invalid) {
						const response = await this.$axios.put(
							`labels_category/${this.selected_category.id}`,
							this.selected_category
						);
						if (
							response?.status === 200 &&
							response?.data?.result
						) {
							// this.$toastr.s(this.$tr('successfully_updated'));
				this.$toasterNA("green", this.$tr('successfully_updated'));

							this.label.categories = response?.data?.categories;
							this.categoryEdit = !this.categoryEdit;
							this.label.label_category =
								this.selected_category.id;
						} else {
							// this.$toastr.w(this.$tr('something_went_wrong'));
						this.$toasterNA("orange",this.$tr("something_went_wrong"));
							
						}
					}
				} catch (error) {
					HandleException.handleApiException(this, error);
				}
			},
		},

		data() {
			return {
				validate: GlobalRules.validate,
				items: [{ tab: 'general' }, { tab: 'status_transformation' }],
				editable: true,
				categoryEdit: true,
				label_categorys: '',
				selected_id: '',
				updated_category: '',
				updatedLable: {},
				selected_category: { title: '', id: '' },

				label: {
					id: '',
					subSystems: this.subSystems,
					selectedSubSystems: [],
					subSystem: '',
					title: '',
					status: '',
					label: '',
					label_category: '',
					color: '',
					selectedCategory: [],
					categories: this.labelCategory,
				},
				indexKey: 0,
				colors: [
					'#5A89E1',
					'#60B1DC',
					'#62C5A0',
					'#9BCD66',
					'#EACB59',
					'#EC6051',
					'#D64643',
					'#9981E1',
				],
				subSystemErrors: '',
				isLoading: false,
				isSubmitting: false,
				title: this.$tr('labels') + this.$tr(' ') + this.$tr('title'),
				categoryErrors: '',
				isUpdated: false,
			};
		},
	};
</script>

<style>
	.v-text-field--enclosed.v-input--dense:not(.v-text-field--solo).v-text-field--outlined
		.v-input__append-inner {
		margin-top: 6px;
	}
</style>
