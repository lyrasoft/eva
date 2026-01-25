import 'bootstrap';
import { useFavoriteButton } from '@lyrasoft/favorite';
import { useMeloFrontLessons } from '@lyrasoft/melo';
import { useShopGoCatalog } from '@lyrasoft/shopgo';
import { useShopGoEcpay } from '@lyrasoft/shopgo-ecpay';
import { App, defineJsModules } from '@windwalker-io/core/app';
import { pushUnicornToGlobal, useUIBootstrap5, useUnicorn, useUnicornPhpAdapter } from '@windwalker-io/unicorn-next';
import { useLuna } from '@lyrasoft/luna';
import { useRatingButtons } from '@lyrasoft/feedback';

const app = new App(defineJsModules());

const u = useUnicorn();

await useUIBootstrap5(true, true);

useUnicornPhpAdapter();

pushUnicornToGlobal();
useLuna();
useShopGoCatalog();
useShopGoEcpay();
useMeloFrontLessons();
useFavoriteButton();
useRatingButtons();

export { app as default, u };
