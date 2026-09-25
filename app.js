const dialog=document.querySelector('#contact-dialog'),form=document.querySelector('#contact-form');let opener;
document.querySelector('#year').textContent=new Date().getFullYear();
document.querySelectorAll('[data-contact]').forEach(button=>button.addEventListener('click',()=>{opener=button;if(button.dataset.service)form.elements.service.value=button.dataset.service;dialog.showModal();document.body.classList.add('modal-open');form.elements.fullName.focus();}));
document.querySelector('.close').addEventListener('click',()=>dialog.close());
dialog.addEventListener('click',e=>{const r=dialog.getBoundingClientRect();if(e.target===dialog&&(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom))dialog.close();});
dialog.addEventListener('close',()=>{document.body.classList.remove('modal-open');opener?.focus();});
const params=new URLSearchParams(location.search);
const status=document.querySelector('#form-status');
if(params.get('sent')==='1'&&status){status.textContent='Thank you. Your request has been submitted to Melicia.';dialog.showModal();document.body.classList.add('modal-open');status.scrollIntoView({block:'center'});}
if(params.get('error')==='1'&&status){status.textContent='We’re sorry—there was an error submitting your request through the website. Please contact Melicia directly at 445-245-9160 or au-somenotarific@gmail.com.';dialog.showModal();document.body.classList.add('modal-open');}