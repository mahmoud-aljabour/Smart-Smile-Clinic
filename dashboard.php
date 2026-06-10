<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "admin/index.php");
}

?>

<script src="<?php echo web_root; ?>dist/js/jquery-1.11.1.min.js"></script>
<style type="text/css">
  /* تحsين الكروت بتصميم حديث */
  .card {
    border: 1px solid #bbdefb;
    /* حد فاتح زي الجدول */
    border-radius: 12px;
    background: linear-gradient(135deg, #e3f2fd, #f0f7ff);
    /* gradient خفيف */
    margin-bottom: 30px;
    transition: transform 0.2s ease, background 0.2s ease;
    position: relative;
    padding: 15px;
    /* تباعد داخلي */
  }

  /* تأثير hover خفيف */
  .card:hover {
    transform: translateY(-3px);
    background: linear-gradient(135deg, #d9ecff, #e6f3ff);
    /* gradient أفتح */
  }

  /* توسيط محتوى الكرت */
  .card-header {
    text-align: center;
    padding: 10px 15px;
    position: relative;
    background: transparent;
  }

  /* تحsين الأيقونات بأسلوب حديث */
  .card-icon {
    border-radius: 50%;
    width: 70px;
    height: 70px;
    line-height: 70px;
    text-align: center;
    font-size: 34px;
    margin: 0 auto 15px;
    transition: transform 0.2s ease, background 0.2s ease;
    border: 2px solid rgba(255, 255, 255, 0.3);
    /* لمسة إضافية */
  }

  .card-icon:hover {
    transform: scale(1.05);
  }

  /* تحsين الألوان لتتماشى مع الجدول */
  .card-header-warning .card-icon {
    background: #ffca28;
    /* أصفر زي الـdashboard */
    color: #fff;
  }

  .card-header-success .card-icon {
    background: #4caf50;
    /* أخضر */
    color: #fff;
  }

  .card-header-danger .card-icon {
    background: #d32f2f;
    /* أحمر زي Delete */
    color: #fff;
  }

  .card-header-info .card-icon {
    background: #2196f3;
    /* أزرق زي View Records */
    color: #fff;
  }

  /* توسيط وتحsين النصوص */
  .card-category {
    font-size: 16px;
    color: #333;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 10px;
    letter-spacing: 1px;
    text-align: center;
  }

  .card-title {
    font-size: 28px;
    font-weight: bold;
    color: #000;
    margin: 0;
    text-align: center;
  }

  /* تحsين الفوتر وتوسيط الرابط */
  .card-footer {
    background: transparent;
    border-top: none;
    padding: 10px;
    text-align: center;
    position: relative;
    z-index: 10;
  }

  .card-footer .stats a {
    color: #00c853;
    /* أخضر فاتح زي View All */
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    transition: color 0.2s ease, transform 0.2s ease;
    display: inline-block;
    text-decoration: none;
  }

  .card-footer .stats a:hover {
    color: #00b248;
    text-decoration: none;
    transform: scale(1.05);
  }

  /* تأثير خفيف على الكرت عند الـhover */
  .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
    opacity: 0;
    transition: opacity 0.2s ease;
    z-index: 1;
  }

  .card:hover::before {
    opacity: 1;
  }

  /* تحsين الـResponsive */
  @media (max-width: 767px) {
    .card {
      margin-bottom: 20px;
    }

    .card-icon {
      width: 60px;
      height: 60px;
      line-height: 60px;
      font-size: 28px;
    }

    .card-title {
      font-size: 24px;
    }

    .card-category {
      font-size: 14px;
    }
  }

  /* تحsين الـchart container لو كنت بتستخدمه */
  .card-body .chartContainer {
    width: 100%;
    height: 200px;
    margin: 0 auto;
  }

  auto;
  }
</style>
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-header card-header-warning card-header-icon">
        <div class="card-icon">
          <i class="fa fa-users"></i>
        </div>
        <p class="card-category">Patient</p>
        <h3 class="card-title">
          <?php
          $sql = "Select Count(*) as allmemeber From tblpatients";
          @$mydb->setQuery($sql);
          $p = @$mydb->loadSingleResult();
          echo @$p->allmemeber;
          ?>
        </h3>
      </div>
      <div class="card-footer">
        <div class="stats">
          <a href="<?php echo web_root; ?>patients/index.php">
            <i class="fa fa-info"> </i> View All</a>
        </div>
      </div>
    </div>
  </div>
  <?php if ($_SESSION['ADMIN_ROLE'] == "Administrator") { ?>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card card-stats">
        <div class="card-header card-header-success card-header-icon">
          <div class="card-icon">
            <i class="fa fa-tree"></i>
          </div>
          <p class="card-category">Services <br /></p>
          <h3 class="card-title">
            <?php
            $sql = "Select Count(*) as allmemeber From tblservices";
            @$mydb->setQuery($sql);
            $p = @$mydb->loadSingleResult();
            echo @$p->allmemeber;
            ?>
          </h3>
        </div>
        <div class="card-footer">
          <div class="stats">
            <a href="<?php echo web_root; ?>services/index.php">
              <i class="fa fa-info"> </i> View All</a>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  <div class="col-lg-3 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-header card-header-danger card-header-icon">
        <div class="card-icon">
          <i class="fa fa-dollar"></i>
        </div>
        <p class="card-category">Invoices</p>
        <h3 class="card-title">
          <?php
          $sql = "Select Count(*) as allmemeber From tblinvoice";
          @$mydb->setQuery($sql);
          $p = @$mydb->loadSingleResult();
          echo @$p->allmemeber;
          ?>
        </h3>
      </div>
      <div class="card-footer">
        <div class="stats">
          <a href="<?php echo web_root; ?>invoices/">
            <i class="fa fa-info"> </i> View All</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-header card-header-info card-header-icon">
        <div class="card-icon">
          <i class="fa fa-calendar"></i>
        </div>
        <p class="card-category">Appointments</p>
        <h3 class="card-title">
          <?php
          // تأكد من أن ملف initialize.php مضمن في مكان ما قبل هذا الكود
          // لضمان تعريف الكائن $mydb

          // 1. تحديد التاريخ الحالي للخادم
          // نستخدم دالة CURDATE() من SQL، وهي الأفضل للبيانات المخزنة في قاعدة البيانات.

          $sql = "SELECT COUNT(*) AS today_appointments 
        FROM tblappointments 
        WHERE A_Date = CURDATE()";

          // 2. تعيين وتنفيذ الاستعلام
          $mydb->setQuery($sql);
          $p = $mydb->loadSingleResult();

          // 3. عرض النتيجة
          // (إذا لم يكن هناك حجوزات، ستكون النتيجة 0)
          echo $p->today_appointments;
          ?>
        </h3>
      </div>
      <div class="card-footer">
        <div class="stats">
          <a href="<?php echo web_root; ?>appointments/">
            <i class="fa fa-info"> </i> View All</a>
        </div>
      </div>
    </div>
  </div>
</div>
<hr />
<!-- <div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-header card-header-warning card-header-icon">
        <div class="card-icon">
          <i class="fa fa-barcode"></i>
        </div>
        <p class="card-category">Stocks</p>
        <h3 class="card-title">
          <?php
          $sql = "Select Count(*) as allmemeber From tblstocks GROUP BY ProductID";
          @$mydb->setQuery($sql);
          $p = @$mydb->loadSingleResult();
          echo @$p->allmemeber;
          ?>
        </h3>
      </div>
      <div class="card-footer">
        <div class="stats">
          <a href="<?php echo web_root; ?>stocks/index.php">
            <i class="fa fa-info"> </i> View All</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-header card-header-success card-header-icon">
        <div class="card-icon">
          <i class="fa fa-barcode"></i>
        </div>
        <p class="card-category">Sold <br /></p>
        <h3 class="card-title">
          <?php
          $sql = "Select Count(*) as allmemeber From tblstocks WHERE Sold=1 GROUP BY ProductID";
          @$mydb->setQuery($sql);
          $p = @$mydb->loadSingleResult();
          echo @$p->allmemeber;
          ?>
        </h3>
      </div>
      <div class="card-footer">
        <div class="stats">
          <a href="<?php echo web_root; ?>stockout/index.php">
            <i class="fa fa-info"> </i> View All</a>
        </div>
      </div>
    </div>
  </div> -->
<?php if ($_SESSION['ADMIN_ROLE'] == "Administrator") { ?>


  <div class="col-lg-3 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-header card-header-info card-header-icon">
        <div class="card-icon">
          <i class="fa fa-users"></i>
        </div>
        <p class="card-category">Manage Users</p>
        <h3 class="card-title">
          <?php
          $sql = "Select Count(*) as allmemeber From tblusers";
          @$mydb->setQuery($sql);
          $p = @$mydb->loadSingleResult();
          echo @$p->allmemeber;
          ?>
        </h3>
      </div>
      <div class="card-footer">
        <div class="stats">
          <a href="<?php echo web_root; ?>user/">
            <i class="fa fa-info"> </i> View All</a>
        </div>
      </div>
    </div>
  <?php } ?>
  </div>
  </div>

  <!--   <div class="search pull-right"> 
   <label>Search for:</label>
    <div ><input class="form-control" id="findProducts" placeholder="Services...." autocomplete="off" /></div> 
</div>  -->
  <!------ Include the above in your tag ---------->