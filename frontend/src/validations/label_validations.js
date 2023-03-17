import Validations from "./validations";
import Rules from "./rules-new";

export default {

  // model schema
  schema: {
    title: '',
    label_category: null,
  },

  // roles
  rules: {
    titleRule: Rules.titleRule,
  },

  // validations
  validations: {
    title: Validations.name100Validation,
    label_category: Validations.requiredValidation,
  }
}
