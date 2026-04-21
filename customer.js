guardPage('customer').then(user=>{
    if(!user) return;
    document.getElementById('userName').textContent = user.name;
    API.post(API.salons,{action:'customer_stats'}).then(res=>{
        if(res.ok){
            document.getElementById('totalSalons').textContent  = res.salons;
            document.getElementById('totalBarbers').textContent = res.barbers;
        }
    });
    API.post(API.appointments,{action:'get_mine'}).then(res=>{
        if(res.ok) document.getElementById('myAppointments').textContent = res.appointments.length;
    });
});
