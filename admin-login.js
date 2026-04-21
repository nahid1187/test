function adminLogin(){
    const email     = document.getElementById('email').value.trim();
    const password  = document.getElementById('password').value;
    const secret    = document.getElementById('secretKey').value;
    if(!email||!password||!secret){ showMsg('msg','All fields required'); return; }
    API.post(API.auth,{action:'admin_login',email,password,secret})
    .then(res=>{
        showMsg('msg', res.msg||'', res.ok);
        if(res.ok) location.href = BASE+'admin.html';
    });
}
