// ── api.js  (load this FIRST on every page) ──────────────────
const BASE = (function(){
    const parts = location.pathname.split('/');
    parts.pop();
    return parts.join('/') + '/';
})();

const API = {
    auth:         BASE + 'api/auth.php',
    salons:       BASE + 'api/salons.php',
    services:     BASE + 'api/services.php',
    barbers:      BASE + 'api/barbers.php',
    appointments: BASE + 'api/appointments.php',
    admin:        BASE + 'api/admin.php',

    async post(url, data){
        try {
            const r = await fetch(url, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data),
                credentials: 'same-origin'
            });
            const text = await r.text();
            try { return JSON.parse(text); }
            catch(e){ console.error('Bad JSON:', text); return {ok:false, msg:'Server error - check PHP logs'}; }
        } catch(e){
            console.error('Fetch error:', e);
            return {ok:false, msg:'Network error'};
        }
    }
};

function logout(){
    API.post(API.auth,{action:'logout'}).then(()=> location.href = BASE+'index.html');
}

function showMsg(id, msg, ok){
    const el = document.getElementById(id);
    if(!el) return;
    el.textContent = msg;
    el.style.color = ok ? 'green' : 'red';
}

async function guardPage(role){
    const res = await API.post(API.auth,{action:'me'});
    if(!res.ok || !res.user || res.user.role !== role){
        alert('Please login first!');
        location.href = BASE+'index.html';
        return null;
    }
    return res.user;
}

function togglePass(){
    const p = document.getElementById('password');
    if(p) p.type = p.type==='password' ? 'text' : 'password';
}
