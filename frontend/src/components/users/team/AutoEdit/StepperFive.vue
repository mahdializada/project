<template>
  <div>
    <template>
      <div class="topDiv">
        <div class="topTitle">
          <h1
            class="text-center text-h4 font-weight-bold border-bottom py-2 text-uppercase"
            style="color: #58595b"
          >
            {{ $tr("remarks") }}
          </h1>
        </div>       
        <div class="mx-3">
          <CustomInput
            :items="customSelect"
            :label="$tr('label_required',$tr('remarks'))"
            type="textarea"
            class=""
          />
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import CustomInput from "~/components/design/components/CustomInput.vue";
export default {
  components: { CustomInput },
  props: {
    autoEditData: {
      require: true,
      type: Object,
    },
  },
  data() {
    return {
      customSelect: ["Foo", "Bar", "Fizz", "Buzz"],
    };
  },
  methods: {
    setNull(data) {
      for (const [key, value] of Object.entries(this.RolesForm)) {
        this.RolesForm[key] = data[key];
      }
    },
  },
};
</script>

<style>
.topDiv {
  position: relative;
}
.topTitle {
  top: 0px;
  position: sticky;
  z-index: 2;
  background-color: white;
}
</style>
