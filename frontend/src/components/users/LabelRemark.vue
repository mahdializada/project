<template>
	<div>
		<div class="d-flex">
			<CustomInput
				:label="$tr('label_required', $tr('note'))"
				placeholder="note"
				type="textarea"
				v-model="itemData.note.$model"
				:rules="validate(itemData.note, $root, 'note')"
				class="mb-0 w-full"
				rows="4"
			/>
		</div>
	</div>
</template>

<script>
import CustomInput from "../design/components/CustomInput";
import GlobalRules from "~/helpers/vuelidate";

export default {
	name: "LabelRemark",
	components: { CustomInput },

	props: {
		item: {
			required: true,
			type: Object,
		},
	},

	data() {
		return {
			validate: GlobalRules.validate,
			itemData: this.item,
		};
	},
	methods: {
		// check validations
		checkValidation() {	
			return !this.itemData.note.$invalid;;
		},
	},
};
</script>

<style scoped></style>
