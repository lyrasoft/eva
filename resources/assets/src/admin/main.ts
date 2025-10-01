import { App, defineJsModules } from '@windwalker-io/core/app';
import {
  pushUnicornToGlobal,
  useUIBootstrap5,
  useUnicorn,
  useUnicornPhpAdapter
} from '@windwalker-io/unicorn-next';
import { useNexusTheme } from '~theme/nexus/src/ts/nexus';

const app = new App(defineJsModules());

const u = useUnicorn();

await useUIBootstrap5(true, true);

useUnicornPhpAdapter();

pushUnicornToGlobal();

useNexusTheme();

export { app as default, u };
