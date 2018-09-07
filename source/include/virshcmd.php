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

	$generatedXML = printf("<hostdev mode='subsystem' type='pci' managed='yes'>
      <driver name='vfio'/>
      <source>
        <address domain='0x0000' bus='0x0b' slot='0x00' function='0x0'/>
      </source>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x06' function='0x0'/>
    </hostdev>", $deviceHexID);

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
