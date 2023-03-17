export default {
 
  /**
   * Main Toast
   */
  showToast: (state, toast) => {
    const { color, timeout, message } = toast;

    state.toast = {
      message,
      color,
      timeout,
      show: true,
    };
  },
  hideToast: (state) => {
    state.toast.show = false;
  },
  resetToast: (state) => {
    state.toast = {
      show: false,
      color: 'black',
      message: '',
      timeout: 3000,
    };
  },

  /**
   * Theme and Layout
   */
  setGlobalTheme: (state, theme) => {
    state.globalTheme = theme;
  },
  setRTL: (state, isRTL) => {
    state.isRTL = isRTL;
  },
  setContentBoxed: (state, isBoxed) => {
    state.isContentBoxed = isBoxed;
  },
  setMenuTheme: (state, theme) => {
    state.menuTheme = theme;
  },
  setToolbarTheme: (state, theme) => {
    state.toolbarTheme = theme;
  },

  setToolbarDetached: (state, isDetached) => {
    state.isToolbarDetached = isDetached;
  },
};
