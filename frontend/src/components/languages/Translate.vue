<template>
	<v-form lazy-validation ref="translationForm" @submit.prevent="saveAndNext">
		<v-card
			:color="$vuetify.theme.dark ? 'black' : '#f9fafd'"
			class="height-80-vh position-relative"
		>
			<v-card-title class="py-2 ps-3 pb-5 d-flex align-center title">
				<h2 class="translation-title">
					{{
						edit
							? $tr("edit")
							: autoEdit
							? $tr("auto_edit")
							: $tr("translation")
					}}
				</h2>
				<v-spacer />
				<div class="pe-7 mt-1" v-if="data.length > 1">
					<div class="d-flex">
						<p class="ma-0 font-weight-normal num-lable">
							{{ $tr("translation_no") }} :
						</p>
						<p class="ma-0 font-weight-normal ms-2 num-range">
							{{ current + 1 }} / {{ data2.length }}
						</p>
					</div>
					<v-btn-toggle rounded class="ms-auto float-right pt-0" mandatory>
						<v-btn active-class="primary" x-small @click="prev">
							<v-icon x-small>fa-angle-double-left</v-icon>
						</v-btn>
						<v-btn active-class="primary" x-small @click="next">
							<v-icon x-small>fa-angle-double-right</v-icon>
						</v-btn>
					</v-btn-toggle>
				</div>
			</v-card-title>
			<v-card-text class="pa-0 body">
				<v-card
					elevation="0"
					class="pa-4 d-flex justify-center align-center h-full"
					style="z-index: 10000000"
					v-if="data.length > 0"
				>
					<v-card
						elevation="2"
						class="pa-2 pt-12 px-4 translation-card rounded-0"
						v-if="!successMessage"
						style="max-width: 500px"
					>
						<h2 class="mb-4 text-center" style="font-weight: 600 !important">
							{{ this.data2[this.current].base_lang_word.translation }}
						</h2>
						<CustomInput
							autofocus
							:rtl="rtl"
							v-model="$v.obj.translation.$model"
							:rules="rules.translationRule($v.obj, $root, 'translation')"
							language
							label="translation"
							placeholder="translation"
							ref="translationInput"
							type="textfield"
							class="mb-md-2 mb-0"
							:loading="isSubmitting"
						/>
					</v-card>
					<DoneMessage
						v-else-if="successMessage"
						:title="`${
							edit
								? $tr('updated_successfully_', $tr('word_sentence'))
								: autoEdit
								? $tr('updated_successfully_', $tr('words_sentences'))
								: $tr('translated_successfully_', $tr('words_sentences'))
						}`"
						:subTitle="$tr('you_can_access_your_item', $tr('words_sentences'))"
					/>
				</v-card>

				<v-card
					elevation="0"
					class="pa-4 d-flex justify-center align-center text-center rounded-0 h-full"
					style="z-index: 10000000"
					v-if="!data.length"
				>
					<div class="d-flex flex-column align-center">
						<lottie-player
							src="/assets/success.json"
							background="transparent"
							speed="1"
							style="width: 280px; height: 280px"
							loop
							autoplay
						>
						</lottie-player>
						<h2 class="mb-1" style="color: #2cda94">
							{{ $tr("all_words_are_translated") }}
						</h2>
						<h4 style="color: #2cda94">
							{{ $tr("there_are_no_words_to_translate") }}
						</h4>
					</div>
				</v-card>
			</v-card-text>
			<div class="footer-outer pa-md-3 pa-2">
				<div
					:class="`footer py-2 px-1 px-md-8 d-flex ${
						current != 0 && !successMessage
							? 'justify-space-between'
							: 'justify-end'
					}`"
				>
					<v-btn
						class="stepper-btn ms-1 px-3"
						v-if="current != 0 && !successMessage"
						:type="'button'"
						@click="prev"
					>
						<v-icon left small>fa-angle-left</v-icon>
						{{ $tr("prev") }}
					</v-btn>
					<div>
						<v-btn
							color="primary"
							class="stepper-btn ms-1 px-3"
							:type="'button'"
							@click="saveAndNext"
							v-if="data.length > 1 && !successMessage"
						>
							{{ $tr("save_next") }}
							<v-icon right small>fa-angle-double-right</v-icon>
						</v-btn>
						<v-btn
							:color="edit ? 'primary' : ''"
							elevation="2"
							@click="save"
							class="stepper-btn ms-1 px-3"
							:type="'button'"
							v-if="!successMessage && data.length != 0"
						>
							{{ $tr("save") }}
							<v-icon right small>fa-check</v-icon>
						</v-btn>
						<v-btn
							elevation="2"
							class="stepper-btn ms-1 px-3"
							@click="next"
							:type="'button'"
							v-if="data.length > 1 && !successMessage"
						>
							{{ $tr("next") }}
							<v-icon right small>fa-angle-right</v-icon>
						</v-btn>
						<v-btn
							color="primary"
							elevation="2"
							class="stepper-btn ms-1 px-3"
							@click="close"
							:type="'button'"
							v-if="successMessage || data.length == 0"
						>
							{{ $tr("close") }}
							<v-icon right small>fa-times</v-icon>
						</v-btn>
					</div>
				</div>
			</div>
		</v-card>
	</v-form>
</template>
<script>
import CustomStepper from "~/components/design/FormStepper/CustomStepper.vue";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import { mapActions, mapGetters } from "vuex";
import CustomInput from "~/components/design/components/CustomInput.vue";
import Validations from "~/validations/language_validations.js";

export default {
	components: {
		CustomStepper,
		DoneMessage,
		CustomInput,
	},
	validations: {
		obj: Validations.validations.language,
	},

	data() {
		return {
			rules: Validations.rules,
			obj: {
				translation: "",
			},
			current: 0,
			successMessage: false,
			isSubmitting: false,
		};
	},

	props: {
		data: {
			type: Array,
		},
		language: {
			type: Object,
		},
		edit: {
			type: Boolean,
			default: false,
		},
		autoEdit: {
			type: Boolean,
			default: false,
		},
		rtl: {
			type: Boolean,
			default: false,
		},
	},

	async created() {
		if (this.data.length > 0) {
			this.data2 = JSON.parse(JSON.stringify(this.data));
			this.setTranslation();
		}
	},
	computed: {
		...mapGetters({}),
	},
	methods: {
		...mapActions({}),
		next() {
			if (this.current !== this.data2.length - 1) {
				this.current += 1;
			}
			this.$refs.translationForm.resetValidation();
			this.setTranslation();
		},
		prev() {
			if (this.current !== 0) {
				this.current -= 1;
			}
			this.$refs.translationForm.resetValidation();
			this.setTranslation();
		},
		close() {
			this.$emit("close");
		},
		setTranslation() {
			this.obj.translation = this.data2[this.current].lang_word
				? this.data2[this.current].lang_word.translation
				: "";
		},
		async save() {
			this.$refs.translationForm.validate();
			if (!this.$v.obj.translation.$invalid) {
				try {
					this.isSubmitting = true;
					const response = await this.$axios.post("translations", {
						phrase_id: this.data2[this.current].id,
						translation: this.obj.translation,
						translated_language_id: this.language.id,
					});
					if (response?.status === 201) {
						this.$emit("insertRecord", response?.data?.data);
						this.insertRecord(response?.data?.data);
						if (this.edit) {
							this.$toasterNA("green", this.$tr('successfully_updated'));
							this.success();
						} else if (this.autoEdit) {
							this.$toasterNA("green", this.$tr('successfully_updated'));
							this.success();
						} else {
							// this.$toastr.s(this.$tr("successfully_translated"));
				this.$toasterNA("green", this.$tr('successfully_translated'));

							this.success();
						}
						this.isSubmitting = false;
						return true;
					} else {
						// this.$toasterNA("red", this.$tr('something_went_wrong'));
					this.$toasterNA("red", this.$tr('something_went_wrong'));

						this.isSubmitting = false;
						return false;
					}
				} catch (error) {
					this.isSubmitting = false;
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
					this.$toasterNA("red", this.$tr('something_went_wrong'));

					return false;
				}
			} else {
				return false;
			}
		},
		insertRecord(response) {
			this.data2 = this.data2.map((item) => {
				if (item.id == response.phrase_id) {
					item.lang_word = response;
				}
				return item;
			});
		},

		async saveAndNext() {
			let res = await this.save();
			if (res) {
				this.next();
			}
		},

		success() {
			if (this.current == this.data2.length - 1) {
				this.successMessage = true;
			}
		},
	},
};
</script>
<style scoped>
.title {
	position: absolute;
	top: 0;
	right: 0;
	left: 0;
}
.body {
	position: absolute;
	top: 100px;
	left: 0;
	right: 0;
	bottom: 100px;
}
.translation-title {
	font-size: 22px;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 2px;
	color: var(--close-btn-icon-color);
}
.translation-card {
	min-width: 700px;
}
@media screen and (max-width: 800px) {
	.translation-card {
		min-width: auto;
	}
}
.footer-outer {
	background: var(--bg-white);
	position: absolute;
	bottom: 0;
	right: 0;
	left: 0;
}
.footer {
	background: var(--stepper-header-bg);
}
.stepper-btn {
	font-size: 16px;
	height: 38px !important;
	font-weight: 500;
	border-radius: 4px;
}
.num-lable {
	font-weight: 400;
	font-size: 16px;
}
.num-range {
	font-weight: 600;
	font-size: 16px;
}
</style>
