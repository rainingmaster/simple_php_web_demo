<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php
/*function net_match_mask($net,$mask)
{
	$net_array = explode(".",$net);
	$mask_array = explode(".",$mask);
	$add_zero = "0";
	$match_result = 1;
	$not_zero= array("1");
	
	for($i=0;$i<4;$i++)
	{
		$net_array_bin[$i] = decbin($net_array[$i]);
		$mask_array_bin[$i] = decbin($mask_array[$i]);
		$len1 = strlen($net_array_bin[$i]);
		for($j=0;$j<8-$len1;$j++)
			$net_array_bin[$i] = $add_zero."".$net_array_bin[$i];
		$len2 = strlen($mask_array_bin[$i]);
		for($j=0;$j<8-$len2;$j++)
			$mask_array_bin[$i] = $add_zero."".$mask_array_bin[$i];
	}

	$net_bin =  $net_array_bin[0]."".$net_array_bin[1]."".$net_array_bin[2]."".$net_array_bin[3];
	$mask_bin = $mask_array_bin[0]."".$mask_array_bin[1]."".$mask_array_bin[2]."".$mask_array_bin[3];
	$length = substr_count($mask_bin, '0'); 
	$net_check = substr($net_bin,-$length);

	for($i=0;$i<strlen($net_check);$i++)
	{
		if(in_array(substr($net_check,$i,1), $not_zero))
			$match_result = 0;
	}

	if($length == 0)
		$match_result = 1;

	return $match_result;	
}*/

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
$Pool_net =$_POST['Pool_net'];
$Pool_mask =$_POST['Pool_mask'];

if(filter_var($Pool_net, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($Pool_mask, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
{
	$mask_check_result = mask_check($Pool_mask);
	//$net_match_mask_result = net_match_mask($Pool_net,$Pool_mask);
	if($mask_check_result==1)
	{
		snmp_set_quick_print(1);
		snmpset($Board_IP, "public",".1.3.6.1.4.1.54322.3.1.2.0","a","$Pool_net");
		snmpset($Board_IP, "public",".1.3.6.1.4.1.54322.3.1.3.0","a","$Pool_mask");
	}
	else
	{
		?>
		<script>window.alert('地址池输入有误，请重新配置！');</script>
		<?php
	}
}
else
{
		?>
		<script>window.alert('地址池输入有误，请重新配置！');</script>
		<?php
}
?>
<script>location.href="nat64_information.php?Board_IP=<?php echo $Board_IP; ?>"</script>
</body>

