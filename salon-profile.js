guardPage('salon').then(user=>{
    if(!user) return;
    API.post(API.salons,{action:'get_mine'}).then(res=>{
        if(res.ok && res.salon){
            document.getElementById('salonName').value           = res.salon.salon_name;
            document.getElementById('location').value            = res.salon.location;
            document.getElementById('displayName').textContent   = res.salon.salon_name;
        }
    });
});

function saveProfile(){
    const salon_name = document.getElementById('salonName').value.trim();
    const location   = document.getElementById('location').value.trim();
    if(!salon_name){ showMsg('msg','Salon name is required'); return; }
    API.post(API.salons,{action:'save_profile',salon_name,location})
    .then(res=>{
        showMsg('msg', res.msg||'Done', res.ok);
        if(res.ok) document.getElementById('displayName').textContent = salon_name;
    });
}
