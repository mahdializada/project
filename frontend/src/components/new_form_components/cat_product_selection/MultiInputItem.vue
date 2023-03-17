<template>
  <div
    :class="`${title == 'ads' ? 'mt-0' : 'mt-5'}`"
    style="height: 90%"
    :key="i"
  >
    <InputCard
      height="80vh"
      class="my-2 px-2 overflow-auto expand"
      :title="$tr('add_item', $tr(title))"
      :sub_title="$tr('create_one_or_more', $tr(title))"
    >
      <v-expansion-panels flat accordion focusable mandatory>
        <v-timeline class="customTimeLine2" dense flat>
          <v-timeline-item class="pb-0" v-for="(item, ind) in items" :key="ind">
            <template v-slot:icon>
              <v-icon
                light
                size="15"
                :style="`${
                  item.$invalid ? 'color:red;font-weight: bold;' : 'color:white'
                }`"
                >{{
                  item.$invalid ? "mdi-close-thick" : "mdi-check-bold"
                }}</v-icon
              >
            </template>
            <v-expansion-panel>
              <v-expansion-panel-header
                disable-icon-rotate
                class="py-0 ps-1 pe-2 my-1 text-subtitle-2"
              >
                <div>
                  <p class="mb-0 primary--text text-h6 font-weight-medium">
                    {{ otherTitel?item[otherTitel]?.$model:$tr(title) + " " + (parseInt(ind) + 1) }}
                  </p>
                </div>
                <template v-slot:actions>
                  <span class="d-flex justify-center align-center">
                    <v-icon
                      @click.stop="closeLabel(ind)"
                      color="primary"
                      size="20"
                    >
                      mdi-close
                    </v-icon>
                  </span>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                <slot name="content" :item="item" :index="ind"></slot>
              </v-expansion-panel-content>
            </v-expansion-panel>
            <v-divider></v-divider>
          </v-timeline-item>
        </v-timeline>
      </v-expansion-panels>
      <div
        class="
          primary
          rounded-circle
          mx-4
          mt-2
          d-flex
          justify-center
          align-center
        "
        style="width: 35px; height: 35px"
        v-if="loading"
      >
        <v-progress-circular
          indeterminate
          color="white"
          :size="20"
        ></v-progress-circular>
      </div>

      <v-icon
        v-else
        size="20"
        class="primary rounded-circle pa-1 mb-5"
        color="white"
        style="left: 30px !important; top: 30px !important"
        @click="addOneMore"
        >mdi-plus
      </v-icon>
    </InputCard>
  </div>
</template>
<script>
import InputCard from "../components/InputCard.vue";

export default {
  props: {
    items: {
      required: true,
    },
    title: String,
    loading: Boolean,
    otherTitel:String,
  },
  data() {
    return {
      i: 0,
      key: 0,
    };
  },
  methods: {
    closeLabel(item) {
      this.$emit("removeItem", item);
    },
    addOneMore() {
      this.$emit("addMore");
    },
  },
  components: { InputCard },
};
</script>
<style>
.customTimeLine2 .v-timeline-item__dot {
  box-shadow: unset !important;
  position: absolute;
  top: 20px;
  left: calc(50% - 20px);
  border: 1px solid var(--v-primary-base);
  display: flex;
  align-items: center;
  justify-content: center;
  height: 40px;
  width: 40px;
}
.customTimeLine2.v-timeline::before {
  top: 50px !important;
}
.customTimeLine2 .v-expansion-panel-content__wrap {
  padding: 0 !important;
}
.customTimeLine2 .v-expansion-panels {
  z-index: 0 !important;
}
.expand .v-expansion-panels {
  display: block !important;
  z-index: 0 !important;
}
.expand .v-expansion-panel::before {
  box-shadow: none !important;
}
.expand .v-expansion-panel-header:before {
  background-color: var(--v-primary-base) !important;
  border-radius: 10px !important;
  left: -90px !important;
}
</style>
