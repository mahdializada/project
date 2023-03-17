import {
	maxLength,
	minLength,
	required,
	email,
	requiredIf,
	sameAs,
	numeric,
	decimal,
	minValue,
	helpers,
	url,
} from "vuelidate/lib/validators";

export const noteValidation = (note) => {
	let div = document.createElement("div");
	div.innerHTML = note;
	let actaulText = div.innerText;
	if (actaulText && actaulText.trim().length >= 10) {
		return true;
	}
	return false;
};

export const storyNoteValidation = (note) => {
	let div = document.createElement("div");
	div.innerHTML = note;
	let actaulText = div.innerText;
	if (actaulText && actaulText.trim().length >= 5) {
		return true;
	}
	return false;
};

export const phoneNumber = helpers.regex("phoneNumber", /^[\d()+]{7,14}$/);
export const domain = helpers.regex(
	"domain",
	/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i
);
// export const phoneNumber = helpers.regex(
//   'phoneNumber',
//   /^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/
// );

// check password validation
export const validPassword = helpers.regex(
	"validPassword",
	/((?=.*\d)(?=.*[A-z])(?=.*[~!@#$%^&*]).{6,40})/
);

export const validPdf = (pdf) => {
	if (pdf) {
		if (pdf instanceof File) {
			const fileExtension = pdf.type;
			const allowedExtensions = ["application/pdf", ".pdf"];
			return allowedExtensions.includes(fileExtension);
		}
		return false;
	}
	return false;
};

const validImage = (image) => {
	if (image) {
		if (image instanceof File) {
			const fileExtension = image.type;
			const allowedExtensions = ["image/jpeg", "image/jpg", "image/png"];
			return allowedExtensions.includes(fileExtension);
		}
		return true;
	}
	return false;
};
const urlRegex = helpers.regex(
	"urlRegex",
	/^((ftp|http|https):\/\/)?(www.)?(?!.*(ftp|http|https|www.))[a-zA-Z0-9_-]+(\.[a-zA-Z]+)+((\/)[\w#]+)*(\/\w+\?[a-zA-Z0-9_]+=\w+(&[a-zA-Z0-9_]+=\w+)*)?$/gm
);

const urlRegexV = (link) => {
	if (link) {
		const expression =
			/^((ftp|http|https):\/\/)?(www.)?(?!.*(ftp|http|https|www.))[a-zA-Z0-9_-]+(\.[a-zA-Z]+)+((\/)[\w#]+)*(\/\w+\?[a-zA-Z0-9_]+=\w+(&[a-zA-Z0-9_]+=\w+)*)?$/gm;
		var regex = new RegExp(expression);
		if (link.match(regex)) {
			return true;
		} else {
			return false;
		}
	}
};

//if an excel file is selected in bulk upload
const validFile = (file) => {
	if (file) {
		if (file instanceof File) {
			const fileExtension = file.type;
			const allowedExtensions =
				"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
			return allowedExtensions == fileExtension;
		}
		return true;
	}
	return false;
};

const MAX_LENGTH = 32;
const MIN_LENGTH = 2;

const Validations = {
	remarksValidation: {
		required,
		minLength: minLength(3),
		maxLength: maxLength(500),
	},
	name100Validation: {
		required,
		minLength: minLength(2),
		maxLength: maxLength(100),
	},
	usernameValidation: {
		required,
		minLength: minLength(2),
		maxLength: maxLength(32),
	},
	nameValidation: {
		required,
		minLength: minLength(MIN_LENGTH),
		maxLength: maxLength(MAX_LENGTH),
	},
	requiredValidation: { required },
	emailValidation: { required, email },
	phoneValidation: { required, phoneNumber },
	passwordValidation: {
		required,
		validPassword,
		minLength: minLength(6),
		sameAsConfirmPassword: sameAs("confirm_password"),
	},
	password2Validation: {
		required,
		validPassword,
		sameAsPassword: sameAs("password"),
		minLength: minLength(6),
	},
	requiredIf: (value) => requiredIf(() => value),
	dayValidation: { required, numeric, minLength: minLength(1) },
	numberValidation: { required, numeric, minLength: minLength(1) },
	numberValidationNew: { required, numeric, minLength: minLength(6),maxLength: maxLength(6)},
	bankNumberValidation: {
		required,
		numeric,
		minLength: minLength(15),
		maxLength: maxLength(18),
	},
	numberValidationMin30: { required, numeric, minValue: minValue(30) },
	decimalValidation: { required, decimal, minValue: minValue(0) },
	onlyDecimalValidation: { decimal, minValue: minValue(0) },
	imageValidation: { required, validImage },
	fileValidation: { required, validFile },
	urlValidation: { url },
	domainValidation: { domain },
	urlValidationRequired: { required, url },
	urlRegexpValidationRequired: { required, urlRegex },
	htmlNoteValidation: { required, noteValidation },
	htmlStoryNoteValidation: { required, storyNoteValidation },
};

export default Validations;
