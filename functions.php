<?php 
 +-------------------------------------------------------------------------+
 | RubioVPN  - A ProtonVPN connection web-based                            |
 | Version 1.0.0                                                           |
 |                                                                         |
 | This program is free software: you can redistribute it and/or modify    |
 | it under the terms of the GNU General Public License as published by    |
 | the Free Software Foundation.                                           |
 |                                                                         |
 | This file forms part of the RubioTV software.                           |
 |                                                                         |
 | If you wish to use this file in another project or create a modified    |
 | version that will not be part of the RubioTV Software, you              |
 | may remove the exception above and use this source code under the       |
 | original version of the license.                                        |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the            |
 | GNU General Public License for more details.                            |
 |                                                                         |
 | You should have received a copy of the GNU General Public License       |
 | along with this program.  If not, see http://www.gnu.org/licenses/.     |
 |                                                                         |
 +-------------------------------------------------------------------------+
 | Author: Jaime Rubio <jaime@rubiogafsi.com>                              |
 +-------------------------------------------------------------------------+
	 
defined( "RUBIOVPN" ) or die( "Forbidden Access" );

$pid = null;
$node = null;
$iso = null;

function load_iso(){
	$ret = array();
	$handle = fopen(dirname(__FILE__) . "/iso.txt", "r");
	if ($handle) {
    	while (($line = fgets($handle)) !== false) {
        	$line = trim(preg_replace('/\s\s+/', ' ', $line));
        	if(strpos($line,";"))
            {
            	$arr = explode(";", $line);
            	$code = strtolower($arr[0]);
            	$name = $arr[1];
            	$ret[$code] = $name;
			}
    	}
    	fclose($handle);
	} else {
	    $ret= null;
	}
	return $ret;
}

function check_state(){
	global $pid;
	global $node;
	global $iso;
	exec("ls /run/openvpn/ | grep '.pid'" , $out);	
	if(sizeof($out)) {
    	$iso = load_iso();
    	$pid = $out[0];
		$node = substr("$pid",0,strpos($pid,".pid"));    	
    	$country = $iso[substr($node,3,2)];
    } else {
    	$pid = null;
    	$node = null;
    	$country = null;
    }
	return array("pid"=>$pid, "node"=>$node , "country"=>$country);
}

function start_vpn( $newnode ){
	global $pid;
	global $node;
	check_state();
	if ($node != null) {	
        do_vpn('stop',$node);
    	do_vpn('disable',$node);
    }
	return do_vpn('enable',$newnode) && do_vpn('start',$newnode);    
}
function stop_vpn(){
	global $pid;
	global $node;	
	check_state();
	if ($node){
    	return do_vpn('stop',$node) && do_vpn('disable',$node);
    }
}

function do_vpn( $cmd , $node){	
	exec ("sudo ./.exec-wrapper $cmd protonvpn@$node.service");
	return true;
}

