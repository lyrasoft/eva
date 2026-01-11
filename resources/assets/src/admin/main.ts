import { App, defineJsModules } from '@windwalker-io/core/app';
import {
  pushUnicornToGlobal,
  useUIBootstrap5,
  useUnicorn,
  useUnicornPhpAdapter,
  useListDependent,
} from '@windwalker-io/unicorn-next';
import { useNexusTheme } from '@lyrasoft/nexus';
import { useLuna } from '@lyrasoft/luna';

const app = new App(defineJsModules());

const u = useUnicorn();

await useUIBootstrap5(true, true);

useUnicornPhpAdapter();
useLuna();

pushUnicornToGlobal();

useNexusTheme();

// Todo: Remove after Unicorn Fix this
// @ts-ignore
u.$ui.listDependent = useListDependent;

export { app as default, u };
