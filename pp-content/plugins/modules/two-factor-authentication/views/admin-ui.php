<?php
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }

    $plugin_slug = 'two-factor-authentication';
    $settings = pp_get_plugin_setting($plugin_slug);
    
    require __DIR__ . '/../vendor-two-factor-authentication/autoload.php'; // If using Composer
    
    $ga = new PHPGangsta_GoogleAuthenticator();
?>

<form id="smtpSettingsForm" method="post" action="">
    <!-- Page Header -->
    <div class="page-header">
      <div class="row align-items-end">
        <div class="col-sm mb-2 mb-sm-0">
          <h1 class="page-header-title">Two-factor authentication Settings</h1>
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
                <input type="hidden" name="two-factor-authentication-action" value="plugin_update-submit">
                <input type="hidden" name="plugin_slug" value="<?php echo $plugin_slug?>">
                
                <div class="row mb-4">
                  <div class="col-sm-12">
                     <center>
                         <?php
                            $secret_key = $settings['secret_key'] ?? '';
                            
                            if($secret_key == ""){
                                $secret = $ga->createSecret();
                            }else{
                                $secret = $secret_key;
                            }
                            
                            $qrCodeUrl = $ga->getQRCodeGoogleUrl("PipraPay - Payment Panel", $secret, "PipraPay - Payment Panel");
                         ?>

                        <img src="<?php echo $qrCodeUrl; ?>" class="img-fluid img-thumbnail mx-auto d-block" alt="2FA QR Code" style="max-width: 250px;">
    
                        <p class="mt-4 mb-1 text-muted">Scan or Copy the Secret Code</p>
                        
                        <div class="alert alert-light border rounded-pill d-inline-block px-3 py-2">
                            <code><?php echo $secret; ?></code>
                        </div>
    
                        <p class="mt-1 small text-muted">Use this code with your authenticator app (Google Authenticator, Authy, etc.)</p>
                        
                        <input type="hidden" name="secret_key" id="secret_key" value="<?php echo $secret; ?>">
                     </center>
                  </div>
                    
                    
                  <div class="col-sm-6">
                    <label for="auth_code" class="col-sm-12 col-form-label form-label">Auth Code</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="auth_code" id="auth_code" placeholder="Enter Auth Code" aria-label="Enter Auth Code" value="<?= htmlspecialchars($settings['auth_code'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2">Get it from authenticator app</div>
                  </div>
                  
                  <div class="col-sm-6">
                    <label for="auth_status" class="col-sm-12 col-form-label form-label">Auth Status</label>
                    <div class="input-group">
                      <select class="form-control" name="auth_status" id="auth_status">
                        <?php $status_gateway = isset($settings['auth_status']) ? strtolower($settings['auth_status']) : ''; ?>
                        <option value="disable" <?php echo ($status_gateway === 'disable') ? 'selected' : ''; ?>>Disable</option>
                        <option value="enable" <?php echo ($status_gateway === 'enable') ? 'selected' : ''; ?>>Enable</option>
                      </select>
                    </div>
                    <div class="text-secondary mt-2"></div>
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
                    
                    if (response.status === "true") {
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