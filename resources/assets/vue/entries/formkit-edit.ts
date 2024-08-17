
import { createApp } from 'vue';
import FormkitEditApp from '@vue/app/FormkitEditApp.vue';

await S.import('@main');
await u.domready();

const app = createApp(
  FormkitEditApp,
  u.data('formkit.props') || {}
);

app.mount('formkit-edit-app');
