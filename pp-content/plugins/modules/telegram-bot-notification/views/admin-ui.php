<?php
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }

    $plugin_slug = 'telegram-bot-notification';
    $settings = pp_get_plugin_setting($plugin_slug);
?>

<form id="smtpSettingsForm" method="post" action="">
    <!-- Page Header -->
    <div class="page-header">
      <div class="row align-items-end">
        <div class="col-sm mb-2 mb-sm-0">
          <h1 class="page-header-title">Telegram Bot Notification Settings</h1>
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
                <input type="hidden" name="telegram-bot-notification-action" value="plugin_update-submit">
                <input type="hidden" name="plugin_slug" value="<?php echo $plugin_slug?>">
                
                <div class="row mb-4">
                  <div class="col-sm-12">
                    <label for="telegram_bot_token" class="col-sm-12 col-form-label form-label">Telegram Bot Token</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="telegram_bot_token" id="telegram_bot_token" placeholder="Enter Telegram Bot Token" aria-label="Enter Telegram Bot Token" value="<?= htmlspecialchars($settings['telegram_bot_token'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2">Get it from telegram BotFather</div>
                  </div>
                  
                  <div class="col-sm-12">
                    <label for="auth_code" class="col-sm-12 col-form-label form-label">Auth Code</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="auth_code" id="auth_code" placeholder="" aria-label="" value="<?= htmlspecialchars($settings['auth_code'] ?? rand().uniqid()) ?>" readonly>
                    </div>
                    <div class="text-secondary mt-2">It will be generated automatically.</div>
                  </div>
                </div>
                
                <input type="hidden" name="chat_id" id="chat_id" value="<?= htmlspecialchars($settings['chat_id'] ?? '') ?>">

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
                    
                    if (response.status === true) {
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