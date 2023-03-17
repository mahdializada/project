<template>
	<div>
		<div class="h-full d-flex align-center flex-column justify-center mt-5">
			<CTitle :text="`reasons`" />

			<InputCard
				style="height: 580px"
				:title="$tr('reasons')"
				:sub_title="$tr('you can add one or more reasons if you want')">
				<template>
					<v-textarea
						:class="`form-new-item form-custom-text-area ${
							hasItems ? 'has-append' : ''
						}`"
						background-color="var(--new-input-bg)"
						outlined
						counter
						hide-details
						v-model="title"
						ref="textAreaRefs"
						prepend-inner-icon="mdi-text-box "
						:rows="5"
						no-resize>
						<template v-if="hasItems" slot="append" class="pe-0">
							<v-btn fab small color="primary" class="ma-0" @click="addItem">
								<v-icon dark size="20"> mdi-plus </v-icon>
							</v-btn>
						</template>
					</v-textarea>
				</template>
				<div
					style="height: 300px !important"
					class="overflow-auto"
					:class="
						form.titles.$model.length == 0
							? 'd-flex align-center justify-center'
							: ''
					">
					<div v-if="form.titles.$model.length > 0">
						<span v-for="(item, i) in form.titles.$model" :key="i">
							<v-textarea
								:class="`form-new-item form-custom-text-area  customTextArea${
									hasItems ? 'has-append' : ''
								}`"
								style="border: 1px solid; border-radius: 13px"
								outlined
								v-model="item.value"
								:value="item.value"
								hide-details
								class="my-1"
								prepend-inner-icon="mdi-text-box"
								:rows="3"
								:disabled="item.disabled"
								@blur="itemBlur(item.value, i)"
								auto-grow>
								<template v-if="hasItems" slot="append" class="pe-0">
									<div class="">
										<v-icon color="primary" size="20" @click="removeItem(i)"
											>mdi-delete</v-icon
										>
										<v-divider class="grey my-1"></v-divider>
										<v-icon color="primary" size="20" @click="editItem(i)"
											>mdi-square-edit-outline</v-icon
										>
									</div>
								</template>
							</v-textarea>
						</span>
					</div>
					<div v-else class="d-flex justify-center align-center flex-column">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="80"
							height="80"
							viewBox="0 0 41.348 47.204">
							<g
								id="no_-reason"
								data-name="no -reason"
								transform="translate(-1226.018 -591.21)">
								<path
									id="Path_320"
									data-name="Path 320"
									d="M5.915,3.546c0-.695-.021-1.284,0-1.872A1.591,1.591,0,1,1,9.1,1.69c.024.586,0,1.174,0,1.808H13.42c0-.608-.009-1.215,0-1.821A1.588,1.588,0,0,1,15,0a1.544,1.544,0,0,1,1.545,1.622c.017.61,0,1.221,0,1.876h4.374c0-.592-.01-1.2,0-1.8A1.554,1.554,0,0,1,22.468,0a1.6,1.6,0,0,1,1.584,1.747c.006.564,0,1.128,0,1.8.542,0,1.048,0,1.553,0a4.321,4.321,0,0,1,4.354,4.36q.01,12.78,0,25.56A4.312,4.312,0,0,1,25.6,37.824q-10.524.011-21.048,0A4.357,4.357,0,0,1,.008,33.272c-.014-5.713,0-11.426,0-17.138,0-2.635,0-5.27,0-7.9A4.389,4.389,0,0,1,4.722,3.546H5.915m9.871,21.692c-2.559,0-5.118-.03-7.675.026a2.422,2.422,0,0,0-1.418.582,1.292,1.292,0,0,0-.2,1.635,1.622,1.622,0,0,0,1.57.9q7.676-.011,15.352-.005a1.623,1.623,0,0,0,1.822-1.589c-.007-.955-.7-1.541-1.849-1.543q-3.8-.008-7.6,0m-.028-3.192c2.534,0,5.068.006,7.6-.007a2.619,2.619,0,0,0,1-.165,1.524,1.524,0,0,0,.848-1.712,1.624,1.624,0,0,0-1.691-1.249q-7.676-.008-15.351,0a1.628,1.628,0,0,0-1.841,1.57c0,.956.692,1.56,1.83,1.563,2.534.006,5.068,0,7.6,0M11.9,12.59c-1.253,0-2.507-.011-3.76,0a1.606,1.606,0,1,0,.026,3.181q3.686.016,7.372,0a1.608,1.608,0,1,0-.026-3.181c-1.2-.012-2.408,0-3.612,0"
									transform="translate(1226.018 600.586)"
									fill="#2e7be6" />
								<path
									id="Path_1322"
									data-name="Path 1322"
									d="M20462.689-12316.533a1.346,1.346,0,0,1,1.166-1.535,16.851,16.851,0,0,1,3.211-.3,1.84,1.84,0,0,1,1.3.8c.242.4.1,1.017.129,1.562a4.946,4.946,0,0,1-1.2.564,30.564,30.564,0,0,1-3.133.263h-.051A1.383,1.383,0,0,1,20462.689-12316.533Zm-3.525-6.354a1.463,1.463,0,0,1-.328-2.055c.58-.832,1.191-1.648,1.834-2.435a1.45,1.45,0,0,1,2.064-.257,1.486,1.486,0,0,1,.273,2.065c-.277.41-.592.791-.889,1.187s-.576.8-.895,1.182a1.587,1.587,0,0,1-1.205.6A1.442,1.442,0,0,1,20459.164-12322.888Zm-7.611-1.68a1.711,1.711,0,0,1-1.111-.935,18.7,18.7,0,0,1-.672-3.267,1.338,1.338,0,0,1,1.09-1.521,1.256,1.256,0,0,1,1.668.792,29.527,29.527,0,0,1,.678,3.637c.037.241-.148.519-.3.971a4.916,4.916,0,0,1-1.182.343A.741.741,0,0,1,20451.553-12324.567Z"
									transform="translate(-19201.645 12922.093)"
									fill="#2e7be6"
									stroke="rgba(0,0,0,0)"
									stroke-width="1"
									opacity="0.3" />
							</g>
						</svg>
						<p class="mt-2">{{$tr('no_reasons_yet')}}</p>
					</div>
				</div>
			</InputCard>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";

export default {
	name: "SectionStep",
	props: {
		form: Object, // default prop
	},
	data() {
		return {
			validateRule: GlobalRules.validate, // gloabl function fro validate
			title: "",
			hasItems: true,
			disabled: true,
		};
	},
	methods: {
		async validate() {
			// validate function must validate this step here and return true of false
			if (this.form.$model.titles.length > 0) {
				return true;
			}
			return false;
		},

		addItem() {
			if (this.title.length > 0) {
				this.form.$model.titles.push({ disabled: true, value: this.title });
				this.title = "";
			}
		},
		removeItem(index) {
			this.form.$model.titles = this.form.$model.titles.filter(
				(item, i) => i !== index,
			);
		},
		editItem(i) {
			this.form.$model.titles[i].disabled = false;
		},
		itemBlur(item, i) {
			this.form.$model.titles[i].disabled = true;
			this.form.$model.titles[i].value = item;
			console.log(this.form.$model.titles);
		},
	},

	components: { CTitle, InputCard },
};
</script>

<style>
.form-custom-text-area .v-input__prepend-inner {
	margin-top: 12px;
	margin-right: 8px !important;
}
.form-custom-text-area .v-input__slot,
.form-custom-text-area .v-input__slot fieldset {
	border-radius: 12px !important;
}
.form-custom-text-area .v-counter {
	position: absolute;
	top: -20px;
	right: 8px;
	padding: 0;
	color: var(--v-primary-base);
}
.v-application--is-rtl .form-custom-text-area .v-counter {
	right: unset;
	left: 8px;
}
.form-custom-text-area.has-append .v-input__append-inner {
	margin-top: auto !important;
	margin-bottom: auto !important;
}
.customTextArea .v-input__icon.v-input__icon--prepend-inner .v-icon {
	color: var(--v-primary-base) !important;
	margin-top: auto !important;
	margin-bottom: auto !important;
}
</style>
