import { maxLength } from "vuelidate/lib/validators";

export default {
  //file rule for bulk upload
  fileRule(validationObject, context, textField = "file") {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr(textField)
    );
    const validFileText = context.$tr(
      "item_is_invalid",
      context.$tr(textField)
    );
    return [
      (_) => validationObject.file.required || requiredText,
      (_) => validationObject.file.validFile || validFileText,
    ];
  },

  nameRule(validationObject, context, textFieldName = "name") {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr(textFieldName)
    );
    const minLengthText = context.$tr(
      "min_length",
      context.$tr(textFieldName),
      validationObject.name.$params.minLength.min
    );
    const maxLength = context.$tr(
      "max_length",
      context.$tr(textFieldName),
      validationObject.name.$params.maxLength.max
    );

    return [
      (_) => validationObject.name.required || requiredText,
      (_) => validationObject.name.minLength || minLengthText,
      (_) => validationObject.name.maxLength || maxLength,
    ];
  },

  firstnameRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("firstname")
    );
    const minLengthText = context.$tr(
      "min_length",
      context.$tr("firstname"),
      validationObject.firstname.$params.minLength.min
    );
    const maxLength = context.$tr(
      "max_length",
      context.$tr("firstname"),
      validationObject.firstname.$params.maxLength.max
    );
    return [
      (_) => validationObject.firstname.required || requiredText,
      (_) => validationObject.firstname.minLength || minLengthText,
      (_) => validationObject.firstname.maxLength || maxLength,
    ];
  },
  lastnameRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("lastname")
    );
    const minLengthText = context.$tr(
      "min_length",
      context.$tr("lastname"),
      validationObject.lastname.$params.minLength.min
    );
    const maxLength = context.$tr(
      "max_length",
      context.$tr("lastname"),
      validationObject.lastname.$params.maxLength.max
    );
    return [
      (_) => validationObject.lastname.required || requiredText,
      (_) => validationObject.lastname.minLength || minLengthText,
      (_) => validationObject.lastname.maxLength || maxLength,
    ];
  },

  usernameRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("username")
    );
    const minLengthText = context.$tr(
      "min_length",
      context.$tr("username"),
      validationObject.username.$params.minLength.min
    );
    const maxLength = context.$tr(
      "max_length",
      context.$tr("username"),
      validationObject.username.$params.maxLength.max
    );

    return [
      (_) => validationObject.username.required || requiredText,
      (_) => validationObject.username.minLength || minLengthText,
      (_) => validationObject.username.maxLength || maxLength,
    ];
  },

  phoneRule(validationObject, context) {
    const requiredText = context.$tr("item_is_required", context.$tr("phone"));
    const invalidPhone = context.$tr("item_is_invalid", context.$tr("phone"));
    return [
      (_) => validationObject.phone.required || requiredText,
      (_) => validationObject.phone.phoneNumber || invalidPhone,
    ];
  },

  whatsappRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("whatsapp")
    );
    const invalidWhatsApp = context.$tr(
      "item_is_invalid",
      context.$tr("whatsapp")
    );

    return [
      (_) => validationObject.whatsapp.required || requiredText,
      (_) => validationObject.whatsapp.phoneNumber || invalidWhatsApp,
    ];
  },

  emailRule(validationObject, context) {
    const requiredText = context.$tr("item_is_required", context.$tr("email"));
    const invalidEmail = context.$tr("item_is_invalid", context.$tr("email"));

    return [
      (_) => validationObject.email.required || requiredText,
      (_) => validationObject.email.email || invalidEmail,
    ];
  },

  genderRule(validationObject, context) {
    const requiredText = context.$tr("item_is_required", context.$tr("gender"));
    return [(_) => validationObject.gender.required || requiredText];
  },

  passwordRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("password")
    );
    const minLengthText = context.$tr(
      "min_length",
      context.$tr("password"),
      validationObject.password.$params.minLength.min
    );
    return [
      (_) => validationObject.password.required || requiredText,
      (_) => validationObject.password.minLength || minLengthText,
    ];
  },

  password2Rule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("confirm_password")
    );
    const minLengthText = context.$tr(
      "min_length",
      context.$tr("confirm_password"),
      validationObject.password.$params.minLength.min
    );
    const sameAsText = context.$tr(
      "same_password",
      context.$tr("confirm_password")
    );

    return [
      (_) => validationObject.confirm_password.required || requiredText,
      (_) => validationObject.confirm_password.sameAsPassword || sameAsText,
      (_) => validationObject.confirm_password.minLength || minLengthText,
    ];
  },

  addressRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("address")
    );
    return [(_) => validationObject.address.required || requiredText];
  },

  dobRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("birthday_date")
    );
    return [(_) => validationObject.birth_date.required || requiredText];
  },

  imageRule(validationObject, context, textField = "image") {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr(textField)
    );
    const validImageText = context.$tr(
      "item_is_invalid",
      context.$tr(textField)
    );
    return [
      (_) => validationObject.image.required || requiredText,
      (_) => validationObject.image.validImage || validImageText,
    ];
  },

  noteRule(validationObject, context) {
    const requiredText = context.$tr("item_is_required", [context.$tr("note")]);
    const minLengthText = context.$tr(
      "min_length",
      context.$tr("note"),
      validationObject.note.$params.minLength.min
    );
    const maxLength = context.$tr(
      "max_length",
      context.$tr("note"),
      validationObject.note.$params.maxLength.max
    );

    return [
      (_) => validationObject.note.required || requiredText,
      (_) => validationObject.note.minLength || minLengthText,
      (_) => validationObject.note.maxLength || maxLength,
    ];
  },

  actionRule(validationObject, context) {
    const requiredText = context.$tr('item_is_required', [
      context.$tr('actions'),
    ]);
    return [(_) => validationObject.action_id.required || requiredText];
  },

  roleRule(validationObject, context) {
    const requiredText = context.$tr("item_is_required", context.$tr("roles"));
    return [(_) => validationObject.role_id.required || requiredText];
  },

  systemRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("systems")
    );
    return [(_) => validationObject.system_id.required || requiredText];
  },

  userRule(validationObject, context) {
    const requiredText = context.$tr('item_is_required', [
      context.$tr('users'),
    ]);
    return [(_) => validationObject.user_id.required || requiredText];
  },

  sub_systemRule(validationObject, context) {
    const requiredText = context.$tr('item_is_required', [
      context.$tr('subsystem'),
    ]);
    return [(_) => validationObject.sub_system_id.required || requiredText];
  },

  sourcerRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("sourcers")
    );
    return [(_) => validationObject.sourcer_id.required || requiredText];
  },

  companyRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("companies")
    );
    return [(_) => validationObject.company_id.required || requiredText];
  },
  languageRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("language")
    );
    return [(_) => validationObject.language_id.required || requiredText];
  },

  departmentRule(validationObject, context) {
    const requiredText = context.$tr("required", [
      context.$tr("label_required", context.$tr("department")),
    ]);
    return [(_) => validationObject.department_id.required || requiredText];
  },

  countryRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("countries")
    );
    return [(_) => validationObject.country_id.required || requiredText];
  },

  teamRule(validationObject, context) {
    const requiredText = context.$tr("item_is_required", context.$tr("team"));
    return [(_) => validationObject.team_id.required || requiredText];
  },

  schedule_typeRule(validationObject, context) {
    const requiredText = context.$tr('item_is_required', [
      context.$tr('schedule_type'),
    ]);
    return [(_) => validationObject.schedule_type.required || requiredText];
  },

  investmentTypeRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("investment_type")
    );
    return [(_) => validationObject.investment_type.required || requiredText];
  },

  // Inserted by Rohullah Muhammadee
  logoRule(validationObject, context) {
    const requiredText = context.$tr("item_is_required", context.$tr("icon"));
    return [(_) => validationObject.logo.required || requiredText];
  },

  selectedDepartmentsRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("department")
    );
    return [
      (_) => validationObject.selected_departments.required || requiredText,
    ];
  },

  selectedPermissionsRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("department")
    );
    return [
      (_) => validationObject.selected_permissions.required || requiredText,
    ];
  },

  labelRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("label")
    );
    return [(_) => validationObject.label_id.required || requiredText];
  },

  industryRule(validationObject, context) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr("industry")
    );
    return [(_) => validationObject.industry_id.required || requiredText];
  },

  titleRule(validationObject, context) {
    const requiredText = context.$tr("item_is_required", context.$tr("title"));
    const minLengthText = context.$tr(
      "min_length",
      context.$tr("title"),
      validationObject.title.$params.minLength.min
    );
    const maxLength = context.$tr(
      "max_length",
      context.$tr("title"),
      validationObject.title.$params.maxLength.max
    );

    return [
      (_) => validationObject.title.required || requiredText,
      (_) => validationObject.title.minLength || minLengthText,
      (_) => validationObject.title.maxLength || maxLength,
    ];
  },

  remarksRule(validationObject, context, textFieldName) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr(textFieldName)
    );
    const minLengthText = context.$tr(
      "min_length",
      context.$tr(textFieldName),
      validationObject.remarks.$params.minLength.min
    );
    const maxLength = context.$tr(
      "max_length",
      context.$tr(textFieldName),
      validationObject.remarks.$params.maxLength.max
    );

    return [
      (_) => validationObject.remarks.required || requiredText,
      (_) => validationObject.remarks.minLength || minLengthText,
      (_) => validationObject.remarks.maxLength || maxLength,
    ];
  },

  addLanguageRule(validationObject, context, textFieldName) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr(textFieldName)
    );
    return [
      (_) => validationObject.selectedLangaugeId.required || requiredText,
    ];
  },

  directionRule(validationObject, context, textFieldName) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr(textFieldName)
    );
    return [(_) => validationObject.direction.required || requiredText];
  },

  countryStateRule(validationObject, context) {
    const requiredText = context.$tr("item_is_required", context.$tr("city"));
    return [(_) => validationObject.state_id.required || requiredText];
  },

  translationRule(validationObject, context, textFieldName) {
    const requiredText = context.$tr(
      "item_is_required",
      context.$tr(textFieldName)
    );
    return [(_) => validationObject.translation.required || requiredText];
  },

  mapLinkRule(validationObject, context) {
    const urlText = context.$tr("item_is_invalid", context.$tr("map_link"));

    return [(_) => validationObject.map_link.url || urlText];
  },
  socialMediaRule(validationObject, context, textFieldName) {
    const urlText = context.$tr("invalid_link");
    const requiredText = context.$tr(
      "item_is_required",
      textFieldName
    );

    return [
      (_) => validationObject.url.required || requiredText,
      (_) => validationObject.url.url || urlText,

    ];
  },
  websiteRule(validationObject, context, textFieldName) {
    const urlText = context.$tr("invalid_link");
    const requiredText = context.$tr(
      "item_is_required",
      textFieldName
    );

    return [
      (_) => validationObject.website.required || requiredText,
      (_) => validationObject.website.url || urlText,

    ];
  },
  sampleAccountUrlRule(validationObject, context) {
    const urlText = context.$tr("invalid_link");
    return [
      (_) => validationObject.sample_url_account.url || urlText,
    ];
  },
  requiredMediaRule(validationObject, context, textFieldName) {
    const requiredText = context.$tr(
      "item_is_required",
      textFieldName
    );
    return [(_) => validationObject.url.required || requiredText];
  },
};
