<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เข้าสู่ระบบ | เช็กอาการไข้เลือดออก</title>
  <style>
    * { box-sizing: border-box; }
    body { min-height:100vh; margin:0; padding:24px; display:flex; align-items:center; justify-content:center; background:#FF9F1C; color:#1a1a1a; font-family:'Sarabun','Prompt',sans-serif; }
    .card { width:100%; max-width:430px; padding:32px; background:#06D6A0; border:3px solid #1a1a1a; border-radius:24px; box-shadow:8px 8px 0 #1a1a1a; }
    h1 { margin:0 0 8px; text-align:center; font-size:28px; }
    .intro { margin:0 0 24px; text-align:center; line-height:1.6; }
    .field { margin-bottom:16px; }
    label { display:block; margin-bottom:7px; font-weight:700; }
    input { display:block; width:100%; min-height:46px; padding:10px 12px; border:2px solid #1a1a1a; border-radius:10px; background:#fff; color:#1a1a1a; font:inherit; }
    button { width:100%; min-height:48px; border:2px solid #1a1a1a; border-radius:12px; background:#EF476F; color:#fff; font:inherit; font-weight:700; cursor:pointer; box-shadow:3px 3px 0 #1a1a1a; }
    button:disabled { opacity:.65; cursor:wait; }
    .switch { margin:18px 0 0; text-align:center; }
    .switch button { width:auto; min-height:0; padding:0; border:0; background:none; color:#073B4C; box-shadow:none; text-decoration:underline; }
    .notice { display:none; margin:0 0 16px; padding:10px 12px; border:2px solid #1a1a1a; border-radius:10px; background:#fff; font-weight:700; }
    .notice.error { display:block; background:#FFE0E6; color:#8C1833; }
    .notice.success { display:block; background:#D8F3DC; color:#0F382C; }
    .note { margin:18px 0 0; font-size:13px; line-height:1.5; }
  </style>
</head>
<body>
  <main class="card">
    <h1 id="formTitle">เข้าสู่ระบบ</h1>
    <p class="intro" id="formIntro">ใช้ชื่อที่ตั้งไว้และรหัสนักศึกษา 11 หลัก</p>
    <div id="notice" class="notice" role="status" aria-live="polite"></div>
    <form id="accountForm">
      <div class="field">
        <label for="userName">ชื่อผู้ใช้</label>
        <input id="userName" name="userName" type="text" maxlength="100" autocomplete="username" required>
      </div>
      <div class="field">
        <label for="studentId">รหัสนักศึกษา 11 หลัก</label>
        <input id="studentId" name="studentId" type="password" inputmode="numeric" pattern="[0-9]{11}" minlength="11" maxlength="11" autocomplete="current-password" placeholder="ตัวเลข 11 หลัก" required>
      </div>
      <button id="submitButton" type="submit">เข้าสู่ระบบ</button>
    </form>
    <p class="switch"><span id="switchText">ยังไม่มีบัญชี?</span> <button id="switchMode" type="button">สร้างบัญชีใหม่</button></p>
    <p class="note">บัญชีจะบันทึกใน Firebase โดยไม่ตรวจสอบกับ phpMyAdmin รหัสนักศึกษาที่มีบัญชีแล้วจะสมัครซ้ำไม่ได้</p>
  </main>
  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.19.0/firebase-app.js";
    import { getAuth, createUserWithEmailAndPassword, signInWithEmailAndPassword, updateProfile, signOut, deleteUser } from "https://www.gstatic.com/firebasejs/12.19.0/firebase-auth.js";

    const firebaseConfig = {
      apiKey: "AIzaSyCqP4MyuuIu4Vr2cTsIhFaFv_8fj8zfryg",
      authDomain: "index-blood.firebaseapp.com",
      projectId: "index-blood",
      storageBucket: "index-blood.firebasestorage.app",
      messagingSenderId: "944055889621",
      appId: "1:944055889621:web:a0a3ffca69e178be5dbc5c",
      measurementId: "G-PXN7T8E0DY"
    };
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const form = document.getElementById("accountForm");
    const userNameInput = document.getElementById("userName");
    const studentIdInput = document.getElementById("studentId");
    const submitButton = document.getElementById("submitButton");
    const notice = document.getElementById("notice");
    const switchMode = document.getElementById("switchMode");
    let isRegisterMode = false;

    // ใช้อีเมลภายในที่สร้างจากรหัสนักศึกษา เพื่อให้ Firebase บังคับความไม่ซ้ำของรหัส
    const emailForStudentId = (id) => `student${id}@index-blood.firebaseapp.com`;
    const showNotice = (message, type = "error") => {
      notice.textContent = message;
      notice.className = `notice ${type}`;
    };

    switchMode.addEventListener("click", () => {
      isRegisterMode = !isRegisterMode;
      document.getElementById("formTitle").textContent = isRegisterMode ? "สร้างบัญชีใหม่" : "เข้าสู่ระบบ";
      document.getElementById("formIntro").textContent = isRegisterMode ? "ตั้งชื่อผู้ใช้และใช้รหัสนักศึกษา 11 หลักเป็นรหัสผ่าน" : "ใช้ชื่อที่ตั้งไว้และรหัสนักศึกษา 11 หลัก";
      document.getElementById("switchText").textContent = isRegisterMode ? "มีบัญชีแล้ว?" : "ยังไม่มีบัญชี?";
      switchMode.textContent = isRegisterMode ? "กลับไปเข้าสู่ระบบ" : "สร้างบัญชีใหม่";
      submitButton.textContent = isRegisterMode ? "สมัครบัญชี" : "เข้าสู่ระบบ";
      notice.className = "notice";
      notice.textContent = "";
    });

    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      const userName = userNameInput.value.trim();
      const studentId = studentIdInput.value.trim();
      if (!userName || !/^\d{11}$/.test(studentId)) {
        showNotice("กรุณาระบุชื่อผู้ใช้และรหัสนักศึกษาให้ครบ 11 หลัก");
        return;
      }

      submitButton.disabled = true;
      try {
        const email = emailForStudentId(studentId);
        if (isRegisterMode) {
          const credential = await createUserWithEmailAndPassword(auth, email, studentId);
          try {
            await updateProfile(credential.user, { displayName: userName });
          } catch (profileError) {
            await deleteUser(credential.user);
            throw profileError;
          }
          showNotice("สมัครบัญชีสำเร็จ กำลังเข้าสู่ระบบ…", "success");
          window.setTimeout(() => window.location.replace("index.html"), 700);
          return;
        }

        const credential = await signInWithEmailAndPassword(auth, email, studentId);
        if ((credential.user.displayName || "").trim() !== userName) {
          await signOut(auth);
          throw new Error("ชื่อผู้ใช้หรือรหัสนักศึกษาไม่ถูกต้อง");
        }
        window.location.replace("index.html");
      } catch (error) {
        console.error("Firebase account error:", error);
        if (isRegisterMode && ["auth/email-already-in-use", "auth/credential-already-in-use"].includes(error.code)) {
          showNotice("รหัสซ้ำ: สมัครไม่สำเร็จ บัญชีนี้มีผู้ใช้แล้ว");
        } else if (error.code === "auth/operation-not-allowed") {
          showNotice("ยังไม่ได้เปิดใช้งาน Email/Password ใน Firebase Authentication");
        } else if (error.code === "auth/network-request-failed") {
          showNotice("เชื่อมต่อ Firebase ไม่สำเร็จ กรุณาตรวจสอบอินเทอร์เน็ต");
        } else if (error.code === "auth/unauthorized-domain") {
          showNotice("โดเมนที่เปิดเว็บยังไม่ได้รับอนุญาต: เพิ่มโดเมนนี้ใน Firebase Authentication → Settings → Authorized domains");
        } else if (error.code === "auth/invalid-email") {
          showNotice("Firebase ปฏิเสธอีเมลบัญชีภายใน กรุณาตรวจสอบการตั้งค่า Firebase Authentication");
        } else if (error.code === "auth/too-many-requests") {
          showNotice("มีการสมัครหลายครั้งเกินไป กรุณารอสักครู่แล้วลองใหม่");
        } else if (["auth/invalid-api-key", "auth/app-not-authorized"].includes(error.code)) {
          showNotice("การตั้งค่า Firebase สำหรับเว็บไซต์นี้ไม่ถูกต้อง กรุณาตรวจ Firebase Web App และ API key");
        } else if (isRegisterMode && error.code === "auth/weak-password") {
          showNotice("Firebase ปฏิเสธรหัสผ่านนี้ตามนโยบายความปลอดภัยของโปรเจกต์");
        } else if (!isRegisterMode || error.code === "auth/weak-password") {
          showNotice("เข้าสู่ระบบไม่สำเร็จ กรุณาตรวจสอบชื่อผู้ใช้และรหัสนักศึกษา");
        } else {
          showNotice(`สร้างบัญชีไม่สำเร็จ กรุณาส่งรหัสข้อผิดพลาดนี้ให้ผู้ดูแล: ${error.code || error.message || "ไม่ทราบรหัสข้อผิดพลาด"}`);
        }
      } finally {
        submitButton.disabled = false;
      }
    });
  </script>
</body>
</html>
