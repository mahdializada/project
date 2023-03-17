<template>
  <div>
    <v-dialog v-model="passwordModel" persistent width="600">
      <v-card>
        <v-card-title>
          <span style="font-weight: bold; font-size: 1.5rem">
            {{ $tr('change_your_account_password') }}
          </span>
        </v-card-title>
        <v-divider />

        <v-card-text
          class="pt-3 d-flex align-center"
          style="height: 300px; overflow-y: auto"
        >
          <div class="w-full">
            <CustomInput
              label="password"
              :type="showPass ? 'textfield' : 'password'"
              :append-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
              @click:append="showPass = !showPass"
              v-model="$v.passwordForm.password.$model"
              :rules="validate($v.passwordForm.password, $root, 'password')"
            />

            <CustomInput
              label="confirm_password"
              :type="showPass2 ? 'textfield' : 'password'"
              :append-icon="showPass2 ? 'mdi-eye' : 'mdi-eye-off'"
              @click:append="showPass2 = !showPass2"
              v-model="$v.passwordForm.password_confirmation.$model"
              :rules="
                validate(
                  $v.passwordForm.password_confirmation,
                  $root,
                  'confirm_password'
                )
              "
            />
          </div>
        </v-card-text>
        <v-divider />
        <v-card-actions class="justify-end">
          <v-spacer />
          <TextButton
            type="primary"
            :text="$tr('change_password')"
            @click="updateUserPassword"
            :loading="isLoading"
          >
          </TextButton>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-if="!passwordModel"
      v-model="companyModel"
      persistent
      width="600"
    >
      <v-card>
        <v-card-title class="d-flex justify-space-between">
          <span style="font-weight: bold; font-size: 1.5rem">
            {{ $tr('choice_your_default_company') }}
          </span>
          <close-btn @click="closeCompany" />
        </v-card-title>
        <v-divider />

        <v-card-text
          class="pt-1"
          style="padding: 0 15px 10px; height: 300px; overflow-y: auto"
        >
          <v-list dense>
            <v-list-item-group>
              <v-list-item
                v-for="(company, index) in user.companies"
                :key="index"
                :disabled="company.status !== 'active'"
              >
                <v-list-item-icon style="height: 35px">
                  <v-avatar size="40">
                    <v-img :src="company.logo"></v-img>
                  </v-avatar>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title style="font-weight: 700"
                    >{{ company.name }}
                  </v-list-item-title>
                </v-list-item-content>
                <v-switch
                  v-model="selectedCompanyIds"
                  :disabled="
                    selectedCompanyIds.length === 1 &&
                    selectedCompanyIds.includes(company.id)
                  "
                  color="white"
                  inset
                  dense
                  :value="company.id"
                  hide-details
                  :class="`mt-0 pt-0 custom-switch ${
                    selectedCompanyIds.includes(company.id) ? 'selected' : ''
                  }`"
                ></v-switch>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card-text>

        <v-divider />
        <v-card-actions class="justify-end">
          <v-spacer />
          <TextButton
            type="primary"
            :text="$tr('continue')"
            @click="onCompanySelected"
            :loading="isLoading"
          >
          </TextButton>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import TextField from '~/components/common/TextField';
import CustomInput from '../../components/design/components/CustomInput.vue';
import { validPassword } from '~/validations/validations';
import { minLength, required, sameAs } from 'vuelidate/lib/validators';
import GlobleRules from '~/helpers/vuelidate';
import HandleException from '~/helpers/handle-exception';
import TextButton from '../common/buttons/TextButton.vue';
import { mapState } from 'vuex';
import CloseBtn from '../design/Dialog/CloseBtn.vue';

export default {
  components: {
    TextField,
    TextButton,
    CloseBtn,
    CustomInput,
  },

  validations: {
    passwordForm: {
      password: {
        required,
        validPassword,
        minLength: minLength(6),
      },
      password_confirmation: {
        required,
        validPassword,
        sameAsPassword: sameAs('password'),
        minLength: minLength(6),
      },
    },
  },
  data() {
    return {
      isLoading: false,
      showPass: false,
      showPass2: false,
      validate: GlobleRules.validate,
      passwordModel: this.$auth.user.change_password,
      passwordForm: {
        password: '',
        password_confirmation: '',
      },

      companyModel: false,
      isLoading: false,
      selectedCompanyIds: [],
      selectedCompanies: [],
    };
  },

  watch: {
    selectedCompanyIds(newItem) {
      if (newItem) {
        this.selectedCompanies = this.user.companies.filter((item) =>
          newItem.includes(item.id)
        );
      }
    },
  },

  computed: {
    ...mapState('auth', ['user']),
  },

  created() {
    this.selectedCompanyIds = this.user.selectedCompanies.map(
      (item) => item.id
    );
    this.selectedCompanies = JSON.parse(
      JSON.stringify(this.user.selectedCompanies)
    );
  },

  mounted() {
    this.checkCompanyPop();
  },
  methods: {
    //change password
    async updateUserPassword(id) {
      this.$v.passwordForm.$touch();
      if (!this.$v.passwordForm.$invalid) {
        this.isLoading = true;
        try {
          await this.$axios.post(
            'users/changePassword/id?check-current=true',
            this.passwordForm
          );
          this.passwordModel = false;
          this.checkCompanyPop();
          this.$auth.fetchUser();
          // this.$toastr.s(this.$tr('password_changed_successfully'));
				this.$toasterNA("green", this.$tr('password_changed_successfully'));

        } catch (error) {
          HandleException.handleApiException(this, error);
        }
      }
      this.isLoading = false;
    },

    checkCompanyPop() {
      if (process.client) {
        if (localStorage.getItem('company_popup_dialog')) {
        } else {
          this.companyModel = true;
        }
      }
    },
    async onCompanySelected() {
      this.isLoading = true;
      const data = { companies: this.selectedCompanyIds };
      try {
        await this.$axios.post('change-auth-user-companies', data);
        await this.$auth.fetchUser();
        if (process.client) {
          localStorage.setItem('company_popup_dialog', true);
        }
        // this.$toastr.i(this.$tr('company_changed'));
			  this.$toasterNA("primary", this.$tr("company_changed"));

      } catch (error) {
        // this.$toastr.w(this.$tr('selected_company_is_not_save'));
						this.$toasterNA("orange",this.$tr('selected_company_is_not_save'));

      }
      this.isLoading = false;
      this.companyModel = false;
    },
    closeCompany() {
      if (this.isLoading) return;
      for (let i = 0; i < this.user.companies.length; i++) {
        this.selectedCompanyIds.push(this.user.companies[i].id);
      }
      this.onCompanySelected();
      this.companyModel = false;
    },
  },
};
</script>

<style></style>
