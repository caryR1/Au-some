// Mobile navigation — independent of page-specific features
const menuToggle=document.querySelector('.menu-toggle');
const mainNav=document.querySelector('#main-nav');
if(menuToggle&&mainNav){
  menuToggle.addEventListener('click',()=>{
    const open=mainNav.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded',String(open));
    menuToggle.setAttribute('aria-label',open?'Close menu':'Open menu');
  });
  mainNav.querySelectorAll('a,button').forEach(el=>el.addEventListener('click',()=>{
    mainNav.classList.remove('open');
    menuToggle.setAttribute('aria-expanded','false');
    menuToggle.setAttribute('aria-label','Open menu');
  }));
}

// Homepage contact/form features only run when their elements exist.
const dialog=document.querySelector('#contact-dialog'),form=document.querySelector('#contact-form');
if(dialog&&form){
let opener;
document.querySelector('#year').textContent=new Date().getFullYear();
document.querySelectorAll('[data-contact]').forEach(button=>button.addEventListener('click',()=>{opener=button;if(button.dataset.service)form.elements.service.value=button.dataset.service;dialog.showModal();document.body.classList.add('modal-open');form.elements.fullName.focus();}));
document.querySelector('.close').addEventListener('click',()=>dialog.close());
dialog.addEventListener('click',e=>{const r=dialog.getBoundingClientRect();if(e.target===dialog&&(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom))dialog.close();});
dialog.addEventListener('close',()=>{document.body.classList.remove('modal-open');opener?.focus();});

// Result pop-up. Contact details are read from the page so they live in one place (index.html).
const result=document.querySelector('#result-dialog'),submit=form.querySelector('.form-submit'),STORE='au-some-request';
const directContact=()=>{const tel=document.querySelector('.contact-links a[href^="tel:"] span:last-child'),mail=document.querySelector('.contact-links a[href^="mailto:"]');return tel&&mail?` If it still doesn’t go through, please call or email Melicia directly at ${tel.lastChild.textContent.trim()} or ${mail.href.replace('mailto:','')} and let her know the website form didn’t work.`:' If it still doesn’t go through, please contact Melicia directly and let her know the website form didn’t work.';};
function showResult(ok){
  result.classList.toggle('ok',ok);result.classList.toggle('err',!ok);
  result.querySelector('#result-title').textContent=ok?'Request sent':'Request not sent';
  result.querySelector('#result-message').textContent=ok?'Thank you. Melicia has received your request and will be in touch soon.':'Sorry, something went wrong and your request was not sent. Your details are still filled in, so you can simply try again.'+directContact();
  result.querySelector('#result-close').textContent=ok?'Done':'Try again';
  result.showModal();
}
result.addEventListener('close',()=>{if(result.classList.contains('err')){if(!dialog.open){dialog.showModal();document.body.classList.add('modal-open');}submit.focus();}});
document.querySelector('#result-close').addEventListener('click',()=>result.close());

// Keep a copy of the answers until the request succeeds, so a failed attempt can be retried without retyping.
const save=()=>{try{sessionStorage.setItem(STORE,JSON.stringify(Object.fromEntries(new FormData(form))));}catch{}};
const restore=()=>{try{const d=JSON.parse(sessionStorage.getItem(STORE)||'null');if(d)for(const[k,v]of Object.entries(d))if(form.elements[k])form.elements[k].value=v;}catch{}};
const forget=()=>{try{sessionStorage.removeItem(STORE);}catch{}};

form.addEventListener('submit',async e=>{
  e.preventDefault();save();
  const label=submit.innerHTML;submit.disabled=true;submit.textContent='Sending…';
  let ok=false;
  try{const r=await fetch(form.action,{method:'POST',body:new FormData(form),headers:{'X-Requested-With':'fetch'}});ok=r.ok&&(await r.json()).ok===true;}catch{}
  submit.disabled=false;submit.innerHTML=label;
  if(ok){forget();form.reset();dialog.close();}
  showResult(ok);
});

// Fallback for the plain (non-fetch) form post, which returns with ?sent=1 or ?error=1.
const params=new URLSearchParams(location.search);
if(params.has('sent')||params.has('error')){
  history.replaceState(null,'',location.pathname+location.hash);
  if(params.get('sent')==='1'){forget();showResult(true);}
  else{restore();dialog.showModal();document.body.classList.add('modal-open');showResult(false);}
}



}
