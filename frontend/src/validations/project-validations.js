import Validations from "./validations";
import Rules from "./rules-new";
import { requiredIf } from "vuelidate/lib/validators";

export default {
  form: {
    file: '',
  },
  fileExtension: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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
    name: "",
    investment_type: "",
    email: "",
    logo: null,
    note: "",
  },

  // roles
  rules: {
    nameRule: Rules.nameRule,
    investmentTypeRule: Rules.investmentTypeRule,
    logoRule: Rules.logoRule,
    noteRule: Rules.noteRule,
    emailRule: Rules.emailRule,
    fileRule: Rules.fileRule,
    systemRule: Rules.systemRule,
    countryRule: Rules.countryRule,
  },

  // validations
  validations: {
    company: {
      name: Validations.name100Validation,
      investment_type: Validations.requiredValidation,
      logo: Validations.requiredValidation,
      note: Validations.remarksValidation,
      email: Validations.emailValidation,
      // domain:Validations.domainValidation,
      domain: {
        required: requiredIf((value) => {
          return value ? Validations.urlValidationRequired :""
            
        }),
      },
      
    },
    system_id: Validations.requiredValidation,
    country_id: Validations.requiredValidation,
    file: Validations.fileValidation,
  },
};
