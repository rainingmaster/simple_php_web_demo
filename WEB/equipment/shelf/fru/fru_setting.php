<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
</head>
<body>
<?php		
$fru_ipmb_and_id = $_POST['fru_ipmb_and_id'];
$control = $_POST['control'];
$piecesconf=explode("+",$fru_ipmb_and_id);
echo "�������ã����Ժ�...";

include('../../atca.class.php');
//�������������Ҫ����Ϊfalse����ȥ��ȡ��������Ϣ������ر�֮���޷��ٴο���
$fru = new Fru($piecesconf[0], $piecesconf[1], false);
if($control == 1)
	$fru->startFru();
if($control == 2)
	$fru->shutdownFru();
?>
<script>location.href="fru.php"</script>
</body>

