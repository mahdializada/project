<template>
  <div class="connection__container">
    <div class="friqiBase--text title">
      {{ $tr("how_you_choose_your_canva") }}
    </div>
    <div class="mb-3 d-flex mt-0">
      <ItemCard
        v-for="(item, index) in items"
        :key="index"
        :item="item"
        :selected="item.type == form.creation_type.$model"
        class="custom__item__card"
        @click="handleClick(item)"
      />
    </div>
    <div class="auto-complete-div">
      <!-- @blur="form.ad_account_id.$touch()" -->
      <CAutoComplete
        v-show="form.creation_type.$model == 'new_account'"
        :items="items1"
        v-model="form.ad_account_id.$model"
        :invalid="form.ad_account_id.$invalid"
        :rules="
          validateRule(
            form.ad_account_id, // validate objec
            $root, // context
            $tr(showType) // lable for feedback
          )
        "
        :placeholder="$tr('choose_item', $tr(showType))"
        :title="$tr(showType)"
        item-value="id"
        item-text="name"
        outlined
        isCheckNeeded
        @change="changeSelection"
        prepend-inner-icon="fa fa-globe"
        :loading="attributeLoading"
        :rounded="false"
        :readonly="form.creation_type.$model != 'new_account'"
      />
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import ItemCard from "~/components/new_form_components/components/ItemCard.vue";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import CAutoComplete from "~/components/new_form_components/Inputs/CAutoComplete.vue";
import CTextField from "~/components/new_form_components/Inputs/CTextField.vue";

export default {
  props: {
    form: Object,
    data: Object,
  },
  watch: {
    "form.creation_type.$model": function (type) {
      if (type == "new_account") {
        const { platform_id, application_id, ad_account_id } = this.data;
        this.currentAdAccount = { platform_id, application_id, ad_account_id };
        this.adAccounts = this.data.adAccounts;
        this.form.shouldReset.$model = false;
        this.form.platform_id.$model = null;
        this.form.application_id.$model = null;
        this.form.ad_account_id.$model = null;
      } else {
        this.form.platform_id.$model = this.currentAdAccount.platform_id;
        this.form.application_id.$model = this.currentAdAccount.application_id;
        this.form.ad_account_id.$model = this.currentAdAccount.ad_account_id;
      }
    },
  },
  data() {
    return {
      currentAdAccount: {
        platform_id: null,
        application_id: null,
        ad_account_id: null,
      },
      validateRule: GlobalRules.validate,
      items: [
        {
          type: "new_account",
          name: this.$tr("new_ad_account"),
        },
        {
          type: "current_account",
          name: this.$tr("current_ad_account"),
        },
      ],
      adAccounts: [],
      attributeLoading: false,
      items1: [],
      type: "applications",
      showType: "platforms",
    };
  },
  async created() {
    try {
      this.attributeLoading = true;
      const platform = await this.$axios.get(
        "advertisement/platforms?only_platforms=true"
      );
      if (platform.status == 200) {
        this.items1 = platform.data.platforms.map((item) => {
          return {
            id: item.id,
            name: item.platform_name,
          };
        });
        this.attributeLoading = false;
      }
    } catch (error) {
      this.attributeLoading = false;
    }
  },
  methods: {
    setAdAccounts(data) {
      this.adAccounts = data;
    },
    handleClick(item) {
      this.form.creation_type.$model = item.type;
    },
    changeSelection(id) {
      if (this.type != "other") {
        this.form.ad_account_id.$model = null;
        this.fetchItems({
          type: this.type,
          id: id,
        });
      }
      if (this.showType == "platforms") this.showType = "applications";
      if (this.type == "adAccounts") {
        this.form.application_id.$model = id;
        this.showType = "ad_account";
        this.type = "other";
      }
      if (this.type == "applications") {
        this.form.platform_id.$model = id;
        this.type = "adAccounts";
        this.showType = "applications";
      }
    },

    async validate() {
      this.form.creation_type.$touch();
      let isValid =
        !this.form.platform_id.$invalid &&
        !this.form.application_id.$invalid &&
        !this.form.ad_account_id.$invalid &&
        !this.form.creation_type.$invalid;
      if (isValid) {
        // if (this.form.is_account_po_required.$model) {
        // 	const isPoValid = !this.form.account_po.$invalid;
        // 	return isPoValid;
        // }
        isValid = await this.fetchAccountPo();
        if (isValid) {
          try {
            let connection = this.data;
            delete connection.connection_copy;
            const url = "advertisement/connection/generate_link";
            const { data } = await this.$axios.post(url, connection);
            if (data.result) {
              this.$toasterNA("green", this.$tr("successfully_inserted"));
              const insertedRecord = data.connection;
              // this.form.generated_link.$model = insertedRecord.generated_link;
              // this.form.connection_id.$model = insertedRecord.id;
              this.form.$model.ads[0].generated_link =
                insertedRecord.generated_link;
              this.form.$model.ads[0].connection_id = insertedRecord.id;
              return true;
            } else {
              // this.$toastr.e(this.$tr(data.message || "something_went_wrong"));
              this.$toasterNA(
                "red",
                this.$tr(data.message || "something_went_wrong")
              );
            }
          } catch (error) {
            HandleException.handleApiException(this, error);
          }
        }
        if (isValid && this.form.generated_link.$model) {
          return true;
        }
        return false;
      }
      return false;
    },

    async fetchAccountPo() {
      try {
        const id = this.form.ad_account_id.$model;
        const url = `advertisement/ad-accounts/po/${id}`;
        const { data } = await this.$axios.get(url);
        if (data.result) {
          this.form.is_account_po_required.$model = false;
          return true;
        }
        this.form.is_account_po_required.$model = true;
        this.$toasterNA("red", this.$tr("please_ad_account_po_code"));
        return false;
      } catch (error) {
        return false;
      }
    },
    async fetchItems({ type, id }) {
      try {
        this.attributeLoading = true;
        const url = `advertisement/connection/generate_link/${type}/${id}`;
        const { data } = await this.$axios.get(url);
        // this[data.type] = data.items;
        this.items1 = data.items;
        this.attributeLoading = false;
      } catch (error) {}
      this.attributeLoading = false;
    },
  },
  components: {
    CTitle,
    ItemCard,
    CTextField,
    CAutoComplete,
  },
};
</script>

<style scoped lang="scss">
.connection__container {
  display: flex;
  flex-direction: column;
  align-items: center;
  // height: 100% !important;
  min-height: 450px;
  background-color: #f7f8fc;
}
.custom__item__card {
  width: 200px;
  height: 140px;
}
.custom__item__card .item-name {
  font-size: 15px !important;
  font-weight: 600 !important;
}

.auto-complete-div {
  width: 60%;
  min-height: 150px;
}
.title {
  font-size: 20px;
  font-weight: 300px;
  padding: 20px;
  margin: 10px;
}
</style>
