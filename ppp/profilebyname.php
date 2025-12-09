<?php
/*
 *  Copyright (C) 2018 Laksamadi Guko.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
} else {


  if (substr($ppp, 0, 1) == "*") {
    $ppp = $ppp;
  } elseif (substr($userprofile, 0, 1) != "") {
    $getprofile = $API->comm("/ppp/profile/print", array(
      "?name" => "$ppp",
    ));
    $ppp = $getprofile[0]['.id'];
    if ($ppp == "") {
      echo "<b>User Profile not found</b>";
    }
  } else {
    $ppp = substr($ppp, 13);
  }

  $getbridge = $API->comm("/interface/bridge/print");
  $getprofiles = $API->comm("/ppp/profile/print");

  $getprofile = $API->comm("/ppp/profile/print", array(
    "?.id" => "$ppp"
  ));
  $profiledetalis = $getprofile[0];
  $pid = $profiledetalis['.id'];
  $pname = $profiledetalis['name'];
  $localaddress = $profiledetalis['local-address'];
  $remoteaddress = $profiledetalis['remote-address'];
  $bridge = $profiledetalis['bridge'];
  $ratelimit = $profiledetalis['rate-limit'];
  $onlyone = $profiledetalis['only-one'];
  $bridgeportpriority = $profiledetalis['bridge-port-priority'];
  $bridgepathcost = $profiledetalis['bridge-path-cost'];
  $bridgehorizon = $profiledetalis['bridge-horizon'];
  $incomingfilter = $profiledetalis['incoming-filter'];
  $outgoingfilter = $profiledetalis['outgoing-filter'];
  $addresslist = $profiledetalis['address-list'];
  $interfacelist = $profiledetalis['interface-list'];
  $dnsserver = $profiledetalis['dns-server'];
  $winsserver = $profiledetalis['wins-server'];
  $changetcp = $profiledetalis['change-tcp-mss'];
  $useupnp = $profiledetalis['use-upnp'];
  
  // Get script field (on-up or script)
  $profile_script = '';
  
  // Try different possible script field names
  $script_fields = ['on-up', 'script', 'on-login', 'login-script'];
  foreach ($script_fields as $field) {
    if (isset($profiledetalis[$field]) && !empty($profiledetalis[$field])) {
      $profile_script = $profiledetalis[$field];
      break;
    }
  }
  
  // Check if this is an ISOLIR profile based on script content
  $is_isolir_profile = false;
  if (!empty($profile_script) && (
      strpos($profile_script, 'exp-$pengguna') !== false || 
      strpos($profile_script, '$pengguna') !== false ||
      strpos($profile_script, 'scheduler remove') !== false ||
      strpos($profile_script, 'profile ISOLIR') !== false
  )) {
    $is_isolir_profile = true;
  }
  
  // Also check if profile name suggests it's an isolir profile
  if (stripos($pname, 'isolir') !== false || stripos($pname, 'block') !== false) {
    $is_isolir_profile = true;
  }
  
  // Parse isolir settings from comment
  $comment = isset($profiledetalis['comment']) ? $profiledetalis['comment'] : '';
  $enable_isolir = false;
  $isolir_profile = '';
  $isolir_interval = '';
  
  if (!empty($comment) && strpos($comment, 'ISOLIR:') === 0) {
    $enable_isolir = true;
    $parts = explode(':', $comment);
    if (count($parts) >= 3) {
      $isolir_profile = $parts[1];
      $isolir_interval = $parts[2];
    }
  }

  if (isset($_POST['name'])) {
    $name = (preg_replace('/\s+/', '-', $_POST['name']));
    $localaddress = ($_POST['localaddress']);
    $remoteaddress = ($_POST['remoteaddress']);
    $bridge = ($_POST['bridge']);
    $ratelimit = ($_POST['ratelimit']);
    $onlyone = ($_POST['onlyone']);
    $incomingfilter = ($_POST['incomingfilter']);
    $outgoingfilter = ($_POST['outgoingfilter']);
    $addresslist = ($_POST['addresslist']);
    $interfacelist = ($_POST['interfacelist']);
    $dnsserver = ($_POST['dnsserver']);
    $winsserver = ($_POST['winsserver']);
    $changetcp = ($_POST['changetcp']);
    $useupnp = ($_POST['useupnp']);
    
    // Auto-Isolir settings
    $enable_isolir = isset($_POST['enable_isolir']) ? true : false;
    $isolir_profile = isset($_POST['isolir_profile']) ? trim($_POST['isolir_profile']) : '';
    $isolir_interval = isset($_POST['isolir_interval']) ? trim($_POST['isolir_interval']) : '';
    
    // Get script from form
    $profile_script = isset($_POST['profile_script']) ? trim($_POST['profile_script']) : '';
    
    // Build comment with isolir settings if enabled
    $comment = '';
    if ($enable_isolir && !empty($isolir_profile) && !empty($isolir_interval)) {
      $comment = 'ISOLIR:' . $isolir_profile . ':' . $isolir_interval;
      
      // Auto-generate script if isolir enabled and script is empty
      if (empty($profile_script)) {
        // Escape profile name for script
        $isolir_profile_escaped = str_replace('\\', '\\\\', $isolir_profile);
        $isolir_profile_escaped = str_replace('"', '\\"', $isolir_profile_escaped);
        
        // Generate script without exp- prefix for consistency
        $profile_script = ':local pengguna $"user"; :local date [/system clock get date]; :local time [/system clock get time]; :log info "User PPPoE $pengguna login pada $time tanggal $date"; { :if ([/system scheduler find name="$pengguna"]="") do={ /system scheduler add name="$pengguna" interval="' . $isolir_interval . '" on-event="/ppp secret set profile=\"' . $isolir_profile_escaped . '\" [find name=\\$pengguna]; /ppp active remove [find name=\\$pengguna]; :log warning \"User \\$pengguna expired dan dipindah ke profile ' . $isolir_profile_escaped . '\"; /system scheduler remove [find name=\"\\$pengguna\"]"; :log info "Scheduler auto expiry dibuat untuk user $pengguna (' . $isolir_interval . ')"; :local userID [/ppp secret find name=$pengguna]; :if ($userID != "") do={ /ppp secret set $userID comment="AUTO-ISOLIR: Scheduler dibuat pada $date $time, expire ' . $isolir_interval . '"; }; } }';
      }
    }

    if ($bridge != '' || $bridge != NULL) {
      $profileParams = array(
        /*"add-mac-cookie" => "yes",*/
        ".id" => "$pid",
        "name" => "$name",
        "local-address" => "$localaddress",
        "remote-address" => "$remoteaddress",
        "bridge" => "$bridge",
        "rate-limit" => "$ratelimit",
        "only-one" => "$onlyone",
        "incoming-filter" => "$incomingfilter",
        "outgoing-filter" => "$outgoingfilter",
        "address-list" => "$addresslist",
        "dns-server" => "$dnsserver",
        "wins-server" => "$winsserver",
        "change-tcp-mss" => "$changetcp",
        "use-upnp" => "$useupnp",
      );
      
      // Add comment if isolir enabled
      if (!empty($comment)) {
        $profileParams["comment"] = $comment;
      } else {
        // Remove comment if isolir disabled
        $profileParams["comment"] = "";
      }
      
      // Add script (on-up) if provided
      if (!empty($profile_script)) {
        $profileParams["on-up"] = $profile_script;
      } else {
        // Remove script if empty
        $profileParams["on-up"] = "";
      }
      
      $API->comm("/ppp/profile/set", $profileParams);
    } else {
      $profileParams = array(
        /*"add-mac-cookie" => "yes",*/
        ".id" => "$pid",
        "name" => "$name",
        "local-address" => "$localaddress",
        "remote-address" => "$remoteaddress",
        // "bridge" => "$bridge",
        "rate-limit" => "$ratelimit",
        "only-one" => "$onlyone",
        "incoming-filter" => "$incomingfilter",
        "outgoing-filter" => "$outgoingfilter",
        "address-list" => "$addresslist",
        "dns-server" => "$dnsserver",
        "wins-server" => "$winsserver",
        "change-tcp-mss" => "$changetcp",
        "use-upnp" => "$useupnp",
      );
      
      // Add comment if isolir enabled
      if (!empty($comment)) {
        $profileParams["comment"] = $comment;
      } else {
        // Remove comment if isolir disabled
        $profileParams["comment"] = "";
      }
      
      // Add script (on-up) if provided
      if (!empty($profile_script)) {
        $profileParams["on-up"] = $profile_script;
      } else {
        // Remove script if empty
        $profileParams["on-up"] = "";
      }
      
      $API->comm("/ppp/profile/set", $profileParams);
    }

    echo "<script>window.location='./?ppp=profiles&session=" . $session . "'</script>";
  }
}
?>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3><i class="fa fa-edit"></i>
          Edit PPP Profiles </h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" method="post" action="">
          <div>
            <a class="btn bg-warning" href="./?ppp=profiles&session=<?= $session; ?>"> <i class="fa fa-close"></i> <?= $_close ?></a>
            <button type="submit" name="save" class="btn bg-primary"><i class="fa fa-save"></i>
              <?= $_save ?></button>
          </div>
          <table class="table">
            <tr>
              <td class="align-middle"><?= $_name ?></td>
              <td><input class="form-control" type="text" onchange="remSpace();" autocomplete="off" name="name" value="<?= $pname; ?>" required="1" autofocus></td>
            </tr>
            <tr>
              <td class="align-middle">Local Address</td>
              <td><input class="form-control" type="text" required="1" size="4" value="<?= $localaddress; ?>" autocomplete="off" name="localaddress"></td>
            </tr>
            <tr>
              <td class="align-middle">Remote Address</td>
              <td><input class="form-control" type="text" required="1" size="4" value="<?= $remoteaddress; ?>" autocomplete="off" name="remoteaddress"></td>
            </tr>
            <?php if (count($getbridge) != 0) { ?>
              <tr>
                <td class="align-middle">Bridge</td>
                <td>
                  <select class="form-control " name="bridge">
                    <?php if ($bridge == '') { ?>
                        <option value="">==Pilih==</option>
                    <?php } else { ?>
                        <option value="<?php echo $bridge; ?>"><?php echo $bridge ?></option>
                    <?php } ?>
                    <?php
                    $TotalReg = count($getbridge);
                    for ($i = 0; $i < $TotalReg; $i++) {
                      echo "<option value='" . $getbridge[$i]['name'] . "'>" . $getbridge[$i]['name'] . "</option>";
                    }
                    ?>
                  </select>
                </td>
              </tr>
            <?php } ?>
            <tr>
              <td class="align-middle">Incoming Filter</td>
              <td>
                <?php if ($incomingfilter == 'forward') { ?>
                  <select class="form-control" id="incomingfilter" name="incomingfilter">
                    <option value="">== Pilih ==</option>
                    <option value="input">input</option>
                    <option value="forward" selected>forward</option>
                    <option value="output">output</option>
                  </select>
                <?php  } elseif ($incomingfilter == 'output') { ?>
                  <select class="form-control" id="incomingfilter" name="incomingfilter">
                    <option value="">== Pilih ==</option>
                    <option value="input">input</option>
                    <option value="forward">forward</option>
                    <option value="output" selected>output</option>
                  </select>
                <?php } elseif ($incomingfilter == 'input') { ?>
                  <select class="form-control" id="incomingfilter" name="incomingfilter">
                    <option value="">== Pilih ==</option>
                    <option value="input" selected>input</option>
                    <option value="forward">forward</option>
                    <option value="output">output</option>
                  </select>
                <?php } else { ?>
                  <select class="form-control" id="incomingfilter" name="incomingfilter">
                    <option value="">== Pilih ==</option>
                    <option value="input">input</option>
                    <option value="forward">forward</option>
                    <option value="output">output</option>
                  </select>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td class="align-middle">Outgoing Filter</td>
              <td>
                <?php if ($outgoingfilter == 'forward') { ?>
                  <select class="form-control" id="outgoingfilter" name="outgoingfilter">
                    <option value="">== Pilih ==</option>
                    <option value="input">input</option>
                    <option value="forward" selected>forward</option>
                    <option value="yes">yes</option>
                  </select>
                <?php  } elseif ($outgoingfilter == 'output') { ?>
                  <select class="form-control" id="outgoingfilter" name="outgoingfilter">
                    <option value="">== Pilih ==</option>
                    <option value="input">input</option>
                    <option value="forward">forward</option>
                    <option value="output" selected>output</option>
                  </select>
                <?php } elseif ($outgoingfilter == 'input') { ?>
                  <select class="form-control" id="outgoingfilter" name="outgoingfilter">
                    <option value="">== Pilih ==</option>
                    <option value="input" selected>input</option>
                    <option value="forward">forward</option>
                    <option value="output">output</option>
                  </select>
                <?php } else { ?>
                  <select class="form-control" id="outgoingfilter" name="outgoingfilter">
                    <option value="">== Pilih ==</option>
                    <option value="input">input</option>
                    <option value="forward">forward</option>
                    <option value="output">output</option>
                  </select>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td class="align-middle">Address List</td>
              <td><input class="form-control" type="text" size="4" value="<?= $addresslist; ?>" autocomplete="off" name="addresslist"></td>
            </tr>
            <tr>
              <td class="align-middle">DNS Server</td>
              <td><input class="form-control" type="text" size="4" value="<?= $dnsserver; ?>" autocomplete="off" name="dnsserver"></td>
            </tr>
            <tr>
              <td class="align-middle">WINS Server</td>
              <td><input class="form-control" type="text" size="4" value="<?= $winsserver; ?>" autocomplete="off" name="winsserver"></td>
            </tr>
            <tr>
              <td class="align-middle">Change TCP MSS</td>
              <td>
                <?php if ($changetcp == 'no') { ?>
                  <select class="form-control" id="changetcp" name="changetcp">
                    <option value="default">default</option>
                    <option value="no" selected>no</option>
                    <option value="yes">yes</option>
                  </select>
                <?php  } elseif ($changetcp == 'yes') { ?>
                  <select class="form-control" id="changetcp" name="changetcp">
                    <option value="default">default</option>
                    <option value="no">no</option>
                    <option value="yes" selected>yes</option>
                  </select>
                <?php } elseif ($changetcp == 'default') { ?>
                  <select class="form-control" id="changetcp" name="changetcp">
                    <option value="default" selected>default</option>
                    <option value="no">no</option>
                    <option value="yes">yes</option>
                  </select>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td class="align-middle">Use UPnP</td>
              <td>
                <?php if ($useupnp == 'no') { ?>
                  <select class="form-control" id="useupnp" name="useupnp">
                    <option value="default">default</option>
                    <option value="no" selected>no</option>
                    <option value="yes">yes</option>
                  </select>
                <?php  } elseif ($useupnp == 'yes') { ?>
                  <select class="form-control" id="useupnp" name="useupnp">
                    <option value="default">default</option>
                    <option value="no">no</option>
                    <option value="yes" selected>yes</option>
                  </select>
                <?php } elseif ($useupnp == 'default') { ?>
                  <select class="form-control" id="useupnp" name="useupnp">
                    <option value="default" selected>default</option>
                    <option value="no">no</option>
                    <option value="yes">yes</option>
                  </select>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td class="align-middle">Rate Limit</td>
              <td><input class="form-control" type="text" required="1" value="<?= $ratelimit; ?>" size="4" autocomplete="off" name="ratelimit" placeholder="example: rx/tx"></td>
            </tr>
            <tr>
              <td class="align-middle">Only One</td>
              <td>
                <?php if ($onlyone == 'no') { ?>
                  <select class="form-control" id="onlyone" name="onlyone">
                    <option value="default">default</option>
                    <option value="no" selected>no</option>
                    <option value="yes">yes</option>
                  </select>
                <?php  } elseif ($onlyone == 'yes') { ?>
                  <select class="form-control" id="onlyone" name="onlyone">
                    <option value="default">default</option>
                    <option value="no">no</option>
                    <option value="yes" selected>yes</option>
                  </select>
                <?php } elseif ($onlyone == 'default') { ?>
                  <select class="form-control" id="onlyone" name="onlyone">
                    <option value="default" selected>default</option>
                    <option value="no">no</option>
                    <option value="yes">yes</option>
                  </select>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="background-color: #e8f4fd; padding: 10px; border-left: 4px solid #007bff;">
                <strong><i class="fa fa-ban"></i> Profile ISOLIR Template</strong>
                <small class="text-muted d-block mt-1">Konversi ke profile isolir dengan mudah</small>
              </td>
            </tr>
            <tr>
              <td class="align-middle"><?= $is_isolir_profile ? 'Profile ISOLIR' : 'Konversi ke ISOLIR' ?></td>
              <td>
                <?php if ($is_isolir_profile) { ?>
                  <div class="alert alert-success" style="margin: 0; padding: 8px;">
                    <i class="fa fa-check-circle"></i> <strong>Profile ISOLIR Terdeteksi</strong>
                    <br><small>Profile ini sudah dikonfigurasi sebagai profile isolir</small>
                  </div>
                  <?php if (empty($profile_script)) { ?>
                    <button type="button" class="btn btn-info btn-sm mt-2" onclick="refreshIsolirScript()">
                      <i class="fa fa-refresh"></i> Isi Script ISOLIR
                    </button>
                    <small class="text-muted d-block">Script kosong, klik untuk mengisi script auto-delete scheduler</small>
                  <?php } ?>
                <?php } else { ?>
                  <button type="button" class="btn btn-warning btn-sm" onclick="convertToIsolirProfile()">
                    <i class="fa fa-ban"></i> Konversi ke Profile ISOLIR
                  </button>
                  <small class="text-muted d-block mt-1">Mengubah profile ini menjadi profile isolir dengan script auto-delete scheduler</small>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="background-color: #f0f0f0; padding: 10px;">
                <strong><i class="fa fa-shield"></i> Auto Isolir Settings</strong>
              </td>
            </tr>
            <tr>
              <td class="align-middle">Enable Auto Isolir</td>
              <td>
                <label>
                  <input type="checkbox" name="enable_isolir" id="enable_isolir" <?= $enable_isolir ? 'checked' : ''; ?> onchange="toggleIsolirFields()">
                  Aktifkan fitur auto isolir (otomatis ganti profile setelah interval)
                </label>
              </td>
            </tr>
            <tr id="isolir_profile_row" style="display: <?= $enable_isolir ? '' : 'none'; ?>;">
              <td class="align-middle">Profile ISOLIR</td>
              <td>
                <select class="form-control" name="isolir_profile" id="isolir_profile">
                  <option value="">== Pilih Profile ISOLIR ==</option>
                  <?php 
                  $TotalProfiles = count($getprofiles);
                  for ($i = 0; $i < $TotalProfiles; $i++) {
                    $profileName = $getprofiles[$i]['name'];
                    $selected = ($profileName == $isolir_profile) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($profileName) . "' $selected>" . htmlspecialchars($profileName) . "</option>";
                  }
                  ?>
                </select>
                <small class="text-muted">Pilih profile yang akan digunakan saat isolir</small>
              </td>
            </tr>
            <tr id="isolir_interval_row" style="display: <?= $enable_isolir ? '' : 'none'; ?>;">
              <td class="align-middle">Interval Scheduler</td>
              <td>
                <input class="form-control" type="text" name="isolir_interval" id="isolir_interval" value="<?= htmlspecialchars($isolir_interval); ?>" placeholder="Contoh: 1h, 30m, 2d">
                <small class="text-muted">Format: 1h (1 jam), 30m (30 menit), 2d (2 hari), atau 00:30:00 (jam:menit:detik)</small>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="background-color: #f0f0f0; padding: 10px;">
                <strong><i class="fa fa-code"></i> Script (On-Up)</strong>
              </td>
            </tr>
            <tr>
              <td class="align-middle" style="vertical-align: top;">Script</td>
              <td>
                <textarea class="form-control" name="profile_script" id="profile_script" rows="8" style="font-family: monospace; font-size: 12px;" placeholder="Script akan otomatis di-generate jika Auto Isolir diaktifkan"><?= htmlspecialchars($profile_script); ?></textarea>
                <small class="text-muted">Script ini akan dijalankan saat user PPPoE login. Variable yang tersedia: <code>$user</code> (username), <code>$interface</code> (interface name)</small>
              </td>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function remSpace() {
    var upName = document.getElementsByName("name")[0];
    var newUpName = upName.value.replace(/\s/g, "-");
    upName.value = newUpName;
    upName.focus();
  }
  
  function convertToIsolirProfile() {
    if (confirm('Apakah Anda yakin ingin mengkonversi profile ini menjadi profile ISOLIR?\n\nPerubahan yang akan dilakukan:\n- Rate Limit: 64k/64k\n- Only One: yes\n- Script: Auto-delete scheduler\n- Auto Isolir: disabled\n\nProfile ini akan digunakan sebagai target isolir.')) {
      
      // Set isolir values
      document.getElementsByName('ratelimit')[0].value = '64k/64k';
      document.getElementById('onlyone').value = 'yes';
      
      // Set isolir script
      var isolirScript = ':local pengguna $"user"; :local date [/system clock get date]; :local time [/system clock get time]; :log warning "User $pengguna login menggunakan profile ISOLIR pada $time tanggal $date"; :local schedulerName "$pengguna"; :local schedulerID [/system scheduler find name=$schedulerName]; :if ($schedulerID != "") do={ /system scheduler remove $schedulerID; :log info "Scheduler \'$schedulerName\' berhasil dihapus karena user $pengguna menggunakan profile isolir"; :local userID [/ppp secret find name=$pengguna]; :if ($userID != "") do={ /ppp secret set $userID comment="ISOLIR: Scheduler dihapus pada $date $time"; :log info "Comment user $pengguna diupdate: scheduler dihapus"; } } else={ :log info "Tidak ada scheduler \'$schedulerName\' yang perlu dihapus untuk user $pengguna"; :local userID [/ppp secret find name=$pengguna]; :if ($userID != "") do={ /ppp secret set $userID comment="ISOLIR: Login dengan profile isolir pada $date $time"; :log info "Comment user $pengguna diupdate: login isolir tanpa scheduler"; } }';
      document.getElementById('profile_script').value = isolirScript;
      
      // Disable auto isolir
      var enableIsolir = document.getElementById('enable_isolir');
      enableIsolir.checked = false;
      enableIsolir.disabled = true;
      toggleIsolirFields();
      
      alert('Profile berhasil dikonversi ke profile ISOLIR!\n\nJangan lupa klik SAVE untuk menyimpan perubahan.');
    }
  }
  
  function refreshIsolirScript() {
    if (confirm('Apakah Anda ingin mengisi script auto-delete scheduler untuk profile ISOLIR ini?')) {
      var isolirScript = ':local pengguna $"user"; :local date [/system clock get date]; :local time [/system clock get time]; :log warning "User $pengguna login menggunakan profile ISOLIR pada $time tanggal $date"; :local schedulerName "$pengguna"; :local schedulerID [/system scheduler find name=$schedulerName]; :if ($schedulerID != "") do={ /system scheduler remove $schedulerID; :log info "Scheduler \'$schedulerName\' berhasil dihapus karena user $pengguna menggunakan profile isolir"; :local userID [/ppp secret find name=$pengguna]; :if ($userID != "") do={ /ppp secret set $userID comment="ISOLIR: Scheduler dihapus pada $date $time"; :log info "Comment user $pengguna diupdate: scheduler dihapus"; } } else={ :log info "Tidak ada scheduler \'$schedulerName\' yang perlu dihapus untuk user $pengguna"; :local userID [/ppp secret find name=$pengguna]; :if ($userID != "") do={ /ppp secret set $userID comment="ISOLIR: Login dengan profile isolir pada $date $time"; :log info "Comment user $pengguna diupdate: login isolir tanpa scheduler"; } }';
      document.getElementById('profile_script').value = isolirScript;
      
      alert('Script ISOLIR berhasil diisi!\n\nJangan lupa klik SAVE untuk menyimpan perubahan.');
    }
  }
  
  function toggleIsolirFields() {
    var enableCheckbox = document.getElementById('enable_isolir');
    var profileRow = document.getElementById('isolir_profile_row');
    var intervalRow = document.getElementById('isolir_interval_row');
    var scriptField = document.getElementById('profile_script');
    
    if (enableCheckbox.checked) {
      profileRow.style.display = '';
      intervalRow.style.display = '';
      document.getElementById('isolir_profile').required = true;
      document.getElementById('isolir_interval').required = true;
      
      // Auto-generate script when isolir is enabled
      var isolirProfile = document.getElementById('isolir_profile').value;
      var isolirInterval = document.getElementById('isolir_interval').value;
      
      if (isolirProfile && isolirInterval && isolirInterval.length > 1) {
        var generatedScript = ':local pengguna $"user"; :local date [/system clock get date]; :local time [/system clock get time]; :log info "User PPPoE $pengguna login pada $time tanggal $date"; { :if ([/system scheduler find name="$pengguna"]="") do={ /system scheduler add name="$pengguna" interval="' + isolirInterval + '" on-event="/ppp secret set profile=\\"' + isolirProfile + '\\" [find name=\\$pengguna]; /ppp active remove [find name=\\$pengguna]; :log warning \\"User \\$pengguna expired dan dipindah ke profile ' + isolirProfile + '\\"; /system scheduler remove [find name=\\"\\$pengguna\\"]"; :log info "Scheduler auto expiry dibuat untuk user $pengguna (' + isolirInterval + ')"; :local userID [/ppp secret find name=$pengguna]; :if ($userID != "") do={ /ppp secret set $userID comment="AUTO-ISOLIR: Scheduler dibuat pada $date $time, expire ' + isolirInterval + '"; }; } }';
        scriptField.value = generatedScript;
      }
    } else {
      profileRow.style.display = 'none';
      intervalRow.style.display = 'none';
      document.getElementById('isolir_profile').required = false;
      document.getElementById('isolir_interval').required = false;
      
      // Clear script when isolir is disabled
      scriptField.value = '';
    }
  }
  
  // Auto-update script when profile or interval changes
  document.addEventListener('DOMContentLoaded', function() {
    var isolirProfile = document.getElementById('isolir_profile');
    var isolirInterval = document.getElementById('isolir_interval');
    var enableIsolir = document.getElementById('enable_isolir');
    
    if (isolirProfile && isolirInterval && enableIsolir) {
      isolirProfile.addEventListener('change', function() {
        if (enableIsolir.checked) {
          toggleIsolirFields();
        }
      });
      
      isolirInterval.addEventListener('blur', function() {
        if (enableIsolir.checked) {
          toggleIsolirFields();
        }
      });
    }
  });
</script>