guardPage('salon').then(user=>{ if(user) loadServices(); });

function loadServices(){
    API.post(API.services,{action:'get'}).then(res=>{
        const div = document.getElementById('serviceList');
        if(!res.ok||!res.services.length){ div.innerHTML='<p style="color:#888">No services yet.</p>'; return; }
        div.innerHTML = res.services.map(s=>`
            <div class="item-row">
                <span><strong>${s.name}</strong> — $${parseFloat(s.price).toFixed(2)}</span>
                <button class="btn-delete" onclick="deleteService(${s.id})">Delete</button>
            </div>`).join('');
    });
}

function addService(){
    const name  = document.getElementById('serviceName').value.trim();
    const price = document.getElementById('servicePrice').value;
    if(!name||!price){ showMsg('msg','Name and price are required'); return; }
    API.post(API.services,{action:'add',name,price}).then(res=>{
        showMsg('msg', res.msg||'Done', res.ok);
        if(res.ok){
            document.getElementById('serviceName').value  = '';
            document.getElementById('servicePrice').value = '';
            loadServices();
        }
    });
}

function deleteService(id){
    if(!confirm('Delete this service?')) return;
    API.post(API.services,{action:'delete',service_id:id}).then(()=> loadServices());
}
