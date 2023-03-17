const Rules = {
	nameRule(validationObject, context) {
		const requiredText = context.$tr(
			"item_is_required",
			context.$tr("permission")
		);
		const minLengthText = context.$tr("min_length", [
			context.$tr("permission"),
			validationObject.name.$params.minLength.min,
		]);
		const maxLength = context.$tr("max_length", [
			context.$tr("permission"),
			validationObject.name.$params.maxLength.max,
		]);
		return [
			(_) => validationObject.name.required || requiredText,
			(_) => validationObject.name.minLength || minLengthText,
			(_) => validationObject.name.maxLength || maxLength,
		];
	},
	projectNameRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("project"),
		]);
		const minLengthText = context.$tr("min_length", [
			context.$tr("project"),
			validationObject.name.$params.minLength.min,
		]);
		const maxLength = context.$tr("max_length", [
			context.$tr("project"),
			validationObject.name.$params.maxLength.max,
		]);
		return [
			(_) => validationObject.name.required || requiredText,
			(_) => validationObject.name.minLength || minLengthText,
			(_) => validationObject.name.maxLength || maxLength,
		];
	},
	roleNameRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("role_name"),
		]);
		const minLengthText = context.$tr("min_length", [
			context.$tr("role_name"),
			validationObject.name.$params.minLength.min,
		]);
		const maxLength = context.$tr("max_length", [
			context.$tr("role_name"),
			validationObject.name.$params.maxLength.max,
		]);
		return [
			(_) => validationObject.name.required || requiredText,
			(_) => validationObject.name.minLength || minLengthText,
			(_) => validationObject.name.maxLength || maxLength,
		];
	},
	permissionIdRule(validationObject, context) {
		let arr = [1, 2, 3, 4];
		const ifError = validationObject.$model.permission_ids
			.map((p) => p.checked)
			.some((item) => arr.includes(item));
		const requiredText = context.$tr("item_is_required", [
			context.$tr("item_id", context.$tr("permission")),
		]);
		if (ifError) {
			return [(_) => true];
		} else {
			return [(_) => validationObject.permissionId.required || requiredText];
		}
	},

	firstnameRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("firstname"),
		]);
		const minLengthText = context.$tr("min_length", [
			context.$tr("firstname"),
			validationObject.firstname.$params.minLength.min,
		]);
		const maxLength = context.$tr("max_length", [
			context.$tr("firstname"),
			validationObject.firstname.$params.maxLength.max,
		]);
		return [
			(_) => validationObject.firstname.required || requiredText,
			(_) => validationObject.firstname.minLength || minLengthText,
			(_) => validationObject.firstname.maxLength || maxLength,
		];
	},
	lastnameRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("lastname"),
		]);
		const minLengthText = context.$tr("min_length", [
			context.$tr("lastname"),
			validationObject.lastname.$params.minLength.min,
		]);
		const maxLength = context.$tr("max_length", [
			context.$tr("lastname"),
			validationObject.lastname.$params.maxLength.max,
		]);

		return [
			(_) => validationObject.lastname.required || requiredText,
			(_) => validationObject.lastname.minLength || minLengthText,
			(_) => validationObject.lastname.maxLength || maxLength,
		];
	},

	phoneRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("phone"),
		]);
		const invalidPhone = context.$tr("item_is_invalid", [context.$tr("phone")]);
		return [
			validationObject.phone.required || requiredText,
			validationObject.phone.phoneNumber || invalidPhone,
		];
	},
	whatsappRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("whatsapp"),
		]);
		const invalidWhatsApp = context.$tr("item_is_invalid", [
			context.$tr("whatsapp"),
		]);

		return [
			validationObject.whatsapp.required || requiredText,
			validationObject.whatsapp.phoneNumber || invalidWhatsApp,
		];
	},

	emailRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("email"),
		]);
		const invalidEmail = context.$tr("item_is_invalid", [context.$tr("email")]);

		return [
			(_) => validationObject.email.required || requiredText,
			(_) => validationObject.email.email || invalidEmail,
		];
	},

	dobRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("birthday_date"),
		]);
		return [(_) => validationObject.birth_date.required || requiredText];
	},

	countryRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("country"),
		]);

		return [(_) => validationObject.country_id.required || requiredText];
	},

	projectRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("project"),
		]);

		return [(_) => validationObject.projects.required || requiredText];
	},
	departmentRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("department.department"),
		]);

		return [(_) => validationObject.department_id.required || requiredText];
	},

	genderRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("gender"),
		]);

		return [(_) => validationObject.gender.required || requiredText];
	},

	OTPRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("OTP_code"),
		]);

		return [(_) => validationObject.OTP_code.required || requiredText];
	},

	passwordRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("password"),
		]);
		const minLengthText = context.$tr("min_length", [
			context.$tr("password"),
			validationObject.password.$params.minLength.min,
		]);

		return [
			(_) => validationObject.password.required || requiredText,
			(_) => validationObject.password.minLength || minLengthText,
		];
	},
	password2Rule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("confirm_password"),
		]);
		const minLengthText = context.$tr("min_length", [
			context.$tr("confirm_password"),
			validationObject.password.$params.minLength.min,
		]);
		const sameAsText = context.$tr("same_password", [
			context.$tr("confirm_password"),
		]);

		return [
			(_) => validationObject.confirm_password.required || requiredText,
			(_) => validationObject.confirm_password.sameAsPassword || sameAsText,
			(_) => validationObject.confirm_password.minLength || minLengthText,
		];
	},

	addressRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("address"),
		]);
		return [(_) => validationObject.address.required || requiredText];
	},

	requiredBusinessType(validationObject, context) {
		return [
			(_) =>
				validationObject.business_type_id.required ||
				context.$tr("item_is_required", context.$tr("business_type")),
		];
	},

	requiredInvestmentType(validationObject, context) {
		return [
			(_) =>
				validationObject.investmentType.required ||
				context.$tr("investment_type"),
		];
	},

	businessTypeRule(validationObject, context) {
		const requiredText = context.$tr("item_is_required", [
			context.$tr("business_type.business_type_name"),
		]);
		const minLengthText = context.$tr("min_length", [
			context.$tr("business_type.business_type_name"),
			validationObject.name.$params.minLength.min,
		]);
		const maxLength = context.$tr("max_length", [
			context.$tr("business_type.business_type_name"),
			validationObject.name.$params.maxLength.max,
		]);

		return [
			(_) => validationObject.name.required || requiredText,
			(_) => validationObject.name.minLength || minLengthText,
			(_) => validationObject.name.maxLength || maxLength,
		];
	},
};

export default Rules;
