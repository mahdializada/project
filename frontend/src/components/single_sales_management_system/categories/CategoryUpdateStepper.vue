<template>
	<div>
		<Stepper
			ref="Stepper"
			title="Category"
			cookieName="category_stepper"
			:show="show"
			:steps.sync="steps"
			:form="form"
			:validateRules="validateRules"
			:submit="submit"
			@close="show = false"
			@reset="reset"
		/>
	</div>
</template>
<script>
import Stepper from "../../../components/horizontal_stepper/Stepper.vue"; 
import CategoryInfo from "./Steps/CategoryInfo.vue";
import Validations from "~/validations/validations";
import CategroyAttributes from "./Steps/CategroyAttributes.vue";
import Helpers from "~/helpers/helpers";
export default {
	data() {
		return {
			isEdit:false,
			show: false,
			steps: [ 
				{
					id: "info",
					title: "Info",
					component: CategoryInfo,
				},
				{
					id: "attributes",
					title: "Attributes",
					component: CategroyAttributes,
				},
				
			],
			form: {
				itemType: "",
				selectedCategory: null,
				name: "",
				arabic_name: "",
				description: "",
				arabic_description: "",
				icon: null,
				// banner: null,
				attribute:[],
				// newAttribute:[]
			},
			validateRules: {
				form: { 
					name: Validations.name100Validation,
					arabic_name: Validations.name100Validation,
					itemType: Validations.requiredValidation,
					selectedCategory: '',
					description: "",
					arabic_description: "",
					icon: Validations.requiredValidation,
					// banner: Validations.requiredValidation,
					attribute: Validations.requiredValidation,
					// newAttribute: {
					// 	$each: {
					// 		name: Validations.name100Validation,
					// 		value: "",
					// 	},
					// },
				},
			},
		};
	},
	methods: {
		reset() {
			this.form = {
				itemType: "",
				selectedCategory: null,
				arabic_name: "",
				description: "",
				arabic_description: "",
				icon: null,
				// banner: null,
				attribute:[],
				// newAttribute:[]
			};
		},
		async submit(formRef, vuelidate) { 

			if (!this.form.selectedCategory) {
				this.form.selectedCategory = "";
			}
			try {
				const form=await this._.cloneDeep(this.form);
				form.id=this.form.editData.id; 
				delete this.form.editData;
				delete form.editData;
			    form.attribute= form.attribute.map(attr => attr.id);
				const category = Helpers.getFormData(form);
				const url = "single-sales/categories-ssp";
				const data = await this.$axios.post(url, category);
				 
				if (data.data.result) {
				this.$toasterNA("green", this.$tr('successfully_updated'));
				this.$emit("updateRecord",data.data.category);
				 
				return true;
				}else if(data.data.duplicate_error) {
				 
                let duplicateError='Attribute';
				data.data.duplicate_error.forEach(element => {
					duplicateError=duplicateError+' '+element;
					
				});
				duplicateError=duplicateError+'Already Exist!'; 
				// this.$toastr.e(duplicateError);
				this.$toasterNA("red", this.$tr(duplicateError));

				return;
				} 
				else {
				// this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

				return false;
				}
			} catch (error) {
				 
				console.log('error',error);
			}
		},

		onEditategory(data){
            this.form.editData=data;
            this.form.name=data.name;
            this.form.arabic_name=data.arabic_name;
			this.form.icon=data.icon;
			// this.form.banner=data.banner; 
			this.form.description=data.description;
			this.form.arabic_description=data.arabic_description;
		}
	},
	components: { Stepper },
};
</script>
