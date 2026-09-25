const dialog=document.querySelector('#contact-dialog'),form=document.querySelector('#contact-form'),status=document.querySelector('#form-status');let opener;
document.querySelector('#year').textContent=new Date().getFullYear();
document.querySelectorAll('[data-contact]').forEach(button=>button.addEventListener('click',()=>{opener=button;if(button.dataset.service)form.elements.service.value=button.dataset.service;dialog.showModal();document.body.classList.add('modal-open');form.elements.fullName.focus();}));
document.querySelector('.close').addEventListener('click',()=>dialog.close());
dialog.addEventListener('click',event=>{const r=dialog.getBoundingClientRect();if(event.target===dialog&&(event.clientX<r.left||event.clientX>r.right||event.clientY<r.top||event.clientY>r.bottom))dialog.close();});
dialog.addEventListener('close',()=>{document.body.classList.remove('modal-open');opener?.focus();});
form.addEventListener('submit',async event=>{event.preventDefault();if(!form.reportValidity())return;status.textContent='Sending your request…';const submit=form.querySelector('[type="submit"]');submit.disabled=true;
try{const response=await fetch('submit.php',{method:'POST',body:new FormData(form),headers:{'Accept':'application/json'}});const result=await response.json();if(!response.ok||!result.ok)throw new Error(result.message||'Unable to send request.');status.textContent=result.message;form.reset();}
catch(error){status.textContent=error.message||'Your request could not be sent. Please contact Melicia directly.';}finally{submit.disabled=false;}});
