<template>
  <stepper
    :title="$tr('supplier')"
    cookieName="label"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="SupplierRules"
    :submit="submit"
    @close="close"
    @reset="reset"
    :showBack="showBack"
  />
</template>
<script>
import Validations from '~/validations/validations'
import HandleException from '~/helpers/handle-exception'
import Stepper from '../../horizontal_stepper/Stepper.vue'
import GeneralInfoStep from './GeneralInfoStep.vue'
import GeneralInfoDetailsStep from './GeneralInfoDetailsStep.vue'
import SupplierInfoStep from './SupplierInfoStep.vue'
import SupplierBankAccountStep from './SupplierBankAccountStep.vue'
import SupplierLocationStep from './SupplierLocationStep.vue'
import { requiredIf } from 'vuelidate/lib/validators'
import SupplierNoteStepVue from './SupplierNoteStep.vue'
import { mapMutations } from 'vuex'
export default {
  components: { Stepper },
  data() {
    return {
      show: false,
      showBack: true,
      countries: [],
      tabKey: '',
      steps: [
        {
          title: this.$tr('general_info'),
          component: GeneralInfoStep,
        },
        {
          title: this.$tr('general_info_details'),
          component: GeneralInfoDetailsStep,
        },
        {
          title: this.$tr('supplier_info'),
          component: SupplierInfoStep,
        },
        {
          title: this.$tr('bank_account_info'),
          component: SupplierBankAccountStep,
          skip: true,
        },
        {
          title: this.$tr('supplier_location'),
          component: SupplierLocationStep,
          skip: true,
        },
        {
          title: this.$tr('note'),
          component: SupplierNoteStepVue,
        },
      ],
      form: {
        country_id: '',
        companyIds: [],
        supply_type: '',
        commercial_type: '',
        legal_type: '',
        country_type: '',
        sourcerIds: [],
        supplier_trading_name: '',
        supplier_name: '',
        main_phone: '',
        email: '',
        purchase_order_phone: '',
        isRequired: true,
        images: [],
        
        website: '',
        prepration_period: '',
        bank: [
          {
            bank_name: '',
            bank_account_name: '',
            bank_account_number: '',
            isChecked: true,
            bank_account_number_iban: '',
            swift_code: '',
            address: '',
            
          },
        ],
        location: [
          {
            country_id: '',
            state_id: '',
            location_type: '',
            map_link: '',
            location_phone: '',
            crowd_status: '',
            contact_staff_name: '',
            address: '',
          },
        ],
        note: '',
      },
      SupplierRules: {
        form: {
          country_id: Validations.requiredValidation,
          companyIds: Validations.requiredValidation,
          supply_type: Validations.requiredValidation,
          commercial_type: Validations.requiredValidation,
          legal_type: Validations.requiredValidation,
          country_type: Validations.requiredValidation,
          sourcerIds: Validations.requiredValidation,
          supplier_trading_name: Validations.name100Validation,
          supplier_name: Validations.name100Validation,
          main_phone: Validations.phoneValidation,
          images: Validations.requiredValidation,
          email: Validations.emailValidation,
          purchase_order_phone: Validations.phoneValidation,
          isRequired: true,
          website: {
            required: requiredIf(function (value) {
              return this.form.isRequired
            }),
          },
          prepration_period: Validations.requiredValidation,
          note: Validations.remarksValidation,
          bank: {
            $each: {
              bank_name: Validations.name100Validation,
              bank_account_name: Validations.name100Validation,
              bank_account_number: Validations.numberValidation,
              isChecked: true,
              bank_account_number_iban: {
                required: requiredIf(function (value) {
                  console.log("iban",value);
                  return value.isChecked
                }),
              },
              // bank_account_number_iban: Validations.numberValidation,
              swift_code: Validations.numberValidation,
              address: Validations.remarksValidation,
            },
          },
          location: {
            $each: {
              country_id: Validations.requiredValidation,
              state_id: Validations.requiredValidation,
              location_type: Validations.requiredValidation,
              map_link: Validations.requiredValidation,
              location_phone: Validations.phoneValidation,
              crowd_status: Validations.requiredValidation,
              contact_staff_name: Validations.name100Validation,
              address: Validations.remarksValidation,
            },
          },
        },
      },
      // SupplierRules: {
      //   form: {
      //     country_id: Validations.requiredValidation,
      //     companyIds: Validations.requiredValidation,
      //     supply_type: Validations.requiredValidation,
      //     commercial_type: Validations.requiredValidation,
      //     legal_type: Validations.requiredValidation,
      //     country_type: Validations.requiredValidation,
      //     sourcerIds: Validations.requiredValidation,
      //     supplier_trading_name: Validations.name100Validation,
      //     supplier_name: Validations.name100Validation,
      //     main_phone: Validations.phoneValidation,
      //     email: Validations.emailValidation,
      //     purchase_order_phone: Validations.phoneValidation,
      //     website: Validations.urlValidationRequired,
      //     prepration_period: Validations.requiredValidation,
      //     note: Validations.remarksValidation,
      //     bank: {
      //       $each: {
      //         bank_name: Validations.name100Validation,
      //         bank_account_name: Validations.name100Validation,
      //         bank_account_number: Validations.numberValidation,
      //         bank_account_number_iban: Validations.numberValidation,
      //         swift_code: Validations.numberValidation,
      //         address: Validations.remarksValidation,
      //       },
      //     },
      //     location: {
      //       $each: {
      //         country_id: Validations.requiredValidation,
      //         state_id: Validations.requiredValidation,
      //         location_type: Validations.requiredValidation,
      //         map_link: Validations.requiredValidation,
      //         location_phone: Validations.phoneValidation,
      //         crowd_status: Validations.requiredValidation,
      //         contact_staff_name: Validations.name100Validation,
      //         address: Validations.remarksValidation,
      //       },
      //     },
      //   },
      // },
    }
  },
  methods: {
    ...mapMutations({
      insertNewItem: 'suppliers/INSERT_ITEM',
      updateItem: 'suppliers/UPDATE_ITEM',
    }),
    async submit() {
      try {
        if (!this.form.location[0]?.country_id) {
          delete this.form.location
        }
        if (!this.form.bank[0]?.bank_name) {
          delete this.form.bank
        }
        if (this.form.supplier_id) {
          return this.update()
        } else {
          const result = await this.$axios.post('suppliers', this.form)
          if (result.status == 201) {
            this.insertNewItem(result.data.data, this.tabKey)
            this.$toasterNA('green', this.$tr('successfully_inserted'))
            return true
          }
          return false
        }
      } catch (error) {
        console.error(error)
        this.$toasterNA('red', this.$tr('something_went_wrong'))
        return false
      }
    },
    async update() {
      try {
        delete this.form.editData
        const response = await this.$axios.put('suppliers/id', this.form)
        if (response.status == 200) {
          this.$toasterNA(
            'green',
            this.$tr('updated_successfully_', this.$tr('supplier')),
          )
          this.updateItem(response.data.data)
          return true
        }
        return false
      } catch (error) {
        this.$toasterNA('red', this.$tr('something_went_wrong'))
        return false
      }
    },
    reset() {
      this.form = {
        country_id: '',
        companyIds: [],
        supply_type: '',
        commercial_type: '',
        legal_type: '',
        country_type: '',
        sourcerIds: [],
        supplier_trading_name: '',
        supplier_name: '',
        main_phone: '',
        email: '',
        purchase_order_phone: '',
        isRequired: true,
     
        website: '',
        prepration_period: '',
        note: '',
        bank: [
          {
            bank_name: '',
            bank_account_name: '',
            bank_account_number: '',
            bank_account_number_iban: '',
            swift_code: '',
            address: '',
            isChecked: true,
          },
        ],
        location: [
          {
            country_id: '',
            state_id: '',
            location_type: '',
            map_link: '',
            location_phone: '',
            crowd_status: '',
            contact_staff_name: '',
            address: '',
          },
        ],
      }
    },
    close() {
      this.show = false
      this.reset()
    },
    toggle(tabKey) {
      this.tabKey = tabKey
      this.show = true
    },
    toggleEdit(item) {
      this.form.editData = item
      this.form.supplier_id = item.id
      this.form.supply_type = item.supply_type
      this.form.commercial_type = item.commercial_type
      this.form.legal_type = item.legal_type
      this.form.country_type = item.country_type
      this.form.sourcerIds = item.sourcer
      this.form.supplier_trading_name = item.supplier_trading_name
      this.form.supplier_name = item.supplier_name
      this.form.main_phone = item.main_phone
      this.form.email = item.email
      this.form.purchase_order_phone = item.purchase_order_phone
      this.form.website = item.website
      this.form.prepration_period = item.prepration_period
      this.form.note = item.note
      this.show = true
      this.getSupplierLocations(item.id)
      this.getSupplierBankAccount(item.id)
    },
    async getSupplierLocations(id) {
      const result = await this.$axios.get(`get-locations/${id}`)
      if (result.status == 200 && result.data.length > 0) {
        this.form.location = []
        const locationItems = result.data
        locationItems.forEach((element) => {
          this.form.location.push({
            country_id: element.country_id,
            state_id: element.state_id,
            location_type: parseInt(element.location_type),
            map_link: element.map_link,
            location_phone: element.location_phone,
            crowd_status: element.crowd_status,
            contact_staff_name: element.contact_staff_name,
            address: element.address,
          })
        })
      }
    },
    close() {
      this.show = false;
      if (this.submited == false) {
        for (let i = 0; i < this.form?.images?.length; i++) {
          this.deleteFileBackend(this.form.images[i]);
        }
      }
    },
    async deleteFileBackend(item) {
      if (item && item.source) {
        item.source.cancel("file-cancled");
        delete item.source;
      }
      await this.$axios.post("supplier-images", {
        file_path: item.path,
      });
    },
    async getSupplierBankAccount(id) {
      const result = await this.$axios.get(`get-supplier-bank-account/${id}`)
      if (result.status == 200 && result.data.length > 0) {
        this.form.bank = []
        const items = result.data
        items.forEach((element) => {
          this.form.bank.push({
            bank_name: element.bank_name,
            bank_account_name: element.bank_account_number,
            bank_account_number: element.bank_account_number,
            bank_account_number_iban: element.bank_account_number_iban,
            swift_code: element.swift_code,
            address: element.address,
          })
        })
      }
    },
  },
}
</script>
