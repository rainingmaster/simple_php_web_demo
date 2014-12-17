<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>安全管理菜单</title>
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
body { font-size: 0.75em; } /* Base font 12px */
table { font-size: 100%; /* IE hack */ }

input, select, textarea, th, td { font-size:1em; }

/* CSS Accordion styles */
dl
{
	padding: 10px;
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
			padding: 10px;
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
		height: 6.667em;
	}
}
</style>
<title>Accordian菜单，手风琴菜单，折叠菜单代码</title>
</head>
<body>
	
	<dl>
		<dt><a href="#Section1">TePA设置</a></dt>
		<dt><a href="#Section2">SAVI设置</a></dt>
	</dl>
</body>
</html>