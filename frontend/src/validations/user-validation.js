import Validations, { validPassword } from './validations';
import Rules from "./rules-new";
import { minLength, requiredIf, sameAs } from "vuelidate/lib/validators";
import required from "vuelidate/lib/validators/required";

export default {
	steppers: [
		{
			icon: "fa-lock",
			title: "general",
			slotName: "step1",
		},
		{
			icon: "fa-lock",
			title: "account",
			slotName: "step2",
		},
		{
			icon: "fa-user",
			title: "personal",
			slotName: "step3",
		},
		{
			icon: "fa-user-shield",
			title: "roles",
			slotName: "step4",
		},
		{
			icon: "fa-comment-dots",
			title: "remarks",
			slotName: "step5",
		},
		{
			icon: "fa-thumbs-up",
			title: "done",
			slotName: "step6",
		},
	],

	// model schema
	schema: {
		firstname: "",
		lastname: "",
		username: "",
		email: "",
		password: "",
		confirm_password: "",
		phone: "",
		whatsapp: "",
		gender: "",
		birth_date: "",
		image: "",
		address: "",
		change_password: true,
		email_password: true,
		generate_password: true,
		note: "",

		// relations
		language_id: "",
		state_id: "",
		country_id: "",
		current_country_id: "",
	},

	// roles
	rules: {
		firstnameRule: Rules.firstnameRule,
		lastnameRule: Rules.lastnameRule,
		usernameRule: Rules.usernameRule,
		phoneRule: Rules.phoneRule,
		emailRule: Rules.emailRule,
		dobRule: Rules.dobRule,
		passwordRule: Rules.passwordRule,
		password2Rule: Rules.password2Rule,
		whatsappRule: Rules.whatsappRule,
		countryRule: Rules.countryRule,
		genderRule: Rules.genderRule,
		addressRule: Rules.addressRule,
		imageRule: Rules.imageRule,
		noteRule: Rules.noteRule,
		languageRule: Rules.languageRule,
		countryStateRule: Rules.countryStateRule,
		currentCountryRule(validationObject, context) {
			const requiredText = context.$tr(
				"item_is_required",
				context.$tr("current_country")
			);
			return [
				(_) => validationObject.current_country_id.required || requiredText,
			];
		},
	},

	// validations
	validations: {
		firstname: Validations.name100Validation,
		lastname: Validations.name100Validation,
		username: Validations.usernameValidation,
		birth_date: Validations.requiredValidation,
		address: Validations.requiredValidation,
		phone: Validations.phoneValidation,
		whatsapp: Validations.phoneValidation,
		gender: Validations.requiredValidation,
		image: Validations.imageValidation,
		email: Validations.emailValidation,
		password: {
			required: requiredIf(function (value) {
				return !value.generate_password;
			}),
			validPassword,
			minLength: minLength(6),
		},
		confirm_password: {
			required: requiredIf(function (value) {
				return !value.generate_password;
			}),
			validPassword,
			sameAsPassword: sameAs("password"),
			minLength: minLength(6),
		},
		generate_password: Validations.requiredValidation,
		change_password: Validations.requiredValidation,
		email_password: Validations.requiredValidation,
		note: Validations.remarksValidation,

		// relations
		country_id: Validations.requiredValidation,
		language_id: Validations.requiredValidation,
		state_id: Validations.requiredValidation,
		current_country_id: Validations.requiredValidation,
	},
};
