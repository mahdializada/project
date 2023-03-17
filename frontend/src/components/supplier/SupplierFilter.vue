<template>
  <v-card class="departmentFilter">
    <v-card-title primary-title class="border">
      <span class="me-1">
        <v-icon>mdi-filter</v-icon>
      </span>
      <h2>{{ $tr("supplier_filter") }}</h2>
      <v-spacer />
      <CloseBtn @click="closeDialog" class="title" />
    </v-card-title>

    <v-card-text>
      <v-form ref="supplierFilterForm" lazy-validation>
        <v-row class="">
          <v-col cols="12" sm="4" class="pa-0">
            <v-row class="ma-0 pa-0">
              <v-col class="ma-0 pa-0">
                <v-card class="mx-auto rounded-0">
                  <div class="scroll">
                    <h2 class="text-center mt-1">
                      {{ $tr("selection") }}
                    </h2>
                    <v-col cols="12 pb-0 mb-0" class="">
                      <label class="subtitle-1">{{
                        $tr("label_required", $tr("prepration_period"))
                      }}</label>
                      <v-autocomplete
                        v-model="form.prepration_period"
                        :items="prepration_periods"
                        item-text="value"
                        item-value="value"
                        dense
                        filled
                        :placeholder="
                          $tr('choose_item', $tr('prepration_period'))
                        "
                      ></v-autocomplete>
                    </v-col>
                    <v-col cols="12 py-0 my-0" class="">
                      <label class="subtitle-1">{{
                        $tr("label_required", $tr("commercial_type"))
                      }}</label>
                      <v-autocomplete
                        v-model="form.commercial_type"
                        :items="commercialTypes"
                        item-text="type"
                        item-value="type"
                        dense
                        filled
                        :placeholder="
                          $tr('choose_item', $tr('commercial_type'))
                        "
                      ></v-autocomplete>
                    </v-col>
                    <v-col cols="12 py-0 my-0">
                      <label class="subtitle-1">{{
                        $tr("label_required", $tr("supply_type"))
                      }}</label>
                      <v-autocomplete
                        v-model="form.supply_type"
                        :items="supplyTypes"
                        item-text="type"
                        item-value="type"
                        dense
                        filled
                        :placeholder="$tr('choose_item', $tr('supply_type'))"
                      ></v-autocomplete>
                    </v-col>
                    <v-col cols="12 py-0 my-0 ">
                      <label class="subtitle-1">{{
                        $tr("label_required", $tr("legal_type"))
                      }}</label>
                      <v-autocomplete
                        v-model="form.legal_type"
                        :items="legalTypes"
                        item-text="type"
                        item-value="type"
                        dense
                        filled
                        :placeholder="$tr('choose_item', $tr('legal_type'))"
                      ></v-autocomplete>
                    </v-col>
                    <v-col cols="12 py-0 mt-0 mb-2">
                      <label class="subtitle-1">{{
                        $tr("label_required", $tr("country_type"))
                      }}</label>
                      <v-autocomplete
                        v-model="form.country_type"
                        :items="countryTypes"
                        item-text="type"
                        item-value="type"
                        dense
                        filled
                        :placeholder="$tr('choose_item', $tr('country_type'))"
                      ></v-autocomplete>
                    </v-col>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12 ma-0 pa-0" sm="4">
            <v-row class="ma-0 pa-0">
              <v-col class="ma-0 pa-0">
                <v-card class="mx-auto rounded-0">
                  <div class="scroll">
                    <h2 class="text-center mt-1">
                      {{ $tr("date_filter") }}
                    </h2>
                    <v-col cols="12 pb-0 mb-0" class="">
                      <label class="subtitle-1">{{
                        $tr("select_created_date")
                      }}</label>
                      <v-menu
                        ref="dialog"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            v-model="form.created_date"
                            :placeholder="$tr('select_created_date')"
                            prepend-inner-icon="mdi-calendar"
                            readonly
                            filled
                            dense
                            v-bind="attrs"
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="form.created_date" range>
                          <v-spacer></v-spacer>
                          <v-btn text color="primary" @click="createFalse()">
                            {{ $tr("cancel") }}
                          </v-btn>
                          <v-btn
                            text
                            color="primary"
                            @click="$refs.dialog.save(form.created_date)"
                          >
                            {{ $tr("ok") }}
                          </v-btn>
                        </v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12 py-0 my-0" class="">
                      <label class="subtitle-1">{{ $tr("last_edited") }}</label>
                      <v-menu
                        ref="dialog2"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            v-model="form.updated_date"
                            :placeholder="$tr('updated_date')"
                            prepend-inner-icon="mdi-calendar"
                            readonly
                            filled
                            dense
                            v-bind="attrs"
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="form.updated_date" range>
                          <v-spacer></v-spacer>
                          <v-btn text color="primary" @click="updateFalse()">
                            {{ $tr("cancel") }}
                          </v-btn>
                          <v-btn
                            text
                            color="primary"
                            @click="$refs.dialog2.save(form.updated_date)"
                          >
                            {{ $tr("ok") }}
                          </v-btn>
                        </v-date-picker>
                      </v-menu>
                    </v-col>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12 pa-0 ma-0" sm="4">
            <v-row class="ma-0 pa-0">
              <v-col class="ma-0 pa-0">
                <v-card class="mx-auto rounded-0">
                  <div class="scroll">
                    <h2 class="text-center mt-1">
                      {{ $tr("specifiec_filter") }}
                    </h2>
                    <v-col cols="12" class="">
                      <label class="subtitle-1">{{
                        `supplier ` + $tr("id")
                      }}</label>
                      <v-text-field
                        placeholder="1000100"
                        v-model="supplier_id"
                        @keyup.enter="addId"
                        filled
                        dense
                        class="mb-0 pb-0"
                      >
                      </v-text-field>
                      <div
                        class="id-container mt-1 pt-0 d-flex align-center flex-wrap mb-1 pt-1"
                      >
                        <div
                          v-for="id in form.supplier_ids"
                          :key="id.key"
                          class="department-ids d-inline-block"
                        >
                          <v-chip
                            close
                            small
                            color="primary"
                            class="ms-1 mb-1"
                            @click:close="clearId(id)"
                          >
                            {{ id }}
                          </v-chip>
                        </div>
                      </div>
                      <v-btn-toggle
                        v-model="form.include"
                        rounded
                        class="pa-0 ma-0 float-right"
                        mandatory
                      >
                        <v-btn
                          @click="form.include = 0"
                          active-class="primary white--text"
                          x-small
                        >
                          {{ $tr("include_caps") }}
                        </v-btn>
                        <v-btn
                          @click="form.include = 1"
                          active-class="error white--text"
                          x-small
                        >
                          {{ $tr("exclude_caps") }}
                        </v-btn>
                      </v-btn-toggle>
                      <div class="float-none"></div>
                    </v-col>
                    <v-col cols="12 py-0 my-0" class="">
                      <label class="subtitle-1">{{
                        $tr("supplier_name")
                      }}</label>
                      <v-text-field
                        :placeholder="$tr('supplier_name')"
                        filled
                        dense
                        class="mb-0 pb-0"
                        v-model="form.supplier_name"
                      >
                      </v-text-field>
                    </v-col>
                    <v-col cols="12 py-0 my-0" class="">
                      <label class="subtitle-1">{{
                        $tr("supplier_trading_name")
                      }}</label>
                      <v-text-field
                        :placeholder="$tr('supplier_trading_name')"
                        filled
                        dense
                        class="mb-0 pb-0"
                        v-model="form.supplier_trading_name"
                      >
                      </v-text-field>
                    </v-col>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
        <div class="d-flex flex-row-reverse mt-2">
          <TextButton
            type="secondary"
            class="ms-1 float-right"
            :text="$tr('cancel')"
            @click="cancel"
          >
          </TextButton>
          <TextButton
            type="danger"
            class="ms-1 float-right"
            :text="$tr('clear_filter_button')"
            @click="clear"
          >
          </TextButton>
          <TextButton
            type="primary"
            class="float-right"
            :text="$tr('submit')"
            @click="onSubmit"
          >
          </TextButton>
        </div>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import TextField from "../common/TextField.vue";
import CloseBtn from "~/components/design/Dialog/CloseBtn";
import TextButton from "../common/buttons/TextButton.vue";

export default {
  name: "SupplierFilter",
  components: { CloseBtn, TextField, TextButton },
  props: {
    prevFilterData: {
      type:Object,
      required: true,
    },
  },
  data() {
    return {
      supplier_id: "",
    form : {
      commercial_type: this?.prevFilterData?.commercial_type
        ? this.prevFilterData?.commercial_type
        : "",
      supplier_name: this?.prevFilterData?.supplier_name
        ? this.prevFilterData?.supplier_name
        : "",
      supplier_trading_name: this?.prevFilterData?.supplier_trading_name
        ? this.prevFilterData?.supplier_trading_name
        : "",
      include: this?.prevFilterData?.include ? this.prevFilterData?.include : 0,
      created_date: this?.prevFilterData?.created_date
        ? this.prevFilterData?.created_date
        : "",
      updated_date: this?.prevFilterData?.updated_date
        ? this.prevFilterData?.updated_date
        : "",
      country_type: this?.prevFilterData?.country_type
        ? this.prevFilterData?.country_type
        : "",
      prepration_period: this?.prevFilterData?.prepration_period
        ? this.prevFilterData?.prepration_period
        : "",
      supply_type: this?.prevFilterData?.supply_type
        ? this.prevFilterData?.supply_type
        : "",
      legal_type: this?.prevFilterData?.legal_type
        ? this.prevFilterData?.legal_type
        : "",
      supplier_ids:
        this?.prevFilterData?.supplier_ids?.length > 0
          ? this.prevFilterData?.supplier_ids
          : [],
    },

      // static selectBox
      commercialTypes: [
        { id: "1", type: "Distributer" },
        { id: "2", type: "Brand" },
        { id: "2", type: "Factory" },
      ],
      countryTypes: [
        { id: "1", type: "Local" },
        { id: "2", type: "International" },
      ],
      supplyTypes: [
        { id: "1", type: "Row Material" },
        { id: "2", type: "Ready Made" },
      ],
      legalTypes: [
        { id: "1", type: "Company" },
        { id: "2", type: "Indivitual" },
      ],
      prepration_periods:[
        { value:"1 Day"},
        { value:"2 Day"},
        { value:"3 Day"},
        { value:"4 Day"},
        { value:"5 Day"},
        { value:"6 Day"},
        { value:"7 Day"},
        { value:"8 Day"},
        { value:"9 Day"},
        { value:"10 Day"},
        { value:"11 Day"},
        { value:"12 Day"},
        { value:"13 Day"},
        { value:"14 Day"},
        { value:"15 Day"},
        { value:"16 Day"},
        { value:"17 Day"},
        { value:"18 Day"},
        { value:"19 Day"},
        { value:"20 Day"},
        { value:"21 Day"},
        { value:"22 Day"},
        { value:"23 Day"},
        { value:"24 Day"},
        { value:"25 Day"},
        { value:"26 Day"},
        { value:"27 Day"},
        { value:"28 Day"},
        { value:"29 Day"},
        { value:"30 Day"},
        { value:"31 Day"},
        { value:"32 Day"},
        { value:"33 Day"},
        { value:"34 Day"},
        { value:"35 Day"},
        { value:"36 Day"},
        { value:"37 Day"},
        { value:"38 Day"},
        { value:"39 Day"},
        { value:"40 Day"},
        { value:"41 Day"},
        { value:"42 Day"},
        { value:"43 Day"},
        { value:"44 Day"},
        { value:"45 Day"},
      ]
    };
  },
  created() {
   
  },
  methods: {
    addId() {
      if (!this.form.supplier_ids.includes(this.supplier_id)) {
        this.form.supplier_ids.unshift(this.supplier_id);
      }
      this.supplier_id = "";
    },
    clearId(id) {
      this.form.supplier_ids = this.form.supplier_ids.filter(
        (element) => element !== id
      );
    },
    closeDialog() {
      this.$emit("close");
    },
    updateFalse() {
      this.menu2 = false;
      this.form.updated_date = "";
    },
    createFalse() {
      this.menu = false;
      this.form.created_date = "";
    },
    async onSubmit() {
      this.$root.$emit("getData", this.form);
      this.$emit("getRecord", this.form);
      this.$emit("close");
    },
    clear() {
      this.supplier_id = "";
      this.form = {
        commercial_type: "",
        prepration_period: "",
        supply_type: "",
        country_type: "",
        legal_type: "",
        supplier_name: "",
        supplier_trading_name: "",
        supplier_ids: [],
        include: 0,
      };
      this.$root.$emit("getData", this.form);
      this.$emit("getRecord", this.form);
    },
    cancel() {
      // this.supplier_id = "";
      // this.form = {
      //   supplier_ids: [],
      // };
      // this.$root.$emit("getData", this.form);
      // this.$emit("getRecord", this.form);
      this.$emit("close");
    },
  },
};
</script>

<style scoped>
.departmentFilter .title {
  color: #b6c1d2 !important;
}
.button {
  color: #b6c1d2 !important;
}
.subtitle-1 {
  color: #8d9296;
}
.border {
  border-bottom: 0.5px solid #ece9e9;
}
div.scroll {
  width: 100%;
  max-height: 350px !important;
  height: 350px;
  overflow-y: scroll !important;
}
.show-cursor {
  cursor: pointer;
}
.id-container {
  min-height: 40px;
  max-height: 100px;
  overflow-y: scroll !important;
  width: 100%;
  border: 0.5px solid #ece9e9;
  border-radius: 4px;
}
</style>
<style>
/* Auto Complete Style Light theme */
.departmentFilter
  .v-input.v-input--is-dirty.v-input--dense.theme--light.v-select.v-autocomplete.v-text-field--filled {
  /* background: rgb(189 221 246 / 40%) !important; */
  background-color: var(--v-primary-base);
}
.departmentFilter .v-input.v-input--dense.theme--light.v-select.v-autocomplete {
  background: #dbe7ff;
}
.departmentFilter
  .v-input.v-input--is-dirty.v-input--dense.theme--light.v-select.v-autocomplete
  .v-input__slot
  input {
  color: white !important;
  font-weight: bold;
}
.departmentFilter
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder.v-select.v-autocomplete
  .v-select__slot
  input::placeholder {
  color: var(--v-primary-base);
  font-weight: bold;
}
.departmentFilter
  .v-input.v-input--is-dirty.v-input--dense.theme--light.v-select.v-autocomplete.v-text-field--filled
  .v-select__slot
  .v-input__append-inner
  .v-input__icon
  .v-icon,
.v-input.v-input--is-dirty.v-input--dense.theme--light.v-select.v-autocomplete.v-text-field--filled.v-select--is-menu-active
  .v-select__slot
  .v-input__append-inner
  .v-input__icon
  .v-icon {
  color: white !important;
}
.departmentFilter
  .v-input.v-input--dense.theme--light.v-select.v-autocomplete.v-text-field--filled
  .v-select__slot
  .v-input__append-inner
  .v-input__icon
  .v-icon,
.v-input.v-input--dense.theme--light.v-select.v-autocomplete.v-text-field--filled.v-select--is-menu-active
  .v-select__slot
  .v-input__append-inner
  .v-input__icon
  .v-icon {
  color: var(--v-primary-base);
}
/* End Of Auto Complete Light Theme */

.departmentFilter
  .v-input.v-input--dense.theme--light.v-text-field--placeholder {
  background: #dbe7ff;
}
.departmentFilter
  .v-input.v-input--dense.theme--light.v-text-field--placeholder
  .v-input__slot
  .v-input__prepend-inner
  .v-input__icon
  .v-icon {
  color: var(--v-primary-base);
}
.departmentFilter
  .v-input.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  input::placeholder {
  color: var(--v-primary-base);
  font-weight: bold;
}
.departmentFilter
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  input {
  color: white !important;
  font-weight: bold;
}
.departmentFilter
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder {
  background-color: var(--v-primary-base);
}
.departmentFilter
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  .v-input__prepend-inner
  .v-input__icon
  .v-icon {
  color: white !important;
}
.departmentFilter
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--placeholder.v-select.v-autocomplete
  .v-input__slot {
  border-radius: 4px !important;
}
.departmentFilter
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--placeholder.v-select.v-autocomplete
  .v-input__slot::before,
.departmentFilter
  .v-input--is-focused.v-input.v-input--dense.theme--light.v-text-field.v-select.v-autocomplete
  .v-input__slot::after {
  display: none !important;
}
.departmentFilter
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder {
  background-color: var(--v-primary-base);
}
.departmentFilter
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  input {
  color: white !important;
  font-weight: bold;
}
.departmentFilter
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  input::placeholder {
  color: var(--v-primary-base);
  font-weight: bold;
}
.departmentFilter
  .v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--placeholder
  .v-input__slot {
  border-radius: 4px !important;
}
.departmentFilter
  .v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--placeholder
  .v-input__slot::before,
.departmentFilter
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot::after {
  display: none !important;
}
.departmentFilter
  .v-input.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot::before,
.departmentFilter
  .v-input.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot::after {
  display: none !important;
}
.departmentFilter
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--placeholder
  .v-input__slot::before,
.departmentFilter
  .v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot::after {
  display: none !important;
}
.departmentFilter .v-text-field--filled {
  border-radius: 4px;
}
.departmentFilter
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--placeholder
  .v-input__slot {
  margin: 0;
}
.departmentFilter
  .v-input--dense.theme--light.v-text-field.v-text-field--placeholder
  .v-text-field__details {
  display: none;
}
</style>
