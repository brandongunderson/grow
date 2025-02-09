<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$pageTitle = "Account Settings - My Application";
$pageDescription = "Manage your account settings here.";
include 'header.php';
include 'sidebar.php';
include 'navbar.php';
?>

<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Navigation Pills -->
    <div class="row">
      <div class="col-md-12">
        <div class="nav-align-top">
          <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0">
            <li class="nav-item">
              <a class="nav-link active" href="edit_account.php">
                <i class="ti-sm ti ti-users me-1_5"></i> Account
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="pages-account-settings-notifications.html">
                <i class="ti-sm ti ti-bell me-1_5"></i> Notifications
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="pages-account-settings-connections.html">
                <i class="ti-sm ti ti-link me-1_5"></i> Connections
              </a>
            </li>
          </ul>
        </div>

        <!-- Profile Card -->
        <div class="card mb-6">
          <!-- Profile Picture Section -->
          <div class="card-body">
            <div class="d-flex align-items-start align-items-sm-center gap-6">
              <img src="<?= htmlspecialchars($user['profile_picture'] ?? './assets/img/avatars/1.png') ?>"
                   alt="user-avatar"
                   class="d-block w-px-100 h-px-100 rounded"
                   id="uploadedAvatar" />
              <div class="button-wrapper">
                <label for="uploadPhoto" class="btn btn-primary me-3 mb-4" tabindex="0">
                  <span class="d-none d-sm-block">Upload new photo</span>
                  <i class="ti ti-upload d-block d-sm-none"></i>
                </label>
                <input type="file" id="uploadPhoto" name="profile_picture" class="account-file-input" hidden accept="image/png, image/jpeg">
                <button type="button" class="btn btn-label-secondary account-image-reset mb-4" id="resetImageBtn">
                  <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                  <span class="d-none d-sm-block">Reset</span>
                </button>
                <div>Allowed JPG, GIF or PNG. Max size of 800K</div>
                <!-- Progress Bar -->
                <div id="uploadProgressContainer" style="display:none; margin-top:10px;">
                  <div class="progress">
                    <div id="uploadProgress" class="progress-bar" role="progressbar" style="width:0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                  </div>
                </div>
                <!-- Inline alert for upload status -->
                <div id="uploadAlert" class="alert" style="display:none; margin-top:10px;"></div>
              </div>
            </div>
          </div>

          <!-- Profile Details Form -->
          <div class="card-body pt-4">
            <form id="formAccountSettings" method="post">
              <input type="hidden" id="profile_picture_url" name="profile_picture_url" value="<?= htmlspecialchars($user['profile_picture'] ?? '') ?>">
              <div class="row">
                <div class="mb-4 col-md-6">
                  <label for="full_name" class="form-label">Display Name</label>
                  <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? 'John Doe') ?>">
                </div>
                <div class="mb-4 col-md-6">
                  <label for="email" class="form-label">E-mail</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? 'john@example.com') ?>">
                </div>
                <div class="mb-4 col-md-6">
                  <label for="company" class="form-label">Organization/Company</label>
                  <input type="text" class="form-control" id="company" name="company" value="<?= htmlspecialchars($user['company'] ?? '') ?>">
                </div>
                <div class="mb-4 col-md-6">
                  <label for="state" class="form-label">State</label>
                  <input type="text" class="form-control" id="state" name="state" value="<?= htmlspecialchars($user['state'] ?? '') ?>">
                </div>
                <div class="mb-4 col-md-6">
                  <label for="country" class="form-label">Country</label>
                  <select id="country" name="country" class="select2 form-select">
                    <option value="">Select Country</option>
                    <?php
                    $countries = [
                      'Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi','Cabo Verde','Cambodia','Cameroon','Canada','Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo, Democratic Republic of the','Congo, Republic of the','Costa Rica','Côte d’Ivoire','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Eswatini','Ethiopia','Federated States of Micronesia','Fiji','Finland','France','Gabon','Gambia','Georgia','Germany','Ghana','Greece','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati','Korea, North','Korea, South','Kosovo','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania','Mauritius','Mexico','Moldova','Monaco','Mongolia','Montenegro','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','Norway','Oman','Pakistan','Palau','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Rwanda','St Kitts and Nevis','St Lucia','St Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Sudan','Spain','Sri Lanka','Sudan','Suriname','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu','Vatican City','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe'
                    ];
                    foreach ($countries as $c) {
                      $selected = (isset($user['country']) && $user['country'] === $c) ? 'selected' : '';
                      echo "<option value=\"$c\" $selected>$c</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="mb-4 col-md-6">
                  <label for="timeZones" class="form-label">Timezone</label>
                  <select id="timeZones" name="timeZones" class="select2 form-select">
                    <option value="">Select Timezone</option>
                    <?php
                    $timeZones = [
                      'Pacific/Midway' => '(GMT-11:00) Midway Island, Samoa',
                      'America/Adak' => '(GMT-10:00) Hawaii-Aleutian',
                      'Etc/GMT+10' => '(GMT-10:00) Hawaii',
                      'Pacific/Marquesas' => '(GMT-09:30) Marquesas Islands',
                      'Pacific/Gambier' => '(GMT-09:00) Gambier Islands',
                      'America/Anchorage' => '(GMT-09:00) Alaska',
                      'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada)',
                      'America/Tijuana' => '(GMT-08:00) Tijuana, Baja California',
                      'America/Denver' => '(GMT-07:00) Mountain Time (US & Canada)',
                      'America/Chihuahua' => '(GMT-07:00) Chihuahua, La Paz, Mazatlan',
                      'America/Phoenix' => '(GMT-07:00) Arizona',
                      'America/Chicago' => '(GMT-06:00) Central Time (US & Canada)',
                      'America/Mexico_City' => '(GMT-06:00) Mexico City, Monterrey',
                      'America/New_York' => '(GMT-05:00) Eastern Time (US & Canada)',
                      'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
                      'America/Bogota' => '(GMT-05:00) Bogota, Lima, Quito, Rio Branco',
                      'America/Caracas' => '(GMT-04:30) Caracas',
                      'America/Halifax' => '(GMT-04:00) Atlantic Time (Canada)',
                      'America/La_Paz' => '(GMT-04:00) La Paz',
                      'America/Santiago' => '(GMT-04:00) Santiago',
                      'America/St_Johns' => '(GMT-03:30) Newfoundland',
                      'America/Argentina/Buenos_Aires' => '(GMT-03:00) Buenos Aires, Georgetown',
                      'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
                      'Atlantic/South_Georgia' => '(GMT-02:00) Mid-Atlantic',
                      'Atlantic/Azores' => '(GMT-01:00) Azores',
                      'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
                      'Etc/UTC' => '(GMT+00:00) UTC',
                      'Europe/London' => '(GMT+00:00) London, Lisbon, Casablanca',
                      'Europe/Amsterdam' => '(GMT+01:00) Amsterdam, Berlin, Rome, Stockholm, Vienna',
                      'Europe/Belgrade' => '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague',
                      'Europe/Brussels' => '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris',
                      'Africa/Algiers' => '(GMT+01:00) West Central Africa',
                      'Europe/Warsaw' => '(GMT+01:00) Warsaw',
                      'Africa/Lagos' => '(GMT+01:00) West Africa',
                      'Asia/Beirut' => '(GMT+02:00) Beirut',
                      'Africa/Cairo' => '(GMT+02:00) Cairo',
                      'Africa/Harare' => '(GMT+02:00) Harare, Pretoria',
                      'Europe/Helsinki' => '(GMT+02:00) Helsinki, Riga, Sofia, Tallinn, Vilnius',
                      'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
                      'Asia/Istanbul' => '(GMT+03:00) Istanbul',
                      'Europe/Moscow' => '(GMT+03:00) Moscow, St. Petersburg, Volgograd',
                      'Asia/Tehran' => '(GMT+03:30) Tehran',
                      'Asia/Dubai' => '(GMT+04:00) Abu Dhabi, Muscat',
                      'Asia/Yerevan' => '(GMT+04:00) Yerevan',
                      'Asia/Kabul' => '(GMT+04:30) Kabul',
                      'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
                      'Asia/Karachi' => '(GMT+05:00) Karachi, Tashkent',
                      'Asia/Calcutta' => '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi',
                      'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
                      'Asia/Dhaka' => '(GMT+06:00) Dhaka',
                      'Asia/Rangoon' => '(GMT+06:30) Yangon (Rangoon)',
                      'Asia/Bangkok' => '(GMT+07:00) Bangkok, Hanoi, Jakarta',
                      'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong, Beijing, Singapore',
                      'Asia/Taipei' => '(GMT+08:00) Taipei',
                      'Asia/Irkutsk' => '(GMT+09:00) Irkutsk',
                      'Asia/Tokyo' => '(GMT+09:00) Tokyo, Seoul',
                      'Asia/Seoul' => '(GMT+09:00) Seoul',
                      'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
                      'Australia/Darwin' => '(GMT+09:30) Darwin',
                      'Australia/Adelaide' => '(GMT+09:30) Adelaide',
                      'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
                      'Australia/Brisbane' => '(GMT+10:00) Brisbane',
                      'Australia/Sydney' => '(GMT+10:00) Sydney, Canberra, Melbourne',
                      'Pacific/Guam' => '(GMT+10:00) Guam, Port Moresby',
                      'Asia/Magadan' => '(GMT+11:00) Magadan, Solomon Is., New Caledonia',
                      'Pacific/Auckland' => '(GMT+12:00) Auckland, Wellington',
                      'Pacific/Fiji' => '(GMT+12:00) Fiji, Marshall Is.',
                      'Pacific/Tongatapu' => '(GMT+13:00) Nuku\'alofa'
                    ];
                    foreach ($timeZones as $zone => $label) {
                      $selected = (isset($user['timeZones']) && $user['timeZones'] == $zone) ? 'selected' : '';
                      echo "<option value=\"$zone\" $selected>$label</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="mt-2">
                <button type="submit" class="btn btn-primary me-3">Save changes</button>
                <button type="reset" class="btn btn-label-secondary">Cancel</button>
              </div>
              <!-- Inline alert for account update notifications -->
              <div id="updateAlert" class="alert" style="display:none; margin-top:15px;"></div>
            </form>
          </div>
          <!-- /Profile Details Form -->
        </div>
      </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
  // File Upload with Progress Bar
  $("#uploadPhoto").change(function() {
    if (this.files && this.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $("#uploadedAvatar").attr("src", e.target.result);
      };
      reader.readAsDataURL(this.files[0]);
      
      var formData = new FormData();
      formData.append("profile_picture", this.files[0]);
      
      $("#uploadProgressContainer").show();
      $("#uploadProgress").css("width", "0%").text("0%");
      $("#uploadAlert").hide();
      
      $.ajax({
        url: "upload_photo.php",
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        xhr: function() {
          var xhr = $.ajaxSettings.xhr();
          if (xhr.upload) {
            xhr.upload.addEventListener("progress", function(evt) {
              if (evt.lengthComputable) {
                var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                $("#uploadProgress").css("width", percentComplete + "%").text(percentComplete + "%");
              }
            }, false);
          }
          return xhr;
        },
        success: function(data) {
          $("#uploadProgressContainer").hide();
          if (data.success) {
            $("#uploadAlert").removeClass("alert-danger").addClass("alert-success").text(data.message).show();
            $("#profile_picture_url").val(data.newImagePath);
            $("#uploadedAvatar").attr("src", data.newImagePath);
            originalSrc = data.newImagePath;
          } else {
            $("#uploadAlert").removeClass("alert-success").addClass("alert-danger").text("Upload error: " + data.message).show();
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $("#uploadProgressContainer").hide();
          $("#uploadAlert").removeClass("alert-success").addClass("alert-danger").text("An error occurred during file upload.").show();
          console.error("Upload error:", textStatus, errorThrown);
        }
      });
    }
  });

  var originalSrc = $("#uploadedAvatar").attr("src");
  $("#resetImageBtn").click(function() {
    $("#uploadedAvatar").attr("src", originalSrc);
    $("#uploadPhoto").val("");
    $("#profile_picture_url").val("");
  });

  // Profile Form Submission via AJAX
  $("#formAccountSettings").submit(function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
      url: "update_account.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function(data) {
        if (data.success) {
          $("#updateAlert").removeClass("alert-danger").addClass("alert-success").text(data.message).show();
        } else {
          $("#updateAlert").removeClass("alert-success").addClass("alert-danger").text("Error: " + data.message).show();
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Profile update error:", textStatus, errorThrown);
        $("#updateAlert").removeClass("alert-success").addClass("alert-danger").text("An error occurred while updating the account.").show();
      }
    });
  });
});
</script>
