import { createApp } from 'vue';
import FormkitEditApp from '@vue/app/FormkitEditApp.vue';
import { createBootstrap } from 'bootstrap-vue-next';

// @ts-ignore
import('bootstrap-vue-next/dist/bootstrap-vue-next.css');

await S.import('@main');
await u.domready();

FormkitEditApp.name = 'Formkit';

const app = createApp(
  FormkitEditApp,
  u.data('formkit.props') || {}
);

app.use(createBootstrap());
app.mount('formkit-edit-app');
