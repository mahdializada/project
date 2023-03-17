import Validations from './validations';
import Rules from './rules-new';

export default {
  form: {
    file: '',
  },
  fileExtension:
  "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
  perpageDropdown: [
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
  steppers: [
    { icon: 'fa-lock', title: 'general', slotName: 'step1' },
    { icon: 'fa-info-circle', title: 'information', slotName: 'step2' },
    { icon: 'fa-comment-dots', title: 'remarks', slotName: 'step3' },
    { icon: 'fa-thumbs-up', title: 'done', slotName: 'step4' },
  ],

  // model schema
  schema: {
    name: '',
    email: '',
    location_type: '',
    map_link: '',
    address: '',
    note: '',

    // relations
    country_id: '',
    company_id: '',
    state_id: '',
  },

  // roles
  rules: {
    nameRule: Rules.nameRule,
    emailRule: Rules.emailRule,
    addressRule: Rules.addressRule,
    noteRule: Rules.noteRule,

    // relations
    countryRule: Rules.countryRule,
    companyRule: Rules.companyRule,
    countryStateRule: Rules.countryStateRule,
    mapLinkRule: Rules.mapLinkRule,

    locationTypeRule(validationObject, context) {
      const requiredText = context.$tr('required', [
        context.$tr('location_type'),
      ]);

      return [(_) => validationObject.location_type.required || requiredText];
    },
  },

  // validations
  validations: {
    name: Validations.name100Validation,
    email: Validations.emailValidation,
    location_type: Validations.requiredValidation,
    map_link: Validations.urlValidation,
    address: Validations.requiredValidation,
    note: Validations.remarksValidation,

    // relations
    country_id: Validations.requiredValidation,
    company_id: Validations.requiredValidation,
    state_id: Validations.requiredValidation,
  },
};
