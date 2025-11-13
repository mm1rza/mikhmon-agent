<?php
/*
 * GenieACS Configuration
 */

// GenieACS API Configuration
$genieacs_host = '192.168.8.89';  // GenieACS server IP/hostname
$genieacs_port = 7557;            // Using port 7557 as confirmed by actual server configuration
$genieacs_protocol = 'http';      // http or https
$genieacs_username = 'alijaya';   // If authentication is required
$genieacs_password = 'password_sebenarnya'; // If authentication is required

$genieacs_enabled = true;
$genieacs_timeout = 30; // seconds

// Alternative ports to try if default fails
$genieacs_alternative_ports = array(80, 7557, 8080, 3000);

// API Endpoints
$genieacs_api_base = $genieacs_protocol . '://' . $genieacs_host . ':' . $genieacs_port;

// Common TR-069 Parameters
$genieacs_parameters = array(
    // WiFi Parameters
    'wifi_ssid' => 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID',
    'wifi_password' => 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.PreSharedKey.1.PreSharedKey',
    
    // PPPoE Parameters
    'pppoe_username' => 'VirtualParameters.pppoeUsername',
    'pppoe_username2' => 'VirtualParameters.pppoeUsername2',
    'pppoe_password' => 'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.Password',
    'pppoe_ip' => 'VirtualParameters.pppoeIP',
    'pppoe_mac' => 'VirtualParameters.pppoeMac',
    
    // Device Info Parameters
    'device_model' => 'InternetGatewayDevice.DeviceInfo.ModelName',
    'device_manufacturer' => 'InternetGatewayDevice.DeviceInfo.Manufacturer',
    'device_serial' => 'VirtualParameters.getSerialNumber',
    'device_uptime' => 'VirtualParameters.getdeviceuptime',
    
    // Optical Parameters
    'optical_rx_power' => 'VirtualParameters.RXPower',
    'optical_pon_mode' => 'VirtualParameters.getponmode',
    'optical_temp' => 'VirtualParameters.gettemp',
    'optical_mac' => 'VirtualParameters.PonMac',
    
    // Network Parameters
    'ip_tr069' => 'VirtualParameters.IPTR069',
    'hotspot' => 'VirtualParameters.hotspot',
    
    // Connection Info
    'total_associations' => 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.TotalAssociations',
    'product_class' => 'DeviceID.ProductClass',
    'registered_time' => 'Events.Registered',
    'last_inform' => 'Events.Inform'
);

// Virtual Parameters (to be populated from GenieACS server)
$genieacs_virtual_parameters = array();

// ------------------------------------------------------------------
// Define constants expected by GenieACS library (backward compatible)
// ------------------------------------------------------------------
if (!defined('GENIEACS_HOST')) {
    define('GENIEACS_HOST', $genieacs_host);
}

if (!defined('GENIEACS_PORT')) {
    define('GENIEACS_PORT', $genieacs_port);
}

if (!defined('GENIEACS_PROTOCOL')) {
    define('GENIEACS_PROTOCOL', $genieacs_protocol);
}

if (!defined('GENIEACS_USERNAME')) {
    define('GENIEACS_USERNAME', $genieacs_username);
}

if (!defined('GENIEACS_PASSWORD')) {
    define('GENIEACS_PASSWORD', $genieacs_password);
}

if (!defined('GENIEACS_API_URL')) {
    define('GENIEACS_API_URL', $genieacs_api_base);
}

if (!defined('GENIEACS_ENABLED')) {
    define('GENIEACS_ENABLED', (bool)$genieacs_enabled);
}

if (!defined('GENIEACS_TIMEOUT')) {
    define('GENIEACS_TIMEOUT', (int)$genieacs_timeout);
}

// WiFi parameter paths
if (!defined('GENIEACS_WIFI_SSID_PATH')) {
    define('GENIEACS_WIFI_SSID_PATH', $genieacs_parameters['wifi_ssid'] ?? 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID');
}

if (!defined('GENIEACS_WIFI_PASSWORD_PATH')) {
    define('GENIEACS_WIFI_PASSWORD_PATH', $genieacs_parameters['wifi_password'] ?? 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.PreSharedKey.1.KeyPassphrase');
}

if (!defined('GENIEACS_WIFI_ENABLE_PATH')) {
    define('GENIEACS_WIFI_ENABLE_PATH', 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.Enable');
}

if (!defined('GENIEACS_WIFI_SSID_5G_PATH')) {
    define('GENIEACS_WIFI_SSID_5G_PATH', 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.2.SSID');
}

if (!defined('GENIEACS_WIFI_PASSWORD_5G_PATH')) {
    define('GENIEACS_WIFI_PASSWORD_5G_PATH', 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.2.PreSharedKey.1.KeyPassphrase');
}

// Device info paths
if (!defined('GENIEACS_DEVICE_MODEL_PATH')) {
    define('GENIEACS_DEVICE_MODEL_PATH', $genieacs_parameters['device_model'] ?? 'InternetGatewayDevice.DeviceInfo.ModelName');
}

if (!defined('GENIEACS_DEVICE_MANUFACTURER_PATH')) {
    define('GENIEACS_DEVICE_MANUFACTURER_PATH', $genieacs_parameters['device_manufacturer'] ?? 'InternetGatewayDevice.DeviceInfo.Manufacturer');
}

if (!defined('GENIEACS_DEVICE_SERIAL_PATH')) {
    define('GENIEACS_DEVICE_SERIAL_PATH', $genieacs_parameters['device_serial'] ?? 'InternetGatewayDevice.DeviceInfo.SerialNumber');
}

if (!defined('GENIEACS_DEVICE_MAC_PATH')) {
    define('GENIEACS_DEVICE_MAC_PATH', 'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.MACAddress');
}

if (!defined('GENIEACS_DEVICE_FIRMWARE_PATH')) {
    define('GENIEACS_DEVICE_FIRMWARE_PATH', 'InternetGatewayDevice.DeviceInfo.SoftwareVersion');
}

if (!defined('GENIEACS_DEVICE_HARDWARE_PATH')) {
    define('GENIEACS_DEVICE_HARDWARE_PATH', 'InternetGatewayDevice.DeviceInfo.HardwareVersion');
}

// WAN/LAN paths
if (!defined('GENIEACS_WAN_IP_PATH')) {
    define('GENIEACS_WAN_IP_PATH', $genieacs_parameters['pppoe_ip'] ?? 'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ExternalIPAddress');
}

if (!defined('GENIEACS_WAN_STATUS_PATH')) {
    define('GENIEACS_WAN_STATUS_PATH', 'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ConnectionStatus');
}

if (!defined('GENIEACS_LAN_IP_PATH')) {
    define('GENIEACS_LAN_IP_PATH', 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.IPInterface.1.IPInterfaceIPAddress');
}

if (!defined('GENIEACS_LAN_SUBNET_PATH')) {
    define('GENIEACS_LAN_SUBNET_PATH', 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.IPInterface.1.IPInterfaceSubnetMask');
}

// Operational thresholds
if (!defined('GENIEACS_AUTO_REFRESH_INTERVAL')) {
    define('GENIEACS_AUTO_REFRESH_INTERVAL', 300);
}

if (!defined('GENIEACS_ONLINE_THRESHOLD')) {
    define('GENIEACS_ONLINE_THRESHOLD', 300);
}

?>