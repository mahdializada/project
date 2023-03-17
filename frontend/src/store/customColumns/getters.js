const getters = {
  get_custom_columns: (state) => state.custom_columns,
  get_personal_setting: (state) => state.personal_setting,
  get_prev_setting: (state) => state.prev_setting,
  get_shared_setting: (state) => state.shared_setting,
  get_setting_name: (state) => state.setting_name,
  get_view_id: (state) => state.view_id,
  get_view_scope: (state) => state.view_scope,
};

export default getters;
