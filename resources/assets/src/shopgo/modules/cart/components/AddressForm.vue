<script setup lang="ts">
import { __, data, data as udata, route, useHttpClient, useTomSelect } from '@windwalker-io/unicorn-next';
import { Modal } from 'bootstrap';
import { Md5 } from 'ts-md5';
import { nextTick, watch, ref, computed, onMounted, useTemplateRef } from 'vue';
import { ComponentExposed } from 'vue-component-type-helpers';
import CascadeSelect from '~shopgo/modules/cart/components/CascadeSelect.vue';
import TaiwanAddress from './TaiwanAddress.vue';
import { Address, AddressFormData, User } from '~shopgo/types';

const props = defineProps<{
  type: string;
  user: User | null;
  syncData?: any;
  title: string;
  syncLabel?: string;
}>();

const defaultAddress = {
  // addressId: '',
  // locationId: 0,
  // firstname: '',
  // lastname: '',
  // name: '',
  // email: '',
  // phone: '',
  // mobile: '',
  // company: '',
  // vat: '',
  // country: '',
  // state: '',
  // city: '',
  // postcode: '',
  // address1: '',
  // address2: '',
  // save: false,
};

const emit = defineEmits<{
  'validated': [pass: boolean];
}>()

const modelValue = defineModel<AddressFormData>({
  required: true,
});

// Replace reactive `state` with individual refs
const addressLoading = ref(false);
const currentState = ref(props.syncData == null ? 'initializing' : 'sync');
const locationPath = ref<(number | string)[]>([]);
const cascadeOptions = {
  ajaxUrl: route('@address_ajax/locationOptions'),
  labels: udata('location.labels') || [],
  placeholder: __('unicorn.select.placeholder'),
  onSelectInit(e: CustomEvent) {
    const select = e.detail.el;

    useTomSelect(select);
  }
};

modelValue.value = Object.assign(
  {},
  defaultAddress,
  {
    firstName: props.user?.firstname || '',
    lastName: props.user?.lastname || '',
    name: props.user?.name || '',
  },
  modelValue.value
);
const addresses = ref<any[]>([]);
const currentAddressHash = ref('');
const sync = ref(props.syncData != null);
const addressSelecting = ref(false);

const form = ref();
const locationSelector = ref<ComponentExposed<typeof CascadeSelect>>();
const modalElement = useTemplateRef<HTMLDivElement>('modal');

if (!modelValue.value || Object.keys(modelValue.value).length === 0) {
  findMyAddress().then((addrs) => {
    const firstAddress = addrs[0] || null;

    if (firstAddress) {
      modelValue.value = prepareAddressData(firstAddress);
    }
  });
}

onMounted(async () => {
  if (!sync.value) {
    const addrs = await findMyAddress();
    let address;

    if (modelValue.value.id) {
      address = addrs.find((addr: Address) => String(addr.id) === String(modelValue.value.id));
    }

    if (!address) {
      address = addrs[0];
    }

    if (address) {
      setAddressToData(address);
    }

    currentState.value = 'selected';
  } else {
    currentState.value = 'form';
  }

  updateLocationList();
});

function validate() {
  if (sync.value) {
    return true;
  }

  if (form.value) {
    let pass = true;
    const inputs = form.value.querySelectorAll('input,textarea,select');

    for (const input of inputs) {
      if (!input.checkValidity()) {
        pass = pass && false;

        input.reportValidity();
        break;
      }
    }

    emit('validated', pass);

    return pass;
  }

  return true;
}

// watch(() => state.data, () => {
//   emit('update:modelValue', state.data);
// }, { deep: true, immediate: true });

watch(() => props.syncData, async () => {
  if (sync.value && props.syncData) {
    syncAddressFromOutside();
  }
}, { deep: true, immediate: true });

watch(sync, (v) => {
  if (!v) {
    currentState.value = 'form';
    modelValue.value.id = undefined;
    modelValue.value.addressId = undefined;
  } else if (props.syncData) {
  } else if (props.syncData) {
    currentState.value = 'sync';
    syncAddressFromOutside();
  }
});

function syncAddressFromOutside() {
  modelValue.value = JSON.parse(JSON.stringify(props.syncData || {}));

  // await updateLocationList();
}

const showSaveButton = computed(() => {
  return currentAddressHash.value !== Md5.hashStr(JSON.stringify(modelValue.value));
});

function locationChanged(e: CustomEvent) {
  if (e.detail) {
    modelValue.value.locationId = e.detail.value;
    locationPath.value = e.detail.path;
  }
}

function buildInputId(name: string) {
  return `input-${props.type}-${name}`;
}

function buildInputName(name: string) {
  return `checkout[${props.type}_data][${name}]`;
}

function createNew() {
  currentState.value = 'new';
  locationPath.value = [];
  modelValue.value = Object.assign({}, defaultAddress) as AddressFormData;
}

async function findMyAddress(): Promise<AddressFormData[]> {
  const { get } = await useHttpClient();

  const res = await get('@address_ajax/myAddresses');

  return res.data.data;
}

// Select

async function openAddressSelector() {
  addressLoading.value = true;

  const modalInstance = Modal.getOrCreateInstance(modalElement.value!);

  modalInstance.show();

  try {
    addresses.value = await findMyAddress();
  } finally {
    addressLoading.value = false;
  }
}

async function selectAddress(address: any) {
  addressSelecting.value = true;
  modelValue.value = Object.assign(
    {},
    defaultAddress,
    address
  );

  await setAddressToData(address);

  currentAddressHash.value = Md5.hashStr(JSON.stringify(modelValue.value));

  await updateLocationList();

  addressSelecting.value = false;
}

function prepareAddressData(data: AddressFormData): AddressFormData {
  data.locationPath = data.locationPath.map((v) => String(v));
  data.addressId = String(data.id);

  return data;
}

async function setAddressToData(address: AddressFormData) {

  const data = Object.assign(
    {},
    defaultAddress,
    address
  );

  modelValue.value = prepareAddressData(data);

  Modal.getOrCreateInstance(modalElement.value!).hide();

  await updateLocationList();
}

async function updateLocationList() {
  locationPath.value = modelValue.value.locationPath || [];
  await nextTick();
  await locationSelector.value?.prepareValues();
}

defineExpose({
  validate,
});
</script>

<template>
  <div class="card mb-4">
    <div class="card-body">
      <div class="card-title d-flex justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <h4 class="m-0">
            {{ title }}
          </h4>

          <div class="form-check" v-if="syncData">
            <label :for="`input-${type}-sync`" class="form-check-label">
              {{ syncLabel || $lang('shopgo.cart.address.form.same.with.buyer') }}
            </label>
            <input type="checkbox" v-model="sync" :id="`input-${type}-sync`"
              :name="buildInputName('sync')"
              class="form-check-input"
              value="1"
            />
          </div>
        </div>

        <div v-if="user && !sync">
          <button type="button"
            class="btn btn-outline-success btn-sm"
            style="min-width: 100px"
            @click="createNew"
          >
            {{ $lang('shopgo.cart.address.form.new.address') }}
          </button>
          <button type="button"
            class="btn btn-outline-primary btn-sm"
            style="min-width: 100px"
            @click="openAddressSelector"
          >
            {{ $lang('shopgo.cart.address.form.select') }}
          </button>
        </div>
      </div>

      <transition name="fade" mode="out-in">
        <div v-if="currentState === 'initializing'">
          <div class="placeholder-glow">
            <span class="placeholder col-7"></span>
          </div>
        </div>
        <div v-else-if="!sync && modelValue.addressId" class="mt-3"
          style="animation-duration: .3s">
          {{ modelValue.formatted }}
        </div>
        <div v-else-if="!sync && !modelValue.addressId" class="row mt-3" style="animation-duration: .3s"
          ref="form">
          <TaiwanAddress :type v-model="modelValue" />

        </div>
      </transition>

      <div class="d-none">
        <input :id="buildInputId('addressId')" type="hidden"
          :name="buildInputName('addressId')"
          v-model="modelValue.addressId"
        />
      </div>
    </div>

    <!-- Modal -->
    <div ref="modal" class="modal fade" :id="`${type}-address-modal`" tabindex="-1" role="dialog" aria-labelledby="address-modal-label"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="address-modal-label">
              {{ $lang('shopgo.cart.address.form.modal.title') }}
            </h4>
            <button type="button" class="close btn-close" data-bs-dismiss="modal" data-dismiss="modal"
              aria-label="Close">
              <span aria-hidden="true" class="visually-hidden">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="!addressLoading && addresses.length" class="list-group list-group-flush">
              <a href="javascript://" class="list-group-item d-flex gap-2 justify-content-between"
                v-for="address of addresses"
                :key="address"
                @click="selectAddress(address)"
              >
                <div>
                  {{ address.formatted }}
                </div>
                <div>
                  <span class="btn btn-outline-secondary btn-sm text-nowrap">
                      {{ $lang('shopgo.cart.address.form.button.select') }}
                  </span>
                </div>
              </a>
            </div>
            <div v-else class="card bg-light text-center py-5">
              <template v-if="addressLoading">
                <span class="spinner spinner-border mx-auto"></span>
              </template>
              <template v-else>
                {{ $lang('shopgo.cart.address.form.no.addresses') }}
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
