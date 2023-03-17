<template>
	<InputCard v-bind="$attrs" v-on="$listeners" pbNone>
		<div class="d-flex">
			<v-menu
				ref="menu"
				v-model="startDateMenu"
				:close-on-content-click="false"
				transition="scale-transition"
				offset-y
				min-width="auto"
			>
				<template v-slot:activator="{ on, attrs }">
					<v-text-field
						v-model="value.date"
						v-bind="attrs"
						v-on="on"
						:placeholder="placeholder[0]"
						readonly
						background-color="var(--new-input-bg)"
						outlined
						rounded
						:class="`form-new-item start-date`"
						:prepend-inner-icon="icon[0]"
						:rules="rules[0]"
					></v-text-field>
				</template>
				<v-date-picker
					v-model="value.date"
					no-title
					@input="startDateMenu = false"
				>
				</v-date-picker>
			</v-menu>
			<v-menu
				ref="menu"
				v-model="timeMenu"
				@input="timeMenuInput"
				:close-on-content-click="false"
				transition="scale-transition"
				offset-y
				min-width="260px"
			>
				<template v-slot:activator="{ on, attrs }">
					<v-text-field
						:value="value.time"
						v-bind="attrs"
						v-on="on"
						:placeholder="placeholder[1]"
						readonly
						background-color="var(--new-input-bg)"
						outlined
						rounded
						:class="`form-new-item end-date`"
						:prepend-inner-icon="icon[1]"
						:rules="rules[1]"
					></v-text-field>
				</template>
				<div
					class="time-picker-custom w-full background py-1 px-3 d-flex align-center justify-center"
					:key="key"
				>
					<HourPicker v-model="hour" />
					:
					<MinutePicker v-model="minute" />
					<AmPmPicker v-model="amPm" />
				</div>
			</v-menu>
		</div>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
import HourPicker from "../components/HourPicker.vue";
import MinutePicker from "../components/MinutePicker.vue";
import AmPmPicker from "../components/AmPmPicker.vue";
export default {
	props: {
		value: Object,
		icon: Array,
		rules: Array,
		placeholder: Array,
	},
	data() {
		return {
			startDateMenu: false,
			timeMenu: false,
			hour: 12,
			minute: 0,
			amPm: "pm",
			key: 0,
		};
	},
	methods: {
		pad(d) {
			return d < 10 ? "0" + d.toString() : d.toString();
		},
		timeMenuInput(value) {
			this.key++;
		},
	},
	watch: {
		hour() {
			this.value.time = `${this.pad(this.hour)}:${this.pad(
				this.minute
			)} ${this.pad(this.amPm)}`;
		},
		minute() {
			this.value.time = `${this.pad(this.hour)}:${this.pad(
				this.minute
			)} ${this.pad(this.amPm)}`;
		},
		amPm() {
			this.value.time = `${this.pad(this.hour)}:${this.pad(
				this.minute
			)} ${this.pad(this.amPm)}`;
		},
	},
	components: {
		InputCard,
		HourPicker,
		MinutePicker,
		AmPmPicker,
	},
};
</script>
<style>
.form-new-item.start-date {
	border-top-right-radius: 0 !important;
	border-bottom-right-radius: 0 !important;
}
.form-new-item.end-date {
	border-top-left-radius: 0 !important;
	border-bottom-left-radius: 0 !important;
}
.form-new-item.start-date::after {
	content: "";
	display: block;
	height: 30px;
	width: 4px;
	background: #dddcdc;
	border-radius: 4px;
	position: absolute;
	right: -2px;
	top: 13px;
}
.v-application--is-rtl .form-new-item.start-date {
	border-top-right-radius: 28px !important;
	border-bottom-right-radius: 28px !important;
	border-top-left-radius: 0 !important;
	border-bottom-left-radius: 0 !important;
}
.v-application--is-rtl .form-new-item.end-date {
	border-top-left-radius: 28px !important;
	border-bottom-left-radius: 28px !important;
	border-top-right-radius: 0 !important;
	border-bottom-right-radius: 0 !important;
}
.v-application--is-rtl .form-new-item.start-date::after {
	left: -2px;
	right: unset;
}
</style>
