guardPage('salon').then(user=>{ if(user) loadAppointments(); });

function loadAppointments(){
    API.post(API.appointments,{action:'get_salon'}).then(res=>{
        const tb = document.getElementById('appointmentTable');
        if(!res.ok||!res.appointments.length){
            tb.innerHTML='<tr><td colspan="7" style="padding:20px">No appointments yet.</td></tr>'; return;
        }
        tb.innerHTML = res.appointments.map(a=>`
            <tr>
                <td>${a.customer_name}<br><small style="color:#888">${a.customer_email}</small></td>
                <td>${a.service_name}</td>
                <td>${a.barber_name}</td>
                <td>${a.appt_date}</td>
                <td>${a.appt_time}</td>
                <td class="status-${a.status}">${a.status}</td>
                <td>
                    <button class="btn-accept" onclick="updateStatus(${a.id},'Accepted')">Accept</button>
                    <button class="btn-reject" onclick="updateStatus(${a.id},'Rejected')">Reject</button>
                    <button class="btn-done"   onclick="updateStatus(${a.id},'Completed')">Done</button>
                </td>
            </tr>`).join('');
    });
}

function updateStatus(id, status){
    API.post(API.appointments,{action:'update_status',appointment_id:id,status})
    .then(()=> loadAppointments());
}
