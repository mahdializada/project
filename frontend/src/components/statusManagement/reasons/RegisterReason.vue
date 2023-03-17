<template>
	<v-form lazy-validation ref="reasonForm" @submit.prevent="submitForm">
		<CustomStepper
			:headers="steppers"
			@close="closeDialog"
			:canNext="false"
			@validate="() => {}"
			:is-submitting="isLoading"
			@submit="submitForm"
			ref="customStepper">
			<template v-slot:step1>
				<div class="w-full pb-3">
					<CustomInput
						:label="$tr('label_required', $tr('title'))"
						placeholder="title"
						type="textarea"
						v-model.trim="$v.reason.title.$model"
						:rules="titleRule($v.reason.title)" />
				</div>
				<!-- tabs advertisment -->
				<!-- <div class="d-flex flex-column flex-md-row" v-if="slug == 'advertisement'">
					<div class="w-full">
						<CustomInput
							:items="
								tabItems.map((row) => {
									row.tab_name = $tr(row.tab_name);
									return row;
								})
							"
							v-model="tab"
							:label="$tr('tab_system')"
							type="autocomplete"
							item-text="tab_name"
							item-value="id"
							:placeholder="$tr('choose_item', $tr('tab_system'))"
							class="me-md-4 mb-md-2 mb-0" />
					</div>
					<div class="w-100">
						<FormBtn @click="addTabSystem" title="add_plus" class="mt-md-4 mb-2" />
					</div>
				</div>

				<div class="selected d-flex flex-wrap">
					<template v-for="(sub, index) in reason.selectedTabSystems">
						<SelectedItem @close="removeTabSystem(index)" :key="index" :title="$tr(sub.tab_name)" />
					</template>
				</div> -->
				<!-- tabs advertisment -->
			</template>

			<template v-slot:step2>
				<DoneMessage
					:title="$tr('item_all_set', $tr('reason'))"
					:subTitle="$tr('you_can_access_your_item', $tr('reason'))" />
			</template>
		</CustomStepper>
	</v-form>
</template>

<script>
import { mapActions } from "vuex";
import { minLength, required } from "vuelidate/lib/validators";
import CustomStepper from "~/components/design/FormStepper/CustomStepper";
import HandleException from "~/helpers/handle-exception";
import CustomInput from "~/components/design/components/CustomInput.vue";
import FormBtn from "~/components/design/components/FormBtn.vue";
import SelectedItem from "~/components/design/components/SelectedItem";
import DoneMessage from "~/components/design/components/DoneMessage.vue";

export default {
	name: "RegisterReason",
	components: { CustomInput, FormBtn, SelectedItem, CustomStepper, DoneMessage },

	data() {
		return {
			isLoading: false,
			steppers: [
				{ icon: "fa-lock", title: "general", slotName: "step1" },
				{ icon: "fa-thumbs-up", title: "done", slotName: "step2" },
			],
			reason: {
				title: "",
				// selectedTabSystems: [],
			},
			// tab: "",
			// tabItems: [
			// 	{
			// 		id: 1,
			// 		tab_name: "country",
			// 	},
			// 	{
			// 		id: 2,
			// 		tab_name: "company",
			// 	},
			// 	{
			// 		id: 3,
			// 		tab_name: "item_code",
			// 	},
			// 	{
			// 		id: 4,
			// 		tab_name: "issp_code",
			// 	},
			// 	{
			// 		id: 5,
			// 		tab_name: "platform",
			// 	},
			// 	{
			// 		id: 6,
			// 		tab_name: "organization",
			// 	},
			// 	{
			// 		id: 7,
			// 		tab_name: "ad_account",
			// 	},
			// 	{
			// 		id: 8,
			// 		tab_name: "campaign",
			// 	},
			// 	{
			// 		id: 9,
			// 		tab_name: "ad_set",
			// 	},
			// 	{
			// 		id: 10,
			// 		tab_name: "ad",
			// 	},
			// ],
		};
	},

	validations: {
		reason: {
			title: { required, minLength: minLength(2) },
		},
	},
	props: {
		slug: {
			type: String,
		},
	},
	
	methods: {
		...mapActions({
			fetchReasons: "reasons/fetchReasons",
		}),

		// addTabSystem() {
		// 	if (this.tab == "") {
		// 		return;
		// 	} else {
		// 		this.reason.selectedTabSystems.push(this.tabItems.find((ele) => ele.id === this.tab));
		// 		var set = new Set(this.reason.selectedTabSystems);
		// 		this.reason.selectedTabSystems = Array.from(set);
		// 		this.tab = "";
		// 	}
		// },

		// removeTabSystem(index) {
		// 	this.reason.selectedTabSystems.splice(index, 1);
		// },

		async submitForm() {
			this.$refs.reasonForm.validate();
			this.$v.reason.$touch();
			if (!this.$v.reason.$invalid) {
				this.isLoading = true;
				await this.insertRecord();
			} else {
				// this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

			}
		},

		async insertRecord() {
			this.reason.slug = this.slug;
			// this.reason.selectedTabSystems = this.reason.selectedTabSystems.map((row) => {
			// 	return row.tab_name;
			// });
			try {
				const response = await this.$axios.post("reasons", this.reason);
				if (response?.status == 200) {
          this.$refs.customStepper.setCurrent(2);
					this.fetchReasons({ slug: this.slug });
					this.isLoading = false;
					this.resetForm();
				} else {
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

					this.isLoading = false;
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
				this.isLoading = false;
			}
		},

		resetForm() {
			this.reason.title = "";
            // this.reason.selectedTabSystems =[];
            // this.tab = "";
			this.$refs.reasonForm?.resetValidation();
		},

		closeDialog() {
			this.resetForm();
			this.$refs.customStepper.fillMore();
		},
		titleRule(title) {
			return [
				(_) => title.required || this.$tr("item_is_required", this.$tr("title")),
				(_) => title.minLength || this.$tr("min_length", this.$tr("title"), this.$tr("2")),
			];
		},
	},
};
</script>
