export const SET_COUTRIES_WITH_COMPANIES = "SET_COUTRIES_WITH_COMPANIES";
export const SET_COMPANIES_OF_COUNTRIES = "SET_COMPANIES_OF_COUNTRIES";
export const SET_DEPARTMENTS_OF_COMPANIES = "SET_DEPARTMENTS_OF_COMPANIES";
export const SET_USERS_OF_COMAPNIES = "SET_USERS_OF_COMAPNIES";
export const REMOVE_COMPANIES = "REMOVE_COMPANIES";
export const REMOVE_DEPARTMENTS = "REMOVE_DEPARTMENTS";
export const REMOVE_ALL_COMPANIES = "REMOVE_ALL_COMPANIES";
export const REMOVE_ALL_DEPARTMENTS = "REMOVE_ALL_DEPARTMENTS";
export const SET_COUNTRY_LANGUAGES = "SET_COUNTRY_LANGUAGES";
export const SET_TEAMS_WITH_META_DATA = "SET_TEAMS_WITH_META_DATA";
export const SET_ROLES_WITH_META_DATA = "SET_ROLES_WITH_META_DATA";
export const SET_INDUSTRIES = "SET_INDUSTRIES";
export const FILTER_SELECTED_COUNTRY = "FILTER_SELECTED_COUNTRY";
export const INSERT_SELECTED_COUNTRY = "INSERT_SELECTED_COUNTRY";
//filter and insert selected company
export const FILTER_SELECTED_COMPANY = "FILTER_SELECTED_COMPANY";
export const INSERT_SELECTED_COMPANY = "INSERT_SELECTED_COMPANY";
//filter and insert selected department
export const INSERT_SELECTED_DEPARTMENT = 'INSERT_SELECTED_DEPARTMENT';
export const FILTER_SELECTED_DEPARTMENT = 'FILTER_SELECTED_DEPARTMENT';

export default {
  // filter selected department
  [FILTER_SELECTED_DEPARTMENT](state, selectedDepartmentId) {
    state.DepartmentsOfTheCompanies = state.DepartmentsOfTheCompanies.filter(
      (c) => c.id !== selectedDepartmentId
    );
  },
  //filter selected  deparment
  [INSERT_SELECTED_DEPARTMENT](state, { selectedDepartment, length }) {
    state.DepartmentsOfTheCompanies.length =
      state.DepartmentsOfTheCompanies.length + length;
    state.DepartmentsOfTheCompanies.splice(
      selectedDepartment.index,
      0,
      selectedDepartment
    );
  },
  // filter selected company
  [FILTER_SELECTED_COMPANY](state, selectedCompanyId) {
    state.companiesOfTheCountries = state.companiesOfTheCountries.filter(
      (c) => c.id !== selectedCompanyId
    );
  },
  //filter selected country
  [INSERT_SELECTED_COUNTRY](state, { selectedCountry, length }) {
    state.countriesWithCompanies.length =
      state.countriesWithCompanies.length + length;
    state.countriesWithCompanies.splice(
      selectedCountry.index,
      0,
      selectedCountry
    );
  },
  //filter selected company
  [INSERT_SELECTED_COMPANY](state, { selectedCompany, length }) {
    state.companiesOfTheCountries.length =
      state.companiesOfTheCountries.length + length;
    state.companiesOfTheCountries.splice(
      selectedCompany.index,
      0,
      selectedCompany
    );
  },
  [FILTER_SELECTED_COUNTRY](state, selectedCountryId) {
    state.countriesWithCompanies = state.countriesWithCompanies.filter(
      (c) => c.id !== selectedCountryId
    );
  },
  // Set country languages
  [SET_COUNTRY_LANGUAGES](state, languages) {
    state.countryLanguages = languages;
  },

  [SET_COUTRIES_WITH_COMPANIES](state, value) {
    state.countriesWithCompanies = value;
  },

  [SET_COMPANIES_OF_COUNTRIES](state, { companies, selected_companies }) {
    state.companiesOfTheCountries = companies;
    state.companiesOfTheCountries = state.companiesOfTheCountries.filter(
      (c) => {
        if (!selected_companies.includes(c.id)) {
          return c;
        }
      }
    );
  },

  [SET_DEPARTMENTS_OF_COMPANIES](state, {departments, selected_departments}) {
    state.DepartmentsOfTheCompanies = departments;
    state.DepartmentsOfTheCompanies = state.DepartmentsOfTheCompanies.filter(
      (c) => {
        if (!selected_departments.includes(c.id)) {
          return c;
        }
      }
    );
  },

  [SET_USERS_OF_COMAPNIES](state, value) {
    state.UsersOfCompanies = value;
  },

  [SET_TEAMS_WITH_META_DATA](state, value) {
    state.teamsWithMetaData = value;
  },
  [SET_ROLES_WITH_META_DATA](state, value) {
    state.rolesWithMetaData = value;
  },

  [SET_INDUSTRIES](state, value) {
    state.industries = value;
  },

  [REMOVE_COMPANIES](state, countryId) {
    state.companiesOftheCountries = state.companiesOftheCountries?.filter(
      (project) => {
        if (project?.country_id === countryId) {
          state.DepartmentsOftheCompanies =
            state.DepartmentsOftheCompanies?.filter(
              (department) => department?.project_id !== project?.id
            );
        }
        return project?.country_id !== countryId;
      }
    );
  },

  [REMOVE_DEPARTMENTS](state, projectId) {
    state.DepartmentsOftheCompanies = state.DepartmentsOftheCompanies?.filter(
      (department) => department?.project_id !== projectId
    );
  },

  [REMOVE_ALL_COMPANIES](state) {
    state.companiesOfTheCountries = [];
  },

  [REMOVE_ALL_DEPARTMENTS](state) {
    state.DepartmentsOfTheCompanies = [];
  },

  clearCountryLanguages(state) {
    state.countryLanguages = [];
  },
};
