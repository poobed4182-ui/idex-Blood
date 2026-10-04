<?php
$requestedPage = isset($_GET['page']) ? basename((string) $_GET['page']) : 'index.html';
$allowedPages = array(
    'index.html',
    '1.ยุงลายไม่ได้กัดแค่ตอนกลางวัน.html',
    '2.ไข้หวัดธรรมดา หรือ ไข้เลือดออก.html',
    '3.ระยะวิกฤต ไข้ลดไม่ได้แปลว่าหาย.html',
    '4.ยาที่ห้ามกินเด็ดขาด.html',
    '5.จุดอับในบ้านที่ยุงลายชอบวางไข่.html'
);
if (!in_array($requestedPage, $allowedPages, true)) {
    http_response_code(404);
    echo 'ไม่พบหน้าที่ต้องการ';
    exit();
}

$pagePath = __DIR__ . '/' . $requestedPage;
$pageHtml = file_get_contents($pagePath);
if ($pageHtml === false) {
    http_response_code(500);
    echo 'ไม่สามารถเปิดหน้าเว็บไซต์ได้';
    exit();
}

ob_start();
require __DIR__ . '/menu.php';
$menuHtml = ob_get_clean();
$authScript = <<<'HTML'
<script type="module">
  import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/12.19.0/firebase-app.js";
  import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/12.19.0/firebase-auth.js";
  const firebaseConfig = {
    apiKey: "AIzaSyCqP4MyuuIu4Vr2cTsIhFaFv_8fj8zfryg",
    authDomain: "index-blood.firebaseapp.com",
    projectId: "index-blood",
    storageBucket: "index-blood.firebasestorage.app",
    messagingSenderId: "944055889621",
    appId: "1:944055889621:web:a0a3ffca69e178be5dbc5c",
    measurementId: "G-PXN7T8E0DY"
  };
  const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
  const auth = getAuth(app);
  const menu = document.getElementById("accountMenu");
  onAuthStateChanged(auth, (user) => {
    if (!user) {
      window.location.replace("login.php");
      return;
    }
    document.getElementById("accountName").textContent = user.displayName || "ผู้ใช้งาน";
    menu.hidden = false;
    menu.style.display = "flex";
  });
  document.getElementById("logoutButton").addEventListener("click", () => {
    window.location.assign("logout.php");
  });
</script>
HTML;

$bodyStart = stripos($pageHtml, '<body');
if ($bodyStart !== false) {
    $bodyEnd = strpos($pageHtml, '>', $bodyStart);
    if ($bodyEnd !== false) {
        $pageHtml = substr($pageHtml, 0, $bodyEnd + 1)
            . $menuHtml . $authScript
            . substr($pageHtml, $bodyEnd + 1);
    }
}

header('Content-Type: text/html; charset=utf-8');
echo $pageHtml;
?>
