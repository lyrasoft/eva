<?php

declare(strict_types=1);

namespace App\view;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

?>

<template id="c-address-form" type="x-template">
    <div class="card mb-4">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between">
                <h4 class="m-0">
                    收件地址
                </h4>

                <div v-if="user">
                    <button type="button"
                        class="btn btn-outline-success btn-sm"
                        style="width: 100px"
                        @click="createNew"
                    >
                        新地址
                    </button>
                    <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        style="width: 100px"
                        @click="openAddressSelector"
                    >
                        選擇
                    </button>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-5">
                    {{-- First Name --}}
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('firstName')" class="form-label col-3">
                            名
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('firstName')" type="text" class="form-control"
                                v-model="data.firstName" />
                        </div>
                    </div>

                    {{-- Last Name --}}
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('lastName')" class="form-label col-3">
                            姓
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('lastName')" type="text" class="form-control"
                                v-model="data.lastName" />
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('email')" class="form-label col-3">
                            Email
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('email')" type="text" class="form-control"
                                v-model="data.email" />
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('phone')" class="form-label col-3">
                            電話
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('phone')" type="text" class="form-control"
                                v-model="data.phone" />
                        </div>
                    </div>

                    {{-- Mobile --}}
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('mobile')" class="form-label col-3">
                            手機
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('mobile')" type="text" class="form-control"
                                v-model="data.mobile" />
                        </div>
                    </div>

                    {{-- Company --}}
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('company')" class="form-label col-3">
                            公司
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('company')" type="text" class="form-control"
                                v-model="data.company" />
                        </div>
                    </div>

                    {{-- VAT --}}
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('vat')" class="form-label col-3">
                            統編
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('vat')" type="text" class="form-control"
                                v-model="data.vat" />
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="form-group mb-4">
                        <label :for="buildInputId('country')" class="form-label">
                            國家/地區
                        </label>
                        <cascade-select :options="cascadeOptions"
                            v-model="locationPath"
                            @change="locationChanged"
                            :select-attrs="{ 'v-tom-select': '{}' }"
                        >

                        </cascade-select>
                    </div>
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('postcode')" class="form-label col-3">
                            郵遞區號
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('postcode')" type="text" class="form-control"
                                v-model="data.postcode" maxlength="10" />
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('address1')" class="form-label col-3">
                            地址1
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('address1')" type="text" class="form-control"
                                v-model="data.address1" />
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('address2')" class="form-label col-3">
                            地址2
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('address2')" type="text" class="form-control"
                                v-model="data.address2" />
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label :for="buildInputId('save')" class="form-label col-3">
                            儲存供下次使用
                        </label>
                        <div class="col-9">
                            <input :id="buildInputId('save')" type="checkbox" class="form-check-input"
                                v-model="data.save" />
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Modal --}}
        <div ref="modal" class="modal fade" :id="`${type}-address-modal`" tabindex="-1" role="dialog" aria-labelledby="address-modal-label"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="address-modal-label">
                            我的地址
                        </h4>
                        <button type="button" class="close btn-close" data-bs-dismiss="modal" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true" class="visually-hidden">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="list-group list-group-flush">
                            <a href="javascript://" class="list-group-item"
                                v-for="address of addresses"
                                :key="address"
                            >
                                @{{ address.formatted }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    function addressForm() {
      const { ref, toRefs, reactive, computed, watch, onMounted, inject } = Vue;

      const defaultAddress = {
        addressId: '',
        locationId: '',
        firstName: '',
        lastName: '',
        name: '',
        email: '',
        phone: '',
        mobile: '',
        company: '',
        vat: '',
        country: '',
        state: '',
        city: '',
        postcode: '',
        address1: '',
        address2: '',
        save: false,
      };

      return {
        template: '#c-address-form',
        components: {
          'cascade-select': cascadeSelect(),
        },
        props: {
          type: {
            type: String,
            required: true
          },
          modelValue: Object,
          user: Object
        },
        setup(props) {
          const state = reactive({
            currentState: 'new',
            locationPath: [],
            cascadeOptions: {
              ajaxUrl: u.route('@address_ajax/locationOptions'),
              labels: [
                '洲',
                '國家',
                '州/縣市',
                '區'
              ],
              placeholder: '- 請選擇 -',
              onSelectInit(e) {
                const select = e.detail.el;

                u.$ui.tomSelect(select);
              }
            },
            data: Object.assign(
              {},
              defaultAddress,
              {
                firstName: this.user?.firstName || '',
                lastName: this.user?.lastName || '',
                name: this.user?.name || '',
              },
              props.modelValue
            ),
            addresses: u.data('addresses') || []
          });

          onMounted(() => {
            //
          });

          function locationChanged(e) {
            state.data.locationId = e.detail.value;
            state.locationPath = e.detail.path;
          }

          function buildInputId(name) {
            return `input-${props.type}-${name}`;
          }

          function createNew() {
            state.currentState = 'new';
            state.locationPath = [];
            state.data = Object.assign({}, defaultAddress);
          }

          // Select
          const modal = ref(null);

          function openAddressSelector() {
            u.$ui.bootstrap.modal(modal.value).show();
          }

          return {
            ...toRefs(state),
            modal,

            createNew,
            locationChanged,
            buildInputId,
            openAddressSelector,
          };
        }
      }
    }
</script>
