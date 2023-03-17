<template>
	<div class="mt-10" style="height: 90%" :key="key">
		<v-card-title>
			<CTitle :text="`upload_only_excel`" />
			<v-spacer></v-spacer>
			<v-btn
				v-if="form.$model.check_type == 'ad'"
				color="primary"
				class="download-button px-3"
				style="min-width: 160px"
				@click="downloadTemplate"
				:loading="isFileDownloaded"
			>
				<template v-slot:loader>
					<span>
						<span>
							{{ $tr("downloading") }}
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
				<template>
					<div>
						<span> {{ $tr("download_format") }}</span>
						<i class="fas fa-download ml-1"></i>
					</div>
				</template>
			</v-btn>
			<div
				class="px-4"
				style="max-width: 350px; font-weight: normal !important"
				v-else
			>
				<FilterDateRange
					:dateRangeProp="filterByDate"
					:hide-title="true"
					@dateChanged="onDateRangeSubmit"
				/>
			</div>
		</v-card-title>

		<!-- <CDatePicker
			title="Arrival time"
			v-model="form.dateRange.$model"
			:icon="'mdi-calendar-clock'"
			:placeholder="$tr('date')"
			:rules="validateRule(form.dateRange, $root, 'Date')"
			@blur="form.dateRange.$touch()"
			:invalid="form.dateRange.$invalid"
			:minDate="minDate"
		/> -->
		<CFileUpload
			:accept="'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'"
			py="py-3"
			:fileIcon="true"
			v-model="form.file.$model"
			:isUpload="false"
			:dropFileType="'Excel Sheet'"
			:form="form.$model"
			:rules="validateRule(form.file, $root, $tr('file'))"
			@click="addClick"
		/>
	</div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CFileUpload from "../../new_form_components/Inputs/CFileUpload.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import FilterDateRange from "../FilterDateRange.vue";
import moment from "moment";

export default {
	components: { CFileUpload, CTitle, FilterDateRange },
	props: {
		form: Object,
	},

	watch: {},
	data() {
		return {
			isFileDownloaded: false,
			key: 0,
			validateRule: GlobalRules.validate,
			filterByDate: {
				start_date: moment().format("YYYY-MM-DD"),
				end_date: moment().format("YYYY-MM-DD"),
			},
			minDate: moment().format("YYYY-MM-DD"),
		};
	},
	methods: {
		async validate() {
			this.form.file.$touch();
			return Array.isArray(this.form.file.$model)
				? !this.form.file.$invalid
				: false;
		},
		async loaded() {
			this.key++;
		},
		addClick() {
			this.form.$model.file = null;
		},

		async downloadTemplate() {
			if (!this.isFileDownloaded) {
				try {
					this.isFileDownloaded = true;
					const response = await this.$axios.get(
						"/advertisement/checking-ad-id-template"
					);
					const url = response.data;
					window.location.href = url;
				} catch (error) {
					HandleException.handleApiException(this, error);
				}
				this.isFileDownloaded = false;
			}
		},
		onDateRangeSubmit(range) {
			this.form.dateRange.$model = range;
		},
	},
};
</script>

<style></style>
