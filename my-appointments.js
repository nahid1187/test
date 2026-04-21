guardPage('customer').then(user=>{ if(user) loadAppointments(); });

function loadAppointments(){
    API.post(API.appointments,{action:'get_mine'}).then(res=>{
        const tb = document.getElementById('appointmentTable');
        if(!res.ok||!res.appointments.length){
            tb.innerHTML='<tr><td colspan="7" style="padding:20px">No appointments yet.</td></tr>'; return;
        }
        tb.innerHTML = res.appointments.map(a=>`
            <tr>
                <td>${a.salon_name}</td>
                <td>${a.service_name}</td>
                <td>${a.barber_name}</td>
                <td>${a.appt_date}</td>
                <td>${a.appt_time}</td>
                <td class="status-${a.status}">${a.status}</td>
                <td>${a.status!=='Cancelled'&&a.status!=='Completed'
                    ? `<button class="btn-delete" onclick="cancelAppointment(${a.id})">Cancel</button>`
                    : '—'}</td>
            </tr>`).join('');
    });
}

function cancelAppointment(id){
    if(!confirm('Cancel this appointment?')) return;
    API.post(API.appointments,{action:'cancel',appointment_id:id})
    .then(()=> loadAppointments());
}
