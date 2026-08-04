// Authentication Logic (PHP/MySQL)

document.addEventListener('DOMContentLoaded', () => {
    
    
    // Inject auth modal if it doesn't exist
    if (!document.getElementById('auth-modal')) {
        const authHtml = `
        <div class="modal-mask" id="auth-mask"></div>
        <div class="auth-modal" id="auth-modal">
            <button class="auth-close" onclick="closeModal('auth')">&times;</button>
            <div class="auth-tabs">
                <div class="auth-tab active" data-target="login">تسجيل الدخول</div>
                <div class="auth-tab" data-target="register">إنشاء حساب</div>
            </div>
            
            <div class="auth-forms">
                <!-- Login Form -->
                <form id="login-form" class="auth-form active">
                    <div class="field">
                        <label>البريد الإلكتروني</label>
                        <input type="email" id="login-email" required placeholder="أدخل بريدك الإلكتروني">
                    </div>
                    <div class="field">
                        <label>كلمة المرور</label>
                        <input type="password" id="login-password" required placeholder="أدخل كلمة المرور">
                    </div>
                    <a href="#" class="forgot">نسيت كلمة المرور؟</a>
                    <button type="submit" class="btn btn-primary w-full">تسجيل الدخول</button>
                </form>

                <!-- Register Form -->
                <form id="signup-form" class="auth-form">
                    <div class="field">
                        <label>الاسم الكامل</label>
                        <input type="text" id="signup-name" required placeholder="الاسم الكامل">
                    </div>
                    <div class="field">
                        <label>البريد الإلكتروني</label>
                        <input type="email" id="signup-email" required placeholder="أدخل بريدك الإلكتروني">
                    </div>
                    <div class="field">
                        <label>كلمة المرور</label>
                        <input type="password" id="signup-password" required minlength="8" placeholder="أدخل كلمة المرور (8 أحرف على الأقل)">
                    </div>
                    <div class="field">
                        <label>رقم الجوال <span style="color:red">*</span></label>
                        <input type="tel" id="signup-phone" required placeholder="أدخل رقم الجوال (ضروري)">
                    </div>
                    <button type="submit" class="btn btn-primary w-full">إنشاء حساب</button>
                </form>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', authHtml);
        
        // Tab switching logic for the modal
        document.querySelectorAll('.auth-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.target === 'login' ? 'login-form' : 'signup-form').classList.add('active');
            });
        });
        
        // Also add logic to close modal when clicking the mask
        document.getElementById('auth-mask').addEventListener('click', () => closeModal('auth'));
    }
// Check if user is logged in
    checkAuthStatus();

    // 1. Handle Login
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            try {
                const res = await fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();
                
                if (data.success) {
                    showToast('تم تسجيل الدخول بنجاح! 👋', 'success');
                    if (data.user && data.user.role === 'admin') {
                        window.location.href = 'admin.php';
                        return;
                    }
                    updateUIAfterLogin(data.user);
                    if(window.closeModal) closeModal('auth');
                    // Reload the page so the prices/locked products update
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                showToast('حدث خطأ في الاتصال بالسيرفر', 'error');
            }
        });
    }

    // 2. Handle Registration
    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('signup-name').value;
            const email = document.getElementById('signup-email').value;
            const password = document.getElementById('signup-password').value;
            const phone = document.getElementById('signup-phone').value;

            try {
                const res = await fetch('api/register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, email, password, phone })
                });
                const data = await res.json();
                
                if (data.success) {
                    showToast(data.message || 'تم إنشاء الحساب وتسجيل الدخول بنجاح! 🎉', 'success');
                    if (data.user) {
                        updateUIAfterLogin(data.user);
                    }
                    if(window.closeModal) closeModal('auth');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                showToast('حدث خطأ في الاتصال بالسيرفر', 'error');
            }
        });
    }

    // 3. Handle Forgot Password
    const forgotLinks = document.querySelectorAll('.forgot');
    forgotLinks.forEach(link => {
        link.addEventListener('click', async (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            if(!email) {
                showToast('الرجاء كتابة إيميلك في خانة البريد الإلكتروني أولاً ثم ضغط (نسيت كلمة المرور)', 'error');
                return;
            }

            try {
                showToast('جاري إرسال الرابط...', 'success');
                const res = await fetch('api/forgot_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                if(data.success) {
                    showToast('تم الإرسال! راجع بريدك الإلكتروني', 'success');
                } else {
                    showToast(data.message, 'error');
                }
            } catch(err) {
                showToast('حدث خطأ', 'error');
            }
        });
    });

});

window.authUser = null;

// Check auth status on page load
async function checkAuthStatus() {
    try {
        const res = await fetch('api/check_auth.php?t=' + Date.now());
        const data = await res.json();
        if (data.loggedIn) {
            window.authUser = data.user;
            updateUIAfterLogin(data.user);
        } else {
            window.authUser = null;
        }
    } catch(err) {
        console.log("Not logged in");
        window.authUser = null;
    }
    window.dispatchEvent(new Event('authLoaded'));
}

// Update UI
function updateUIAfterLogin(user) {
    window.authUser = user;
    window.dispatchEvent(new Event('authLoaded'));
    const hdrUser = document.querySelector('.hdr-user');
    if(hdrUser) {
        hdrUser.innerHTML = `
            <span style="font-weight:bold; color:var(--p)">👤 أهلاً ${user.name.split(' ')[0]}</span>
            <span>|</span>
            ${user.role === 'admin' ? '<a href="admin.php" class="hdr-link-btn" style="color:#f59e0b;font-weight:700;">⚙️ الإدارة</a> <span>|</span>' : ''}
            <a href="#" onclick="logoutUser()" class="hdr-link-btn" style="color:#ef4444;">خروج</a>
        `;
    }
}

// Logout
async function logoutUser() {
    try {
        await fetch('api/logout.php');
        window.location.reload();
    } catch(err) {
        window.location.reload();
    }
}

// 4. Handle Google Login
const GOOGLE_CLIENT_ID = '244006724557-a845r5ljc7rutv6hht4m1kr537de3r42.apps.googleusercontent.com';

// Google Login code removed as per user request

// ----------------------------------------------------
// Toast Notification System
// ----------------------------------------------------
function showToast(msg, type = 'success') {
    let container = document.getElementById('toast-container');
    if(!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.style.cssText = `background:${type==='success'?'#10b981':'#ef4444'};color:#fff;padding:12px 24px;border-radius:8px;font-family:inherit;font-size:14px;font-weight:bold;box-shadow:0 4px 12px rgba(0,0,0,0.15);opacity:0;transform:translateY(20px);transition:all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);`;
    toast.textContent = msg;
    
    container.appendChild(toast);
    
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
