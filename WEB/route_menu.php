<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>路由管理菜单</title>
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
		<dt><a href="#Section1">RIP</a></dt>
		<dd id="Section1">
			<p>
			<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"route")==0)
					{
			?>
						<B><A  href="rip/rip_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B>
			<?php
					}
				}
			?>
                     	</p>
		</dd>
		<dt><a href="#Section2">RIPNG</a></dt>
		<dd id="Section2">
			<p>
			<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"route")==0)
					{
			?>
						<B><A  href="ripng/ripng_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B>
                     	<?php
					}
				}
			?>
			</p>
		</dd>
		<dt><a href="#Section3">OSPF</a></dt>
		<dd id="Section3">
			<p>
			<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"route")==0)
					{
			?>
						<B><A  href="ospf/ospf_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B>
                     	<?php
					}
				}
			?>
                     	</p>
		</dd>
		<dt><a href="#Section4">OSPF6</a></dt>
		<dd id="Section4">
			<p>
			<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"route")==0)
					{
			?>
						<B><A  href="ospf6/ospf6_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B> 
                     	<?php
					}
				}
			?>
                     	</p>
		</dd>
		<dt><a href="#Section5">BGP</a></dt>
		<dd id="Section5">
			<p>
			<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"route")==0)
					{
			?>
						<B><A  href="bgp/bgp_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B> 
                     	<?php
					}
				}
			?>
                     	</p>
		</dd>
		<dt><a href="#Section6">ISIS</a></dt>
		<dd id="Section6">
			<p>
			<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"route")==0)
					{
			?>
	  					<B><A  href="isis/isis_information.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B> 
                     	<?php
					}
				}
			?>
                     	</p>
		</dd>
		<dt><a href="#Section7">静态路由</a></dt>
		<dd id="Section7">
			<p>
			<?php
				for($i = 1; $i <= $board_count; $i++)
				{
					if(strcmp($ini->iniRead("board".$i, "function"),"all")==0 || strcmp($ini->iniRead("board".$i, "function"),"route")==0)
					{
			?>
	  					<B><A  href="static_route/static_route.php?Board_IP=<?php echo $ini->iniRead("board".$i, "ipaddr"); ?>" target="frame2">board<?php echo $i; ?>:<?php echo $ini->iniRead("board".$i, "ipaddr"); ?></A></B> 
                     	<?php
					}
				}
			?>
                     	</p>
		</dd>
	</dl>
</body>
</html>
