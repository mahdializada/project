<template>
  <v-expand-transition>
    <div class="" v-if="data.length">
      <div class="my-2" style="font-size: 18px; font-weight: 500; color: #777">
        {{ $tr("Errors of Validations") }}
      </div>
      <v-row class="mx-0">
        <v-col cols="12 pa-0">
          <AdvertisementTab
            :items="tabItems"
            @onChange="(tabIndex) => changeErrorData(tabItems[tabIndex].key)"
            @unselect="onUnselected"
            :totalRecords="0"
          />
        </v-col>
      </v-row>
      <v-simple-table dense>
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left" v-for="(header, i) in headers" :key="i">
                {{ $tr(header.title) }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr class="not-hover-tr__force" v-for="(row, i) in data" :key="i + 'row'">
              <template v-for="(header, i) in headers">
                <td
                  v-if="header.value == 'descriptions'"
                  :key="i + 'header'"
                  class="error--text"
                >
                  <v-icon color="error" size="18"> mdi-alert-circle-outline </v-icon>
                  {{ Array.from(row[header.value]).join(", ") }}
                </td>
                <td v-else :key="i + 'header2'">
                  {{ row[header.value] }}
                </td>
              </template>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </div>
  </v-expand-transition>
</template>
<script>
import AdvertisementTab from "../../advertisement/AdvertisementTab.vue";

export default {
  components: {
    AdvertisementTab,
  },
  props: {
    headers: Array,
    tabItems: Array,
    isDataClean: Boolean,
    data: {
      type: Array,
      default: () => [],
    },
    changeErrorData: {
      type: Function,
      default: () => {},
    },
  },
  data() {
    return {
      totalRecords: 0,
    };
  },
  computed: {
    geTotalError() {
      return this.data.length;
    },
  },
  methods: {
    onUnselected() {},
  },
};
</script>
