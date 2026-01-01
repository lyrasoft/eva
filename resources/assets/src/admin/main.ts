import { App, defineJsModules } from '@windwalker-io/core/app';
import {
  pushUnicornToGlobal,
  useUIBootstrap5,
  useUnicorn,
  useUnicornPhpAdapter
} from '@windwalker-io/unicorn-next';
import { useNexusTheme } from '@lyrasoft/nexus';
import { useLuna } from '@lyrasoft/luna';
import { useListDependent } from '../../../../vendor/windwalker/unicorn/assets/src/composable';

const app = new App(defineJsModules());

const u = useUnicorn();

await useUIBootstrap5(true, true);

useUnicornPhpAdapter();
useLuna();

pushUnicornToGlobal();

useNexusTheme();

// @ts-ignore
u.$ui.listDependent = useListDependent;

export { app as default, u };
