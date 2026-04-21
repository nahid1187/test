guardPage('admin').then(user=>{
    if(!user) return;
    API.post(API.admin,{action:'stats'}).then(res=>{
        if(res.ok){
            document.getElementById('statUsers').textContent = res.stats.users;
            document.getElementById('statSalons').textContent = res.stats.salons;
            document.getElementById('statAppts').textContent = res.stats.appointments;
        }
    });
});

function showUsers(){
    API.post(API.admin,{action:'get_users'}).then(res=>{
        if(!res.ok) return;
        document.getElementById('dataArea').innerHTML = `
        <h3>All Users</h3>
        <table>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Action</th></tr>
            ${res.users.map(u=>`
            <tr>
                <td>${u.name}</td><td>${u.email}</td><td>${u.role}</td><td>${u.created_at}</td>
                <td><button class="btn-delete" onclick="deleteUser(${u.id})">Delete</button></td>
            </tr>`).join('')}
        </table>`;
    });
}

function deleteUser(id){
    if(!confirm('Delete this user?')) return;
    API.post(API.admin,{action:'delete_user',user_id:id}).then(()=> showUsers());
}

function showSalons(){
    API.post(API.admin,{action:'get_salons'}).then(res=>{
        if(!res.ok) return;
        document.getElementById('dataArea').innerHTML = `
        <h3>All Salons</h3>
        <table>
            <tr><th>Salon Name</th><th>Location</th><th>Owner</th><th>Rating</th><th>Action</th></tr>
            ${res.salons.map(s=>`
            <tr>
                <td>${s.salon_name}</td><td>${s.location||'—'}</td>
                <td>${s.owner_name}</td><td>⭐ ${s.rating||0}</td>
                <td><button class="btn-delete" onclick="deleteSalon(${s.id})">Delete</button></td>
            </tr>`).join('')}
        </table>`;
    });
}

function deleteSalon(id){
    if(!confirm('Delete this salon and all its data?')) return;
    API.post(API.admin,{action:'delete_salon',salon_id:id}).then(()=> showSalons());
}

function showAppointments(){
    API.post(API.admin,{action:'get_appointments'}).then(res=>{
        if(!res.ok) return;
        document.getElementById('dataArea').innerHTML = `
        <h3>All Appointments</h3>
        <table>
            <tr><th>Salon</th><th>Customer</th><th>Service</th><th>Barber</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr>
            ${res.appointments.map(a=>`
            <tr>
                <td>${a.salon_name}</td><td>${a.customer_name}</td>
                <td>${a.service_name}</td><td>${a.barber_name}</td>
                <td>${a.appt_date}</td><td>${a.appt_time}</td>
                <td class="status-${a.status}">${a.status}</td>
                <td><button class="btn-delete" onclick="deleteAppt(${a.id})">Delete</button></td>
            </tr>`).join('')}
        </table>`;
    });
}

function deleteAppt(id){
    if(!confirm('Delete this appointment?')) return;
    API.post(API.admin,{action:'delete_appointment',appointment_id:id}).then(()=> showAppointments());
}
