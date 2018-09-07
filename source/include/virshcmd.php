<?PHP
/*
 *  Execute Virsh Command
 */
?>

<?
$vmname = $_POST['VMNAME'];
$deviceID = $_POST['DEVICEID'];
$generatedXML = '';

if (!empty($deviceID))
{
	$deviceHexID = explode(':', $deviceID);

	var_dump($deviceHexID);

	if(!empty($deviceHexID[1])) {
		$deviceAddress = explode('.', $deviceHexID[1]);
		var_dump($deviceAddress);
		$generatedXML = sprintf("
			<hostdev mode='subsystem' type='pci' managed='yes'>
	      <source>
	        <address domain='0x0000' bus='0x0b' slot='0x00' function='0x0'/>
	      </source>
	    </hostdev>"
		, $deviceHexID);
	}

	$generatedXML .= "<hostdev mode='subsystem' type='usb'>
<source>
<vendor id='0x".$deviceHexID[0]."'/>
<product id='0x".$deviceHexID[1]."'/>
</source>
</hostdev>";
}

die('DONE');
file_put_contents('/tmp/libvirthotplugpci.xml',$generatedXML);

switch ($_POST['action']) {
	case 'detach':
		$rc = shell_exec("/usr/sbin/virsh detach-device '$vmname' /tmp/libvirthotplugpci.xml 2>&1");
		break;

	case 'attach':
		$rc = shell_exec("/usr/sbin/virsh attach-device '$vmname' /tmp/libvirthotplugpci.xml 2>&1");
		break;
}

echo $rc;
?>
