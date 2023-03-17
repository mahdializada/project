<template>
	<v-form ref="teamRoleForm" lazy-validation @submit.prevent="onSubmit">
		<CustomStepper
			:skip="[3, 4]"
			:headers="steppers"
			:canNext="this.canNext"
			@submit="onSubmit"
			@validate="validate"
			@close="close"
			@onSkip="skip"
			ref="teamRoleStepper"
			:loading="isLoading"
			:isSubmitting="isSubmitting"
			@changeToValidate="changeToValidate"
		>
			<template v-slot:step1>
				<FilterCountry ref="filterCountry" />
			</template>
			<template v-slot:step2>
				<CustomInput
					:invalid="$v.teamRole.name.$invalid"
					v-model="$v.teamRole.name.$model"
					:rules="
						rules.nameRule($v.teamRole, $root, role ? 'role_name' : 'team_name')
					"
					:label="
						role
							? $tr('label_required', $tr('role'))
							: $tr('label_required', $tr('team'))
					"
					:placeholder="role ? 'role_name' : 'team_name'"
					type="textfield"
					:error-messages="nameError"
					ref="teamRoleName"
				/>
			</template>
			<template v-slot:step3>
				<StaticPermission
					:leaveDateRange="true"
					ref="staticPermissions"
					:padding="0"
					:allCompaniesProps="companies"
					:selectedCompaniesProps.sync="selectedCompanies"
				/>
			</template>

			<template v-slot:step4>
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
							:error-messages="membersErrorMessage"
						/>
					</div>
					<div class="w-100">
						<FormBtn
							@click="addSelectedMember"
							title="add_plus"
							class="mt-md-4 mb-2"
						/>
					</div>
				</div>
				<div class="selected d-flex flex-wrap">
					<SelectedItem
						v-for="selectedMember in selectedMembers"
						:key="selectedMember.id"
						@close="() => removeSelectedMember(selectedMember.id)"
						:title="`${selectedMember.firstname} ${selectedMember.lastname}`"
						logo-name=""
						:logo-url="selectedMember.image"
					/>
				</div>
			</template>
			<template v-slot:step5>
				<v-row>
					<v-col cols="12" class="py-0">
						<CustomInput
							v-model="$v.teamRole.note.$model"
							:rules="rules.noteRule($v.teamRole, $root)"
							placeholder="remark"
							:label="$tr('label_required', $tr('remark'))"
							type="textarea"
						/>
					</v-col>
				</v-row>
			</template>
			<template v-slot:step6>
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
					"
				/>
			</template>
		</CustomStepper>
	</v-form>
</template>

<script>
import CustomStepper from "~/components/design/FormStepper/CustomStepper";
import FilterCountry from "../FilterCountry.vue";
import StaticPermission from "~/components/users/StaticPermission";

import Teams from "~/configs/pages/teams";
import Roles from "~/configs/pages/roles";
import CustomInput from "~/components/design/components/CustomInput";
import SelectedItem from "~/components/design/components/SelectedItem";
import FormBtn from "~/components/design/components/FormBtn";
import ManualPermission from "~/components/design/ManualPermission.vue";

import { mapActions, mapGetters } from "vuex";
import CloseDialogButton from "~/components/common/buttons/CloseDialogButton";
import AutoComplete from "~/components/common/AutoComplete";
import TeamRoleValidations from "~/validations/team-role-validations";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import Helpers from "~/helpers/helpers";

export default {
	name: "TeamRoleRegistration",
	components: {
		ManualPermission,
		CloseDialogButton,
		AutoComplete,
		CustomStepper,
		Teams,
		Roles,
		CustomInput,
		SelectedItem,
		FormBtn,
		DoneMessage,
		FilterCountry,
		StaticPermission,
	},

	props: {
		role: {
			type: Boolean,
		},
		tabKey: {
			type: String,
		},
	},
	data() {
		return {
			steppers: this.role ? Roles(this).steppers : Teams(this).steppers,
			rules: TeamRoleValidations.rules,
			teamRole: {
				name: "",
				note: "",
			},
			// Step 2
			teamName: "",
			nameError: "",

			// Step 3

			remarks: "",
			canNext: true,
			// General
			registerDialog: false,
			isEdit: false,
			isSaving: false,
			isLoading: false,
			isSubmitting: false,

			// selected necessary
			selectedCountryId: "",
			countryErrorMessage: "",

			selectedDepartmentId: "",
			departmentErrorMessage: "",

			selectedCompanies: [],
			selectedCompanyId: "",
			companyErrorMessage: "",

			selectedMembers: [],
			selectedMemberId: "",
			membersErrorMessage: "",

			permissions: [],
			timeDate: {},
		};
	},

	validations: {
		teamRole: TeamRoleValidations.validations.team_role,
	},

	computed: {
		...mapGetters({
			getMembers: "filterData/getUsersOfCompanies",
			companies: "filterData/getCompaniesOfTheCountries",
		}),
	},

	methods: {
		...mapActions({
			FetchMembers: "filterData/fetchUsersOfCompanies",
			insertTeam: "teams/insertTeamDb",
			insertRole: "roles/insertRoleDb",
		}),

		async changeToValidate(changeTo) {
			this.selectedCompanies = this.$refs.filterCountry?.selectedCompanies;
			this.isLoading = true;
			switch (changeTo) {
				case 1:
					this.$refs.teamRoleStepper.changeTo(changeTo);
					break;
				case 2:
					if (this.$refs.filterCountry.checkValidation()) {
						this.$refs.teamRoleStepper.changeTo(changeTo);
					}
					break;
				case 3:
					if (this.$refs.filterCountry.checkValidation()) {
						if (await this.checkNameValidation()) {
							this.setPermissionsData();
							this.$refs.teamRoleStepper.changeTo(changeTo);
						}
					}
					break;
				case 4:
					if (this.$refs.filterCountry.checkValidation()) {
						if (await this.checkNameValidation()) {
							this.setPermissionsData();
							let validateDateTime =
								await this.$refs.staticPermissions.validateDateTime();
							if (this.$refs.staticPermissions.getData().permissions < 1) {
								if (this.role) {
									// this.$toastr.e(
									// 	this.$tr("please_select_role_permission_no_skip")
									// );

									this.$toasterNA(
								"red",
								this.$tr("please_select_role_permission_no_skip"),
							);
								} else {
									// this.$toastr.e(
									// 	this.$tr("please_select_team_permission_no_skip")
									// );
									this.$toasterNA(
								"red",
								this.$tr("please_select_team_permission_no_skip"),
							);
								}
							} else if (
								this.$refs.staticPermissions.getData().permissions.length > 0 &&
								validateDateTime
							) {
								await this.FetchMembers(
									this.$refs.filterCountry.selectedCompanies.map(
										(company) => company.id
									)
								);
								this.$refs.teamRoleStepper.changeTo(changeTo);
								this.$refs.teamRoleForm.resetValidation();
							}
						}
					}
					break;
				case 5:
					if (this.$refs.filterCountry.checkValidation()) {
						if (await this.checkNameValidation()) {
							if (this.$refs.staticPermissions.getData().permissions < 1) {
								if (this.role) {
									// this.$toastr.e(
									// 	this.$tr("please_select_role_permission_no_skip")
									// );

									this.$toasterNA(
								"red",
								this.$tr("please_select_role_permission_no_skip"),
							);
								} else {
									// this.$toastr.e(
									// 	this.$tr("please_select_team_permission_no_skip")
									// );

									this.$toasterNA(
								"red",
								this.$tr("please_select_team_permission_no_skip"),
							);
								}
							} else if (
								this.$refs.staticPermissions.getData().permissions.length > 0 &&
								(await this.$refs.staticPermissions.validateDateTime())
							) {
								if (this.checkMembersValidation()) {
									this.$refs.teamRoleStepper.changeTo(changeTo);
									this.$refs.teamRoleForm.resetValidation();
								}
							}
							this.nameError = "";
						} else {
							this.nameError = this.$tr("name_exists");
						}
					}
					break;
			}
			this.isLoading = false;
		},

		// add new country to selected countries

		removeSelectedMember(memberId) {
			this.selectedMembers = this.selectedMembers?.filter(
				(member) => member?.id !== memberId
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
					(memberItem) => memberItem?.id === this.selectedMemberId
				);

				if (
					this.selectedMembers?.some(
						(memberItem) => memberItem?.id === member?.id
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

		checkMembersValidation() {
			// validate members
			const MembersRequiredText = this.$tr(
				"item_is_required",
				this.$tr("member")
			);
			this.selectedMembers?.length < 1
				? (this.membersErrorMessage = MembersRequiredText)
				: (this.membersErrorMessage = "");

			// validate general
			const isMembersValid = this.membersErrorMessage;

			return !isMembersValid;
		},

		async setPermissionsData() {
			const companyIds = this.selectedCompanies.map((company) => company.id);
			await this.$refs.staticPermissions.fetchCompaniesSystems(companyIds);
			this.$refs.staticPermissions.setExtraData([], this.selectedCompanies);
		},

		async checkNameValidation() {
			this.$refs.teamRoleForm.validate();
			if (!this.$v.teamRole.name.$invalid) {
				if (await this.checkNameValidationDb(this.teamRole.name)) {
					this.$refs.teamRoleForm.resetValidation();
					this.nameError = "";
					return true;
				} else {
					this.nameError = this.$tr("name_exists");
				}
			}
			return false;
		},

		async onSubmit() {
			this.$refs.teamRoleForm.validate();
			this.setEssentialData();
			let formData = Helpers.getFormData(this.teamRole);
			if (!this.$v.teamRole.note.$invalid) {
				this.isSubmitting = true;
				if (!this.role) {
					await this.insertTeam({ data: formData, vm: this });
				} else {
					await this.insertRole({ data: formData, vm: this });
				}
				this.isSubmitting = false;
			} else {
				this.canNext = false;
			}
		},

		async validate(current) {
			this.selectedCompanies = this.$refs.filterCountry?.selectedCompanies;
			switch (current) {
				case 1:
					const isGeneralSectionValid =
						this.$refs.filterCountry.checkValidation();
					this.canNext = isGeneralSectionValid;
					break;
				case 2:
					this.canNext = false;
					this.isLoading = true;

					this.$refs.teamRoleForm.validate();
					if (!this.$v.teamRole.name.$invalid) {
						if (await this.checkNameValidationDb(this.teamRole.name)) {
							const companyIds = this.selectedCompanies.map(
								(company) => company.id
							);
							await this.$refs.staticPermissions.fetchCompaniesSystems(
								companyIds
							);
							this.$refs.staticPermissions.setExtraData(
								[],
								this.selectedCompanies
							);
							this.$refs.teamRoleStepper.forceNext();
							this.nameError = "";
							this.$refs.teamRoleForm.resetValidation();
						} else {
							this.nameError = this.$tr("name_exists");
						}
						this.$refs.teamRoleForm.resetValidation();
					}
					this.isLoading = false;
					break;
				case 3:
					this.canNext = false;
					let validateDateTime =
						await this.$refs.staticPermissions.validateDateTime();
					this.selectedCompanies = this.$refs.filterCountry.selectedCompanies;
					if (this.$refs.staticPermissions.getData().permissions < 1) {
						if (this.role) {
							this.$toasterNA("red", this.$tr('please_select_role_permission_no_skip'));
						} else {
							// this.$toastr.e(this.$tr("please_select_team_permission_no_skip"));
							this.$toasterNA(
								"red",
								this.$tr("please_select_team_permission_no_skip"),
							);
						}
					} else if (
						this.$refs.staticPermissions.getData().permissions.length > 0 &&
						validateDateTime
					) {
						await this.FetchMembers(
							this.$refs.filterCountry.selectedCompanies.map(
								(company) => company.id
							)
						);
						this.$refs.teamRoleStepper.forceNext();
					}
					break;
				case 4:
					this.canNext = this.checkMembersValidation();
			}
		},

		async checkNameValidationDb(name) {
			let res;
			if (this.role) {
				res = await this.$axios.post(`roles/check-uniqueness`, {
					name,
				});
			} else {
				res = await this.$axios.post(`teams/check-uniqueness`, {
					name,
				});
			}
			return !res.data.exist;
		},

		setEssentialData() {
			this.teamRole.department_ids =
				this.$refs.filterCountry.selectedDepartments.map((dep) => dep.id);
			this.teamRole.user_ids = this.selectedMembers.map((member) => member.id);
			// time restriction

			const permissions = this.$refs.staticPermissions.getData();

			this.teamRole.schedule_type = permissions.schedule_type;
			this.teamRole.time_range = permissions.timeRange;
			this.teamRole.date_range = permissions.dateRange;
			this.teamRole.permissions = permissions.permissions;
		},
		async resetForm() {
			this.$refs.teamRoleForm.reset();
			this.$refs.filterCountry.selectedCountries = [];
			this.$refs.filterCountry.selectedCompanies = [];
			this.$refs.filterCountry.selectedDepartments = [];
			this.selectedMembers = [];
			await this.$store.commit("filterData/REMOVE_ALL_COMPANIES");
			await this.$store.commit("filterData/REMOVE_ALL_DEPARTMENTS");
		},
		close() {
			this.$emit("close");
			this.resetAll();
		},
		resetAll() {
			this.resetForm();
			this.$refs.teamRoleStepper.fillMore();
		},
		async skip() {
			await this.FetchMembers(
				this.$refs.filterCountry.selectedCompanies.map((company) => company.id)
			);
		},
	},
};
</script>

<style>
.custom-logo {
	background-repeat: no-repeat !important;
	background-size: cover !important;
	background-position: center !important;
	background: #edf2f9;
}
.custom-logo .v-file-input__text {
	height: 110px;
	opacity: 0;
}
.custom-logo .v-input__slot fieldset {
	border-style: dashed !important;
}
.custom-logo .v-input__slot {
	min-height: 200px !important;
	display: flex;
	justify-content: center;
	align-items: center;
}
.custom-logo .v-input__prepend-inner {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
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
