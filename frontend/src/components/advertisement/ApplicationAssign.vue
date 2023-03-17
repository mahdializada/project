<template>
	<v-dialog v-model="model" persistent max-width="900" width="900">
		<v-card class="main-Card px-3">
			<v-card-title
				primary-title
				class="pb-1 my-0 title d-flex justify-space-between"
				style="padding: 5px 10px 10px"
			>
				<h2 class="text-h5 font-weight-bold">
					{{ $tr("assign_user") }}
				</h2>
				<CloseBtn @click="toggle" type="button" />
			</v-card-title>
			<v-card-text
				class="position-relative card-pos"
				style="height: 400px; overflow-y: auto"
			>
				<v-form ref="insertForm" lazy-validation>
					<div class="d-flex flex-column">
						<div class="w-full">
							<CustomInput
								:items="fetchedUsers"
								item-value="id"
								multiple
								user
								item-text="firstname"
								:label="$tr('label_required', $tr('users'))"
								placeholder="Users"
								v-model="$v.assign.users.$model"
								value="$v.assign.users.$model"
								:rules="validate($v.assign.users, $root, 'users')"
								type="select"
								return-object
								:loading="isFetchingUsers"
							/>
							<div class="selected d-flex flex-wrap">
								<SelectedItem
									v-for="selectedMember in selectedUsers"
									:key="selectedMember.id"
									@close="() => removeUser(selectedMember)"
									:title="`${selectedMember.firstname} ${selectedMember.lastname}`"
									logo-name=""
									:logo-url="selectedMember.image"
								/>
							</div>
						</div>
					</div>
				</v-form>
			</v-card-text>
			<v-card-actions class="pa-3">
				<v-spacer></v-spacer>
				<v-btn
					color="primary"
					class="stepper-btn px-3"
					style="min-width: 160px"
					@click="validateForm"
					:loading="isSubmitting"
					:disable="isSubmitting"
				>
					<template v-slot:loader>
						<span>
							<span>
								{{ $tr("submitting") + "..." }}
							</span>
							<v-progress-circular
								class="ma-0"
								indeterminate
								color="white"
								size="25"
								:width="2"
							/>
						</span>
					</template>
					{{ $tr("submit") }}
				</v-btn>
				<v-btn
					@click="toggle"
					color="error"
					class="stepper-btn"
					:type="'button'"
				>
					{{ $tr("cancel") }}
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import CustomInput from "../design/components/CustomInput.vue";
import GlobalRules from "~/helpers/vuelidate";
import SelectedItem from "../design/components/SelectedItem.vue";
import Alert from "../../helpers/alert";

export default {
	data() {
		return {
			model: false,
			validate: GlobalRules.validate,
			isSubmitting: false,
			isFetchingUsers: false,
			fetchedUsers: [],
			selectedItems: [],
			selectedUsers: [],
			assign: {
				users: [],
			},
		};
	},

	validations: {
		assign: {
			users: "",
		},
	},

	watch: {
		["assign.users"](value) {
			if (value.length != 0) {
				this.selectedUsers = [...this.selectedUsers, ...value];
				this.fetchedUsers = this.fetchedUsers.filter(
					(item) =>
						this.selectedUsers.findIndex((item2) => item.id == item2.id) < 0
				);
				this.assign.users = [];
			}
		},
	},

	methods: {
		async toggle(items) {
			this.selectedItems = items;
			this.isSubmitting = false;
			setTimeout(() => {
				this.resetForm();
			}, 100);
			this.model = !this.model;
			if (this.model) {
				this.selectedUsers = this.selectedItems[0].users;
				await this.fetchUsers();
			}
		},
		async fetchUsers() {
			try {
				this.isFetchingUsers = true;
				const { data } = await this.$axios.get(
					"/advertisement/applications/users"
				);
				this.fetchedUsers = data.users.filter(
					(item) =>
						this.selectedUsers.findIndex((item2) => item.id == item2.id) < 0
				);
			} catch (error) {}
			this.isFetchingUsers = false;
		},

		removeUser(passedUser) {
			Alert.removeDialogAlert(
				this,
				() => {
					this.selectedUsers = this.selectedUsers.filter(
						(user) => user.id !== passedUser.id
					);
					this.fetchedUsers.push(passedUser);
				},
				"",
				"yes_delete"
			);
		},
		resetForm() {
			if (this.$refs.insertForm) {
				this.$refs.insertForm.reset();
			}
		},

		async validateForm() {
			if (this.selectedUsers.length !== 0) {
				this.isSubmitting = true;
				await this.submit();
				this.isSubmitting = false;
			} else {
				//this.$toastr.e(this.$tr("please_select_at_least_on_item"));
			 	this.$toasterNA("red", this.$tr("please_select_at_least_on_item"));

			}
		},

		async submit() {
			try {
				let assignData = {
					users: this.selectedUsers.map((user) => user.id),
					application: this.selectedItems[0].id,
				};
				const { data } = await this.$axios.post(
					"/advertisement/applications/users",
					assignData
				);
				// this.$toastr.s(this.$tr("selected_users_successfully_assigned"));
				this.$toasterNA("green", this.$tr('selected_users_successfully_assigned'));

				this.$emit("assignData", data?.application);
			} catch (e) {
				//this.$toasterNA("red", this.$tr('something_went_wrong'));
			  this.$toasterNA("red", this.$tr("something_went_wrong"));

			}
		},
	},
	components: { CloseBtn, CustomInput, SelectedItem },
};
</script>
