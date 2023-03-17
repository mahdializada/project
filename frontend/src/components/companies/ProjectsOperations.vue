<template>
	<v-card>
		<v-card-title primary-title>
			<span>{{ $tr("create", $tr("project")) }}</span>
			<v-spacer />
			<CloseDialogButton @click="() => this.$emit('update:dialog')" />
		</v-card-title>
		<v-divider class="mb-1 mt-1 grey lighten-1" />
		<v-card-text>
			<v-form ref="form" lazy-validation>
				<v-row class="pt-3">
					<v-col cols="12" sm="6" class="py-0">
						<TextField
							:label="$tr('item_name', $tr('project'))"
							v-model.trim="$v.project.name.$model"
							:rules="
								rules.nameRule(
									$v.project,
									$root,
									$tr('label_required', $tr('project'))
								)
							"
							:invalid="$v.project.name.$invalid"
							:maxLength="$v.project.name.$params.maxLength.max"
						/>
					</v-col>
					<v-col cols="12" sm="6" class="py-0">
						<AutoComplete
							:items="[]"
							item-text="name"
							item-value="id"
							:label="$tr('label_required', $tr('country'))"
							v-model="$v.project.country_id.$model"
							:rules="rules.countryRule($v.project, $root)"
						/>
					</v-col>

					<v-col cols="12" sm="6" class="py-0">
						<AutoComplete
							:items="[]"
							item-text="name"
							item-value="id"
							label="textField.department.name"
							v-model="$v.project.team_id.$model"
							:rules="rules.teamRule($v.project, $root)"
						/>
					</v-col>

					<v-col cols="12" sm="6" class="py-0">
						<AutoComplete
							:items="[]"
							item-text="name"
							item-value="id"
							label="textField.investment_type"
							v-model="$v.project.investment_type.$model"
							:rules="rules.investmentTypeRule($v.project, $root)"
						/>
					</v-col>

					<v-col cols="12" sm="6" class="py-0">
						<v-file-input
							counter
							v-model="$v.project.logo.$model"
							:label="$tr('logo')"
							accept="image/*"
							prepend-icon=""
							outlined
							dense
							:show-size="$v.project.logo.required"
							:rules="rules.logoRule($v.project, $root)"
						>
							<template v-slot:selection="{ text }">
								<v-chip v-if="text" color="primary" dark label small>
									{{ text }}
								</v-chip>
							</template>
						</v-file-input>
					</v-col>

					<v-col cols="12" sm="6" class="py-0">
						<v-textarea
							outlined
							v-model="$v.project.note.$model"
							dense
							:label="$tr('note')"
							rows="1"
						></v-textarea>
					</v-col>
				</v-row>
			</v-form>
		</v-card-text>

		<v-divider class="mb-1 grey lighten-1" />
		<v-card-actions>
			<v-spacer />
			<v-btn class="px-4" color="primary" @click="submit">
				{{ $tr("submit") }}
			</v-btn>
		</v-card-actions>
	</v-card>
</template>

<script>
import ProjectValidations from "../../validations/project-validations";
import TextField from "../common/TextField";
import AutoComplete from "../common/AutoComplete";
import CloseDialogButton from "../common/buttons/CloseDialogButton";

export default {
	name: "ProjectsOperations",
	components: { CloseDialogButton, AutoComplete, TextField },
	props: {
		dialog: {
			type: Boolean,
			required: true,
		},
	},
	data() {
		return {
			registerDialog: false,
			rules: ProjectValidations.rules,
			project: ProjectValidations.schema,
		};
	},

	methods: {
		submit() {
			this.$refs.form.validate();
		},
	},

	validations: {
		project: ProjectValidations.validations,
	},
};
</script>

<style scoped></style>
