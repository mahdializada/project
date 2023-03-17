import Validations from "../validations";
import Rules from "./index";

// Role Specific RulesNew
export const RoleRules = {
  rolenameRule: Rules.roleNameRule,
  permissionIdRule: Rules.permissionIdRule,
}

// Role Validations
export const RoleValidations = {
  name: Validations.nameValidation,
  permissionId: Validations.requiredValidation,
}


