/**
 * Authentication and Session Management for idex-Blood
 * Supports Firebase Authentication with resilient LocalSession fallback
 */

const FIREBASE_CONFIG = {
  apiKey: "AIzaSyCqP4MyuuIu4Vr2cTsIhFaFv_8fj8zfryg",
  authDomain: "index-blood.firebaseapp.com",
  projectId: "index-blood",
  storageBucket: "index-blood.firebasestorage.app",
  messagingSenderId: "944055889621",
  appId: "1:944055889621:web:a0a3ffca69e178be5dbc5c",
  measurementId: "G-PXN7T8E0DY"
};

const SESSION_KEY = "idex_blood_user_session";

// Helpers for localStorage session
export function getLocalUser() {
  try {
    const raw = localStorage.getItem(SESSION_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
}

export function setLocalUser(user) {
  if (user) {
    localStorage.setItem(SESSION_KEY, JSON.stringify(user));
  } else {
    localStorage.removeItem(SESSION_KEY);
  }
}

// Convert 11-digit student ID to internal email format
export function emailForStudentId(studentId) {
  return `student${studentId}@index-blood.firebaseapp.com`;
}

// Initialize Auth listeners and render the top navbar on any page
export async function setupUserNavbar() {
  const container = document.getElementById("accountMenu");
  if (!container) return;

  const localUser = getLocalUser();

  // Try Firebase Auth if available
  let firebaseUser = null;
  try {
    const { initializeApp, getApps, getApp } = await import("https://www.gstatic.com/firebasejs/12.19.0/firebase-app.js");
    const { getAuth, onAuthStateChanged } = await import("https://www.gstatic.com/firebasejs/12.19.0/firebase-auth.js");

    const app = getApps().length ? getApp() : initializeApp(FIREBASE_CONFIG);
    const auth = getAuth(app);

    onAuthStateChanged(auth, (user) => {
      if (user) {
        const studentMatch = user.email ? user.email.match(/student(\d{11})@/) : null;
        const studentId = studentMatch ? studentMatch[1] : (localUser?.studentId || "ผู้ใช้ภายนอก");
        renderNavbar(container, {
          displayName: user.displayName || localUser?.displayName || "ผู้ใช้งาน",
          studentId: studentId
        }, true);
      } else if (localUser) {
        renderNavbar(container, localUser, false);
      } else {
        renderNavbarGuest(container);
      }
    });
  } catch (err) {
    // Firebase couldn't load, use local session
    if (localUser) {
      renderNavbar(container, localUser, false);
    } else {
      renderNavbarGuest(container);
    }
  }
}

function renderNavbar(container, user, isFirebase) {
  container.innerHTML = `
    <div class="brand-badge">
      <span class="blood-icon">🩸</span>
      <span><strong>idex-Blood</strong> | เช็กอาการไข้เลือดออก</span>
    </div>
    <div class="user-status">
      <span>👤 สวัสดีคุณ <strong>${escapeHtml(user.displayName)}</strong></span>
      <span class="user-tag">รหัส: ${escapeHtml(user.studentId || "นักศึกษา")}</span>
      <a href="index.html" class="btn-nav" title="กลับหน้าแรก">🏠 หน้าแรก</a>
      <button id="logoutButton" type="button" class="btn-nav btn-danger">ออกจากระบบ</button>
    </div>
  `;
  container.hidden = false;
  container.style.display = "flex";

  const logoutBtn = document.getElementById("logoutButton");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", () => {
      logoutUser();
    });
  }
}

function renderNavbarGuest(container) {
  container.innerHTML = `
    <div class="brand-badge">
      <span class="blood-icon">🩸</span>
      <span><strong>idex-Blood</strong> | เช็กอาการไข้เลือดออก</span>
    </div>
    <div class="user-status">
      <a href="index.html" class="btn-nav">🏠 หน้าแรก</a>
      <a href="login.html" class="btn-nav" style="background: #06D6A0;">🔑 เข้าสู่ระบบ / สมัครสมาชิก</a>
    </div>
  `;
  container.hidden = false;
  container.style.display = "flex";
}

export async function logoutUser() {
  setLocalUser(null);
  try {
    const { initializeApp, getApps, getApp } = await import("https://www.gstatic.com/firebasejs/12.19.0/firebase-app.js");
    const { getAuth, signOut } = await import("https://www.gstatic.com/firebasejs/12.19.0/firebase-auth.js");
    const app = getApps().length ? getApp() : initializeApp(FIREBASE_CONFIG);
    await signOut(getAuth(app));
  } catch (e) {
    // ignore
  }
  window.location.replace("login.html");
}

function escapeHtml(str) {
  if (!str) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
