<?php
if ((defined('WECVERSION')) && (WECVERSION >= 3)) {
$wec_groups['LIFX DIMMER'] = array(
                                      'displaypri' => 0,
                                      'collapsible' => TRUE,
                                      'iscollapsed' => TRUE);

$wec_options['LIFXDIMMER'] = array('configname' => 'LIFXDIMMER',
                                                  'configdesc' => "Enable LIFX DIMMER:",
                                                  'longdesc' => "Enable LIFX DIMMER:",
                                                  'group' => 'LIFX DIMMER',
                                                  'type' => WECT_SELECT,
                                                  'page' => WECP_APPS,                                                                                                                                                                                          
                                                  'availval' => array('OFF','ON'),
                                                  'availvalname' => array('OFF','ON'),
                                                  'defaultval' => 'OFF',                                                    
                                                  'currentval' => '',
                                                                                                  'displaypri' => 0,
                                                                                                  'postsavehook' => 'LIFXDIMMER_reload');
																								  
}       

function LIFXDIMMER_reload($wec_option_arr,$setting) {
   system("sudo /apps/lifxdimmer/etc/init.d/S99lifxdimmer restart");
}                                                                 
?>