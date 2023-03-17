<template>
  <div class="permissions">
    <div class="perm-header pa-2">
      <p class="perm-title mb-0">{{ this.$tr('title') }}.</p>
    </div>
    <v-row class="ma-0">
      <v-col cols="12" md="4" class="pa-0 border-right">
        <div class="sys-titles px-1 my-2">
          <div
            v-for="(system, i) in selectedCompaniesSystems"
            :key="i"
            @click="tabChange(i + 1, system)"
            :class="`mb-2 py-1 sys-title color-primary d-flex align-center ss${i} ${
              tab == 'tab-' + (i + 1) ? 'current primary--text' : ''
            }`"
          >
            <div class="icon-container">
              <!-- <v-icon class="">{{ system.logo }}</v-icon> -->
              <img :src="system.logo" alt="" class="w-full" />
            </div>
            <span>{{ system.name }}</span>
            <CheckIcon
              height="22"
              class="primary--text"
              v-if="hasSelected(system.id)"
            />
          </div>
        </div>
      </v-col>
      <v-col cols="12" md="8" class="pa-1 py-3">
        <div class="sys-perm elevation-1">
          <div class="sys-perm-title">
            <div class="sys-title color-primary d-flex align-center">
              <div class="icon-container">
                <img
                  :src="selectedSystem != null ? selectedSystem.logo : ''"
                  alt=""
                  class="w-full"
                />
              </div>
              <span>{{
                selectedSystem != null ? selectedSystem.name : ""
              }}</span>
            </div>
          </div>
          <v-row class="ma-0">
            <v-col cols="12" md="6" class="border-right py-3 px-2">
              <v-tabs-items
                v-model="tab"
                v-for="(system, i) in selectedCompaniesSystems"
                :key="i"
              >
                <v-tab-item
                  :value="'tab-' + (i + 1)"
                  v-if="tab == 'tab-' + (i + 1)"
                >
                  <v-checkbox
                    v-model="sub_systemsAllArr"
                    :label="$tr('all')"
                    :value="system.id"
                    hide-details
                    :key="'all'"
                    class="perm-check mt-0"
                    @click="systemAllClicked(`${system.id}`)"
                  >
                  </v-checkbox>
                  <template v-for="(sub_system, i) in system.sub_systems">
                    <div :key="i">
                      <v-checkbox
                        v-model="sub_systemsArr"
                        :label="`${$tr(sub_system.name)}`"
                        :value="`${system.id}|${sub_system.id}`"
                        hide-details
                        class="perm-check mt-0"
                        @click="
                          subSystemClicked(`${system.id}|${sub_system.id}`)
                        "
                      >
                      </v-checkbox>
                      <div
                        class="perm-check-level-2 ps-4"
                        v-if="
                          isInArr(
                            `${system.id}|${sub_system.id}`,
                            sub_systemsArr
                          )
                        "
                      >
                        <v-checkbox
                          v-for="(action, i) in sub_system.actions"
                          :key="i"
                          v-model="actionsArr"
                          :label="action.name"
                          :value="`${system.id}|${sub_system.id}|${action.id}`"
                          hide-details
                          @click="getPermissions"
                          class="perm-check mt-0"
                        ></v-checkbox>
                      </div>
                    </div>
                  </template>
                </v-tab-item>
              </v-tabs-items>
            </v-col>
            <v-col cols="12" md="6" class="py-3 px-2">
              <div class="choose-project">
                <div class="time">
                  <h3 class="time-title mb-2">
                    {{ $tr("company") }}
                  </h3>
                  <div
                    class="sys-title color-primary d-flex align-center mb-3"
                    v-for="(company, i) in selectedCompanies"
                    :key="i"
                  >
                    <div class="icon-container">
                      <img :src="company.logo" alt="" class="w-full" />
                    </div>
                    <span>{{ company.name }}</span>
                  </div>
                  <h3 class="time-title">
                    {{ $tr("time_schedule") }}
                  </h3>
                  <v-radio-group v-model="radio" class="mt-0">
                    <v-radio
                      :label="$tr('unlimited')"
                      :value="'unlimited'"
                      @click="setTime"
                    ></v-radio>
                    <v-radio
                      :label="$tr('limited')"
                      :value="'limited'"
                      @click="setTime"
                    ></v-radio>
                    <div class="px-3" v-if="radio === 'limited'">
                      <v-radio-group
                        v-model="restrictDateTime"
                        class="mt-0"
                        :error-messages="scheduleErrorMessage"
                      >
                        <v-radio
                          :label="$tr('date')"
                          :value="'date'"
                          @click="setTime"
                        ></v-radio>
                        <div
                          class="d-flex ps-3"
                          v-if="restrictDateTime === 'date'"
                        >
                          <CustomInput
                            small
                            placeholder="end_date"
                            type="date-picker"
                            class="mb-1 w-full"
                            :date-value="end_date"
                            hideDetails
                            ref="dateInputRef"
                            @getDate="getEndDate"
                          />
                        </div>
                        <v-radio
                          :label="$tr('time')"
                          :value="'time'"
                          @click="setTime"
                        ></v-radio>
                        <div
                          class="d-flex ps-3"
                          v-if="restrictDateTime === 'time'"
                        >
                          <CustomInput
                            small
                            placeholder="end_time"
                            type="time-picker"
                            class="mb-1 w-full"
                            :range="true"
                            hideDetails
                            :time-value="end_time"
                            ref="timeInputRef"
                            @getTime="getEndTime"
                          />
                        </div>
                      </v-radio-group>
                    </div>
                  </v-radio-group>
                </div>
              </div>
            </v-col>
          </v-row>
        </div>
      </v-col>
    </v-row>
  </div>
</template>
<script>
import CheckIcon from "./components/CheckIcon.vue";
import CustomInput from "./components/CustomInput.vue";
import SelectedItem from "./components/SelectedItem.vue";

export default {
  components: {
    CheckIcon,
    CustomInput,
    SelectedItem,
  },
  props: {
    selectedCompanies: {
      type: Array,
    },

    allCompanies: {
      type: Array,
    },
  },
  fetch() {
    const companyIds = this.selectedCompanies?.map((company) => company?.id);
    this.fetchCompaniesSystems(companyIds);
  },

  data() {
    return {
      date: "",
      datepicker: false,
      timepicker1: false,
      timepicker2: false,
      end_time: null,
      end_date: null,
      restrictDateTime: "date",
      sub_systemsArr: [],
      actionsArr: [],
      sub_systemsAllArr: [],
      radio: "unlimited",
      tab: "tab-1",
      selectedSystem: null,
      scheduleErrorMessage: this.$tr("please_select_one_option"),
      selectedCompaniesInner: this.selectedCompanies,
      selectedCompaniesSystems: [],
    };
  },

  watch: {
    selectedCompanies(value) {
      const companyIds = value?.map((company) => company?.id);
      this.fetchCompaniesSystems(companyIds);
    },
  },
  methods: {
    async fetchCompaniesSystems(companyIds) {
      try {
        const response = await this.$axios.get(
          `systems?company-ids=${companyIds}`
        );
        this.selectedCompaniesSystems = response?.data;
        if (this.selectedCompaniesSystems.length > 0) {
          this.selectedSystem = this.selectedCompaniesSystems[0];
        }
      } catch (e) {}
    },

    setTime() {
      let obj = {};
      if (this.radio === "limited") {
        if (this.restrictDateTime === "date") {
          obj.schedule_type = "date";
          obj.end_date = this.end_date;
        } else if (this.restrictDateTime === "time") {
          obj.schedule_type = "time";
          obj.end_time = this.end_time;
        }
      } else if (this.radio === "unlimited") {
        obj.schedule_type = "unlimited";
      }
      this.checkScheduleValidation();
      this.$emit("getTimeDate", obj);
    },

    getEndTime(time) {
      this.end_time = time;
      this.setTime();
    },

    getEndDate(date) {
      this.end_date = date;
      this.setTime();
    },

    tabChange(num, system) {
      this.tab = "tab-" + num;
      this.selectedSystem = system;
    },

    getPermissions() {
      let arr = [];
      this.actionsArr.forEach((action) => {
        action = action.split("|");
        arr.push({
          system_id: action[0],
          sub_system_id: action[1],
          action_id: action[2],
        });
      });
      this.$emit("getPermissions", arr);
    },

    subSystemClicked(sub_system) {
      if (!this.isInArr(sub_system, this.sub_systemsArr)) {
        this.actionsArr = this.actionsArr.filter((action) => {
          return !(
            action.split("|")[0] == sub_system.split("|")[0] &&
            action.split("|")[1] == sub_system.split("|")[1]
          );
        });
      }
      this.getPermissions();
    },
    systemAllClicked(system) {
      if (this.isInArr(system, this.sub_systemsAllArr)) {
        this.removeSystemsStuff(system);
        this.selectedCompaniesSystems.forEach((sys) => {
          if (sys.id == system) {
            sys.sub_systems.forEach((sub_sys) => {
              this.sub_systemsArr.push(`${sys.id}|${sub_sys.id}`);
              sub_sys.actions.forEach((act) => {
                this.actionsArr.push(`${sys.id}|${sub_sys.id}|${act.id}`);
              });
            });
          }
        });
      } else {
        this.removeSystemsStuff(system);
      }
      this.getPermissions();
    },

    removeSystemsStuff(system) {
      this.sub_systemsArr = this.sub_systemsArr.filter((sub_system) => {
        return !(sub_system.split("|")[0] == system);
      });
      this.actionsArr = this.actionsArr.filter((action) => {
        return !(action.split("|")[0] == system);
      });
    },

    isInArr(str, arr) {
      let res = arr.find((item) => {
        return item == str;
      });
      return res !== undefined;
    },

    hasSelected(system) {
      let res = this.sub_systemsArr.find((item) => {
        return item.split("|")[0] === system;
      });
      return res !== undefined;
    },

    checkScheduleValidation() {
      if (this.radio === "limited") {
        if (this.restrictDateTime === "date") {
          if (this.end_date) {
            this.scheduleErrorMessage = "";
            return true;
          }
          this.scheduleErrorMessage = this.$tr(
            "please_select_end_date"
          );
          return false;
        } else if (this.restrictDateTime === "time") {
          if (this.end_time) {
            this.scheduleErrorMessage = "";
            return true;
          }
          this.scheduleErrorMessage = this.$tr(
            "please_select_end_time"
          );
          return false;
        }
        return false;
      } else if (this.radio === "unlimited") {
        return true;
      }
    },
  },
};
</script>
<style scoped>
.permissions,
.selected-projects {
  border: 1px solid var(--input-placeholder-color);
}

.perm-header {
  background: var(--stepper-header-bg);
}

.perm-title {
  font-size: 16px;
  color: var(--input-title-color);
  letter-spacing: 1px;
}

.border-right {
  border-right: 1px solid var(--input-placeholder-color);
}

@media only screen and (max-width: 960px) {
  .border-right {
    border-right: 0 !important;
  }
}

.v-application--is-rtl .border-right {
  border-left: 1px solid var(--input-placeholder-color);
}

.sys-title {
  font-weight: 500;
  cursor: pointer;
}

.sys-title .icon-container {
  height: 20px;
  width: 20px;
  overflow: hidden;
  background: var(--input-title-color) !important;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 4px;
  border-radius: 3px;
}

.sys-title.current .icon-container {
  background: var(--v-primary-base) !important;
}

.sys-title .v-icon {
  font-size: 12px;
  vertical-align: middle;
  color: var(--color-white);
}

.v-application--is-rtl .sys-title .v-icon {
  margin-left: 4px;
}

.sys-perm {
  border: 1px solid var(--input-placeholder-color);
  border-radius: 4px;
}

.sys-perm-title {
  padding: 4px 8px;
  border-bottom: 1px solid var(--input-placeholder-color);
}

.time-title {
  font-size: 16px;
  font-weight: 500;
}

.perm-check .v-label {
  font-weight: 500;
}

.perm-check-level-2 .perm-check .v-label {
  font-size: 12px;
  font-weight: 400;
}

.perm-check-level-2 .perm-check .v-icon {
  font-size: 16px !important;
}

.perm-check-level-2
  .perm-check
  .v-input--selection-controls__input:hover
  .v-input--selection-controls__ripple:before {
  transform: scale(0.8) !important;
}
</style>
