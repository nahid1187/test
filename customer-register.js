function register(){
    const name     = document.getElementById('name').value.trim();
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    if(!name||!email||!password){ showMsg('msg','All fields are required'); return; }
    API.post(API.auth,{action:'register',name,email,password,role:'customer'})
    .then(res=>{
        showMsg('msg', res.msg||'Done', res.ok);
        if(res.ok) setTimeout(()=> location.href = BASE+'customer-login.html', 1200);
    });
}
