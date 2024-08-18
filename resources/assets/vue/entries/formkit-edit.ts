import GridOptions from '@vue/components/form/GridOptions.vue';
import SelectOptions from '@vue/components/form/SelectOptions.vue';
import type { FormType } from '@vue/types';
import Vue, { createApp } from 'vue';
import FormkitEditApp from '@vue/app/FormkitEditApp.vue';
import { VueDraggable } from 'vue-draggable-plus'
import { createBootstrap } from 'bootstrap-vue-next';

import '@vue/services/form-mixin';

// @ts-ignore
import('bootstrap-vue-next/dist/bootstrap-vue-next.css');

await S.import('@main');
await u.domready();

FormkitEditApp.name = 'Formkit';
const props = u.data('formkit.props') || {};

const app = createApp(
  FormkitEditApp,
  props
);

app.use(createBootstrap());
app.component('draggable', VueDraggable);
app.component('SelectOptions', SelectOptions);
app.component('GridOptions', GridOptions);

// Init all fields
const types: Record<string, FormType> = props.types;
const promises: Promise<any>[] = [];

for (const id in types) {
  const type = props.types[id];

  if (type.componentModuleUrl) {
    promises.push(
      u.import(type.componentModuleUrl).then((module) => {
        app.component(type.componentName, module.default(app, Vue));
      })
    );
  }
}

await Promise.allSettled(promises);

u.trigger('formkit.prepared', app);

const vm = app.mount('formkit-edit-app');

u.trigger('formkit.mounted', vm, app);
