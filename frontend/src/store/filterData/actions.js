import {
  SET_COUTRIES_WITH_COMPANIES,
  SET_COMPANIES_OF_COUNTRIES,
  SET_DEPARTMENTS_OF_COMPANIES,
  SET_USERS_OF_COMAPNIES,
  SET_COUNTRY_LANGUAGES,
  SET_ROLES_WITH_META_DATA,
  SET_TEAMS_WITH_META_DATA,
  REMOVE_ALL_DEPARTMENTS,
  REMOVE_ALL_COMPANIES,
  SET_INDUSTRIES,
} from "./mutations";

export default {
  async fetchCountriesWithCompanies({ commit }) {
    try {
      const response = await this.$axios.get("countries?withCompany");
      if (response?.status === 200) {
        commit(SET_COUTRIES_WITH_COMPANIES, response?.data);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async fetchCountryLanguages({ commit }, countryId) {
    try {
      const response = await this.$axios.get(
        `languages?countryId=${countryId}`
      );
      if (response?.status === 200) {
        commit(SET_COUNTRY_LANGUAGES, response?.data?.data);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async fetchCompaniesOfCountries(
    { commit },
    { country_ids, selected_company_ids }
  ) {
    country_ids = Array.from(country_ids ?? []);
    try {
      const response = await this.$axios.get("companies?itemsPerPage=-1", {
        params: { country_ids },
      });
      if (response?.status === 200) {
        commit(SET_COMPANIES_OF_COUNTRIES, {
          companies: response?.data.companies,
          selected_companies: selected_company_ids,
        });
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async fetchDepartmentsOfCompanies(
    { commit },
    { company_ids, selected_departments }
  ) {
    company_ids = Array.from(company_ids);
    try {
      const response = await this.$axios.get("departments?itemsPerPage=-1", {
        params: { company_ids },
      });
      if (response?.status === 200) {
        commit(SET_DEPARTMENTS_OF_COMPANIES, {
          departments: response?.data.departments,
          selected_departments,
        });
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async fetchUsersOfCompanies({ commit }, company_ids) {
    company_ids = Array.from(company_ids);
    try {
      const response = await this.$axios.get("users", {
        params: { company_ids },
      });
      if (response?.status === 200) {
        commit(SET_USERS_OF_COMAPNIES, response?.data.users);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async fetchTeamMetaDataForEdit({ commit }, team_ids) {
    try {
      const response = await this.$axios.get("teams?team_meta_data=true", {
        params: { team_ids },
      });
      if (response?.status === 200) {
        commit(SET_TEAMS_WITH_META_DATA, response?.data?.data);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async fetchRoleMetaDataForEdit({ commit }, role_ids) {
    try {
      const response = await this.$axios.get("roles?role_meta_data=true", {
        params: { role_ids },
      });
      if (response?.status === 200) {
        commit(SET_ROLES_WITH_META_DATA, response?.data?.data);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  // remove departments and companies
  clearCompanyDepartment({ commit }) {
    commit(REMOVE_ALL_COMPANIES);
    commit(REMOVE_ALL_DEPARTMENTS);
  },

  async fetchIndustries({ commit, state }) {
    if (state.industries == 0) {
      try {
        const response = await this.$axios.get("industries");
        if (response?.status === 200) {
          commit(SET_INDUSTRIES, response?.data?.data);
        }
        return response;
      } catch (error) {
        return error;
      }
    }
  },
};
