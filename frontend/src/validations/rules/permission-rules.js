import Validations from "../validations";
import Rules from "./index";

// Role Specific RulesNew
export const PermissionRules = {
  nameRule: Rules.nameRule,
}

// Role Validations
export const PermissionValidations = {
  name: Validations.nameValidation,
}


