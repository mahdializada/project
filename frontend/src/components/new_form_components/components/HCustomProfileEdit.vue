<template>
  <span>
    <v-row
      v-if="singleRow"
      :style="
        bgColor
          ? 'background-color: var(--v-surface-darken1)'
          : 'background-color: inherit'
      "
    >
      <v-col cols="12" md="12" class="py-1 ps-2 ps-md-6 d-flex">
        <div style="width: 24%" class="font-weight-black">
          {{ $tr(headerRow) }}
        </div>

        <div style="width: 76%">
          <CustomInput
            :hideDetails="true"
            type="textfield"
            v-bind="$attrs"
            v-on="$listeners"
          />
        </div>

        <div style="width: 76%" v-if="contentRowSelect">
          <CustomInput
            :hideDetails="true"
            v-model="contentRowSelect"
            type="select"
            :items="items"
            item-text="type"
            item-value="type"
          />
        </div>

        <div style="width: 76%" v-if="listItem">
          <span v-for="(item, i) in listItem" :key="i">
            {{ item.name }} ,
          </span>
        </div>

  
      </v-col>
    </v-row>

    <v-row
      v-else
      :style="
        bgColor
          ? 'background-color: var(--v-surface-darken1)'
          : 'background-color: inherit'
      "
    >
      <v-col cols="12" md="6" class="py-1 ps-6 d-flex">
        <div style="width: 50%" class="font-weight-black">
          {{ $tr(headerRow) }}
        </div>
        <div style="width: 50%" v-if="contentRow">
          <CustomInput
          :hideDetails="true"
          v-model="contentRow"
            type="textfield"
          />
        </div>

        <div style="width: 50%" v-if="linkRow1">
          <CustomInput
          :hideDetails="true"
          v-model="linkName1"
            type="textfield"
          />
        </div>




          <div style="width:50%" v-if="contentRowTooltip">
            <CustomInput
            :hideDetails="true"
            v-model="contentRowTooltip"
            type="select"
            :items="contentRowTooltipItems"
            item-text="name"
            item-value="id"
          />
          </div>

          <div style="width:50%" v-if="contentselect">
            <CustomInput
            :hideDetails="true"
            v-model="contentselect"
            type="select"
            :items="contentselectItems"
            item-text="type"
            item-value="type"
          />
          </div>
      </v-col>
      <v-divider vertical v-if="dividerRow"> </v-divider>
      <v-col cols="12" md="6" class="py-1 d-flex">
        <div style="width: 50%" class="font-weight-black">
          {{ $tr(headerRow2) }}
        </div>
        <div style="width: 50%" v-if="contentRow2">
            <CustomInput
            :hideDetails="true"
            v-model="contentRow2"
            type="textfield"
            
          />
        </div>
        <div style="width: 50%" v-if="contentRowSelect2">
            <CustomInput
            item-text="type"
            item-value="type"
            :hideDetails="true"
            v-model="contentRowSelect2"
            type="select"
            :items="items2"

          />
        </div>


        <div style="width: 50%" v-if="linkRow2">
          <CustomInput
          :hideDetails="true"
          v-model="linkName2"
            type="textfield"
          />
        </div>

        
          <div v-if="contentRow2Tooltip">
            <CustomInput
            :hideDetails="true"
            v-model="linkName2"
              type="textfield"
              :items="contentRowTooltipItems2"
            item-text="name"
            item-value="id"
            />
          </div>

      </v-col>
    </v-row>
  </span>
</template>
<script>
import CustomInput from '../../design/components/CustomInput.vue';

export default {
    props: {
        headerRow: {
            type: String,
        },
        contentRow: {
            type: String,
        },
        headerRow2: {
            type: String,
        },
        contentRow2: {
            type: String,
        },
        contentRowSelect: {
            type: String,
        },
        contentRowSelect2: {
            type: String,
        },
        dividerRow: {
            type: Boolean,
            default: true,
        },
        bgColor: {
            type: Boolean,
            default: false,
        },
        contentRowTooltipItems: {
            type: Array,
        },
        contentRowTooltip: {
            type: Array,
        },
        tooltipName: {
            type: String,
        },
        contentRow2Tooltip: {
            type: Array,
        },
        contentRowTooltipItems2: {
            type: Array,
        },
        tooltipName2: {
            type: String,
        },
        singleRow: {
            type: Boolean,
            default: false,
        },
        linkRow1: {
            type: String,
        },
        linkRow2: {
            type: String,
        },
        linkName2: {
            type: String,
        },
        linkName1: {
            type: String,
        },
        listItem: {
            type: Array,
        },
        items: {
            type: Array,
        },
        items2: {
            type: Array,
        },
        contentselectItems: {
            type: Array,
        },
        contentselect: {
            type: String,
        },
        editable: {
            type: Boolean,
            default: false,
        },
    },
    components: { CustomInput }
};
</script>

<style>
::v-deep(.v-text-field.v-text-field--enclosed .v-text-field__details) {
  display: none;
}
</style>