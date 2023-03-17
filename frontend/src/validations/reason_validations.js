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

  // model schema
  schema: {
    reason: {
      title: '',
    }
  },

  // roles
  rules: {
    titleRule: Rules.titleRule,
		fileRule: Rules.fileRule,
  },

  // validations
  validations: {
    reason: {
      title: Validations.name100Validation,
    },
    file: Validations.fileValidation,
  }
}
