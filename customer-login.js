function login(){
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    if(!email||!password){ showMsg('msg','Enter email and password'); return; }
    API.post(API.auth,{action:'login',email,password,role:'customer'})
    .then(res=>{
        showMsg('msg', res.msg||'', res.ok);
        if(res.ok) location.href = BASE+'customer.html';
    });
}
