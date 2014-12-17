<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>过渡管理菜单</title>
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
		height: 7em;
	}
}
</style>
<title>Accordian菜单，手风琴菜单，折叠菜单代码</title>
</head>
<body>
<?php
	include('include/ini.class.php');
	$ini = new ini();
	$board_count = $ini->iniRead("device_info", "board_count");
?>
	<dl>
		<dt><a href="#Section1">Nat-PT</a></dt>
		<dd id="Section1">
			<p>
                     	<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"transition")==0)
					{
			?>
						<B><A  href="natpt/natpt_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B>
			<?php
					}
				}
			?>
			</p>
		</dd>
		<dt><a href="#Section2">NAT64</a></dt>
		<dd id="Section2">
			<p>
                     	<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"transition")==0)
					{
			?>
						<B><A  href="nat64/nat64_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B>
			<?php
					}
				}
			?>
                     	</p>
		</dd>
		<dt><a href="#Section3">IVI</a></dt>
		<dd id="Section3">
			<p>
                     	<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"transition")==0)
					{
			?>
						<B><A  href="ivi/ivi_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B>
			<?php
					}
				}
			?>
                     	</p>
		</dd>
		<dt><a href="#Section4">DS-Lite</a></dt>
		<dd id="Section4">
			<p>
                     	<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"transition")==0)
					{
			?>
						<B><A  href="dslite/dslite_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B> 
			<?php
					}
				}
			?>
                     	</p>
		</dd>
		<dt><a href="#Section5">ECDYSIS</a></dt>
		<dd id="Section5">
			<p>
                     	<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"transition")==0)
					{
			?>
						<B><A  href="ecdysis/ecdysis_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B> 
			<?php
					}
				}
			?>
                     	</p>
		</dd>
	</dl>
</body>
</html>
