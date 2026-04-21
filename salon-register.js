function register(){
    const name     = document.getElementById('name').value.trim();
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    if(!name||!email||!password){ showMsg('msg','All fields are required'); return; }
    API.post(API.auth,{action:'register',name,email,password,role:'salon'})
    .then(res=>{
        showMsg('msg', res.msg||'Done', res.ok);
        if(res.ok) setTimeout(()=> location.href = BASE+'salon-login.html', 1200);
    });
}
