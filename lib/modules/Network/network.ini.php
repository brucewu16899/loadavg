; <?php exit(); __halt_compiler(); ?>
[module]
name = "Network Usage"
description = "This module is used to display and log network usage data."
interface = "true"
tabbed = "true"
has_settings = "true"
logable = "true"
depth = 2
[logging]
args[] = '{"logfile":"network_%s_%s.log","function":"logData"}'
[chart]
args[] = '{"type":"Transmit","id":"tranfer_rate","logfile":"network_%s_%s.log","function":"getTransferRateData","chart_function":"network", "label":"Transmit Rate"}'
args[] = '{"type":"Receive","id":"receive_rate","logfile":"network_%s_%s.log","function":"getReceiveRateData","chart_function":"network", "label":"Receive Rate"}'
[info]
line[] = '{"format":"","args":"","type":"file","filename":"views/links.php"}'
line[] = '{"format":"High (%s): %s KB/s","args":"net_high_time|net_high"}'
line[] = '{"format":"Low (%s): %s KB/s","args":"net_low_time|net_low"}'
line[] = '{"format":"Mean: %s KB/s","args":"net_mean"}'
line[] = '{"format":"Latest: %s KB/s","args":"net_latest"}'
line[] = '{"format":"Todays estimate: %s %s","args":"net_estimate|net_estimate_units"}'
[settings]
threshold_transfer = 15
threshold_receive = 15
transfer_cutoff = 16
receive_cutoff = 16
receive_limiting = "false"
transfer_limiting = "false"