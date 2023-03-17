import Vue from 'vue'

Vue.filter('kFormatter', (value) => {
    if (!value) return 0;

    return Math.abs(value) > 999
        ? Math.sign(value) * ((Math.abs(value) / 1000).toFixed(1)) + 'K'
        : Math.sign(value) * Math.abs(value)
})
