import Validations from "./validations";
import Rules from "./rules-new";
import { minLength, requiredIf } from 'vuelidate/lib/validators';

export default {
  form: {
    file: '',
  },

  perpageDropdown : [
    //  10, 20, 50, 100 , 200, 500 , 100 , all
    {
      text: "5",
      value: 5,
    },
    {
      text: "10",
      value: 10,
    },
    {
      text: "20",
      value: 20,
    },
    {
      text: "50",
      value: 50,
    },
    {
      text: "100",
      value: 100,
    },
    {
      text: "200",
      value: 200,
    },
    {
      text: "500",
      value: 500,
    },
    {
      text: "1000",
      value: 1000,
    },
    {
      text: "All",
      value: -1,
    },
  ],
  // model schema
  schema: {
    firstname: '',
    lastname: '',
    username: '',
    email: '',
    password: '',
    phone: '',
    whatsapp: '',
    gender: '',
    birth_date: '',
    image: '',
    address: '',
    note: '',
    // relations
    language_id: '',
    state_id: '',
    country_id: '',
    current_country_id: '',
    role_id: '',
    company_id: '',
    team_id: '',
  },

  fileExtension: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
   // roles
   rules: {
    roleRule: Rules.roleRule,
    teamRule: Rules.teamRule,
    companyRule: Rules.companyRule,
    fileRule: Rules.fileRule,
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
    currentCountryRule(validationObject, context) {
      const requiredText = context.$tr('required', [
        context.$tr('current_country'),
      ]);
      return [
        (_) => validationObject.current_country_id.required || requiredText,
      ];
    },
    countryStateRule(validationObject, context) {
      const requiredText = context.$tr('required', [
        context.$tr('state'),
      ]);

      return [(_) => validationObject.state_id.required || requiredText];
    },
  },

  // validations
  validations: {
    image: {},
    firstname: Validations.name100Validation,
    lastname: Validations.name100Validation,
    username: Validations.usernameValidation,
    birth_date: Validations.requiredValidation,
    address: Validations.requiredValidation,
    phone: Validations.phoneValidation,
    whatsapp: Validations.phoneValidation,
    gender: Validations.requiredValidation,
    email: Validations.emailValidation,
    password: {
      required: requiredIf(function (value) {
        return !value.generate_password;
      }),
      minLength: minLength(6),
    },
    note: Validations.remarksValidation,

    // relations
    country_id: Validations.requiredValidation,
    language_id: Validations.requiredValidation,
    state_id: Validations.requiredValidation,
    current_country_id: Validations.requiredValidation,
    file: Validations.fileValidation,
    team_id: Validations.requiredValidation,
    company_id: Validations.requiredValidation,
    role_id: Validations.requiredValidation,
  },
}
