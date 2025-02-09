<?php
// grow_space_form.php
// This modular form is used for both adding and editing a Grow Space.
// The including page should set $formMode to 'edit_space' for editing,
// or leave it as 'default' (or 'add') for adding a new grow space.
if (!isset($formMode)) {
    $formMode = 'default'; // default means "add" mode.
}
?>
<div class="card">
  <div class="card-body">
    <h5 class="mb-3"><?= ($formMode == 'edit_space' ? "Edit Grow Space" : "Add New Grow Space") ?></h5>
    <!--
         When in edit mode, we add the space_id as a GET parameter so that when the form is submitted it will
         trigger the UPDATE branch in the processing code.
         Also, we include a hidden field with name "update_space" (for edit) or "add_space" (for add).
    -->
    <form id="addGrowSpaceForm" method="post" action="edit_space.php<?= ($formMode == 'edit_space' && isset($space['id']) ? "?space_id=" . urlencode($space['id']) : "") ?>" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="space_name" class="form-label">Grow Space Name</label>
        <input type="text" id="space_name" name="space_name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="notes_space" class="form-label">Notes</label>
        <textarea id="notes_space" name="notes" class="form-control" rows="3"></textarea>
      </div>
      <div class="mb-3">
        <label for="environment_type" class="form-label">Environment Type</label>
        <select id="environment_type" name="environment_type" class="form-select">
          <option value="">-- Select Environment --</option>
          <option value="Indoor">Indoor</option>
          <option value="Outdoor">Outdoor</option>
          <option value="Greenhouse">Greenhouse</option>
          <option value="Light Deprivation">Light Deprivation</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Length (Feet / Inches)</label>
          <div class="input-group">
            <input type="number" name="size_length_feet" class="form-control" placeholder="Feet">
            <input type="number" name="size_length_inches" class="form-control" placeholder="Inches">
          </div>
        </div>
        <div class="col">
          <label class="form-label">Width (Feet / Inches)</label>
          <div class="input-group">
            <input type="number" name="size_width_feet" class="form-control" placeholder="Feet">
            <input type="number" name="size_width_inches" class="form-control" placeholder="Inches">
          </div>
        </div>
        <div class="col">
          <label class="form-label">Height (Feet / Inches)</label>
          <div class="input-group">
            <input type="number" name="size_height_feet" class="form-control" placeholder="Feet">
            <input type="number" name="size_height_inches" class="form-control" placeholder="Inches">
          </div>
        </div>
      </div>
      <div class="mb-3">
        <label for="photo_space" class="form-label">Grow Space Photo</label>
        <input type="file" id="photo_space" name="photo" class="form-control">
      </div>
      <!-- Lights Section -->
      <div class="mb-3">
        <label class="form-label">Lights (Optional)</label>
        <div id="lights-container">
          <!-- Hidden template for a new light -->
          <div id="light-template" class="light-item border p-2 mb-2" style="display:none;">
            <div class="row">
              <div class="col">
                <label class="form-label">Light Type</label>
                <select class="form-select" name="light_type[]">
                  <option value="">-- Select Light Type --</option>
                  <option value="LED">LED</option>
                  <option value="Compact Fluorescent (CFL)">Compact Fluorescent (CFL)</option>
                  <option value="High Pressure Sodium (HPS)">High Pressure Sodium (HPS)</option>
                  <option value="Metal Halide">Metal Halide</option>
                  <option value="LEC">LEC</option>
                  <option value="T5">T5</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="col">
                <label class="form-label">Light Location</label>
                <select class="form-select" name="light_location[]">
                  <option value="">-- Select Location --</option>
                  <option value="Overhead">Overhead</option>
                  <option value="Side (Supplemental)">Side (Supplemental)</option>
                  <option value="Under Canopy (Supplemental)">Under Canopy (Supplemental)</option>
                </select>
              </div>
              <div class="col">
                <label class="form-label">Brand</label>
                <input type="text" class="form-control" name="brand[]" placeholder="Brand">
              </div>
              <div class="col">
                <label class="form-label">Model</label>
                <input type="text" class="form-control" name="model[]" placeholder="Model">
              </div>
              <div class="col">
                <label class="form-label">Wattage</label>
                <input type="text" class="form-control" name="wattage[]" placeholder="Wattage">
              </div>
              <div class="col-auto d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-light">Remove</button>
              </div>
            </div>
          </div>
        </div>
        <button type="button" class="btn btn-secondary" id="add-light">Add Light</button>
      </div>
      <!-- End Lights Section -->
      <div class="mb-3">
        <?php if ($formMode == 'edit_space'): ?>
          <button type="submit" class="btn btn-primary">Save Grow Space</button>
        <?php else: ?>
          <button type="submit" class="btn btn-primary">Save Grow Space</button>
          <button type="button" id="cancelAddGrowSpace" class="btn btn-secondary">Cancel</button>
        <?php endif; ?>
        <?php
          // In edit mode, include a hidden field with the space ID and a marker.
          if ($formMode == 'edit_space' && isset($space['id'])) {
              echo '<input type="hidden" name="space_id" value="' . htmlspecialchars($space['id']) . '">';
              echo '<input type="hidden" name="update_space" value="1">';
          } else {
              echo '<input type="hidden" name="add_space" value="1">';
          }
        ?>
      </div>
    </form>
    <div id="addGrowSpaceError" class="alert alert-danger" style="display:none;"></div>
  </div>
</div>
