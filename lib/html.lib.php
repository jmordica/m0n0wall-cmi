<?php
/**
 * Various HTML related stuff
 *
 * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
 * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
 * @version 1.0
 * @package libs
 * @subpackage various
 * @category libs
 * @filesource
 */

static $group_pages = array (
	
	"diag_arp.php" => "Diagnostics: ARP table",
	"diag_backup.php" => "Diagnostics: Backup/restore",
	"diag_defaults.php" => "Diagnostics: Factory defaults",
	"diag_dhcp_leases.php" => "Diagnostics: DHCP leases",
	"diag_ipfstat.php" => "Diagnostics: Firewall states",
	"diag_ipsec_sad.php" => "Diagnostics: IPsec",
	"diag_ipsec_spd.php" => "Diagnostics: IPsec",
	"diag_logs.php" => "Diagnostics: Logs",
	"diag_logs_dhcp.php" => "Diagnostics: Logs",
	"diag_logs_filter.php" => "Diagnostics: Logs",
	"diag_logs_portal.php" => "Diagnostics: Logs",
	"diag_logs_settings.php" => "Diagnostics: Logs",
	"diag_logs_vpn.php" => "Diagnostics: Logs",
	"diag_ping.php" => "Diagnostics: Ping",
	"diag_resetstate.php" => "Diagnostics: Reset state",
	"diag_traceroute.php" => "Diagnostics: Traceroute",
	"exec.php" => "",
	"exec_raw.php" => "",
	"firewall_aliases.php" => "Firewall: Aliases",
	"firewall_aliases_edit.php" => "Firewall: Aliases: Edit alias",
	"firewall_nat.php" => "Firewall: NAT: Inbound",
	"firewall_nat_1to1.php" => "Firewall: NAT: 1:1",
	"firewall_nat_1to1_edit.php" => "Firewall: NAT: Edit 1:1",
	"firewall_nat_edit.php" => "Firewall: NAT: Edit",
	"firewall_nat_out.php" => "Firewall: NAT: Outbound",
	"firewall_nat_out_edit.php" => "Firewall: NAT: Edit outbound mapping",
	"firewall_nat_server.php" => "Firewall: NAT: Server NAT",
	"firewall_nat_server_edit.php" => "Firewall: NAT: Edit Server NAT",
	"firewall_rules.php" => "Firewall: Rules",
	"firewall_rules_edit.php" => "Firewall: Rules: Edit",
	"firewall_shaper.php" => "Firewall: Traffic shaper: Rules",
	"firewall_shaper_edit.php" => "Firewall: Traffic shaper: Edit rule",
	"firewall_shaper_magic.php" => "Firewall: Traffic shaper: Magic shaper wizard",
	"firewall_shaper_pipes.php" => "Firewall: Traffic shaper: Pipes",
	"firewall_shaper_pipes_edit.php" => "Firewall: Traffic shaper: Edit pipe",
	"firewall_shaper_queues.php" => "Firewall: Traffic shaper: Queues",
	"firewall_shaper_queues_edit.php" => "Firewall: Traffic shaper: Edit queue",
	"graph.php" => "",
	"graph_cpu.php" => "",
	"index.php" => "m0n0wall webGUI",
	"interfaces_assign.php" => "Interfaces: Assign network ports",
	"interfaces_lan.php" => "Interfaces: LAN",
	"interfaces_opt.php" => "Interfaces: Optional ",
	"interfaces_vlan.php" => "Interfaces: Assign network ports",
	"interfaces_vlan_edit.php" => "Interfaces: Assign network ports: Edit VLAN",
	"interfaces_wan.php" => "Interfaces: WAN",
	"license.php" => "License",
	"reboot.php" => "Diagnostics: Reboot system",
	"services_captiveportal.php" => "Services: Captive portal",
	"services_captiveportal_filemanager.php" => "Services: Captive portal: File Manager",
	"services_captiveportal_ip.php" => "Services: Captive portal: Allowed IP Addresses",
	"services_captiveportal_ip_edit.php" => "Services: Captive portal: Edit allowed IP address",
	"services_captiveportal_mac.php" => "Services: Captive portal: Pass-through MAC",
	"services_captiveportal_mac_edit.php" => "Services: Captive portal: Edit pass-through MAC address",
	"services_captiveportal_users.php" => "Services: Captive portal: Users",
	"services_captiveportal_users_edit.php" => "Services: Captive portal: Edit user",
	"services_dhcp.php" => "Services: DHCP server",
	"services_dhcp_edit.php" => "Services: DHCP server: Edit static mapping",
	"services_dhcp_relay.php" => "Services: DHCP relay",
	"services_dnsmasq.php" => "Services: DNS forwarder",
	"services_dnsmasq_domainoverride_edit.php" => "Services: DNS forwarder: Edit Domain Override",
	"services_dnsmasq_edit.php" => "Services: DNS forwarder: Edit host",
	"services_dyndns.php" => "Services: Dynamic DNS",
	"services_proxyarp.php" => "Services: Proxy ARP",
	"services_proxyarp_edit.php" => "Services: Proxy ARP: Edit",
	"services_snmp.php" => "Services: SNMP",
	"services_wol.php" => "Services: Wake on LAN",
	"services_wol_edit.php" => "Services: Wake on LAN: Edit",
	"status.php" => "title: : false",
	"status_captiveportal.php" => "Status: Captive portal",
	"status_graph.php" => "Status: Traffic graph",
	"status_graph_cpu.php" => "Status: CPU load",
	"status_interfaces.php" => "Status: Interfaces",
	"status_wireless.php" => "Status: Wireless",
	"system.php" => "System: General setup",
	"system_advanced.php" => "System: Advanced setup",
	"system_firmware.php" => "System: Firmware",
	"system_groupmanager.php" => "System: Group manager",
	"system_routes.php" => "System: Static routes",
	"system_routes_edit.php" => "System: Static routes: Edit",
	"system_usermanager.php" => "System: User password",
	"uploadconfig.php" => "",
	"vpn_ipsec.php" => "VPN: IPsec: Tunnels",
	"vpn_ipsec_ca.php" => "VPN: IPsec: CAs",
	"vpn_ipsec_ca_edit.php" => "VPN: IPsec: Edit CA certificate",
	"vpn_ipsec_edit.php" => "VPN: IPsec: Edit tunnel",
	"vpn_ipsec_keys.php" => "VPN: IPsec: Pre-shared keys",
	"vpn_ipsec_keys_edit.php" => "VPN: IPsec: Edit pre-shared key",
	"vpn_ipsec_mobile.php" => "VPN: IPsec: Mobile clients",
	"vpn_pptp.php" => "VPN: PPTP: Configuration",
	"vpn_pptp_users.php" => "VPN: PPTP: Users",
	"vpn_pptp_users_edit.php" => "VPN: PPTP: Edit user"
	);
        $group_pages['interfaces_opt.php'] = "Interfaces: Optional";
        $group_pages['graph.php'] = "Diagnostics: Interface Traffic";
        $group_pages['graph_cpu.php'] = "Diagnostics: CPU Utilization";
        $group_pages['exec.php'] = "Hidden: Exec";
        $group_pages['exec_raw.php'] = "Hidden: Exec Raw";
        $group_pages['status.php'] = "Hidden: Detailed Status";
        $group_pages['uploadconfig.php'] = "Hidden: Upload Configuration";
        $group_pages['index.php'] = "*Landing Page after Login";
        $group_pages['system_usermanager.php'] = "*User Password";
        $group_pages['diag_logs_settings.php'] = "Diagnostics: Logs: Settings";
        $group_pages['diag_logs_vpn.php'] = "Diagnostics: Logs: PPTP VPN";
        $group_pages['diag_logs_filter.php'] = "Diagnostics: Logs: Firewall";
        $group_pages['diag_logs_portal.php'] = "Diagnostics: Logs: Captive Portal";
        $group_pages['diag_logs_dhcp.php'] = "Diagnostics: Logs: DHCP";
        $group_pages['diag_logs.php'] = "Diagnostics: Logs: System";


	static $wkports = array(
		"FTP" => 21,
		"SSH" => 22,
		"Telnet" => 23,
		"SMTP" => 25,
		"DNS" => 53,
		"HTTP" => 80,
		"POP3" => 110,
		"IMAP" => 143,
                "HTTPS" => 443
        );

function specialport($port) {
 global $wkports;
 if (strtolower($port) == "") return 1;
 if (strpos($port, "-")) return 0;
 foreach($wkports as $p) {
  if ($port == $p) {
   return 1;
  }
 }
 return 0;
}

function specialnet($mono, $str) {
 
  $special = array("wanip", "lan", "pptp", "any");
  foreach ($mono->ifaces as $if) {
   if ($if->type == "opt") {
    $ifn = $if->type.$if->num;
    if ($ifn == $str)
     return 1;
   }
  }
  foreach ($special as $sp) {
   if (strtolower($sp) == strtolower($str))
    return 1;
  }
  return 0;
}

function checkPost($name) {
  global $_POST;
  return isset($_POST[$name]);
}

function getHTTPVar($name) {
 global $_GET;
 global $_POST;
 
 /* first check POST, then fallback on GET */
 if (isset($_POST[$name])) return $_POST[$name];
 if (isset($_GET[$name])) return $_POST[$name];
 return NULL;
}

function sanitizeVar($var) {
  return mysql_escape_string($var);
}

function sanitizeArray($var) {
  foreach($var as $name => $value) {
    if (is_array($value)) { sanitizeArray($value); continue; }
    $var[$name] = mysql_escape_string($value);
  }
}


function checkHTTPVars($names) {

}


?>
