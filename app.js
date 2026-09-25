const dialog = document.querySelector('#contact-dialog');
const form = document.querySelector('#contact-form');
const status = document.querySelector('#form-status');
let opener;
document.querySelector('#year').textContent = new Date().getFullYear();
document.querySelectorAll('[data-contact]').forEach(button => {
  button.addEventListener('click', () => {
    opener = button;
    if (button.dataset.service) form.elements.service.value = button.dataset.service;
    dialog.showModal();
    document.body.classList.add('modal-open');
    form.elements.fullName.focus();
  });
});
document.querySelector('.close').addEventListener('click', () => dialog.close());
dialog.addEventListener('click', event => {
  const r = dialog.getBoundingClientRect();
  if (event.target === dialog && (event.clientX < r.left || event.clientX > r.right || event.clientY < r.top || event.clientY > r.bottom)) dialog.close();
});
dialog.addEventListener('close', () => {
  document.body.classList.remove('modal-open');
  opener?.focus();
});
form.addEventListener('submit', event => {
  event.preventDefault();
  if (!form.reportValidity()) return;
  const values = Object.fromEntries(new FormData(form));
  const message = `Hello Melicia,\n\nI would like assistance with ${values.service}.\n\nFull name: ${values.fullName.trim()}\nPhone: ${values.phone.trim()}\nEmail: ${values.email.trim()}\nPreferred contact method: ${values.contactMethod}\nBest contact time: ${values.contactTime.trim()}\nService needed: ${values.service}\nAppointment / availability window: ${values.availability.trim()}\n\nNotes:\n${values.notes.trim() || 'None'}\n\nThank you.`;
  document.querySelector('#prepared-message').value = message;
  document.querySelector('#email-fallback').hidden = false;
  status.textContent = 'Your message is prepared. Review and send it in your email app. It has not been sent yet.';
  window.location.href = `mailto:ausomenotarific@gmail.com?subject=${encodeURIComponent('Service request — ' + values.service)}&body=${encodeURIComponent(message)}`;
});
document.querySelector('#copy-message').addEventListener('click', async () => {
  const prepared = document.querySelector('#prepared-message');
  try {
    await navigator.clipboard.writeText(prepared.value);
    status.textContent = 'Message copied. Paste it into an email to ausomenotarific@gmail.com.';
  } catch {
    prepared.focus(); prepared.select();
    status.textContent = 'Your message is selected. Use your device’s Copy command, then paste it into your email.';
  }
});
