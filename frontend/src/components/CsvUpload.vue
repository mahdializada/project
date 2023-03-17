<template>
  <div style="height: 500px">
        Dowload CSV: 
        <CustomButton
          icon="fa-download"
          text="CSV Download"
          type="primary"
        />
        Import CSV: 
        <CustomButton
          icon="fa-plus"
          text="CSV Download"
          type="primary"
        />
  </div>
</template>
<script>
import CustomButton from './design/components/CustomButton.vue';
export default {
  components: {
    CustomButton
  },
  data() {
    return {
      csv: null,
    };
  },
};
</script>
