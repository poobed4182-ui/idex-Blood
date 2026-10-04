<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>กำลังออกจากระบบ</title>
</head>
<body>
  <p id="status">กำลังออกจากระบบ…</p>
  <script type="module">
    import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/12.19.0/firebase-app.js";
    import { getAuth, signOut } from "https://www.gstatic.com/firebasejs/12.19.0/firebase-auth.js";
    const firebaseConfig = {
      apiKey: "AIzaSyCqP4MyuuIu4Vr2cTsIhFaFv_8fj8zfryg",
      authDomain: "index-blood.firebaseapp.com",
      projectId: "index-blood",
      storageBucket: "index-blood.firebasestorage.app",
      messagingSenderId: "944055889621",
      appId: "1:944055889621:web:a0a3ffca69e178be5dbc5c",
      measurementId: "G-PXN7T8E0DY"
    };
    try {
      const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
      await signOut(getAuth(app));
      window.location.replace("login.php");
    } catch (error) {
      console.error("Firebase sign-out error:", error);
      document.getElementById("status").textContent = "ออกจากระบบไม่สำเร็จ กรุณาลองใหม่";
    }
  </script>
</body>
</html>
