<?php
/**
*
* License:
*
* Copyright (c) 2003-2006 ossim.net
* Copyright (c) 2007-2013 AlienVault
* All rights reserved.
*
* This package is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 dated June, 1991.
* You may not use, modify or distribute this program under any other version
* of the GNU General Public License.
*
* This package is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this package; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
* MA  02110-1301  USA
*
*
* On Debian GNU/Linux systems, the complete text of the GNU General
* Public License can be found in `/usr/share/common-licenses/GPL-2'.
*
* Otherwise you can read it here: http://www.gnu.org/licenses/gpl-2.0.txt
*
*/


require ("base_conf.php");
require ("vars_session.php");
require ("$BASE_path/includes/base_constants.inc.php");
require ("$BASE_path/includes/base_include.inc.php");
include_once ("$BASE_path/base_db_common.php");
include_once ("$BASE_path/base_qry_common.php");
include_once ("$BASE_path/base_stat_common.php");
Session::logcheck("analysis-menu", "EventsForensics");
$db = NewBASEDBConnection($DBlib_path, $DBtype);
$db->baseDBConnect($db_connect_method, $alert_dbname, $alert_host, $alert_port, $alert_user, $alert_password);
$cs = new CriteriaState("base_qry_main.php", "&amp;new=1&amp;submit=" . gettext("Query DB"));
$cs->ReadState();

/* Generate the Criteria entered into a human readable form */
$criteria_arr = array();

$tmp_len = strlen($save_criteria);

// META
/*
$criteria_arr['meta']['Sensor'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Plugin'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Plugin Group'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Userdata'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Source Type'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Category'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Signature'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Sig Class'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Sig Prio'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['ag'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Time'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Risk'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Priority'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Reliability'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Asset Dst'] = '<I> ' . gettext("any") . ' </I>';
$criteria_arr['meta']['Type'] = '<I> ' . gettext("any") . ' </I>';
*/
$db = NewBASEDBConnection($DBlib_path, $DBtype);
$db->baseDBConnect($db_connect_method, $alert_dbname, $alert_host, $alert_port, $alert_user, $alert_password);

if (!$cs->criteria['sensor']->isEmpty()) $criteria_arr['meta']['Sensor'] = preg_replace("/Sensor \= \(([^\)]+)\)/i","Events generated by <b>\\1</b> sensor",$cs->criteria['sensor']->Description());
if (!$cs->criteria['plugin']->isEmpty()) $criteria_arr['meta']['Plugin'] = preg_replace("/Plugin \= \(([^\)]+)\)/i","Events generated by <b>\\1</b> data source",$cs->criteria['plugin']->Description());
if (!$cs->criteria['plugingroup']->isEmpty()) $criteria_arr['meta']['Plugin Group'] = preg_replace("/Plugin group \= \(([^\)]+)\)/i","Events in <b>\\1</b> DS Group",$cs->criteria['plugingroup']->Description());
if ($cs->criteria['userdata']->Description()!='') $criteria_arr['meta']['Userdata'] = preg_replace("/User data \= \(([^\)]+)\)/i","Events user data contains <b>\\1</b>",$cs->criteria['userdata']->Description());
if (!$cs->criteria['sourcetype']->isEmpty()) $criteria_arr['meta']['Source Type'] = $cs->criteria['sourcetype']->Description();
if (!$cs->criteria['category']->isEmpty() && $cs->criteria['category']->Description() != "") $criteria_arr['meta']['Category'] = $cs->criteria['category']->Description();
if (!$cs->criteria['sig']->isEmpty() && $cs->criteria['sig']->Description() != "") $criteria_arr['meta']['Signature'] = preg_replace("/Signature\s+\"([^\"]+)\"/i","Event signature contains <b>\\1</b>",$cs->criteria['sig']->Description());
//if (!$cs->criteria['sig_class']->isEmpty()) $criteria_arr['meta']['Sig Class'] = $cs->criteria['sig_class']->Description();
//if (!$cs->criteria['sig_priority']->isEmpty() && $cs->criteria['sig_priority']->Description() != "") $criteria_arr['meta']['Sig Prio'] = $cs->criteria['sig_priority']->Description();
//if (!$cs->criteria['ag']->isEmpty()) $criteria_arr['meta']['ag'] = $cs->criteria['ag']->Description();
if (!$cs->criteria['time']->isEmpty()) $criteria_arr['meta']['Time'] = $cs->criteria['time']->Description_full();
if (!$cs->criteria['ossim_risk_a']->isEmpty()) $criteria_arr['meta']['Risk'] = preg_replace("/risk \= \[(.+)\]/","Events with <b>\\1</b> risk",$cs->criteria['ossim_risk_a']->Description());
if (!$cs->criteria['ossim_priority']->isEmpty() && $cs->criteria['ossim_priority']->Description() != "") $criteria_arr['meta']['Priority'] = $cs->criteria['ossim_priority']->Description();
if (!$cs->criteria['ossim_reliability']->isEmpty() && $cs->criteria['ossim_reliability']->Description() != "") $criteria_arr['meta']['Reliability'] = $cs->criteria['ossim_reliability']->Description();
if (!$cs->criteria['ossim_asset_dst']->isEmpty() && $cs->criteria['ossim_asset_dst']->Description() != "") $criteria_arr['meta']['Asset Dst'] = $cs->criteria['ossim_asset_dst']->Description();
if (!$cs->criteria['ossim_type']->isEmpty()) $criteria_arr['meta']['Type'] = $cs->criteria['ossim_type']->Description();

// IP
//$criteria_arr['ip']['IP Addr'] = '<I> ' . gettext("any") . ' </I>';
if (!$cs->criteria['ctx']->isEmpty() || !$cs->criteria['ip_addr']->isEmpty() || !$cs->criteria['ip_field']->isEmpty() || !$cs->criteria['networkgroup']->isEmpty() || !$cs->criteria['idm_username']->isEmpty() || !$cs->criteria['idm_hostname']->isEmpty() || !$cs->criteria['idm_domain']->isEmpty()  || !$cs->criteria['rep']->isEmpty() || !$cs->criteria['hostid']->isEmpty()) {

	if ($cs->criteria['ctx']->Description() != "")
	    $criteria_arr['ip']['Context'] =  preg_replace("/Context =/","",preg_replace("/\((.*)\)/","<b>\\1</b>",$cs->criteria['ctx']->Description()));

	if ($cs->criteria['hostid']->Description() != "")
	    $criteria_arr['ip']['Host'] = $cs->criteria['hostid']->Description();
	
	if ($cs->criteria['idm_username']->Description() != "")
	    $criteria_arr['ip']['IDM'] = preg_replace("/IDM /","",preg_replace("/\((.*)\)/","<b>\\1</b>",$cs->criteria['idm_username']->Description()));
	
	if ($cs->criteria['idm_hostname']->Description() != "")
	    $criteria_arr['ip']['IDM'] = ($criteria_arr['ip']['IDM']!="" ? "<br>" : "").preg_replace("/IDM /","",preg_replace("/\((.*)\)/","<b>\\1</b>",$cs->criteria['idm_hostname']->Description()));
	
	if ($cs->criteria['idm_domain']->Description() != "")
	    $criteria_arr['ip']['IDM'] = ($criteria_arr['ip']['IDM']!="" ? "<br>" : "").preg_replace("/IDM /","",preg_replace("/\((.*)\)/","<b>\\1</b>",$cs->criteria['idm_domain']->Description()));
    
    if ($cs->criteria['rep']->Description() != "")
        $criteria_arr['ip']['Reputation'] = preg_replace("/Reputation: /","",preg_replace("/\((.*)\)/","<b>\\1</b>",$cs->criteria['rep']->Description()));
	
	if ($cs->criteria['networkgroup']->Description() != "")
	    $criteria_arr['ip']['Network Group'] = $cs->criteria['networkgroup']->Description();
	
	if ($cs->criteria['ip_addr']->Description() != "")
		$criteria_arr['ip']['IP Address'] = preg_replace("/Destination\=(\d+\.\d+\.\d+\.\d+)/","Event destination address is <b>\\1</b>",preg_replace("/Source\=(\d+\.\d+\.\d+\.\d+)/","Event source address is <b>\\1</b>",$cs->criteria['ip_addr']->Description_full()));
	if ($cs->criteria['ip_field']->Description() != "")
		$criteria_arr['ip']['IP Field'] = $cs->criteria['ip_field']->Description();
}

// LAYER4
/*
$criteria_arr['layer4']['TCP Port'] = '<I> none </I>';
//$criteria_arr['layer4']['TCP Flags'] = '<I> none </I>';
//$criteria_arr['layer4']['TCP Field'] = '<I> none </I>';
$criteria_arr['layer4']['UPD Port'] = '<I> none </I>';
//$criteria_arr['layer4']['UDP Field'] = '<I> none </I>';
$criteria_arr['layer4']['ICMP Field'] = '<I> none </I>';
$criteria_arr['layer4']['RawIP Field'] = '<I> none </I>';
*/		
if ($cs->criteria['layer4']->Get() == "TCP") {
	if (!$cs->criteria['tcp_port']->isEmpty() || !$cs->criteria['tcp_flags']->isEmpty() || !$cs->criteria['tcp_field']->isEmpty()) {
		$criteria_arr['layer4']['TCP Port'] = preg_replace("/dest port\s+\=\s+(\d+)/","Event destination port is <b>\\1</b>",preg_replace("/source port\s+\=\s+(\d+)/","Event source port is <b>\\1</b>",$cs->criteria['tcp_port']->Description()));
		//$criteria_arr['layer4']['TCP Flags'] = $cs->criteria['tcp_flags']->Description();
		//$criteria_arr['layer4']['TCP Field'] = $cs->criteria['tcp_field']->Description();
	}
} else if ($cs->criteria['layer4']->Get() == "UDP") {
	if (!$cs->criteria['udp_port']->isEmpty() || !$cs->criteria['udp_field']->isEmpty()) {
		$criteria_arr['layer4']['UPD Port'] = preg_replace("/dest port\s+\=\s+(\d+)/","Event destination port is <b>\\1</b>",preg_replace("/source port\s+\=\s+(\d+)/","Event source port is <b>\\1</b>",$cs->criteria['udp_port']->Description()));
		//$criteria_arr['layer4']['UDP Field'] = $cs->criteria['udp_field']->Description();
	}
} else if ($cs->criteria['layer4']->Get() == "ICMP") {
	if (!$cs->criteria['icmp_field']->isEmpty()) {
		$criteria_arr['layer4']['ICMP Field'] = preg_replace("/dest port\s+\=\s+(\d+)/","Event destination port is <b>\\1</b>",preg_replace("/source port\s+\=\s+(\d+)/","Event source port is <b>\\1</b>",$cs->criteria['icmp_field']->Description()));
	}
} else if ($cs->criteria['layer4']->Get() == "RawIP") {
	if (!$cs->criteria['rawip_field']->isEmpty()) {
		$criteria_arr['layer4']['RawIP Field'] = preg_replace("/dest port\s+\=\s+(\d+)/","Event destination port is <b>\\1</b>",preg_replace("/source port\s+\=\s+(\d+)/","Event source port is <b>\\1</b>",$cs->criteria['rawip_field']->Description()));
	}
}

/* Payload ************** */
//$criteria_arr['payload']['Data'] = '<I> ' . gettext("any") . ' </I>';
if (!$cs->criteria['data']->isEmpty()) {
	$criteria_arr['payload']['Data'] = preg_replace("/contains \"([^\"]+)\"/i","Event payload contains <b>\\1</b>",$cs->criteria['data']->Description());
}

?>
<html>
<HEAD>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<title>Forensics Console : Query Filters</title>
	<link rel="stylesheet" type="text/css" href="/ossim/style/av_common.css?t=<?php echo Util::get_css_id() ?>">
	<style type='text/css'>
		.t_data{ 
			border: solid 1px #CCCCCC;
			text-align: center;
			padding:4px;
		}
	</style>
</HEAD>
<body>
<table class="transparent" width="100%">
	<?php 
	if (is_array($criteria_arr['meta'])) 
	{ 
		?>
		<tr>
			<td style='padding-top: 5px;'>
				<table class="transparent" cellpadding='0' cellspacing='2' border='0' width="100%">
					<tr>
						<td height="27" align="center" class="headerpr">META</td>
					</tr>
					<?php 
					foreach ($criteria_arr['meta'] as $meta=>$val) 
					{ 
						?>
						<tr>
							<th align="center" style="padding-right:10px;font-size:12px;"><?php echo $meta ?></th>
						</tr>
						<tr>
							<td class='t_data'><?php echo preg_replace("/\<a /i","<a target='main' ",$val) ?></td>
						</tr>
						<?php 
					} 
					?>
				</table>
			</td>
		</tr>
		<?php 
	} 
	
	if (is_array($criteria_arr['payload'])) 
	{ 
		?>
		<tr>
			<td style='padding-top: 5px;'>
				<table class="transparent" cellpadding='0' cellspacing='2' border='0' width="100%">
					<tr>
						<td height="27" align="center" class="headerpr">PAYLOAD</td>
					</tr>
					<?php 
					foreach ($criteria_arr['payload'] as $meta=>$val) 
					{ 
						?>
						<tr>
							<th align="center" style="padding-right:10px;font-size:12px;"><?php echo $meta ?></th>
						</tr>
						<tr>
							<td class='t_data'><?php echo preg_replace("/.*\)\<br\>/i","",preg_replace("/\<a /i","<a target='main' ",$val)) ?></td>
						</tr>
						<?php 
					} 
					?>
				</table>
			</td>
		</tr>
		<?php 
	}
	
	if (is_array($criteria_arr['ip'])) 
	{ 
		?>
		<tr>
			<td style='padding-top: 5px;'>
				<table class="transparent" cellpadding='0' cellspacing='2' border='0' width="100%">
					<tr>
						<td height="27" align="center" class="headerpr">IP</td>
					</tr>
					<?php 
					foreach ($criteria_arr['ip'] as $meta=>$val) 
					{ 
						?>
						<tr>
							<th align="center" style="padding-right:10px;font-size:12px;"><?php echo $meta ?></th>
						</tr>
						<tr>
							<td class='t_data'><?php echo preg_replace("/\<a /i","<a target='main' ",$val) ?></td>
						</tr>
						<?php 
					} 
					?>
				</table>
			</td>
		</tr>
		<?php 
	} 
	
	if (is_array($criteria_arr['layer4'])) 
	{ 
		?>
		<tr>
			<td style='padding-top: 5px;'>
				<table class="transparent" cellpadding='0' cellspacing='2' border='0' width="100%">
					<tr>
						<td height="27" align="center" class="headerpr">LAYER 4</td>
					</tr>
					<?php 
					foreach ($criteria_arr['layer4'] as $meta=>$val) 
					{ 
						?>
						<tr>
							<th align="center" style="padding-right:10px;font-size:12px;"><?php echo $meta ?></th>
						</tr>
						<tr>
							<td class='t_data'><?php echo preg_replace("/\<a /i","<a target='main' ",$val) ?></td>
						</tr>
						<?php 
					} 
					?>
				</table>
			</td>
		</tr>
		<?php 
	} 
	?>
</table>
</body>
</html>
