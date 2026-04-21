guardPage('salon').then(user=>{ if(user) loadBarbers(); });

function loadBarbers(){
    API.post(API.barbers,{action:'get'}).then(res=>{
        const div = document.getElementById('barberList');
        if(!res.ok||!res.barbers.length){ div.innerHTML='<p style="color:#888">No barbers yet.</p>'; return; }
        div.innerHTML = res.barbers.map(b=>`
            <div class="item-row">
                <span>✂️ ${b.name}</span>
                <button class="btn-delete" onclick="deleteBarber(${b.id})">Delete</button>
            </div>`).join('');
    });
}

function addBarber(){
    const name = document.getElementById('barberName').value.trim();
    if(!name){ showMsg('msg','Barber name is required'); return; }
    API.post(API.barbers,{action:'add',name}).then(res=>{
        showMsg('msg', res.msg||'Done', res.ok);
        if(res.ok){ document.getElementById('barberName').value=''; loadBarbers(); }
    });
}

function deleteBarber(id){
    if(!confirm('Delete this barber?')) return;
    API.post(API.barbers,{action:'delete',barber_id:id}).then(()=> loadBarbers());
}
