import Validations from './validations';
import Rules from './rules-new';
import required from 'vuelidate/lib/validators/required';

export default {
  form: {
    file: '',
  },
  fileExtension:
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  perpageDropdown: [
    //  10, 20, 50, 100 , 200, 500 , 100 , all
    {
      text: '5',
      value: 5,
    },
    {
      text: '10',
      value: 10,
    },
    {
      text: '20',
      value: 20,
    },
    {
      text: '50',
      value: 50,
    },
    {
      text: '100',
      value: 100,
    },
    {
      text: '200',
      value: 200,
    },
    {
      text: '500',
      value: 500,
    },
    {
      text: '1000',
      value: 1000,
    },
    {
      text: 'All',
      value: -1,
    },
  ],
  // model schema
  schema: {
    name: '',
    country_id: '',
    company_id: '',
    industries: null,
    note: '',
    business_location_id: '',
  },

  // roles
  rules: {
    nameRule: Rules.nameRule,
    countryRule: Rules.countryRule,
    noteRule: Rules.noteRule,
    fileRule: Rules.fileRule,
    business_locationRule(validationObject, context) {
      const requiredText = context.$tr(
        'item_is_required',
        context.$tr('business_locations')
      );
      return [
        (_) => validationObject.business_location_id.required || requiredText,
      ];
    },
    companyRule(validationObject, context) {
      const requiredText = context.$tr(
        'item_is_required',
        context.$tr('companies')
      );
      return [(_) => validationObject.company_ids.required || requiredText];
    },
    industryRule(validationObject, context) {
      const requiredText = context.$tr(
        "item_is_required",
        context.$tr("industry")
      );
      return [(_) => validationObject.industry_id.required || requiredText];
    },
  },

  // validations
  validations: {
    department: {
      name: Validations.name100Validation,
      company_ids: Validations.requiredValidation,
      note: Validations.remarksValidation,
      industries: {
        required,
        $each: Validations.requiredValidation,
      },
      business_location_id: Validations.requiredValidation,
    },
    // department: {
    // 	name: Validations.name100Validation,
    // 	// should be removed
    // 	country_id: Validations.requiredValidation,
    // 	company_id: Validations.requiredValidation,
    // 	// should be removed
    // 	company_ids: Validations.requiredValidation,
    // 	note: Validations.remarksValidation,
    // 	industry_id: Validations.requiredValidation,
    // 	business_location_id: Validations.requiredValidation,
    // },
    file: Validations.fileValidation,
  },
};
