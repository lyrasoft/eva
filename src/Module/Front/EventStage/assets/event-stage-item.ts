import '@main';

const formSelector = '#attend-form';

u.formValidation().then(() => {
  u.$ui.disableOnSubmit(formSelector);
});
