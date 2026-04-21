let allSalons = [];

guardPage('customer').then(user=>{
    if(!user) return;
    API.post(API.salons,{action:'get_all'}).then(res=>{
        if(res.ok){ allSalons = res.salons; populateSelect(allSalons); }
    });
});

function populateSelect(list){
    const sel = document.getElementById('salonSelect');
    sel.innerHTML = '<option value="">-- Select Salon --</option>';
    list.forEach(s=> sel.innerHTML += `<option value="${s.id}">${s.salon_name} — ${s.location||'No location'}</option>`);
}

function filterSalons(){
    const kw = document.getElementById('searchInput').value.toLowerCase();
    populateSelect(allSalons.filter(s=>
        s.salon_name.toLowerCase().includes(kw) || (s.location||'').toLowerCase().includes(kw)
    ));
    document.getElementById('bookingForm').innerHTML = '';
}

function renderBookingForm(){
    const sid  = document.getElementById('salonSelect').value;
    const form = document.getElementById('bookingForm');
    if(!sid){ form.innerHTML=''; return; }
    const salon = allSalons.find(s=> s.id == sid);
    if(!salon){ form.innerHTML=''; return; }

    form.innerHTML = `
        <p style="margin:10px 0;color:#555">
            <strong>${salon.salon_name}</strong> · ${salon.location||''} · ⭐ ${salon.rating||0}
        </p>
        <div class="form-group"><label>Service</label>
            <select id="service">
                <option value="">-- Select Service --</option>
                ${salon.services.map(s=>`<option value="${s.id}">${s.name} — $${parseFloat(s.price).toFixed(2)}</option>`).join('')}
            </select></div>
        <div class="form-group"><label>Barber</label>
            <select id="barber" onchange="loadSlots()">
                <option value="">-- Select Barber --</option>
                ${salon.barbers.map(b=>`<option value="${b.id}">${b.name}</option>`).join('')}
            </select></div>
        <div class="form-group"><label>Date</label>
            <input type="date" id="date" min="${new Date().toISOString().split('T')[0]}" onchange="loadSlots()">
        </div>
        <div class="form-group"><label>Time Slot</label>
            <select id="time"><option value="">Select barber & date first</option></select>
        </div>
        <div id="msg"></div>
        <button onclick="bookAppointment(${salon.id})">📅 Book Appointment</button>
        <hr>
        <h4 style="margin:15px 0 10px">Rate this Salon (optional)</h4>
        <div class="form-group">
            <select id="rating">
                <option value="">Skip rating</option>
                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                <option value="4">⭐⭐⭐⭐ Good</option>
                <option value="3">⭐⭐⭐ Average</option>
                <option value="2">⭐⭐ Poor</option>
                <option value="1">⭐ Terrible</option>
            </select></div>
        <button onclick="submitRating(${salon.id})" style="background:#f39c12">Submit Rating</button>`;
}

function loadSlots(){
    const sid    = document.getElementById('salonSelect').value;
    const bid    = document.getElementById('barber').value;
    const dt     = document.getElementById('date').value;
    const sel    = document.getElementById('time');
    if(!bid||!dt){ sel.innerHTML='<option value="">Select barber & date first</option>'; return; }
    API.post(API.appointments,{action:'get_slots',salon_id:sid,barber_id:bid,date:dt}).then(res=>{
        sel.innerHTML = '<option value="">-- Select Time --</option>';
        if(res.ok) res.slots.forEach(s=>{
            sel.innerHTML += s.available
                ? `<option value="${s.time}">${s.time}</option>`
                : `<option disabled>${s.time} (Booked)</option>`;
        });
    });
}

function bookAppointment(salonId){
    const service = document.getElementById('service').value;
    const barber  = document.getElementById('barber').value;
    const date    = document.getElementById('date').value;
    const time    = document.getElementById('time').value;
    if(!service||!barber||!date||!time){ showMsg('msg','⚠️ Please fill all fields'); return; }
    API.post(API.appointments,{action:'book',salon_id:salonId,service_id:service,barber_id:barber,date,time})
    .then(res=>{
        showMsg('msg', (res.ok?'✅ ':'❌ ')+(res.msg||''), res.ok);
        if(res.ok) loadSlots();
    });
}

function submitRating(salonId){
    const rating = document.getElementById('rating').value;
    if(!rating) return;
    API.post(API.appointments,{action:'rate',salon_id:salonId,rating})
    .then(res=> showMsg('msg', res.ok?'⭐ Rating saved!':'Error saving rating', res.ok));
}
