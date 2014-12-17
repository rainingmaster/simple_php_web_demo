<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php		
function mask_check($ip)
{
	$mask_piece = explode(".",$ip);
	$valid_mask = array("255", "254", "252", "248","240","224","192","128");
	$valid_mask_zero = array("0");
	$valid_mask_all = array("255", "254", "252", "248","240","224","192","128","0");

	if (in_array($mask_piece[0], $valid_mask)) 
	{
    		if(strcmp($mask_piece[0],"255") == 0)
		{
			if(in_array($mask_piece[1], $valid_mask_all))
			{
				if(strcmp($mask_piece[1],"255") == 0)
				{
					if(strcmp($mask_piece[2],"255") == 0)
					{
						if( in_array($mask_piece[3], $valid_mask_all) )
							return 1;
						else 
							return 0;
					}
					else
					{
						if(in_array($mask_piece[2], $valid_mask_all) && strcmp($mask_piece[3],"0") == 0)
							return 1;
						else
							return 0;
					}
				}
				else
				{
					if( in_array($mask_piece[1], $valid_mask_all) && in_array($mask_piece[2], $valid_mask_zero) && in_array($mask_piece[3], $valid_mask_zero))
						return 1;
					else 
						return 0;
				}
			}
			else
				return 0;	
		}
		else
		{
			if( in_array($mask_piece[0], $valid_mask) && in_array($mask_piece[1], $valid_mask_zero) && in_array($mask_piece[2], $valid_mask_zero) && in_array($mask_piece[3], $valid_mask_zero))
				return 1;
			else 
				return 0;
		}
	}
	else
		return 0;

}
$Board_IP =$_GET['Board_IP'];	
$IPv4_address =$_POST['IPv4_address'];
$IPv4_mask =$_POST['IPv4_mask'];

if(filter_var($IPv4_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($IPv4_mask, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
{
	$mask_check_result = mask_check($IPv4_mask);
	if($mask_check_result==1)
	{
		snmp_set_quick_print(1);
		snmpset($Board_IP, "public",".1.3.6.1.4.1.54321.1.1.0","a","$IPv4_address");
		snmpset($Board_IP, "public",".1.3.6.1.4.1.54321.1.2.0","a","$IPv4_mask");
	}
	else
	{
		?>
		<script>window.alert('IPv4地址输入有误，请重新配置！');</script>
		<?php
	}
}
else
{
		?>
		<script>window.alert('IPv4地址输入有误，请重新配置！');</script>
		<?php
}
?>
<script>location.href="ivi_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

