<?php
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }

    $theme_slug = 'vercel';
    $settings = pp_get_theme_setting($theme_slug);
?>

<form id="themeSettingsForm" method="post" action="">
    <!-- Page Header -->
    <div class="page-header">
      <div class="row align-items-end">
        <div class="col-sm mb-2 mb-sm-0">
          <h1 class="page-header-title">Theme Settings</h1>
        </div>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="d-grid gap-3 gap-lg-5">
            
          <!-- Card -->
          <div class="card">
            <div class="card-header">
              <h2 class="card-title h4">Settings</h2>
            </div>

            <!-- Body -->
            <div class="card-body">
                <input type="hidden" name="action" value="theme_update-submit">
                <input type="hidden" name="theme_slug" value="<?php echo $theme_slug?>">
                
                <div class="row mb-4">
                    <div class="col-sm-6 mt-3">
                      <label for="auto_redirect" class="col-sm-12 col-form-label form-label">Auto redirect</label>
                      <div class="input-group">
                        <select name="auto_redirect" id="auto_redirect" class="form-control">
                            <?php $selected = $settings['auto_redirect'] ?? ''; ?>
                            <option value="Disabled" <?php echo ($selected == 'Disabled') ? 'selected' : ''; ?>>Disabled</option>
                            <option value="Enable" <?php echo ($selected == 'Enable') ? 'selected' : ''; ?>>Enable</option>
                        </select>
                      </div>
                      <div class="text-secondary mt-2">Auto redirect to website after successful payment</div>
                    </div>
                    
                  <div class="col-sm-12 mt-3">
                    <label for="tolerance" class="col-sm-12 col-form-label form-label">Tolerance (Optional)</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="tolerance" id="tolerance" placeholder="Enter tolerance" aria-label="Enter tolerance" value="<?= htmlspecialchars($settings['tolerance'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2">Allow users to overpay within this amount. Example: If invoice is 100 and tolerance is 20, then 100–120 will be accepted.</div>
                  </div>
                </div>


                <div id="ajaxResponse" class="mb-3"></div>

                <button type="submit" class="btn btn-primary btn-primary-add">Save Settings</button>
            </div>
            <!-- End Body -->
          </div>
          <!-- End Card -->
        <div id="stickyBlockEndPoint"></div>
      </div>
    </div>
</form>

        
<script>
    $(document).ready(function() {
        $('#themeSettingsForm').on('submit', function(e) {
            e.preventDefault();
    
            document.querySelector(".btn-primary-add").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
    
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    document.querySelector(".btn-primary-add").innerHTML = 'Save Settings';
                    
                    if(response.status) {
                        $('#ajaxResponse').addClass('alert alert-success').html(response.message);
                    } else {
                        $('#ajaxResponse').addClass('alert alert-danger').html(response.message);
                    }
                },
                error: function() {
                    $('#ajaxResponse').addClass('alert alert-danger').html('An error occurred. Please try again.');
                }
            });
        });
    });
</script>