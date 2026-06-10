<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pearl Dental Clinic</title>

    <!-- خط Titillium Web للنصوص الإنجليزية -->
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@700&display=swap" rel="stylesheet">

    <!-- خط Cairo للنص العربي -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            position: relative;
        }

        .container {
            position: relative;
            width: 100%;
        }

        /* شعار أعلى يسار الصفحة */
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 150px;
            height: auto;
            z-index: 10;
            opacity: 0;
            transform: scale(0.8);
            transition: all 1s ease;
        }

            .logo.animate {
                opacity: 1;
                transform: scale(1);
            }

        /* زر تسجيل الدخول */
        .login-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #4674b7; /* نفس درجة اللون الأزرق */
            color: #fff;
            font-family: 'Titillium Web', sans-serif;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s ease;
            z-index: 10;
        }

            .login-btn:hover {
                background-color: #365d94; /* أغمق قليلاً عند التمرير */
            }

        .bg {
            width: 100%;
            height: auto;
            display: block;
        }

        .text-box {
            position: absolute;
            top: 20%;
            left: 6%;
            text-align: left;
        }

        .welcome, .clinic, .tagline {
            font-weight: 800;
            margin: 0 0 15px 0;
            opacity: 0;
            transform: translateY(50px);
            transition: all 1s ease;
            margin-bottom: 5px;
        }

        .welcome {
            font-family: 'Titillium Web', sans-serif;
            font-size: 100px;
            color: #2c394c;
        }

        .clinic {
            font-family: 'Titillium Web', sans-serif;
            font-size: 66px;
            line-height: 1.0;
            color: #2c394c;
        }

            .clinic .pearl {
                color: #4674b7;
            }

            .clinic .dental {
                color: #5ca2ba;
            }

        .tagline {
            font-family: 'Cairo', sans-serif;
            font-size: 50px;
            color: #5ca2b5;
            margin-top: 80px;
            line-height: 1.5;
            text-align: center;
            position: relative;
            left: -40px;
        }

        /* العنصر السفلي في المنتصف */
        .bottom-center {
            position: absolute;
            bottom: 70px;
            left: 50%;
            transform: translateX(-50%) translateY(50px);
            width: 370px;
            height: auto;
            z-index: 5;
            opacity: 0;
            transition: all 1s ease;
        }

            .bottom-center.animate {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

        .animate {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- شعار أعلى يسار الصفحة -->
        <img src="logo.png" alt="Logo" class="logo">

        <!-- زر تسجيل الدخول -->
        <a href="login.html" class="login-btn">Log in</a>

        <!-- الخلفية -->
        <img src="wep page null logo01.png" alt="Pearl Dental Clinic" class="bg">

        <!-- النصوص -->
        <div class="text-box">
            <div class="welcome">Welcome to</div>
            <div class="clinic">
                <span class="pearl">Pearl</span> <span class="dental">Dental Clinic</span>
            </div>
            <div class="tagline">
                ابتسامتك تبدأ<br>من هنا
            </div>
        </div>

        <!-- عنصر مضاف أسفل الصفحة -->
        <img src="dan1.png" alt="Element" class="bottom-center">
    </div>

    <script>
        window.addEventListener('load', () => {
            const logo = document.querySelector('.logo');
            const bottomElement = document.querySelector('.bottom-center');
            const welcome = document.querySelector('.welcome');
            const clinic = document.querySelector('.clinic');
            const tagline = document.querySelector('.tagline');

            // حركة الشعار والعنصر السفلي معًا
            setTimeout(() => {
                logo.classList.add('animate');
                bottomElement.classList.add('animate');
            }, 200);

            // حركة النصوص
            setTimeout(() => { welcome.classList.add('animate'); }, 300);
            setTimeout(() => { clinic.classList.add('animate'); }, 800);
            setTimeout(() => { tagline.classList.add('animate'); }, 1300);
        });
    </script>
</body>
</html>