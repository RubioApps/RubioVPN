<!DOCTYPE html>
<?php
/**
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
*/

include "constants.php";
include "functions.php";
$iso = load_iso();

if ($handle = opendir(RUBIOVPN_PATH)){ 
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') + 1) == "conf") {
			$allfiles[] = $file;   
		} 
	}
} 
closedir($handle);
sort($allfiles);

foreach($allfiles as $file){
	$from = substr($file,0,2);
	$to = substr($file,3,2);
	$nodes[$from][] = $to;    
	sort($nodes[$from]);
}
?>
<html class="no-js withBanner" lang="fr">
<head>
	<noscript> <style> html[data-bgs="gainsboro"] { background-color: #d6d6d6; } html[data-bgs="nightRider"] { background-color: #1a1c20; } html[data-bgs="nightRider"] div[data-noscript] { color: #979ba080; } html[data-slider-fixed='1'] { margin-right: 0 !important; } body > div[data-noscript] ~ * { display: none !important; } div[data-noscript] { visibility: hidden; animation: 2s noscript-fadein; animation-delay: 1s; text-align: center; animation-fill-mode: forwards; } @keyframes noscript-fadein { 0% { opacity: 0; } 100% { visibility: visible; opacity: 1; } } </style> <div data-noscript> <div class="fa fa-3x fa-exclamation-triangle margined-top-20 text-danger"></div> <h2>JavaScript is disabled</h2> <p>Please enable javascript and refresh the page</p> </div> </noscript>
	<meta charset="utf-8">
 	<meta name="msapplication-TileColor" content="#2d5d9d">
 	<meta name="msapplication-TileImage" content="favicons/mstile-144x144.png">
 	<meta name="theme-color" content="#2d5d9d">    
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
	<link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="48x48" href="favicons/favicon-48x48.png">
	<link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="favicons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="favicons/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon-180x180.png">
  	<title>RubioVPN2</title> 	
    <script src="assets/jquery.js"></script>
    <link href="assets/rubiovpn.css" rel="stylesheet" type="text/css">
  	<style type="text/css">
    body { background-color: rgb(38, 42, 51); }
    #missing-node{border: 1px solid #dde6ec;background:linear-gradient(90deg,transparent 0,transparent 0,#3b404d 0);}
    .spacer { display: block; margin-top:30px;}
    .block-info-standard { color: #fff; }
    .connected{ color: lightgreen; }
    .disconnected{ color: red; }
    .logo-link{ background: url("images/rubiovpn.png") 0 0 no-repeat; width: 153px; display: block;}    
    .pm-button.btn-node {border: 1px solid #505560; padding-top: 40px; color: #fff;font-size:10px; margin:2px;width: 72px; height: 72px;}
	<?php 
    foreach($allfiles as $entry){
        $code = substr($entry,3,2);
        echo "\t.pm-button.btn-node-$code{ background: url(\"images/$code.png\")50% 20% no-repeat;}\n";
    }
	?>
	</style>     
  	<script>
	;(function($){     
    	function vpn_status(){
			$.getJSON("status.php",function(data){
            	if(data.pid !== null){
    				$("#connect").hide();
    				$("#disconnect").show();
    				$('#vpn-status').text('Connecté à ' + data.country)
                		.removeClass('disconnected')
                    	.addClass('connected');                
                } else {
    				$("#connect").show();
    				$("#disconnect").hide();
    				$('#vpn-status').text('Déconnecté')
                		.removeClass('connected')
                    	.addClass('disconnected');                
                }
            });        
		};

		$(document).ready(function() {
        
    		$( "button.pm-node" ).click(function(){
        		$('#missing-node').hide(); 
        		$("input#newnode").val($(this).val());   
				$("input#newcountry").val($(this).text());             
        		$("button#connect").text("Se connecter à " + $(this).text());    
        	});
	
	    	$("#connect").click(function() {
	        	if($('input#newnode').val().length == 0){
	            	$('#missing-node').show(); 
	            	return;
	            }
	        	v = { 'sid': $('input#sid').val() , 'node': $('input#newnode').val() };
	  			$.post("connect.php", v , function(data) {
	            	if(data){
		  				$("#connect").hide();
	            		$("#disconnect").show();
	                	$('#vpn-status')
	                    	.text('Connecté à ' + $('input#newcountry').val())
	                    	.removeClass('disconnected')
	                    	.addClass('connected');
	                }
				});
			});
	    
	        $("#disconnect").click(function() {   
	        	v = { 'sid': $('input#sid').val() };
	  			$.post("disconnect.php", v , function(data) {
	            	if(data){
	                	$('input#newnode').val('');
                    	$('input#newcountry').val('');
		  				$("#disconnect").hide();
	            		$("#connect").text('Se connecter').show();
	                	$('#vpn-status')
	                    	.text('Déconnecté')
	                		.removeClass('connected')
	                    	.addClass('disconnected');
	                }
				});
			});
	    
    		$('#btn-close-error').click(function(){
	        	$('#missing-node').hide(); 
	        });
	
        	$("#missing-node").hide();      
    		$("#connect").hide();
    		$("#disconnect").hide();
    		$('#vpn-status').text('');
        	vpn_status();
    		setInterval( vpn_status , 3000);    
    	});    
	})(jQuery);;
  </script>     
</head>
<body class="isDarkMode is-comfortable">                        	
<div class="content-container flex flex-column flex-nowrap">
	<div class="content flex-item-fluid flex flex-column flex-nowrap">
    	<header class="header flex flex-nowrap">
			<div class="logo-container flex flex-spacebetween flex-items-center flex-nowrap ">
				<a class="logo-link flex nodecoration nodecoration" href="/vpn"></a>
			</div>
    	</header>
		<div class="flex flex-item-fluid flex-nowrap">     
			<div class="flex flex-column flex-nowrap flex-item-fluid ">
        		<main class="flex-item-fluid relative">       
                	<div class="flex flex-spacebetween flex-items-center pl1 pr1">                  	                	
	            		<div class="flex-autogrid w100">
	                    	<div class="flex-autogrid-item flex-nowrap h3">
	                    		<span id="vpn-status"></span>
	                    	</div>
	                    	<div class="flex-autogrid-item h6 alignright">
	                    		<button id="disconnect" class="pm-button pm-button--primary">Quitter</button>
	                        	<button id="connect" class="pm-button pm-button--primary">Se connecter</button>
	                    	</div>                                        	
	                        <input type="hidden" name="node" id="newnode" value="" />
                        	<input type="hidden" name="country" id="newcountry" value="" />                     
	                	</div>
					</div> 
                	<div class="flex flex-column flex-items-center">
                       	<div id="missing-node" class="mb1 p0">
                           	<strong class="bl1 mb1">Erreur</strong>
                           	<div class="flex flex-nowrap flex-items-center">
                               	<p class="flex-item-fluid mt0 mb0 pr2">Il faut choisir un node avant de pouvoir s'y connecter</p>
                               	<button id="btn-close-error" class="pm-button pm-button--primary pm-button--small flex-item-noshrink">Fermer</button>
                           	</div>                    
						</div>                
	                </div>   
                	<?php foreach($nodes as $key=>$node){ ?>
    	        	<section class="container-section-sticky-section p0 mb1">       					                    
						<div class="fflex-nowrap h3 m1 p0"><?php echo $iso[$key]; ?></div>                    	
                    	<div class="flex mt0 mb0 pr1 pl1">
		    			<?php foreach($node as $to){ ?>	
	                    <button class="pm-node pm-button btn-node btn-node-<?php echo $to;?>" value="<?php echo $key."-".$to; ?>"><?php echo $iso[$to];?></button>
						<?php } ?>      
                    	</div>		
    	        	</section>
                    <?php } ?>      
        		</main>
			</div>
    	</div>
	</div>
</div>
</body>
</html>