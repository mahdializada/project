<template>
  <InputCard InputCard pbNone v-bind="$attrs" v-on="$listeners">
    <template slot-scope="{ attrs, listeners }">
      <v-select class="form-new-item form-custom-select" background-color="var(--new-input-bg)" outlined
        :rounded="rounded" :no-data-text="$tr('no_data_available')" :menu-props="{
          bottom: true,
          offsetY: true,
        }" v-bind="attrs" v-on="listeners">
        <template v-if="menuTitle" v-slot:prepend-item>
          <span style="min-width:300px" class="mx-3 text-h6 font-weight-medium"
            :class="for_column ? 'd-flex justify-space-between' : ''">
            <span> {{ menuTitle }}</span>
            <span v-if="for_column"> {{ $tr("default") }}</span>
          </span>
        </template>

        <template v-slot:[`selection`]="{ item }">
          <div class="v-select__selection--comma">
            {{ itemText ? item[itemText] : item }}{{ multiple ? "," : "" }}
          </div>
        </template>
        <template v-slot:[`item`]="{ item, on, attrs }">
          <v-list-item class="select-list-item-custom" :class="for_column ? 'pe-0' : ''" v-on="on" v-bind="attrs">
            <div :class="`select-item-custom w-full d-flex ${!for_column ? 'justify-space-between' : 'justify-space-around'
            } align-center`" style="font-weight: 400" :style="`${for_column
  ? 'width:300px !important ; padding: 5px 0 5px 0'
  : ''
  }`">
              <span>
                {{ itemText ? item[itemText] : item }}
                <p class="mb-0 text-caption" v-if="for_column">
                  {{
                      $auth.user.id == item.user_id
                        ? $tr("you")
                        : item.user.firstname + " " + item.user.lastname
                  }}
                </p>
              </span>
              <span class="d-flex">
                <div v-if="for_column" class="me-2">
                  <span class="text-caption">
                    {{ item.scope_type ? $tr("public") : $tr("private") }}</span>
                  <v-icon :disabled="$auth.user.id != item.user_id" class="ms-1"
                    @click.stop="$emit('deleteView', item.id)">mdi-delete</v-icon>
                </div>

                <!-- <v-checkbox
                  v-if="for_column"
                  :disabled="$auth.user.id != item.user_id"
                  v-model="item.default"
                  @click.stop="$emit('defaultView', item)"
                  hide-details
                  color="primary"
                  class="ms-auto mt-0 pt-0"
                  :false-value="0"
                  :true-value="1"
                ></v-checkbox> -->
                <v-switch inset v-if="for_column" :disabled="$auth.user.id != item.user_id" v-model="item.default"
                  @click.stop="$emit('defaultView', item)" hide-details color="primary" class="ms-auto mt-0 pt-0"
                  :false-value="0" :true-value="1"></v-switch>
                <v-checkbox v-if="checkbox" v-model="attrs.inputValue" hide-details color="primary"
                  class="ms-auto mt-0 pt-0"></v-checkbox>
              </span>
            </div>
          </v-list-item>
        </template>
      </v-select>
    </template>
  </InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
  props: {
    itemText: String,
    multiple: Boolean,
    menuTitle: String,
    rounded: {
      type: Boolean,
      default: true,
    },
    checkbox: {
      type: Boolean,
      default: true,
    },
    for_column: {
      type: Boolean,
      default: false,
    },
  },
  components: { InputCard },
};
</script>
<style>
.form-custom-select .v-input__prepend-inner {
  margin-top: 16px;
  margin-right: 8px !important;
}

.select-list-item-custom {
  border: 1px solid transparent;
  border-radius: 12px;
  margin: 0 12px;
}

.select-list-item-custom:hover {
  border: 1px solid var(--v-primary-base);
}

.select-list-item-custom:hover::before {
  color: transparent;
}

.select-list-item-custom::before {
  color: transparent;
  border-radius: 12px;
}
</style>
