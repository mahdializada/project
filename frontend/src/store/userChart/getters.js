
const getters = {
  get_data:         (state)   => state.data,
  get_categories:   (state)   => state.categories,
  get_api_call:     (state)   => state.api_call,
}

export default getters


// switch (tabKey) {
//   case 'create':
//     return state.logs.filter((log) => log?.method == 'POST');
//   case 'update':
//     return state.logs.filter((log) => log?.method == 'PUT');
//   case 'deleted':
//     return state.logs.filter((log) => log?.method == 'DELETE');
//   case  'all':
//     return state.logs;
//   case   '':
//     return state.logs;
//   case   "department":
//     return state.logs.filter((log)=>log.system=="department");
//   default:
//   return state.logs.filter((log)=> log?.user_id ==tabKey);
// }
