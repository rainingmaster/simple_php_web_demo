<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>设备管理菜单</title>
<style>
*, html { font-family: Verdana, Arial, Helvetica, sans-serif; }
body, form, ul, li, p, h1, h2, h3, h4, h5
{
	margin: 0;
	padding: 0;
}
body { background-color: #CCEEEE; }
img { border: none; }
p
{
	font-size: 1em;
	margin: 0 0 1em 0;
}

html { font-size: 100%; height: 100%; /* IE hack */ }
body { font-size: 0.8em; } /* Base font 12px */
table { font-size: 100%; /* IE hack */ }

input, select, textarea, th, td { font-size:1em; }

/* CSS Accordion styles */
dl
{
	padding: 12px;
	max-width: 160px;
}
	a.ie { background: transparent; text-decoration: none; }
	dl dt
	{
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border: 1px solid #0000cc;
		margin: 0;
	}
		dl dt a,
		dl a.ie dt
		{
			color: #0000CC;
			font-weight: bold;
			text-decoration: none;
			padding: 10px;
			display: block;
		}
	dl dd
	{
		color: #0000CC;
		margin: 0;
		height: 0;
		overflow: hidden;
		-webkit-transition: height 1s ease;
	}
		dl dd p
		{
			padding: 2px;
			margin: 0;
		}
	dl dd:target
	{
		height: auto;
	}
	dl a.ie:hover dd,
	dl a.ie:focus dd
	{
		height: auto;
		color: #0000CC; !important;
	}
	
@media (-webkit-transition) {
	dl dd:target
	{
		height: 9em;
	}
}
</style>
</head>
<body>
	<dl>
		<dt><a href="equipment/shelf/overall.php" target="frame2">机箱装配信息</a></dt>
		<dt><a href="equipment/shelf/fan/fan.php" target="frame2">风扇管理</a></dt>
		<dt><a href="equipment/shelf/voltage/voltage.php" target="frame2">电压管理</a></dt>
		<dt><a href="equipment/shelf/temperature/temperature.php" target="frame2">温度管理</a></dt>
		<dt><a href="equipment/shelf/fru/fru.php" target="frame2">FRU管理</a></dt>
		<dt><a href="equipment/shelf/log/log.php" target="frame2">日志信息</a></dt>
	</dl>
</body>
</html>
