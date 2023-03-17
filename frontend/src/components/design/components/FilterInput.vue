<template>
  <div class="filter-inputs my-1">
    <label class="subtitle-1 text-capitalize"> {{ $tr(label) }} </label>
    <v-text-field
      v-bind="$attrs"
      v-on="$listeners"
      :placeholder="placeholder != '' ? $tr(placeholder) : $tr(label)"
      filled
      :type="type == 'number' ? 'number' : 'text'"
      dense
      clearable
      hide-details
      full-width
      v-if="type == 'textfield' || type == 'number'"
    >
    </v-text-field>
    <v-autocomplete
      full-width
      v-else-if="type == 'autocomplete'"
      :item-text="itemText == 'name' ? 'name' : itemText"
      :item-value="itemValue == 'id' ? 'id' : itemValue"
      :items="items"
      dense
      :clearable="clearable"
      hide-details
      filled
      v-bind="$attrs"
      v-on="$listeners"
      :no-data-text="$tr('no_data_available')"
      :placeholder="placeholder != '' ? $tr(placeholder) : $tr(label)"
    >
      <template v-if="hasFlag" v-slot:[`item`]="{ item }">
        <v-list-item-content>
          <div class="d-flex align-center">
            <div class="me-1">
              <FlagIcon :flag="item.iso2.toLowerCase()" round />
            </div>
            <div>
              <v-list-item-title v-value="`${item.name}`"></v-list-item-title>
            </div>
          </div>
        </v-list-item-content>
      </template>
      <template v-else-if="hasLogo" v-slot:[`item`]="{ item }">
        <v-list-item-content>
          <div class="d-flex align-center">
            <div class="me-1">
              <v-avatar size="30">
                <img :src="item[logoName]" alt="" />
              </v-avatar>
            </div>
            <div>
              <v-list-item-title
                v-value="`${item[itemText]}`"
              ></v-list-item-title>
            </div>
          </div>
        </v-list-item-content>
      </template>
      <template v-else-if="hasAvatar" v-slot:[`item`]="{ item }">
        <v-list-item-content>
          <div class="d-flex align-center">
            <div class="me-1">
              <v-avatar color="primary" size="30">
                <span class="font-weight-bold white--text">
                  {{ item[avatarField].charAt(0).toUpperCase() }}</span
                >
              </v-avatar>
            </div>
            <div>
              <v-list-item-title>{{ item[avatarField] }} </v-list-item-title>
            </div>
          </div>
        </v-list-item-content>
      </template>
      <template v-else-if="hasIcon" v-slot:[`item`]="{ item }">
        <v-list-item-content>
          <div class="d-flex align-center">
            <v-icon>{{ item.icon }}</v-icon>
            <v-list-item-title class="px-1">
              {{ item[itemText] }}</v-list-item-title
            >
          </div>
        </v-list-item-content>
      </template>

      <template v-else v-slot:[`item`]="{ item }">
        {{ $tr(item[itemText]) }}
      </template>

      <template v-slot:[`selection`]="{ item }">
        <div class="white--text">
          {{ $tr(item[itemText]) }}
        </div>
      </template>
    </v-autocomplete>

    <div v-else-if="type == 'id_filtering'">
      <v-text-field
        placeholder="1000100"
        filled
        clearable
        dense
        hide-details
        v-model="id"
        @keyup.enter="addId"
        full-width
      >
      </v-text-field>
      <div
        class="id-container mt-1 pt-0 d-flex align-center flex-wrap mb-1 pt-1"
      >
        <div v-for="id in ids" :key="id.key" class="team-ids d-inline-block">
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
        v-model="isInclude"
        rounded
        class="pa-0 ma-0 float-right"
        mandatory
      >
        <v-btn @click="toggleId(0)" active-class="error white--text" x-small>
          {{ $tr("exclude_caps") }}
        </v-btn>
        <v-btn @click="toggleId(1)" active-class="primary white--text" x-small>
          {{ $tr("include_caps") }}
        </v-btn>
      </v-btn-toggle>
    </div>
    <v-menu
      v-else-if="type == 'date-range'"
      style="top: sticky !important; z-index: 1000000"
      v-model="showDateMenu"
      :close-on-content-click="false"
      :nudge-right="40"
      transition="scale-transition"
      offset-y
      min-width="auto"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-text-field
          :placeholder="placeholder != '' ? $tr(placeholder) : $tr(label)"
          prepend-inner-icon="mdi-calendar"
          readonly
          filled
          dense
          hide-details
          clearable
          v-model="dateRange"
          v-bind="attrs"
          v-on="on"
        ></v-text-field>
      </template>
      <v-date-picker v-model="dateRange" range>
        <v-spacer></v-spacer>
        <v-btn
          text
          color="primary"
          @click="
            () => {
              showDateMenu = false;
              dateRange = null;
            }
          "
        >
          {{ $tr("cancel") }}
        </v-btn>
        <v-btn
          text
          color="primary"
          @click="
            () => {
              $emit('getDate', dateRange, label);
              showDateMenu = false;
            }
          "
        >
          {{ $tr("ok") }}
        </v-btn>
      </v-date-picker>
    </v-menu>

    <div class="d-flex flex-row pt-1" v-else-if="type == 'time'">
      <v-text-field
        type="number"
        hide-details
        :label="$tr('days')"
        class="pe-1 py-0"
        v-model="consumedTime.days"
        @blur="sendTime"
      />
      <v-text-field
        v-model="consumedTime.hours"
        type="number"
        :label="$tr('hours')"
        class="pe-1 py-0"
        hide-details
        @blur="sendTime"
      />
      <v-text-field
        v-model="consumedTime.minutes"
        type="number"
        :label="$tr('minutes')"
        class="px-0 py-0"
        hide-details
        @blur="sendTime"
      />
    </div>
    <div class="d-flex flex-row pt-1" v-else-if="type == 'time-range'">
      <v-menu
        ref="dialog2"
        v-model="menu2"
        class="rounded-0"
        :close-on-content-click="false"
        :nudge-right="40"
        transition="scale-transition"
        offset-y
        min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="time"
            :placeholder="$tr('activity_time')"
            prepend-inner-icon="mdi-clock-time-four-outline"
            readonly
            filled
            dense
            v-bind="attrs"
            v-on="on"
          ></v-text-field>
        </template>
        <template>
          <div class="pb-1 white" style="background: white">
            <v-row justify="space-around" class="px-3 mb-2">
              <v-time-picker
                class="rounded-0"
                format="ampm"
                v-model="startTime"
                :max="endTime"
              ></v-time-picker>
              <v-time-picker
                class="rounded-0"
                format="ampm"
                v-model="endTime"
                :min="startTime"
              >
              </v-time-picker>
            </v-row>
            <div class="d-flex">
              <v-spacer />
              <v-btn
                @click="onOkayClicked"
                small
                color="primary"
                class="stepper-btn"
                :type="'button'"
              >
                {{ $tr("ok") }}
              </v-btn>

              <v-btn
                @click="onCancelClicked"
                text
                small
                color="primary"
                class="stepper-btn px-2 mx-2"
              >
                {{ $tr("cancel") }}
              </v-btn>
            </div>
          </div>
        </template>
      </v-menu>
    </div>
  </div>
</template>

<script>
import FlagIcon from "../../common/FlagIcon.vue";
export default {
  components: { FlagIcon },
  props: {
    label: {
      type: String,
      required: true,
    },
    placeholder: {
      type: String,
      default: "",
    },
    type: {
      type: String,
      default: "textfield",
    },
    clearable: {
      type: Boolean,
      default: true,
    },
    items: {
      type: Array,
      default: () => [],
    },
    itemText: {
      default: "name",
    },
    itemValue: {
      type: String,
      default: "id",
    },
    clearInput: {
      type: Boolean,
      default: false,
    },
    hasFlag: {
      type: Boolean,
      default: false,
    },
    hasLogo: {
      type: Boolean,
      default: false,
    },
    logoName: {
      type: String,
      default: "logo",
    },
    hasAvatar: {
      type: Boolean,
      default: false,
    },
    avatarField: {
      type: String,
      default: "name",
    },
    hasIcon: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      showDateMenu: false,
      dateRange: null,
      ids: [],
      id: "",
      isInclude: 1,

      consumedTime: {
        days: 0,
        hours: 0,
        minutes: 0,
      },
      menu2: false,
      startTime: "",
      endTime: "",
      time: "",
    };
  },

  watch: {
    clearInput: function (val, oldVal) {
      if (val) {
        this.showDateMenu = false;
        this.dateRange = null;
        this.ids = [];
        this.id = "";
        this.isInclude = 1;
        //
        this.menu2 = false;
        this.time = "";
        this.startTime = "";
        this.endTime = "";
      }
    },
  },

  methods: {
    //   new
    getRangeData() {
      const startTime = this.startTime;
      const endTime = this.endTime;
      const data = {
        // type: this.type,
        startTime: startTime,
        endTime: endTime,
      };
      return data;
    },
    onCancelClicked() {
      (this.startTime = ""),
        (this.endTime = ""),
        (this.menu2 = false),
        (this.time = "");
    },
    onOkayClicked() {
      this.menu2 = false;
      let formatedTime = this.getRangeData();
      this.time = formatedTime.startTime + " - " + formatedTime.endTime;

      this.$emit("getTimeRange", formatedTime.startTime, formatedTime.endTime);
    },

    // end
    addId() {
      if (!this.ids.includes(this.id)) this.ids.unshift(this.id);

      this.id = "";
      this.$emit("getIds", this.ids);
    },
    clearId(id) {
      this.ids = this.ids.filter((element) => element !== id);
      this.$emit("getIds", this.ids);
    },

    sendDate() {
      this.$emit("getDate", this.dateRange, this.label);
    },
    clearDate() {
      this.dateRange = null;
      this.showDateMenu = false;
      this.sendDate();
    },

    toggleId(value) {
      this.isInclude = value;
      this.$emit("isInclude", value);
    },

    sendTime() {
      for (const item in this.consumedTime) {
        this.consumedTime[item] = Math.abs(parseInt(this.consumedTime[item]));
        if (isNaN(this.consumedTime[item])) this.consumedTime[item] = 0;
      }
      this.$emit("getTime", this.consumedTime);
    },
  },
};
</script>

<style scoped>
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
.filter-inputs
  .v-input.v-input--is-dirty.v-input--dense.theme--light.v-select.v-autocomplete.v-text-field--filled {
  background-color: var(--v-primary-base);
}
.filter-inputs .v-input.v-input--dense.theme--light.v-select.v-autocomplete {
  background: #dbe7ff;
}
.filter-inputs
  .v-input.v-input--is-dirty.v-input--dense.theme--light.v-select.v-autocomplete
  .v-input__slot
  input {
  color: white !important;
  caret-color: white;
  font-weight: bold;
}
.filter-inputs
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder.v-select.v-autocomplete
  .v-select__slot
  input::placeholder {
  color: var(--v-primary-base);
  font-weight: bold;
}
.filter-inputs
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
.filter-inputs
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

.filter-inputs .v-input.v-input--dense.theme--light.v-text-field--placeholder {
  background: #dbe7ff;
}
.filter-inputs
  .v-input.v-input--dense.theme--light.v-text-field--placeholder
  .v-input__slot
  .v-input__prepend-inner
  .v-input__icon
  .v-icon {
  color: var(--v-primary-base);
}
.filter-inputs
  .v-input.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  input::placeholder {
  color: var(--v-primary-base);
  font-weight: bold;
}
.filter-inputs
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  input {
  color: white !important;
  font-weight: bold;
}
.filter-inputs
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder {
  background-color: var(--v-primary-base);
}
.filter-inputs
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  .v-input__prepend-inner
  .v-input__icon
  .v-icon {
  color: white !important;
}

.filter-inputs .v-input__append-inner .v-input__icon--clear .v-icon {
  color: white !important;
}

.filter-inputs
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--placeholder.v-select.v-autocomplete
  .v-input__slot {
  border-radius: 4px !important;
}
.filter-inputs
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--placeholder.v-select.v-autocomplete
  .v-input__slot::before,
.filter-inputs
  .v-input--is-focused.v-input.v-input--dense.theme--light.v-text-field.v-select.v-autocomplete
  .v-input__slot::after {
  display: none !important;
}
.filter-inputs
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder {
  background-color: var(--v-primary-base);
}
.filter-inputs
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  input {
  color: white !important;
  font-weight: bold;
  caret-color: white;
}
.filter-inputs
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot
  input::placeholder {
  color: var(--v-primary-base);
  font-weight: bold;
}
.filter-inputs
  .v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--placeholder
  .v-input__slot {
  border-radius: 4px !important;
}
.filter-inputs
  .v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--placeholder
  .v-input__slot::before,
.filter-inputs
  .v-input.v-input--is-label-active.v-input--is-dirty.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot::after {
  display: none !important;
}
.filter-inputs
  .v-input.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot::before,
.filter-inputs
  .v-input.v-input--is-readonly.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot::after {
  display: none !important;
}
.filter-inputs
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--placeholder
  .v-input__slot::before,
.filter-inputs
  .v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--is-booted.v-text-field--enclosed.v-text-field--placeholder
  .v-input__slot::after {
  display: none !important;
}
.filter-inputs .v-text-field--filled {
  border-radius: 4px;
}

.filter-inputs
  .v-input.v-input--dense.theme--light.v-text-field.v-text-field--single-line.v-text-field--filled.v-text-field--placeholder
  .v-input__slot {
  margin: 0;
}
.filter-inputs
  .v-input--dense.theme--light.v-text-field.v-text-field--placeholder
  .v-text-field__details {
  display: none;
}

.filter-inputs .icons .v-avatar.v-avatar.primary {
  border: 5px solid white !important;
}
</style>
