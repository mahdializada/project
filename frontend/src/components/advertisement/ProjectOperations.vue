<template>
	<Stepper
		ref="hello"
		:title="
			isEdit
				? $tr('update_item', $tr('project'))
				: $tr('create_item', $tr('project'))
		"
		cookieName="generate_project"
		:show="show"
		:steps="steps"
		:skipStep="skip"
		:form="project"
		:validateRules="projectRules"
		:submit="validateForm"
		@close="
			() => {
				show = false;
				resetForm();
			}
		"
		:showBack="showBack"
	/>
</template>

<script>
import Stepper from "../horizontal_stepper/Stepper.vue";
import ProjectStepOne from "./ProjectStepOne";
import Validations from "~/validations/validations";
import HandleException from "~/helpers/handle-exception";
import CustomInput from "../design/components/CustomInput.vue";
import { requiredIf } from "vuelidate/lib/validators";

export default {
	components: { Stepper, CustomInput },

	data() {
		return {
			show: false,
			skip: [1],
			showBack: true,
			steps: [
				{
					title: this.$tr("connection_type"),
					component: ProjectStepOne,
					id: "type",
					props: {
						isEdit: false,
					},
				},
			],

			project: {
				name: null,
				domain: null,
				company_ids: [],
				country_ids: [],
			},
			projectRules: {
				form: {
					name: Validations.requiredValidation,
					domain: Validations.domainValidation,
					country_ids: Validations.requiredValidation,
					company_ids: Validations.requiredValidation,
				},
			},
			isEdit: false,
		};
	},

	watch: {
		"connection.connection_type": function (type) {
			if (type == "template") {
				this.skip = [];
			} else {
				this.skip = [1];
			}
		},
		"connection.connection_id": function (connection_id) {
			if (connection_id) {
				this.skip = [0, 1, 2, 3, 4];
				this.steps[2].props.fetchCountry = false;
				this.showBack = false;
			}
		},
	},

	methods: {
		toggle(item) {
			this.show = !this.show;

			if (item) {
				this.steps[0].props.isEdit = true;
				let { id, name, domain, countries, companies } = item;

				this.project = {
					id,
					name,
					domain,
					country_ids: countries.map((item) => item.id),
					company_ids: companies.map((item) => item.id),
				};

				this.isEdit = true;
			} else {
				this.isEdit = false;
			}
		},

		async validateForm() {
			const data = {
				name: this.project.name,
				domain: this.project.domain,
				countries: this.project.country_ids,
				companies: this.project.company_ids,
				id: this.project.id,
			};
			return this.insertRecord(data);
		},
		resetForm() {
			this.project = {
				name: null,
				domain: null,
				company_ids: [],
				country_ids: [],
			};
		},
		async insertRecord(project) {
			try {
				const url = "advertisement/projects";
				let data = {};
				if (this.isEdit) {
					const response = await this.$axios.put(
						url + `/${project.id}`,
						project
					);
					data = response.data;
				} else {
					const response = await this.$axios.post(url, project);
					data = response.data;
				}

				if (data.result) {
					if (this.isEdit) {
						this.$toasterNA("green", this.$tr("successfully_updated"));
					} else {
						this.$toasterNA("green", this.$tr("successfully_inserted"));
						const insertedRecord = data.data;
					}
					this.$emit("getRecord");
					this.resetForm();
					return true;
				} else {
					this.$toasterNA("red", this.$tr("something_went_wrong"));
					return false;
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
				return false;
			}
		},
	},
};
</script>

<style></style>
