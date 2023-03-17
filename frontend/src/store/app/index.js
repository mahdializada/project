import configs from '../../configs'
import actions from './actions'
import mutations from './mutations'

const {
  application,
  theme,
} = configs

const {
  globalTheme,
  menuTheme,
  toolbarTheme,
  isToolbarDetached,
  isContentBoxed,
  isRTL
} = theme

// state initial values
const state = () => ({
  
  application,

  // themes and layout configurations
  globalTheme,
  menuTheme,
  toolbarTheme,
  isToolbarDetached,
  isContentBoxed,
  isRTL,

  // App.vue main toast
  toast: {
    show: false,
    color: 'black',
    message: '',
    timeout: 3000,
  },
});


export default {
  namespaced: true,
  state,
  actions,
  mutations
}
