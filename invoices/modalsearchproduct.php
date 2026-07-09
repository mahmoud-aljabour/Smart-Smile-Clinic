<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "admin/index.php");
}

$currency = $setDefault->default_currency();
$ageGroupLabel = isset($_SESSION['PatientAgeGroupLabel']) ? $_SESSION['PatientAgeGroupLabel'] : '';
$maxTeeth = isset($_SESSION['PatientAgeGroupMaxTeeth']) ? (int)$_SESSION['PatientAgeGroupMaxTeeth'] : 0;
$ageGroupId = isset($_SESSION['PatientAgeGroupID']) ? $_SESSION['PatientAgeGroupID'] : null;

$services = fetch_filtered_invoice_services(array(
  'age_group_id' => $ageGroupId,
  'scope' => 'all',
  'tooth' => 0
));
$addedSkus = get_invoice_cart_skus($invno);
$rowsHtml = render_invoice_service_picker_rows($services, $addedSkus, $currency);
?>

<div class="modal fade service-picker-modal" id="modalproducts" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="servicePickerLabel" data-invno="<?php echo htmlspecialchars($invno); ?>" data-max-teeth="<?php echo (int)$maxTeeth; ?>">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="servicePickerLabel">Browse Services</h5>
          <p class="text-muted small mb-0">Filtered by patient age group and tooth scope</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="servicePickerForm" method="POST" action="actionCart.php?action=add">
        <input type="hidden" name="invno" id="invno" value="<?php echo htmlspecialchars($invno); ?>">

        <div id="modal-body" class="modal-body">
          <?php if ($ageGroupLabel): ?>
            <div class="info-badge mb-3 service-picker-age-badge">
              <i class="bi bi-funnel"></i>
              <span>
                Patient age group:
                <strong><?php echo htmlspecialchars($ageGroupLabel); ?></strong>
                <?php if ($maxTeeth > 0): ?>
                  <span class="text-muted">(<?php echo $maxTeeth; ?> teeth)</span>
                <?php endif; ?>
              </span>
            </div>
          <?php else: ?>
            <div class="info-badge mb-3 service-picker-age-badge d-none"></div>
          <?php endif; ?>

          <div class="service-picker-filters mb-3">
            <div class="row g-2 align-items-end">
              <div class="col-md-5">
                <label class="form-label" for="serviceScope">Show</label>
                <select class="form-select" id="serviceScope">
                  <option value="all" selected>All services for this age group</option>
                  <option value="general">General only (all teeth)</option>
                  <option value="tooth">Services for a specific tooth</option>
                </select>
              </div>
              <div class="col-md-3" id="serviceToothWrap" style="display:none;">
                <label class="form-label" for="serviceTooth">Tooth #</label>
                <input type="number" class="form-control" id="serviceTooth" min="1" max="<?php echo max(1, $maxTeeth); ?>" placeholder="e.g. 11">
              </div>
              <div class="col-md-4">
                <label class="form-label" for="findProducts">Search</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-search"></i></span>
                  <input class="form-control" id="findProducts" placeholder="Name or description..." autocomplete="off">
                </div>
              </div>
            </div>
          </div>

          <div class="service-picker-table-wrap">
            <table id="tree_table" class="table table-modern table-hover table-bordered mb-0">
              <thead>
                <tr>
                  <th width="44" class="text-center">
                    <div class="form-check mb-0 d-flex justify-content-center">
                      <input class="form-check-input" type="checkbox" id="selectAllServices" title="Select all">
                    </div>
                  </th>
                  <th>Service</th>
                  <th>Description</th>
                  <th>Tooth</th>
                  <th class="text-end">Price (<?php echo htmlspecialchars($currency); ?>)</th>
                </tr>
              </thead>
              <tbody id="loaddashboard">
                <?php echo $rowsHtml; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">
            <i class="bi bi-x-lg"></i> Close
          </button>
          <button class="btn btn-primary" name="save" type="submit">
            <i class="bi bi-plus-circle"></i> Add to Invoice
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
