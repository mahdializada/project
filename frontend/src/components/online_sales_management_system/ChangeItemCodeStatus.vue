<template>
  <v-dialog v-model="showAlert" width="580" persistent :key="updateKey">
    <v-card>
      <v-card-text
        :class="`blue lighten-5 pt-2 ${
          isNeddedLink ? 'heightAnimation' : 'heightAnimation1'
        }`"
        style="
          background-color: rgba(17, 85, 152, 5%) !important;
          overflow-y: hidden;
        "
      >
        <lottie-player
          v-if="loading"
          src="/assets/man-on-rocket.json"
          background="transparent"
          speed="1"
          loop
          autoplay
          class="py-2 rounded"
          style="background-color: #f7f8fc"
        >
        </lottie-player>
        <div v-else class="">
          <InputCard :title="$tr('are_you_sure_to', $tr(textDialog))">
            <CheckBoxHorizontalItem
              v-model="newStatus"
              :items="filtered_statuses"
              :height="'150px'"
              hasCustomoColor
              item_value="id"
              @input="change"
            ></CheckBoxHorizontalItem>
          </InputCard>
          <v-form ref="IDRef" v-model="valid">
              <CTextField
                :title="$tr('label_required', $tr('content_link'))"
                :placeholder="$tr('https://www.example.com/')"
                prepend-inner-icon="mdi-link-variant "
                :hasCustomBtn="true"
                btnIcon="mdi-clipboard-multiple"
                @add="PasteTo"
                type="textfield"
                class="mt-2"
                rounded
                :rules="isNeddedLink ? urlRules : []"
                v-model="landingPageLink"
              />
          </v-form>
        </div>
      </v-card-text>
      <v-card-actions class="justify-end my-1">
        <v-btn class="px-2" depressed @click="close">{{ $tr("cancel") }}</v-btn>
        <v-btn
          depressed
          class="friqiBase white--text ms-2 px-2"
          @click="chaneStatus"
          :loading="loading"
          >{{ $tr(acceptbBtnTxt) }}</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
  <script>
import CheckBoxHorizontalItem from "../new_form_components/cat_product_selection/CheckBoxHorizontalItem.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";

export default {
  props: {
    acceptbBtnTxt: {
      type: String,
      default: "change",
    },
    textDialog: {
      type: String,
      default: "change_status",
    },
  },
  data() {
    return {
        valid: true,
      updateKey: 0,
      filtered_statuses: [],
      newStatus: "",
      hasStatus: false,
      itemStatus: [
        {
          id: "new_sales",
          count: 0,
          isSelected: 0,
          change_to: "in_source",
          color: "#007f35 !important",
        },
        {
          id: "in_source",
          count: 0,
          isSelected: 0,
          change_to: "in_study",
          color: "purple !important",
        },
        {
          id: "in_study",
          change_to: "store_creation",
          count: 0,
          isSelected: 0,
          color: "#37383a !important",
        },
        {
          id: "store_creation",
          change_to: "content_creation",
          count: 0,
          isSelected: 0,
          color: "#71c9b5 !important",
        },
        {
          id: "content_creation",
          change_to: "final_review",
          count: 0,
          isSelected: 0,
          color: "#fa8059 !important",
        },
        {
          id: "final_review",
          change_to: "ready_to_sale",
          count: 0,
          isSelected: 0,
          color: "#8a53fd !important",
        },
        {
          id: "ready_to_sale",
          change_to: "",
          count: 0,
          isSelected: 0,
          color: "#0049b0 !important",
        },
        {
          id: "cancel",
          change_to: "new_sales",
          count: 0,
          isSelected: 0,
          color: "#fe58bc !important",
        },
        {
          id: "remove",
          change_to: "new_sales",
          count: 0,
          isSelected: 0,
          color: "#bb732b !important",
        },
      ],
      items: [],
      isNeddedLink: false,
      landingPageLink: "",
      selectedItem: null,
      showAlert: false,
      loading: false,
      change_status_icon: `
                <svg width="46" height="57" viewBox="0 0 46 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_73_61)">
                <path d="M41.6747 0.601524C41.3652 6.82006 36.3141 11.6963 30.1891 11.6907H15.8136C9.68868 11.6963 4.63479 6.82006 4.32799 0.601524C1.69833 1.76002 0 4.39725 0 7.30741V49.6926C0.00821771 53.725 3.22134 56.9889 7.18776 57H38.815C42.7814 56.9916 45.9918 53.725 46.0027 49.6926V7.30741C46.0027 4.39447 44.3044 1.76002 41.6747 0.59874V0.601524ZM35.4649 28.5278L21.8208 42.4019C21.1469 43.0897 20.232 43.4741 19.2787 43.4713C18.3255 43.4713 17.4133 43.0842 16.7367 42.4019L10.5351 36.097C9.13261 34.6684 9.13261 32.357 10.5351 30.9284C11.209 30.2405 12.1239 29.8562 13.0771 29.859C14.0304 29.859 14.9453 30.2433 15.6191 30.9284L19.2787 34.6489L30.3836 23.3592C31.0575 22.6713 31.9724 22.287 32.9256 22.2898C33.8789 22.2898 34.7938 22.6741 35.4649 23.3592C36.1415 24.0415 36.5195 24.9744 36.5168 25.9435C36.5168 26.9126 36.1415 27.8428 35.4649 28.5278Z" fill="#105698"/>
                <path d="M30.1892 7.30462H15.8137C11.8473 7.29627 8.63419 4.03244 8.62598 0H37.377C37.3688 4.03244 34.1557 7.29905 30.1892 7.30741V7.30462Z" fill="#B6C6DA"/>
                </g>
                <defs>
                <clipPath id="clip0_73_61">
                <rect width="46" height="57" fill="white"/>
                </clipPath>
                </defs>
                </svg>`,
      urlRules: [
        (v) => !!v || this.$tr("item_is_required", this.$tr("content_link ")),
        (v) =>
          (v &&
            !!v.match(
              /https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,}/gim
            )) ||
          this.$tr("invalid_link"),
      ],
    };
  },
  computed: {},
  methods: {
    change(item) {
        this.isNeddedLink = false;
        this.landingPageLink = "";
        this.$refs.IDRef.reset();
      if (
        this.items.prev_status == "store_creation" &&
        item == "content_creation"
      )
        this.isNeddedLink = true;
    },
    async toggleStatusDialog(item) {
      this.selectedItem = item;
      this.newStatus = item.item_status?.item_status;
      this.items = {
        id: item.id,
        item_code: item.pcode ?? item.product_code,
        country_id: item.country_id,
        prev_status: item.item_status?.item_status,
        profile_info: item?.product_info?.id,
      };
      this.getAvailableStatus();
      this.showAlert = true;
    },
    chaneStatus() {
        if(!this.$refs.IDRef.validate()){
            this.$toasterNA("red", this.$tr("please_add_content_link"));
            return;
        }
        if(this.isNeddedLink){
            const dataAddedToHaneefSys = {
              pcode: this.selectedItem.product_code,
              pname: this.selectedItem.product_name,
              sales_type: this.selectedItem.sales_type,
              company: this.selectedItem.company.name,
              page_link: structuredClone(this.landingPageLink),
            };
            this.items['dataAddedToHaneefSys'] = dataAddedToHaneefSys;
        }
      if (this.items.prev_status == this.newStatus) {
        this.$toasterNA(
          "orange",
          this.$tr("Record is already in " + this.newStatus)
        );
        return;
      }
      if (
        this.newStatus == "in_study" &&
        this.items.profile_info == undefined
      ) {
        this.$toasterNA("orange", this.$tr("profile_info_not_added"));
        return;
      }
      this.loading = true;
      const status1 = this.itemStatus.find((i) => i.id == this.newStatus);
      this.items["item_status"] = {
        status: status1.id,
        color: status1.color,
      };
      this.$emit("changeStatus", this.items);
        this.$refs.IDRef.reset();
    },
    close() {
      this.updateKey++;
      this.loading = false;
      this.showAlert = false;
      this.updateKey++;
      this.isNeddedLink = false;
      this.landingPageLink = "";
    },
    reset() {
    this.isNeddedLink = false;
    this.landingPageLink = "";
      this.loading = false;
    },
    getAvailableStatus() {
      const current_status = this.itemStatus.find(
        (row) => row.id == this.items?.prev_status
      );
      this.filtered_statuses = this.itemStatus.filter((row) => {
        if (
          row.id == current_status.change_to ||
          row.id == current_status.id ||
          row.id == "cancel" ||
          row.id == "remove"
        ) {
          return true;
        }
        return false;
      });
    },
    async PasteTo() {
      try {
        const clipboard = navigator.clipboard;
        const account_po = await clipboard.readText();
        if (account_po) {
          this.landingPageLink = account_po;
        }
      } catch (error) {}
    },
  },
  components: { InputCard, CheckBoxHorizontalItem, CTextField },
};
</script>
  <style>
.heightAnimation {
  height: 450px;
  transition: height 0.5s ease;
}
.heightAnimation1 {
  height: 300px;
  transition: height 0.5s ease;
}
.AvatarCustom {
  background: linear-gradient(
    360deg,
    rgba(17, 85, 152, 15%) 0%,
    rgba(17, 85, 152, 1%) 100%
  ) !important;
  width: 88px;
  height: 85px;
  display: flex;
  justify-content: center;
  border-radius: 50%;
}
</style>
  