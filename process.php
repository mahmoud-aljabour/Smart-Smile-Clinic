<?php
require_once("include/initialize.php");
// إذا session_start مش موجود في initialize.php، أضفه هنا:
// session_start();

if (isset($_POST['btnLogin'])) {
  $email = trim($_POST['user_email']);
  $upass = trim($_POST['user_pass']);
  
  // حماية إضافية: هرب الإدخال من XSS (غير ضروري للـ DB، بس جيد للعرض)
  $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
  
  if ($email == '' || $upass == '') {
    message("Invalid Username and Password!", "error");
    redirect("login.php");
  } else {
    // هاش كلمة المرور: غير SHA1 لـ password_hash للأمان الأفضل (يتطلب تحديث DB)
    // مثال للتحسين: $h_upass = password_hash($upass, PASSWORD_DEFAULT);
    // ثم في userAuthentication: if (password_verify($h_upass, $stored_hash)) { ... }
    $h_upass = sha1($upass);  // مؤقتاً، غيره لاحقاً

    // إنشاء كائن User
    $user = new User();
    // التحقق من الدخول (مع prepared statements في accounts.php)
    $res = $user->userAuthentication($email, $h_upass);
    
    if ($res == true) {
      // رسالة نجاح (يمكن إزالتها لو مش تبي)
      // message("You logon as " . $_SESSION['ADMIN_ROLE'] . ".", "success"); 
      redirect(web_root . "index.php");
    } else {
      message("Account does not exist!", "error");
      redirect(web_root . "login.php");  // إصلاح: كان web_root مكرر
    }
  }
}
?>