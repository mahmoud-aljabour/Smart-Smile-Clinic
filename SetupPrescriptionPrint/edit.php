<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

// Fetch header and footer data from tplprintprescriptions
$sql = "SELECT * FROM `tplprintprescriptions` LIMIT 1";
$mydb->setQuery($sql);
$res = $mydb->loadSingleResult();
?>
<form class="form-horizontal span6" action="controller.php?action=edit" method="POST">
  <div class="row">
    <div class="col-lg-12">
      <h1 class="page-header">Setup Prescription Print</h1>
    </div>
    <!-- /.col-lg-12 -->
  </div>
  <h3>Header</h3>
  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Suplier">First Line:</label>
      <div class="col-md-8">
        <textarea class="form-control input-sm" id="HFirstLine" name="HFirstLine"><?php echo isset($res->header1) ? $res->header1 : ""; ?></textarea>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Suplier">Second Line:</label>
      <div class="col-md-8">
        <textarea class="form-control input-sm" id="HSecondLine" name="HSecondLine"><?php echo isset($res->header2) ? $res->header2 : ""; ?></textarea>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Suplier">Third Line:</label>
      <div class="col-md-8">
        <textarea class="form-control input-sm" id="HThirdLine" name="HThirdLine"><?php echo isset($res->header3) ? $res->header3 : ""; ?></textarea>
      </div>
    </div>
  </div>
  <hr />
  <h3>Footer</h3>
  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Suplier">First Line:</label>
      <div class="col-md-8">
        <textarea class="form-control input-sm" id="FFirstLine" name="FFirstLine"><?php echo isset($res->footer1) ? $res->footer1 : ""; ?></textarea>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Suplier">Second Line:</label>
      <div class="col-md-8">
        <textarea class="form-control input-sm" id="FSecondLine" name="FSecondLine"><?php echo isset($res->footer2) ? $res->footer2 : ""; ?></textarea>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Suplier">Third Line:</label>
      <div class="col-md-8">
        <textarea class="form-control input-sm" id="FThirdLine" name="FThirdLine"><?php echo isset($res->footer3) ? $res->footer3 : ""; ?></textarea>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="idno"></label>
      <div class="col-md-8">
        <button class="btn btn-primary btn-md" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript">
  var map = null;
  var directionsDisplay = null;
  var directionsService = null;
  function initialize() {
    var myLatlng = new google.maps.LatLng(10.640739, 122.968956);
    var myOptions = {
      zoom: 7,
      center: {
        lat: 10.640739,
        lng: 122.968956
      },
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map($("#map_canvas").get(0), myOptions);
    directionsDisplay = new google.maps.DirectionsRenderer();
    directionsService = new google.maps.DirectionsService();
    var input = document.getElementById('StoreAddress');
    var searchBox = new google.maps.places.SearchBox(input);
  }
  $(document).ready(function() {
    initialize();
  });
</script>
<div id="results" style="width: 990px; height: 500px; display: none;">
  <div id="map_canvas" style="width: 80%; height: 100%; float: left;"></div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTanm_xZQi4_RHeCAxerOqXN96NUwrbZU&libraries=places"></script>
<script type="text/javascript">
  $("#StoreAddress").on("keyup", function() {
    var geocoder = new google.maps.Geocoder();
    var address = $(this).val();
    if (address == '') {
      $("#lat").val('');
      $("#lng").val('');
    } else {
      geocoder.geocode({
        'address': address
      }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          var latitude = results[0].geometry.location.lat();
          var longitude = results[0].geometry.location.lng();
          $("#lat").val(latitude);
          $("#lng").val(longitude);
        }
      });
    }
  });
</script>