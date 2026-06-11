<?php
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }

    $plugin_slug = 'smtp-mailer';
    $settings = pp_get_plugin_setting($plugin_slug);
?>

<form id="smtpSettingsForm" method="post" action="">
    <!-- Page Header -->
    <div class="page-header">
      <div class="row align-items-end">
        <div class="col-sm mb-2 mb-sm-0">
          <h1 class="page-header-title">SMTP Mailer Settings</h1>
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
                <input type="hidden" name="action" value="plugin_update-submit">
                <input type="hidden" name="plugin_slug" value="<?php echo $plugin_slug?>">
                
                <div class="row mb-4">
                  <div class="col-sm-6">
                    <label for="host" class="col-sm-12 col-form-label form-label">SMTP Host</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="host" id="host" placeholder="Enter host" aria-label="Enter host" value="<?= htmlspecialchars($settings['host'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                  <div class="col-sm-6">
                    <label for="port" class="col-sm-12 col-form-label form-label">SMTP Port</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="port" id="port" placeholder="Enter port" aria-label="Enter port" value="<?= htmlspecialchars($settings['port'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-sm-6">
                    <label for="username" class="col-sm-12 col-form-label form-label">SMTP Username</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" aria-label="Enter username" value="<?= htmlspecialchars($settings['username'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                  <div class="col-sm-6">
                    <label for="password" class="col-sm-12 col-form-label form-label">SMTP Password</label>
                    <div class="input-group">
                      <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" aria-label="Enter password" value="<?= htmlspecialchars($settings['password'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                </div>
                
                <div class="row mb-4">
                  <div class="col-sm-6">
                    <label for="from" class="col-sm-12 col-form-label form-label">From Name</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="from" id="from" placeholder="Enter from name" aria-label="Enter from name" value="<?= htmlspecialchars($settings['from'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                    
                  <div class="col-sm-6">
                    <label for="secure" class="col-sm-12 col-form-label form-label">SMTP Secure</label>
                    <div class="input-group">
                      <select class="form-control" name="secure" id="secure">
                        <?php $secure_value = isset($settings['secure']) ? strtolower($settings['secure']) : ''; ?>
                        <option value="ssl" <?php echo ($secure_value === 'ssl') ? 'selected' : ''; ?>>SSL</option>
                        <option value="tls" <?php echo ($secure_value === 'tls') ? 'selected' : ''; ?>>TLS</option>
                        <option value="" <?php echo ($secure_value !== 'ssl' && $secure_value !== 'tls') ? 'selected' : ''; ?>>Select Secure Method</option>
                      </select>
                    </div>
                    <div class="text-secondary mt-2"> </div>
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
        $('#smtpSettingsForm').on('submit', function(e) {
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