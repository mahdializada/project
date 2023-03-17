<template>
	<v-form ref="teamRoleEdit" lazy-validation @submit.prevent="onSubmit">
		<Edit
			ref="teamEditStepper"
			class="team-edit"
			:showTitle="true"
			:title="this.editData.name"
			closeCustom
			:totals="totals"
			:is-auto-edit="isAutoEdit"
			:headers="headers"
			:current-index="currentIndex"
			:autoEditData="editData"
			iconOrLogo=""
			:isIcon="false"
			:tableName="tableName"
			:isLoading="isLoading"
			:isSubmitting="isSubmitting"
			@onItemChange="onItemChange"
			@onSubmit="onSubmit"
			@onValidate="validate"
			@onChangeTo="onChangeTo"
			@close="() => this.$emit('close')"
			@onSave="onSave"
			@OnSaveAndNext="saveAndNext">
			<template v-slot:name1>
				<div class="topDive">
					<HeaderTitle title="general" />
					<FilterCountry
						class="px-4 mt-4"
						:companyProps.sync="selectedCompanies"
						:countryProps.sync="selectedCountries"
						:departmentProps.sync="selectedDepartments"
						ref="filterCountry" />
				</div>
			</template>
			<template v-slot:name2>
				<div>
					<template>
						<div class="pb-3 topDiv">
							<HeaderTitle :title="role ? $tr('role') : $tr('team')" />
							<div class="d-flex flex-column flex-md-row mt-6 mx-3">
								<div class="w-full">
									<CustomInput
										:invalid="$v.teamRole.name.$invalid"
										v-model="$v.teamRole.name.$model"
										:rules="
											rules.nameRule(
												$v.teamRole,
												$root,
												role ? 'role_name' : 'team_name',
											)
										"
										:label="role ? 'role_name' : 'team_name'"
										type="textfield"
										:error-messages="nameError" />
								</div>
							</div>
						</div>
					</template>
				</div>
			</template>
			<template v-slot:name3>
				<div class="ma-0 pa-0 topDiv">
					<HeaderTitle title="permissions" />
					<StaticPermission
						:leaveDateRange="true"
						ref="staticPermissions"
						:padding="0"
						:allCompaniesProps="companies"
						:selectedCompaniesProps="selectedCompanies"
						:selectedSubSystemsProps="selectedSubSystems"
						:selectedActionsProps="selectedActions"
						:timeRangeProps="time_range"
						:dateRangeProps="date_range"
						:actualTimeRangeProps.sync="actual_time_range"
						:actualDateRangeProps.sync="actual_date_range"
						:scheduleTypeProps="teamRoleInside.schedule_type" />
				</div>
			</template>
			<template v-slot:name4>
				<div class="topDiv">
					<HeaderTitle title="members" />
					<div class="mx-3 my-3 mt-custom">
						<div class="d-flex flex-column flex-md-row">
							<div class="w-full">
								<CustomInput
									:items="getMembers"
									v-model="selectedMemberId"
									:label="$tr('label_required', $tr('member'))"
									type="autocomplete"
									:placeholder="$tr('choose_item', $tr('member'))"
									class="me-md-4 mb-md-2 mb-0"
									ref="memberInput"
									item-value="id"
									item-text="firstname"
									user
									:error-messages="membersErrorMessage" />
							</div>
							<div class="w-100">
								<FormBtn
									@click="addSelectedMember"
									:title="'add_plus'"
									class="mt-md-4 mb-2" />
							</div>
						</div>
						<div class="selected d-flex flex-wrap">
							<SelectedItem
								v-for="selectedMember in selectedMembers"
								:key="selectedMember.id"
								@close="() => removeSelectedMember(selectedMember.id)"
								:title="`${selectedMember.firstname} ${selectedMember.lastname}`"
								logo-name=""
								:logo-url="selectedMember.image" />
						</div>
					</div>
				</div>
			</template>
			<template v-slot:name5>
				<div class="topDiv">
					<HeaderTitle title="remarks" />
					<v-row class="ma-0 mt-2">
						<v-col cols="12" class="py-0">
							<CustomInput
								placeholder="remark"
								:label="$tr('label_required', $tr('remark'))"
								type="textarea"
								v-model="$v.teamRole.note.$model"
								:rules="rules.noteRule($v.teamRole, $root, 'remark_text')" />
						</v-col>
					</v-row>
				</div>
			</template>
			<template v-slot:name6>
				<div class="topDiv">
					<DoneMessage
						:title="
							role
								? $tr('item_all_set', $tr('role'))
								: $tr('item_all_set', $tr('team'))
						"
						:subTitle="
							role
								? $tr('you_can_access_your_item', $tr('roles'))
								: $tr('you_can_access_your_item', $tr('teams'))
						" />
				</div>
			</template>
		</Edit>
	</v-form>
</template>

<script>
import FilterCountry from "../FilterCountry.vue";
import Edit from "~/components/design/Edit.vue";
import CloseBtn from "~/components/design/Dialog/CloseBtn";
import CustomInput from "~/components/design/components/CustomInput.vue";
import SelectedItem from "~/components/design/components/SelectedItem.vue";
import FormBtn from "~/components/design/components/FormBtn.vue";
import { mapActions, mapGetters } from "vuex";
import TeamRoleValidations from "~/validations/team-role-validations";
import Helpers from "~/helpers/helpers";
import HeaderTitle from "../HeaderTitle.vue";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import StaticPermission from "../StaticPermission";

export default {
	components: {
		FilterCountry,
		CloseBtn,
		Edit,
		CustomInput,
		SelectedItem,
		FormBtn,
		DoneMessage,
		StaticPermission,
		HeaderTitle,
	},
	props: {
		editData: {
			require: true,
			type: Object,
		},
		role: {
			type: Boolean,
			default: false,
		},
		isAutoEdit: {
			type: Boolean,
			default: false,
		},
		totals: {
			type: Number,
			default: 1,
		},
		currentIndex: {
			type: Number,
		},
	},
	async fetch() {
		this.$emit("toggleProgressBar");

		this.teamRoleInside = this.editData;

		this.teamRole.name = this.teamRoleInside.name;

		await this.addSelectedItems();

		await this.setPermissionsAndTime();

		this.teamRoleInside.members.forEach((member) => {
			this.selectedMembers.push(member);
		});

		this.teamRole.note = this.teamRoleInside.note;

		this.$emit("toggleProgressBar");
	},

	computed: {
		...mapGetters({
			getMembers: "filterData/getUsersOfCompanies",
			companies: "filterData/getCompaniesOfTheCountries",
		}),
	},

	data() {
		return {
			name: "",
			rules: TeamRoleValidations.rules,
			teamRole: {
				name: "",
				note: "",
			},

			topIcon: "mdi-account",
			tableName: this.role ? this.$tr("role") : this.$tr("team"),

			headers: [
				{
					icon: "fa-info",
					title: "general",
					slotName: "name1",
				},
				{
					icon: "fa-user",
					title: this.role ? "role" : "team",
					slotName: "name2",
				},
				{
					icon: "fa-user-shield",
					title: "permission",
					slotName: "name3",
				},
				{
					icon: "fa-users",
					title: "members",
					slotName: "name4",
				},
				{
					icon: "fa-comment",
					title: "remarks",
					slotName: "name5",
				},
				{
					icon: "fa-thumbs-up",
					title: "done",
					slotName: "name6",
				},
			],

			teamRoleInside: null,
			permissions: [],
			timeDate: "",

			isLoading: false,
			isSubmitting: false,
			nameError: "",
			selectedCountries: [],

			selectedDepartments: [],

			selectedCompanies: [],

			selectedMembers: [],
			selectedMemberId: "",
			membersErrorMessage: "",

			selectedActions: [],
			selectedSubSystems: [],
			time_range: "",
			date_range: "",
			actual_time_range: {},
			actual_date_range: {},
		};
	},

	validations: {
		teamRole: TeamRoleValidations.validations.team_role,
	},

	methods: {
		...mapActions({
			FetchMembers: "filterData/fetchUsersOfCompanies",
			updateTeam: "teams/updateTeamDb",
			updateRole: "roles/updateRoleDb",
		}),

		async onItemChange(actionType) {
			this.$emit("onItemChange", actionType);
		},
		async onSubmit() {
			this.$refs.teamRoleEdit.validate();
			this.setEssentialData();

			let formData = Helpers.getFormData(this.teamRole);
			if (!this.$v.teamRole.note.$invalid) {
				this.isSubmitting = true;
				if (this.role) {
					await this.updateRole({
						formData,
						id: this.teamRoleInside.id,
						vm: this,
					});
				} else {
					await this.updateTeam({
						formData,
						id: this.teamRoleInside.id,
						vm: this,
					});
				}
				this.isSubmitting = false;
			}
		},

		async onSave() {
			this.$refs.teamRoleEdit.validate();
			this.setEssentialData();
			let formData = Helpers.getFormData(this.teamRole);
			let res = null;
			if (!this.$v.teamRole.note.$invalid) {
				this.isSubmitting = true;
				if (this.role) {
					await this.updateRole({
						formData,
						id: this.teamRoleInside.id,
						vm: this,
					});
				} else {
					await this.updateTeam({
						formData,
						id: this.teamRoleInside.id,
						vm: this,
					});
				}
				this.isSubmitting = false;
			}
			return false;
		},

		async saveAndNext() {
			let res = await this.onSave();
			if (res) {
				this.$emit("nextItem");
			}
		},

		async validate(current) {
			this.selectedCompanies = this.$refs.filterCountry?.selectedCompanies;
			switch (current) {
				case 1:
					const isGeneralSectionValid =
						this.$refs.filterCountry.checkValidation();
					if (isGeneralSectionValid) {
						this.$refs.teamEditStepper.nextForce();
						this.$refs.teamRoleEdit.resetValidation();
					}
					break;
				case 2:
					if (await this.checkTeamValidation()) {
						await this.setStaticPermissionsData();
						this.$refs.teamRoleEdit.resetValidation();
						this.$refs.teamEditStepper.nextForce();
					}
					break;
				case 3:
					if (this.checkPermissionValidation()) {
						this.$refs.teamRoleEdit.resetValidation();
						this.$refs.teamEditStepper.nextForce();
					} else if (this.permissions.length < 1) {
						if (this.role) {
							this.$toasterNA(
								"red",
								this.$tr("please_select_role_permission_no_skip"),
							);
						} else {
							// this.$toastr.e(this.$tr("please_select_team_permission_no_skip"));
							this.$toasterNA(
								"red",
								this.$tr("please_select_team_permission_no_skip"),
							);
						}
					}

					await this.FetchMembers(
						this.selectedCompanies.map((company) => company.id),
					);
					break;
				case 4:
					if (this.checkMembersValidation()) {
						this.$refs.teamEditStepper.nextForce();
					}
					break;
			}
		},

		async setStaticPermissionsData() {
			const companyIds = this.selectedCompanies.map((company) => company.id);
			await this.$refs.staticPermissions.fetchCompaniesSystems(companyIds);
			this.$refs.staticPermissions.setExtraData([], this.selectedCompanies);
			let timeRange = JSON.parse(JSON.parse(this.teamRoleInside.time_range));
			this.actual_time_range = timeRange;
			const to = this.$tr("to");
			if (timeRange) {
				const startTime = Helpers.convertTime(this, timeRange.startRange);
				const endTime = Helpers.convertTime(this, timeRange.endRange);
				timeRange = `${startTime} ${to} ${endTime}`;
			}

			let dateRange = JSON.parse(JSON.parse(this.teamRoleInside.date_range));
			this.actual_date_range = dateRange;
			if (dateRange) {
				const startDate = Helpers.convertTime(this, dateRange.startRange);
				const endDate = Helpers.convertTime(this, dateRange.endRange);
				dateRange = `${startDate} ${to} ${endDate}`;
			}

			this.date_range = dateRange;
			this.time_range = timeRange;
		},

		async onChangeTo(step) {
			this.selectedCompanies = this.$refs.filterCountry?.selectedCompanies;
			await this.setStaticPermissionsData();
			switch (step) {
				case 1:
					const isGeneralSectionValid =
						this.$refs.filterCountry.checkValidation();

					if (isGeneralSectionValid) {
						this.$refs.teamEditStepper.setCurrent(step);
						this.$refs.teamRoleEdit.resetValidation();
					}
					break;
				case 2:
					const isGeneralSectionValid2 =
						this.$refs.filterCountry.checkValidation();

					if (isGeneralSectionValid2) {
						this.$refs.teamEditStepper.setCurrent(step);
						this.$refs.teamRoleEdit.resetValidation();
					}
					break;
				case 3:
					const isGeneralSectionValid3 =
						this.$refs.filterCountry.checkValidation();
					if (isGeneralSectionValid3) {
						if (await this.checkTeamValidation()) {
							this.$refs.teamRoleEdit.resetValidation();
							this.$refs.teamEditStepper.setCurrent(step);
						}
					}
					break;
				case 4:
					const isGeneralSectionValid4 =
						this.$refs.filterCountry.checkValidation();

					if (isGeneralSectionValid4) {
						if (await this.checkTeamValidation()) {
							if (this.checkPermissionValidation()) {
								this.$refs.teamRoleEdit.resetValidation();
								this.$refs.teamEditStepper.setCurrent(step);
							} else if (this.permissions.length < 1) {
								if (this.role) {
									// this.$toastr.e(
									// 	this.$tr("please_select_role_permission_no_skip"),
									// );
									this.$toasterNA(
								"red",
								this.$tr("please_select_role_permission_no_skip"),
							);
								} else {
									// this.$toastr.e(
									// 	this.$tr("please_select_team_permission_no_skip"),
									// );
									this.$toasterNA(
								"red",
								this.$tr("please_select_team_permission_no_skip"),
							);
								}
							}
							// const companyIds =  this.$refs.filterCountry?.selectedCompanies.map(
							//   (company) => company.id
							// );

							// await this.$refs.staticPermission.fetchCompaniesSystems(
							//   companyIds
							// );
							await this.FetchMembers(
								this.selectedCompanies.map((company) => company.id),
							);
						}
					}
					break;
				case 5:
					const isGeneralSectionValid5 =
						this.$refs.filterCountry.checkValidation();

					if (isGeneralSectionValid5) {
						if (await this.checkTeamValidation()) {
							if (this.checkPermissionValidation()) {
								if (this.checkMembersValidation()) {
									this.$refs.teamEditStepper.setCurrent(step);
								}
								this.$refs.teamRoleEdit.resetValidation();
							} else if (this.permissions.length < 1) {
								if (this.role) {
									// this.$toastr.e(
									// 	this.$tr("please_select_role_permission_no_skip"),
									// );
									this.$toasterNA(
								"red",
								this.$tr("please_select_role_permission_no_skip"),
							);
								} else {
									// this.$toastr.e(
									// 	this.$tr("please_select_team_permission_no_skip"),
									// );
									this.$toasterNA(
								"red",
								this.$tr("please_select_team_permission_no_skip"),
							);
								}
							}
						}
					}
					break;
			}
		},

		async checkNameValidationDb(name, id) {
			this.isLoading = true;
			let res;
			if (this.role) {
				res = await this.$axios.post(`roles/check-uniqueness`, {
					name,
					id,
				});
			} else {
				res = await this.$axios.post(`teams/check-uniqueness`, {
					name,
					id,
				});
			}
			if (res.data.exist) {
				this.nameError = this.$tr("name_exists");
			} else {
				this.nameError = "";
			}
			this.isLoading = false;
			return !res.data.exist;
		},

		setEssentialData() {
			this.teamRole.department_ids =
				this.$refs.filterCountry.selectedDepartments.map((dep) => dep.id);
			this.teamRole.user_ids = this.selectedMembers.map((member) => member.id);
			this.teamRole.id = this.teamRoleInside.id;

			const permissions = this.$refs.staticPermissions.getData();

			this.teamRole.schedule_type = permissions.schedule_type;
			this.teamRole.time_range = permissions.timeRange;
			this.teamRole.date_range = permissions.dateRange;
			this.teamRole.permissions = permissions.permissions;
		},

		async checkTeamValidation() {
			this.$refs.teamRoleEdit.validate();
			if (!this.$v.teamRole.name.$invalid) {
				if (
					await this.checkNameValidationDb(this.name, this.teamRoleInside.id)
				) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		},

		checkPermissionValidation() {
			return (
				this.$refs.staticPermissions.getData().permissions.length >= 1 &&
				this.$refs.staticPermissions.validateDateTime()
			);
		},

		checkMembersValidation() {
			// validate members
			const MembersRequiredText = this.$tr(
				"item_is_required",
				this.$tr("member"),
			);

			this.selectedMembers?.length < 1
				? (this.membersErrorMessage = MembersRequiredText)
				: (this.membersErrorMessage = "");

			// validate general
			const isMembersValid = this.membersErrorMessage;

			return !isMembersValid;
		},

		addSelectedItems() {
			this.teamRoleInside.departments.forEach((dept) => {
				this.selectedDepartments.push({
					name: dept.name,
					id: dept.id,
				});
				dept.companies.forEach((comp) => {
					if (!this.isInArray(this.selectedCompanies, comp)) {
						this.selectedCompanies.push({
							name: comp.name,
							id: comp.id,
							logo: comp.logo,
						});
					}
					comp.countries.forEach((count) => {
						if (!this.isInArray(this.selectedCountries, count)) {
							this.selectedCountries.push({
								name: count.name,
								id: count.id,
								iso2: count.iso2,
							});
						}
					});
				});
			});
		},

		setPermissionsAndTime() {
			this.teamRoleInside.permissions.forEach((perm) => {
				if (
					!this.selectedActions.includes(
						`${perm.sub_system.system_id}|${perm.sub_system_id}|${perm.action_id}`,
					)
				) {
					this.selectedActions.push(
						`${perm.sub_system.system_id}|${perm.sub_system_id}|${perm.action_id}`,
					);
				}
				if (
					!this.selectedSubSystems.includes(
						`${perm.sub_system.system_id}|${perm.sub_system_id}`,
					)
				) {
					this.selectedSubSystems.push(
						`${perm.sub_system.system_id}|${perm.sub_system_id}`,
					);
				}
			});
		},

		removeSelectedMember(memberId) {
			this.selectedMembers = this.selectedMembers?.filter(
				(member) => member?.id !== memberId,
			);
			const requiredText = this.$tr("item_is_required", this.$tr("member"));
			this.selectedMembers?.length < 1
				? (this.membersErrorMessage = requiredText)
				: (this.membersErrorMessage = "");
		},

		// add new country to selected countries
		addSelectedMember() {
			const requiredText = this.$tr("item_is_required", this.$tr("member"));
			if (this.selectedMemberId) {
				let member = [];
				member = this.getMembers?.find(
					(memberItem) => memberItem?.id === this.selectedMemberId,
				);

				if (
					this.selectedMembers?.some(
						(memberItem) => memberItem?.id === member?.id,
					)
				) {
					return;
				}

				this.selectedMembers?.unshift(member);
				this.selectedMemberId = "";
				this.selectedMembers?.length < 1
					? (this.memberErrorMessage = requiredText)
					: (this.membersErrorMessage = "");
			} else if (this.selectedMembers?.length < 1) {
				this.membersErrorMessage = requiredText;
			}
		},

		isInArray(arr, search) {
			let res = arr.find((item) => {
				return item.id == search.id;
			});
			return res !== undefined;
		},

		getPermissions(permissions) {
			this.permissions = permissions;
		},

		getTimeDate(timeDate) {
			this.timeDate = timeDate;
		},
	},
};
</script>

<style>
.main-Card {
	background-color: #f9fafd !important;
}
.main-Card .title {
	color: #b6c1d2 !important;
}
.main-Card .v-stepper .v-stepper__header .v-stepper__step__step {
	display: none !important;
}
.main-Card .v-stepper__step.v-stepper__step--active .v-stepper__label .v-btn {
	background-color: var(--v-primary-base);
	color: white;
}
.topDiv {
	position: relative;
}
.topTitle {
	top: 0px;
	position: sticky;
	z-index: 2;
	background-color: var(--bg-white) !important;
}
.border-bottom {
	border-bottom: 2px solid #bfc8d7;
}
.subtitle-1 {
	color: #737885;
}
.border {
	border: 1px solid #b6c1d2;
}
.style.theme--light.v-btn.v-btn--has-bg {
	background-color: #edf2f9 !important;
}
.main-Card
	.v-input.v-input--dense.theme--light.v-text-field.v-text-field--is-booted.v-text-field--enclosed.v-text-field--outlined.v-text-field--placeholder
	.v-input__control {
	border-radius: 3px !important;
}
.main-Card
	.v-input.v-input--is-readonly.v-input--dense.theme--light.v-text-field
	.v-input__control {
	border-radius: 3px;
}
.main-Card
	.v-input.v-textarea.v-input--dense.v-text-field.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder {
	border-radius: 3px;
}
.team-edit .mt-custom .v-input__slot {
	margin-top: 4px !important;
}
@media screen and (min-width: 600px) {
	.buttons {
		padding-left: 0px !important;
		padding-right: 0px !important;
	}
}

.avatar {
	height: 110px;
	width: 110px;
	background-color: var(--gray-cyan);
	border: 2px solid var(--gray-cyan);
	background-repeat: no-repeat;
	background-size: cover;
	background-position: center;
}

.avatar .v-icon {
	color: var(--color-white);
	font-size: 60px;
}

.custom-file .v-file-input__text {
	height: 110px;
	opacity: 0;
}

.custom-file .v-input__slot fieldset {
	border-style: dashed !important;
}

.custom-file .v-input__slot {
	display: flex;
	justify-content: center;
	align-items: center;
}

.upload-first-p {
	font-size: 18px;
	color: var(--upload-input-first-p);
	letter-spacing: 0.5px;
}

.upload-second-p {
	color: var(--input-border-color);
	line-height: 1.5;
}

.custom-file .v-input__prepend-inner {
	position: absolute;
}
</style>
