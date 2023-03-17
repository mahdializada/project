<template>
  <Edit
    :headers="headers"
    :autoEditData="autoEditData"
    :topIcon="topIcon"
    :tableName="tableName"
  >
    <template v-slot:name1>
      <div class="topDiv">
        <div class="topTilte">
          <h1
            class="text-center text-h4 font-weight-bold border-bottom pa-1"
            style="
              color: #58595b;
              border-bottom: 2px solid #bfc8d7;
              text-transform: uppercase;
            "
          >
            {{$tr('account')}}
          </h1>
        </div>
        <div class="pa-2 py-3">
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                :items="[]"
                :label="$tr('label_required',$tr('country'))"
                type="autocomplete"
                class="me-md-4 mb-md-2 mb-0"
              />
            </div>
            <div class="w-100">
              <FormBtn title="add_plus" class="mt-md-4 mb-2" />
            </div>
          </div>
          <div class="selected d-flex flex-wrap">
            <SelectedItem title="Afghanistan" flag="af" />
            <SelectedItem title="United Arab Emirates" flag="ua" />
            <SelectedItem title="Qatar" flag="qa" />
            <SelectedItem title="All Countries" icon="fa-globe" />
          </div>
          <CustomInput
            :items="investment_types"
            item-value="id"
            item-text="name"
            :label="$tr('label_required',$tr('investment_type'))"
            :placeholder="$tr('choose_item',$tr('investment_type'))"
            type="autocomplete"
          />
        </div>
      </div>
    </template>
    <template v-slot:name2>
      <div class="pb-3 topDiv">
        <div class="topTitle">
        <h1
          class="
            text-center text-h4
            font-weight-bold
            border-bottom
            pb-2
            pt-1
            text-uppercase
          "
          style="color: #58595b"
        >
          {{ $tr('project') }}
        </h1>
        </div>
        <div class="d-flex flex-column flex-md-row mt-3 mx-3">
          <div class="w-full">
            <CustomInput
              :label="$tr('label_required',$tr('name'))"
              type="autocomplete"
            />
          </div>
        </div>
        <div class="d-flex mx-auto justify-center mb-1 mt-2">
          <span class="text-h5 font-weight-bold">{{
            $tr("logo")
          }}</span>
        </div>
        <div
          class="d-flex justify-center mx-auto"
          style="width: 200px; height: 200px"
        >
          <v-file-input
            class="custom-logo"
            outlined
            prepend-icon=""
            dense
            @change="profileChange"
            :success="filePath != ''"
            hide-details
            :style="`background-image: url(${filePath})`"
          >
            <template slot="prepend-inner" class="px-0">
              <div
                style="max-width: 200px; text-align: center"
                :class="`${filePath != '' ? 'd-none' : ''}`"
              >
                <v-icon style="font-size: 5rem" color="primary">fa-plus</v-icon>
                <p class="text-subtitle-1 grey--text font-weigth-bold">
                  Upload Logo
                </p>
              </div>
            </template>
          </v-file-input>
        </div>
      </div>
    </template>
    <template v-slot:name3>
      <div class="topDiv">
        <div class="topTitle">
        <h1
          class="text-center text-h4 font-weight-bold border-bottom pa-1"
          style="
            color: #58595b;
            border-bottom: 2px solid #bfc8d7;
            text-transform: uppercase;
          "
        >
          {{$tr('remarks')}}
        </h1>
        </div>     
        <div class="mx-4">
          <CustomInput
            :label="$tr('label_required',$tr('remarks'))"
            type="textarea"
            class=""
          />
        </div>
      </div>
    </template>
  </Edit>
</template>

<script>
import Edit from "../../design/Edit.vue";
import CloseBtn from "~/components/design/Dialog/CloseBtn";
import FormBtn from "../../design/components/FormBtn";
import SelectedItem from "../../design/components/SelectedItem";
import CustomInput from "../../design/components/CustomInput";

export default {
  components: {
    CustomInput,
    SelectedItem,
    FormBtn,
    CloseBtn,
    Edit,
  },
  props: {
    autoEditData: {
      require: true,
      type: Object,
    },
  },
  data() {
    return {
      investment_types: [
        { id: 1, name: "Self" },
        { id: 2, name: "Other" },
      ],
      current: 1,
      checkbox1: true,
      back: false,
      topIcon: "mdi-account",
      tableName: 'roles',
      headers: [
        {
          icon: "fa-info",
          title: "general",
          slotName: "name1",
        },
        {
          icon: "fa-user",
          title: "project",
          slotName: "name2",
        },
        {
          icon: "fa-user-shield",
          title: "remarks",
          slotName: "name3",
        },
      ],
      filePath: "",
    };
  },
  methods: {
    profileChange(file) {
      if (file) {
        const reader = new FileReader();
        reader.addEventListener(
          "load",
          (e) => (this.filePath = e.target.result)
        );
        reader.addEventListener("error", (e) => (this.filePath = ""));
        reader.readAsDataURL(file);
      }
    },
  },
};
</script>

<style>
.main-Card {
  background-color: #f9fafd !important;
}

.main-Card .title {
  color: #b6c1d2 !important;
}

.main-Card .v-stepper .v-stepper__header .v-stepper__step__step {
  display: none !important;
}

.main-Card .v-stepper__step.v-stepper__step--active .v-stepper__label .v-btn {
  background-color: var(--v-primary-base);
  color: white;
}
.custom-logo {
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  background: #edf2f9;
}
.custom-logo .v-file-input__text {
  height: 110px;
  opacity: 0;
}
.custom-logo .v-input__slot fieldset {
  border-style: dashed !important;
}
.custom-logo .v-input__slot {
  min-height: 200px !important;
  display: flex;
  justify-content: center;
  align-items: center;
}
.upload-first-p {
  font-size: 18px;
  color: var(--upload-input-first-p);
  letter-spacing: 0.5px;
}
.upload-second-p {
  color: var(--input-border-color);
  line-height: 1.5;
}
.custom-logo .v-input__prepend-inner {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
}
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
