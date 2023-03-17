import Rules from "./index";
import Validations from "../validations";

export const TeamRules = {
	numberOfPeopleRule(validationObject, context) {
		const requiredText = context.$tr(
			"item_is_required",
			context.$tr("project")
		);
		return [(_) => validationObject.number_of_people.required || requiredText];
	},
	countryRule: Rules.countryRule,
	nameRule(validationObject, context) {
		const requiredText = context.$tr(
			"item_is_required",
			context.$tr("team.name")
		);
		const minLengthText = context.$tr(
			"min_length",
			context.$tr("team.name"),
			validationObject.name.$params.minLength.min
		);
		const maxLength = context.$tr(
			"ax_length",
			context.$tr("team.name"),
			validationObject.name.$params.maxLength.max
		);
		return [
			(_) => validationObject.name.required || requiredText,
			(_) => validationObject.name.minLength || minLengthText,
			(_) => validationObject.name.maxLength || maxLength,
		];
	},
};

// User Validations
export const TeamValidations = {
	name: Validations.name100Validation,
	number_of_people: Validations.requiredValidation,
	country_id: Validations.requiredValidation,
	department_id: Validations.requiredValidation,
	note: {},
};
