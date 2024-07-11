import '@main';

const formSelector = '#attending-form';

u.formValidation().then(() => {
  u.$ui.disableOnSubmit(formSelector);
});

// Invoice Inputs
const carrierInput = u.selectOne<HTMLInputElement>('#input-order-invoice_data-carrier_code')!;
const invoiceTitleInput = u.selectOne<HTMLInputElement>('#input-order-invoice_data-title')!;
const invoiceVatInput = u.selectOne<HTMLInputElement>('#input-order-invoice_data-vat')!;

u.selectAll<HTMLInputElement>('[name="order[invoice_type]"]', (radio) => {
  radio.addEventListener('change', (e) => {
    const v = (e.target as HTMLInputElement).value;

    carrierInput.disabled = true;
    invoiceTitleInput.disabled = true;
    invoiceVatInput.disabled = true;

    if (v === 'personal') {
      carrierInput.disabled = false;
    } else {
      invoiceTitleInput.disabled = false;
      invoiceVatInput.disabled = false;
    }
  });
});
