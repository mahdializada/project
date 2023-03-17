<template lang="">
  <v-card class="rounded-lg mx-1 pb-0" outlined width="250" height="350">
    <v-card-title class="py-1 justify-space-between">
      <p class="mb-0 grey--text">
        {{
          cardItem.stepCounter == 0
            ? $tr(cardItem.title)
            : cardItem.stepCounter == 1
            ? $tr("placement")
            : cardItem.stepCounter == 2
            ? $tr("budget_&_target_CPC")
            : "..."
        }}
      </p>
      <v-icon color="grey" @click="close()">mdi-close</v-icon>
    </v-card-title>
    <v-card-text class="overflow-y-auto" style="height: 240px">
      <v-scroll-x-reverse-transition>
        <div v-if="cardItem.stepCounter == 0">
          <div
            v-for="(item, index) in platform"
            :key="index"
            @click="firstStepClick(item)"
            class="rounded h-10 borderStyle d-flex align-center px-1 mb-1"
          >
            <v-icon color="primary">{{ item.icon }}</v-icon>
            <p class="mb-0 ms-1">{{ $tr(item.title) }}</p>
          </div>
        </div>
      </v-scroll-x-reverse-transition>

      <v-scroll-x-reverse-transition>
        <div v-if="cardItem.stepCounter == 1">
          <div
            v-for="(item, index) in getPlacement()"
            :key="index"
            @click="placementClick(item)"
            class="rounded h-10 d-flex align-center px-1 mb-1 justify-space-between"
            :class="
              cardItem.selectedPlacement.includes(item.title)
                ? 'borderStyleSelected'
                : 'borderStyle'
            "
          >
            <span class="d-flex">
              <v-icon color="primary">{{ item.icon }}</v-icon>
              <p class="mb-0 ms-1">{{ $tr(item.title) }}</p>
            </span>
            <v-icon
              v-if="cardItem.selectedPlacement.includes(item.title)"
              color="primary"
              >mdi-check-circle</v-icon
            >
          </div>
        </div>
      </v-scroll-x-reverse-transition>

      <v-scroll-x-reverse-transition>
        <div v-if="cardItem.stepCounter == 2">
          <CTextField
            hide-details
            v-model="cardItem.budget"
            dense
            px="px-0"
            py="py-0"
            type="number"
            :min="0"
            autofocus
            prepend-inner-icon="mdi-sack-percent"
            title="budget"
            :placeholder="$tr('budget')"
          />

          <CTextField
            hide-details
            v-model="cardItem.target_CPO"
            dense
            px="px-0"
            py="py-2"
            :min="0"
            type="number"
            prepend-inner-icon="mdi-bag-personal-tag"
            title="target_CPO"
            :placeholder="$tr('target_CPO')"
          />
        </div>
      </v-scroll-x-reverse-transition>
    </v-card-text>

    <v-card-actions class="justify-center" style="height: 60px">
      <v-btn
        @click="back()"
        v-if="cardItem.stepCounter != 0"
        icon
        outlined
        color="primary"
        class="mx-1"
        ><v-icon>{{ $vuetify.rtl?'mdi-chevron-right':'mdi-chevron-left'}}</v-icon></v-btn
      >
      <v-icon size="15" :color="cardItem.stepCounter == 0 ? 'primary' : 'grey'"
        >mdi-checkbox-blank-circle</v-icon
      >
      <v-icon size="15" :color="cardItem.stepCounter == 1 ? 'primary' : 'grey'"
        >mdi-checkbox-blank-circle</v-icon
      >
      <v-icon size="15" :color="cardItem.stepCounter == 2 ? 'primary' : 'grey'"
        >mdi-checkbox-blank-circle</v-icon
      >
      <v-btn
        v-if="cardItem.stepCounter != 0"
        @click="
          cardItem.stepCounter == 1
            ? secondStepClick()
            : cardItem.stepCounter == 2
            ? thirdStepClick()
            : () => {}
        "
        icon
        outlined
        :color="colorPrimary ? 'white' : 'primary'"
        :class="colorPrimary ? 'primary' : ''"
        class="mx-1"
        ><v-icon>{{
          cardItem.stepCounter == 1
            ? $vuetify.rtl?'mdi-chevron-left':'mdi-chevron-right'
            : cardItem.stepCounter == 2
            ? "mdi-check"
            : ""
        }}</v-icon></v-btn
      >
    </v-card-actions>
  </v-card>
</template>
<script>
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
export default {
  props: {
    title: String,
    platform: Array,
    cardItem: Object,
  },
  components: { CTextField },
  data() {
    return {
      colorPrimary: false,
    };
  },
  watch: {
    ["cardItem.selectedPlacement"]() {
      if (this.cardItem.selectedPlacement.length > 0) {
        this.colorPrimary = true;
      }
      if (this.cardItem.selectedPlacement.length == 0) {
        this.colorPrimary = false;
      }
    },

    ["cardItem.target_CPO"]() {
      if (
        this.cardItem.target_CPO.length > 0 &&
        this.cardItem.budget.length > 0
      ) {
        this.colorPrimary = true;
      }
      if (
        this.cardItem.target_CPO.length == 0 ||
        this.cardItem.budget.length == 0
      ) {
        this.colorPrimary = false;
      }
    },
    ["cardItem.budget"]() {
      if (
        this.cardItem.target_CPO.length > 0 &&
        this.cardItem.budget.length > 0
      ) {
        this.colorPrimary = true;
      }
      if (
        this.cardItem.target_CPO.length == 0 ||
        this.cardItem.budget.length == 0
      ) {
        this.colorPrimary = false;
      }
    },
  },

  methods: {
    close() {
      this.$emit("close", this.cardItem.id);
    },
    firstStepClick(item) {
      this.cardItem.selectedPlatform = item;
      this.cardItem.stepCounter = 0.5;
      setTimeout(() => {
        this.cardItem.stepCounter = 1;
      }, 400);
    },
    getPlacement() {
      let platform = this.platform.find(
        (item) => item.title == this.cardItem.selectedPlatform.title
      );
      return platform?.placement;
    },
    secondStepClick() {
      if (this.cardItem.selectedPlacement.length > 0) {
        this.cardItem.stepCounter = 1.5;
        setTimeout(() => {
          this.cardItem.stepCounter = 2;
        }, 400);

        if (
          this.cardItem.target_CPO.length > 0 &&
          this.cardItem.budget.length > 0
        ) {
          this.colorPrimary = true;
        }
        if (
          this.cardItem.target_CPO.length == 0 ||
          this.cardItem.budget.length == 0
        ) {
          this.colorPrimary = false;
        }
      }
    },
    thirdStepClick() {
      if (
        this.cardItem.target_CPO.length > 0 &&
        this.cardItem.budget.length > 0
      ) {
        this.$emit("done", this.cardItem);
      }
    },
    back() {
      if (this.cardItem.stepCounter == 1) {
        this.cardItem.selectedPlacement = [];
        this.cardItem.stepCounter = 0.5;
        setTimeout(() => {
          this.cardItem.stepCounter = 0;
        }, 400);
      }
      if (this.cardItem.stepCounter == 2) {
        if (this.cardItem.selectedPlacement.length > 0) {
          this.colorPrimary = true;
        }
        this.cardItem.stepCounter = 1.5;
        setTimeout(() => {
          this.cardItem.stepCounter = 1;
        }, 400);
      }
    },
    placementClick(item) {
      if (this.cardItem.selectedPlacement.includes(item.title)) {
        this.cardItem.selectedPlacement =
          this.cardItem.selectedPlacement.filter((el) => el != item.title);
      } else {
        this.cardItem.selectedPlacement.push(item.title);
      }
    },
  },
};
</script>
<style>
.borderGrey {
  height: 40px;
  width: 40px;
  border: 1px solid var(--v-surface-darken2);
  border-radius: 5px !important;
}

.h-10 {
  height: 40px;
}
.borderStyle {
  border: 1px solid var(--v-surface-darken1);
}
.borderStyleSelected {
  border: 1px solid var(--v-primary-lighten1);
}
.borderStyle:hover {
  cursor: pointer;
}
</style>
