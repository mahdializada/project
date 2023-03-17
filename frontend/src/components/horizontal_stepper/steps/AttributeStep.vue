<template>
	<div class="w-full h-full d-flex align-center-justify-center pt-5">
		<div class="w-full h-full">
			<div class="mb-2 d-flex">
				<div class="me-md-1 w-half">
					<CSelect
						title="Arrival time"
						v-model="selectedItems"
						:items="selectItems"
						:icon="'mdi-calendar-clock'"
						:placeholder="'End Date'"
						:tooltip="'This is sample tooltip here'"
					/>
				</div>
				<div class="ms-md-1 w-half">
					<CDatePicker
						title="Arrival time"
						v-model="form.date_single.$model"
						:icon="'mdi-calendar-clock'"
						:placeholder="'Date'"
						:rules="validateRule(form.date_single, $root, 'Date')"
					/>
				</div>
			</div>
			<div class="mb-2 d-flex">
				<div class="me-md-1 w-full">
					<CDateRangePicker
						title="Arrival time"
						v-model="form.date.$model"
						:icon="['mdi-calendar-clock', 'mdi-calendar-clock']"
						:placeholder="['Start Date', 'End Date']"
						:rules="[
							validateRule(form.date.start_date, $root, 'Start date'),
							validateRule(form.date.end_date, $root, 'End date'),
						]"
					/>
				</div>
				<div class="ms-md-1 w-full">
					<CDatePicker
						title="Arrival time"
						v-model="form.date_single.$model"
						:icon="'mdi-calendar-clock'"
						:placeholder="'Date'"
						:rules="validateRule(form.date_single, $root, 'Date')"
					/>
				</div>
			</div>
			<div class="mb-2 d-flex">
				<div class="me-md-1 w-full">
					<CDateTimePicker
						title="Arrival time"
						v-model="form.date_time.$model"
						:icon="['mdi-calendar-clock', 'mdi-calendar-clock']"
						:placeholder="['Start Date', 'End Date']"
						:rules="[
							validateRule(form.date.start_date, $root, 'Start date'),
							validateRule(form.date.end_date, $root, 'End date'),
						]"
					/>
				</div>
				<div class="ms-md-1 w-full">
					<CDateTimePicker
						title="Arrival time"
						v-model="form.date_time.$model"
						:icon="['mdi-calendar-clock', 'mdi-calendar-clock']"
						:placeholder="['Start Date', 'End Date']"
						:rules="[
							validateRule(form.date_time.date, $root, 'Start date'),
							validateRule(form.date_time.time, $root, 'End date'),
						]"
					/>
				</div>
			</div>
			<div class="mb-3">
				<CAttributeSelection
					:title="'Attributes'"
					:sub_title="'Please Select what attributes does your product have.'"
				/>
			</div>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CAttributeSelection from "../../new_form_components/Inputs/CAttributeSelection.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CDateRangePicker from "../../new_form_components/Inputs/CDateRangePicker.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CDatePicker from "../../new_form_components/Inputs/CDatePicker.vue";
import CDateTimePicker from "../../new_form_components/Inputs/CDateTimePicker.vue";
import CSelect from "../../new_form_components/Inputs/CSelect.vue";

export default {
	props: {
		form: Object,
	},
	data() {
		return {
			validateRule: GlobalRules.validate,
			date: {
				start_date: "",
				end_date: "",
			},
			selectedItems: "hello",
			selectItems: [
				"hello",
				"how are you?",
				"where are you from",
				"is this ok?",
				"I am from Afghanistan",
				"Realy?",
				"That is awsome?",
				"I am from Russia",
				"No way! I like Russia",
				"It is a very good country",
				"Yes I know",
				"Yes I know",
			],
		};
	},
	methods: {
		async loaded() {},
		async validate() {
			this.form.date.start_date.$touch();
			this.form.date.end_date.$touch();
			this.form.date_time.date.$touch();
			this.form.date_time.time.$touch();
			return true;
		},
	},
	watch: {
		selectedItems(value) {
		},
	},
	components: {
		InputCard,
		CAttributeSelection,
		CDateRangePicker,
		CTextField,
		CDatePicker,
		CDateTimePicker,
		CSelect,
	},
};
</script>
