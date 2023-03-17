          <template v-slot:[`item.name`]="{ item, index }">
            <v-edit-dialog
              :return-value.sync="item.name"
              large
              ref="input_dialog"
              persistent
              @open="onOpen(item.name, 'name')"
              @save="onSave('name', index)"
            >
              <span
                :class="`${
                  checkIfHasErr(item.name, 'name', index + 6)
                    ? 'text--white error-class px-2 d-flex align-center justify-center'
                    : ''
                }`"
              >
                {{ item.name ? item.name : "Empty" }}
              </span>
              <template v-slot:input>
                <CustomInput
                  style="width: 300px !important"
                  class="mt-3"
                  placeholder="textField.name"
                  label="textField.name"
                  type="textfield"
                  v-model="$v.company.name.$model"
                  :rules="rules.nameRule($v.company, $root)"
                />
                <div class="my-4">
                  <v-spacer></v-spacer>
                  <v-btn
                    color="blue-grey lighten-4"
                    class="px-3 rounded-sm border style"
                    @click="onCancel()"
                  >
                    {{ $tr("cancel") }}
                  </v-btn>
                  <v-btn
                    color="primary"
                    class="px-3 rounded-sm"
                    @click="onSave('name', index)"
                    >{{ $tr("save") }}</v-btn
                  >
                </div>
              </template>
            </v-edit-dialog>
          </template> -->

          <!-- <template v-slot:[`item.email`]="{ item, index }">
            <v-edit-dialog
              :return-value.sync="item.email"
              large
              persistent
              @save="onSave('email', index)"
              @open="onOpen(item.email, 'email')"
            >
              <span
                :class="`${
                  checkIfHasErr(item.email, 'email', index + 6)
                    ? 'text--white error-class px-2 d-flex align-center justify-center'
                    : ''
                }`"
              >
                {{ item.email ? item.email : "Empty" }}
              </span>
              <template v-slot:input>
                <CustomInput
                  style="width: 300px !important"
                  class="mt-3"
                  placeholder="textField.email"
                  label="textField.email"
                  type="textfield"
                  v-model="$v.company.email.$model"
                  :rules="rules.emailRule($v.company, $root)"
                />
              </template>
            </v-edit-dialog>
          </template> -->

          <!-- <template v-slot:[`item.logo`]="{ item, index }">
            <v-edit-dialog
              :return-value.sync="item.logo"
              large
              persistent
              @save="onSave('logo', index)"
              @open="onOpen(item.logo, 'logo')"
            >
              <span
                :class="`${
                  checkIfHasErr(item.logo, 'logo', index + 6)
                    ? 'text--white error-class px-2 d-flex align-center justify-center'
                    : ''
                }`"
              >
                {{ item.logo ? item.logo : "Empty" }}
              </span>
              <template v-slot:input>
                <CustomInput
                  style="width: 300px !important"
                  class="mt-3"
                  placeholder="textField.logo"
                  label="textField.logo"
                  type="textfield"
                  v-model="$v.company.logo.$model"
                  :rules="rules.logoRule($v.company, $root)"
                />
              </template>
            </v-edit-dialog>
          </template> -->

          <!-- <template v-slot:[`item.note`]="{ item, index }">
            <v-edit-dialog
              :return-value.sync="item.note"
              large
              persistent
              @save="onSave('note', index)"
              @open="onOpen(item.note, 'note')"
            >
              <span
                :class="`${
                  checkIfHasErr(item.note, 'note', index + 6)
                    ? 'text--white error-class px-2 d-flex align-center justify-center'
                    : ''
                }`"
              >
                {{ item.note ? item.note : "Empty" }}
              </span>
              <template v-slot:input>
                <CustomInput
                  style="width: 300px !important"
                  class="mt-3"
                  placeholder="textField.note"
                  label="textField.note"
                  type="textfield"
                  v-model="$v.company.note.$model"
                  :rules="rules.noteRule($v.company, $root)"
                />
              </template>
            </v-edit-dialog>
          </template> -->

          <!-- <template v-slot:[`item.investment_type`]="{ item, index }">
            <v-edit-dialog
              :return-value.sync="item.investment_type"
              large
              persistent
              @save="onSave('investment_type', index)"
              @open="onOpen(item.investment_type, 'investment_type')"
            >
              <span
                :class="`${
                  checkIfHasErr(
                    item.investment_type,
                    'investment_type',
                    index + 6
                  )
                    ? 'text--white error-class px-2 d-flex align-center justify-center'
                    : ''
                }`"
              >
                {{ item.investment_type ? item.investment_type : "Empty" }}
              </span>
              <template v-slot:input>
                <CustomInput
                  class="mt-3"
                  style="width: 300px !important"
                  :items="investmentTypes"
                  item-value="name"
                  item-text="name"
                  :label="$tr('label_required',$tr('investment_type'))"
                  placeholder="textField.investmentType"
                  type="select"
                  v-model="$v.company.investment_type.$model"
                  :rules="rules.investmentTypeRule($v.company, $root)"
                />
              </template>
            </v-edit-dialog>
          </template> -->

          <!-- <template v-slot:[`item.label`]="{ item, index }">
            <v-edit-dialog
              :return-value.sync="item.label"
              large
              persistent
              @save="onSave('label', index)"
              @open="onOpen(item.label, 'label')"
            >
              <span
                :class="`${
                  checkIfHasErr(item.label, 'label', index + 6)
                    ? 'text--white error-class px-2 d-flex align-center justify-center'
                    : ''
                }`"
              >
                {{ item.label ? item.label : "Empty" }}
              </span>
              <template v-slot:input>
                <CustomInput
                  class="mt-3"
                  :items="labels"
                  style="width: 300px !important"
                  item-value="title"
                  item-text="title"
                  :label="$tr('label_required',$tr('investment_type'))"
                  placeholder="textField.investmentType"
                  type="select"
                  v-model="$v.company.label_id.$model"
                  :rules="rules.labelRule($v.company, $root)"
                />
              </template>
            </v-edit-dialog>
          </template> -->

          <!-- <template v-slot:[`item.country`]="{ item, index }">
            <v-edit-dialog
              :return-value.sync="item.country"
              large
              persistent
              @save="onSave('country', index)"
              @open="onOpen(item.country, 'country')"
            >
              <span
                :class="`${
                  checkIfHasErr(item.country, 'country', index + 6)
                    ? 'text--white error-class px-2 d-flex align-center justify-center'
                    : ''
                }`"
              >
                {{ item.country ? item.country : "Empty" }}
              </span>
              <template v-slot:input>
                <CustomInput
                  class="mt-3"
                  :items="countries"
                  style="width: 300px !important"
                  item-value="name"
                  item-text="name"
                  :label="$tr('label_required',$tr('country'))"
                  placeholder="textField.country"
                  country
                  type="autocomplete"
                  v-model="$v.company.country_id.$model"
                  :rules="rules.countryRule($v.company, $root)"
                />
              </template>
            </v-edit-dialog>
          </template> -->

          <!-- <template v-slot:[`item.system`]="{ item, index }">
            <v-edit-dialog
              :return-value.sync="item.system"
              large
              persistent
              @save="onSave('system', index)"
              @open="onOpen(item.system, 'system')"
            >
              <span
                :class="`${
                  checkIfHasErr(item.system, 'system', index + 6)
                    ? 'text--white error-class px-2 d-flex align-center justify-center'
                    : ''
                }`"
              >
                {{ item.system ? item.system : "Empty" }}
              </span>
              <template v-slot:input>
                <CustomInput
                  class="mt-3"
                  :items="systems"
                  style="width: 300px !important"
                  item-value="name"
                  item-text="name"
                  label="textField.system_required"
                  placeholder="textField.system"
                  type="autocomplete"
                  v-model="$v.company.system_id.$model"
                  :rules="rules.systemRule($v.company, $root)"
                />
              </template>
            </v-edit-dialog>
          </template>
