<template>
  <v-dialog v-model="model" persistent width="600">
    <v-card>
      <v-card-title>
        <div>{{ $tr(header) }}</div>
        <v-spacer />
        <CloseBtn @click="toggleModel" />
      </v-card-title>
      <v-divider />

      <div v-if="modelType == 'open_item_model'">
        <v-card-text>
          <v-form @submit.prevent="checkItemPassword">
            <CustomInput
              v-model="$v.passwordForm.current_password.$model"
              :label="$tr('label_required', $tr('password'))"
              :rules="
                validate($v.passwordForm.current_password, $root, 'password')
              "
              placeholder="password"
              type="password"
              class="mb-0"
            />
          </v-form>
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-btn
            @click="checkItemPassword"
            :disabled="isLoading"
            color="primary"
            class="mx-1"
          >
            {{ $tr(isLoading ? "opening" : "open") }}
            {{ isLoading ? "..." : "" }}
          </v-btn>
          <v-spacer />
          <v-btn @click="toggleModel" color="primary" class="mx-1" outlined>
            {{ $tr("cancel") }}
          </v-btn>
        </v-card-actions>
      </div>

      <div v-else>
        <v-card-text>
          <div v-if="selectedItem.is_protected">
            <CustomInput
              v-model="$v.passwordForm.current_password.$model"
              :label="$tr('label_required', $tr('current_password'))"
              :rules="
                validate(
                  $v.passwordForm.current_password,
                  $root,
                  'current_password'
                )
              "
              placeholder="current_password"
              type="password"
              class="mb-0"
            />

            <CustomInput
              v-model="$v.passwordForm.new_password.$model"
              :label="$tr('label_required', $tr('new_password'))"
              :rules="
                validate($v.passwordForm.new_password, $root, 'new_password')
              "
              placeholder="new_password"
              type="password"
              class="mb-0"
            />
          </div>
          <CustomInput
            v-else
            v-model="$v.passwordForm.password.$model"
            :label="$tr('label_required', $tr('password'))"
            :rules="validate($v.passwordForm.password, $root, 'password')"
            placeholder="password"
            type="password"
            class="mb-0"
          />
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-btn
            @click="changePassword"
            v-if="selectedItem.is_protected"
            :loading="isLoading"
            color="primary"
            class="mx-1"
          >
            {{ $tr("change_password") }}
          </v-btn>
          <v-btn
            @click="setPassword"
            v-else
            :loading="isLoading"
            color="primary"
            class="mx-1"
          >
            {{ $tr("set_password") }}
          </v-btn>
          <v-btn
            @click="clearPassword"
            v-if="selectedItem.is_protected"
            :loading="isClearing"
            color="success"
            class="mx-1"
          >
            {{ $tr("clear_password") }}
          </v-btn>
          <v-spacer />
          <v-btn @click="toggleModel" color="primary" class="mx-1" outlined>
            {{ $tr("cancel") }}
          </v-btn>
        </v-card-actions>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
import CloseBtn from "~/components/design/Dialog/CloseBtn.vue";
import CustomInput from "~/components/design/components/CustomInput.vue";
import GlobalRules from "~/helpers/vuelidate";
import HanldeException from "~/helpers/handle-exception";
import { validPassword } from "~/validations/validations";
import { minLength, requiredIf } from "vuelidate/lib/validators";

export default {
  components: {
    CloseBtn,
    CustomInput,
  },

  props: {
    setPasswordUrl: null,
    clearPasswordUrl: null,
    changePasswordUrl: null,
  },

  validations: {
    passwordForm: {
      current_password: {
        required: requiredIf(function (value) {
          return this.selectedItem.is_protected && this.modelType == null;
        }),
        validPassword,
        minLength: minLength(6),
      },
      new_password: {
        required: requiredIf(function (value) {
          return this.selectedItem.is_protected && this.modelType == null;
        }),
        validPassword,
        minLength: minLength(6),
      },
      password: {
        required: requiredIf(function (value) {
          return !this.selectedItem.is_protected && this.modelType != null;
        }),
        validPassword,
        minLength: minLength(6),
      },
    },
  },
  data() {
    return {
      validate: GlobalRules.validate,
      header: "protect_your_file",
      modelType: null,
      model: false,
      selectedItem: {},
      isLoading: false,
      isClearing: false,
      passwordForm: {
        current_password: null,
        new_password: null,
        password: null,
      },
    };
  },

  mounted() {
    this.$root.$on("togglePasswordModel", this.toggleModel);
  },

  methods: {
    async setPassword() {
      this.$v.passwordForm.$touch();
      if (!this.$v.passwordForm.$invalid) {
        this.isLoading = true;
        const { password } = this.passwordForm;
        const body = { password, item: this.selectedItem };
        await this.updateItemProtection(this.setPasswordUrl, body);
        this.isLoading = false;
      }
    },
    async changePassword() {
      this.$v.passwordForm.$touch();
      const isCurrentPassValid =
        !this.$v.passwordForm.current_password.$invalid;
      const isNewPassValid = !this.$v.passwordForm.new_password.$invalid;
      if (isCurrentPassValid && isNewPassValid) {
        this.isLoading = true;
        const { current_password, new_password } = this.passwordForm;
        const body = {
          current_password,
          new_password,
          item: this.selectedItem,
        };
        const url = this.changePasswordUrl || this.setPasswordUrl;
        await this.updateItemProtection(url, body);
        this.isLoading = false;
      }
    },
    async clearPassword() {
      this.$v.passwordForm.$touch();
      if (!this.$v.passwordForm.current_password.$invalid) {
        this.isClearing = true;
        const { current_password } = this.passwordForm;
        const body = {
          clear_password: true,
          current_password,
          item: this.selectedItem,
        };
        const url = this.clearPasswordUrl || this.setPasswordUrl;
        await this.updateItemProtection(url, body);
        this.isClearing = false;
      } else {
        // this.$toastr.w(this.$tr("enter_your_current_password"));
        this.$toasterNA("orange", this.$tr("enter_your_current_password"));

      }
    },

    async checkItemPassword() {
      this.$v.passwordForm.$touch();
      if (!this.$v.passwordForm.current_password.$invalid) {
        this.isLoading = true;
        const { current_password } = this.passwordForm;
        const body = {
          check_password: true,
          current_password,
          item: this.selectedItem,
        };
        const url = this.checkPasswordUrl || this.setPasswordUrl;
        await this.updateItemProtection(url, body);
        this.isLoading = false;
      } else {
        // this.$toastr.w(this.$tr("enter_your_password"));
        this.$toasterNA("orange", this.$tr("enter_your_password"));

      }
    },

    async updateItemProtection(url, body) {
      try {
        const { data } = await this.$axios.post(url, body);
        if (data.result) {
          const { action } = data.item;
          if (action == "password_setted") {
            // this.$toastr.s(this.$tr("password_setted"));
				this.$toasterNA("green", this.$tr('password_setted'));
            
          } else if (action == "password_changed") {
            // this.$toastr.s(this.$tr("password_changed"));
				this.$toasterNA("green", this.$tr('password_changed'));

          } else if (action == "password_cleared") {
            // this.$toastr.s(this.$tr("password_cleared"));
				this.$toasterNA("green", this.$tr('password_cleared'));

          }
          const { check_password } = body;
          if (check_password) {
            let currentPassword = this.passwordForm.current_password;
            this.$emit("onPasswordCorrect", {
              currentPassword,
              is_password_correct: true,
              item: { ...this.selectedItem, ...data.item },
            });
          } else {
            this.$emit("onSuccess", data.item);
          }
          this.toggleModel();
        } else if (data.unauthorized) {
          // this.$toastr.w(this.$tr("unauthorized_to_do_this_operation"));
        this.$toasterNA("orange", this.$tr("unauthorized_to_do_this_operation"));

        } else if (data.invalid_current_password) {
          // this.$toastr.w(this.$tr("incorrect_current_password"));
        this.$toasterNA("orange", this.$tr("incorrect_current_password"));

        } else {
          HanldeException.handleApiException(this, error);
        }
      } catch (error) {
        HanldeException.handleApiException(this, error);
      }
    },

    toggleModel(item) {
      this.modelType = null;

      this.$v.passwordForm.current_password.$model = null;
      this.$v.passwordForm.new_password.$model = null;
      this.$v.passwordForm.password.$model = null;
      this.$v.passwordForm.$reset();
      if (this.model) {
        this.selectedItem = {};
        this.model = false;
      } else {
        if (item.type == "folder") {
          this.header = "protect_your_folder";
        } else {
          this.header = "protect_your_file";
        }

        if (item.modelType == "open_item_model") {
          this.header = "enter_your_password";
          this.modelType = "open_item_model";
        }

        this.selectedItem = item;
        this.model = true;
      }
    },
  },
};
</script>

<style></style>
