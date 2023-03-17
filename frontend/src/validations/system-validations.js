import Validations from "./validations";
import Rules from "./rules-new";

export default {
  // model schema
  schema: {
    name: "",
    logo: null,

    subSystem: [{ current: "", name: "", actions: [], actionIds: [] }],
  },

  // roles
  rules: {
    nameRule: Rules.nameRule,
    logoRule: Rules.logoRule,
  },

  subSystemNameRule(item) {
    return [
      (_) =>
        item.name.required ||
        this.$tr("item_is_required", this.$tr("sub_system")),
      (_) =>
        item.name.minLength ||
        this.$tr("min_length", this.$tr("sub_system"), this.$tr("2")),
      (_) =>
        item.name.maxLength ||
        this.$tr("max_length", this.$tr("sub_system"), this.$tr("100")),
    ];
  },

  subSystemCurrentRule(item) {
    return [
      (_) =>
        item.current.required ||
        this.$tr("item_is_required", this.$tr("action")),
    ];
  },

  // validations
  validations: {
    name: Validations.name100Validation,
    logo: Validations.requiredValidation,

    subSystem: {
      $each: {
        name: Validations.name100Validation,
        actions: Validations.requiredValidation,
        current: Validations.requiredValidation,
        actionIds: {},
        actions: {},
      },
    },
  },
};
