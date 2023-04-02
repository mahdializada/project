import Validations from "./validations";
import Rules from "./rules-new";

export default {
  form: {
    file: "",
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
  schema: {},

  rules: {
    nameRule: Rules.nameRule,
    noteRule: Rules.noteRule,
    fileRule: Rules.fileRule,
    departmentRule: Rules.departmentRule,
    schedule_typeRule(validationObject, context) {
      const requiredText = context.$tr("required", [
        context.$tr("label_required", context.$tr("schedule_type")),
      ]);
      return [(_) => validationObject.schedule_type.required || requiredText];
    },
  },

  validations: {
    team_role: {
      name: Validations.name100Validation,
      note: Validations.remarksValidation,
      department_id: Validations.requiredValidation,
      schedule_type: Validations.requiredValidation,
      date_range: {},
      time_range: {},
    },
    file: Validations.fileValidation,
  },
};