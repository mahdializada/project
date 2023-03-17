<template>
	<v-dialog v-model="showReasonDialog" persistent width="800" scrollable>
		<v-card :color="$vuetify.theme.dark ? 'black' : '#f9fafd'">
			<v-card-title class="py-1">
				<v-app-bar-title>
					<span class="custom-dialog-title">{{
						$tr('reasons')
					}}</span>
				</v-app-bar-title>
				<v-spacer />
				<CloseBtn @click="closeDialog" />
			</v-card-title>
			<v-card-text>
				<v-row>
					<v-card elevation="0" class="ma-3 mt-0 w-full mb-5">
						<div class="text-center pt-2">
                            <span class="text-subtitle-1">
								{{ $tr(title) }}
                            </span>
						</div>
						<v-divider class="gray lighten-3 mt-1"></v-divider>
						<v-form
							ref="form"
							lazy-validation
						>
							<div class="pa-3" v-if="isMultiple">
								<div v-if="allReasons.length === 0" class="py-5 d-flex align-center justify-center">
									{{ $tr('no_reasons_available') }}
								</div>
								<span class="py-0" v-for="reason, index in allReasons" :key="index">
									<v-checkbox
										multiple
										validate-on-blur
										dense
										v-model="selectedReasons"
										hide-details
										:value="reason.reason_id"
										:label="reason.title"
										:error-messages="errorMessages"
										@change="$v.selectedReasons.$touch()"
									></v-checkbox>
								</span>					
							</div>	
							<v-row v-else class="pt-3 ps-3">
								<v-col cols="12" class="py-0">
									<CustomInput
										:loading="isFetchingReasons"
										:items="allReasons"
										v-model="selectedReasons"
										:label="$tr('label_required', $tr('reason'))"
										:error-messages="errorMessages"
										@input="$v.selectedReasons.$touch()"
										@blur="$v.selectedReasons.$touch()"
										type="autocomplete"
										item-text="title"
										item-value="reason_id"
										:placeholder="$tr('choose_item', $tr('reason'))"
										class="me-md-4 mb-md-2 mb-0"
									/>
								</v-col>
							</v-row>						
						</v-form>
					</v-card>
					<v-card elevation="0" class="mx-3 pa-2 w-full mt-5">
						<TextButton
							type="secondary"
							class="ms-1 float-end"
							:text="$tr('cancel')"
							@click="closeDialog"
						>
						</TextButton>
						<TextButton
							type="primary"
							class="float-end"
							:text="$tr('submit')"
							@click="onSubmit"
						>
						</TextButton>
					</v-card>
				</v-row>
			</v-card-text>
		</v-card>
	</v-dialog>
</template>


<script>
import CloseBtn from "./../../components/design/Dialog/CloseBtn.vue";
import TextButton from "./../../components/common/buttons/TextButton.vue";
import CustomInput from "./../../components/design/components/CustomInput.vue";
import { required } from "vuelidate/lib/validators";
export default {
    name: "ReasonDialog",
    props: {
        showReasonDialog:{
            type: Boolean,
            default: false
        },
		isFetchingReasons:{
            type: Boolean,
            default: false
        },
        allReasons: {
            type: Array,
            default: [],
        },
        title: {
            type: String,
            default: '',
        },
		isMultiple: {
			type: Boolean,
			default: false
		}
    },
    components: {
		CustomInput,
		TextButton,
		CloseBtn,
	},
	data() {
		return {
			selectedReasons: [],	
		};
	},

	computed: {
		errorMessages() {
			const errors = [];
			if (!this.$v.selectedReasons.$dirty) return errors;
			!this.$v.selectedReasons.required &&
				errors.push(this.$tr("please_select_one_option"));
			return errors;
		},
	},

	methods:{
		onSubmit(){
			this.$v.$touch();
			if(!this.$v.$invalid){
				this.$emit('onSubmit', this.selectedReasons);
				this.resetForm();
			}else this.$toasterNA("orange", this.$tr("please_select_one_option"));
			
		},

		resetForm(){		
			this.selectedReasons = [];
			this.$v.$reset();
		},

		closeDialog(){
			this.resetForm();
			this.$emit('closeDialog');
		}
	},

	validations: {
		selectedReasons: { required },
	},
}
</script>