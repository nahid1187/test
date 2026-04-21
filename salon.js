guardPage('salon').then(user=>{
    if(!user) return;
    API.post(API.salons,{action:'stats'}).then(res=>{
        if(res.ok){
            document.getElementById('totalAppointments').textContent = res.stats.appointments;
            document.getElementById('totalServices').textContent     = res.stats.services;
            document.getElementById('totalBarbers').textContent      = res.stats.barbers;
        }
    });
});
