<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD><TITLE>CSS导航</TITLE>
<META content="text/html; charset=gb2312" http-equiv=Content-Type>
<style>
<SCRIPT src="js/menu.js" type=text/javascript></SCRIPT>
body {
	font: 20px/1.6em Tahoma,Verdana;
	margin-top: 10px; 
	margin-right: 10px; 
	margin-bottom: 10px; 
	margin-left: 10px;
	line-height: 16.8px;
       background-color: #606061;
	}
#nav {
	Z-INDEX: 500; PADDING-BOTTOM: 0px; LIST-STYLE-TYPE: none; MARGIN: 0px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px;  HEIGHT: 30px; LIST-STYLE-IMAGE: none; PADDING-TOP: 15px;background-color:#49A3FF;
}
#nav LI.top {
	DISPLAY: block; FLOAT: left; HEIGHT: 30px;background-color:#49A3FF;
}
#nav LI A.top_link {
	PADDING-BOTTOM: 0px; LINE-HEIGHT: 30px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; DISPLAY: block; FLOAT: left; HEIGHT: 30px; FONT-SIZE: 12px; CURSOR: pointer; FONT-WEIGHT: bold; TEXT-DECORATION: none; PADDING-TOP: 0px
}
#nav LI A.top_link SPAN {
	PADDING-BOTTOM: 0px; WIDTH: 120px; DISPLAY: block; text-align:center; FLOAT: left; HEIGHT: 27px; PADDING-TOP: 0px;background-color:#49A3FF;
}
#nav LI A.top_link SPAN.down {
	PADDING-BOTTOM: 0px; WIDTH: 110px; DISPLAY: block; text-align:center; background-color:#49A3FF; no-repeat right; FLOAT: left; HEIGHT: 27px; PADDING-TOP: 0px
}
#nav LI:hover A.top_link {
	background-color:#49A3FF; color:#548bcf;
}
#nav LI:hover A.top_link SPAN {
	background-color:#49A3FF; color:#548bcf;
}
#nav LI:hover A.top_link SPAN.down {
	background-color:#548bcf; color:#fff2ee;BACKGROUND: url(/jscss/demoimg/201010/three1a.gif) no-repeat right;
}
#nav LI:hover {
	Z-INDEX: 200; POSITION: relative
}
#nav LI:hover UL.sub {
	Z-INDEX: 270; BORDER-BOTTOM: #ddd 1px solid; BORDER-LEFT: #ddd 1px solid; PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; WIDTH: 150px; PADDING-RIGHT: 0px; WHITE-SPACE: nowrap; BACKGROUND: #e1ecf6; HEIGHT: auto; BORDER-TOP: #ddd 1px solid; TOP: 27px; BORDER-RIGHT: #ddd 1px solid; PADDING-TOP: 0px; LEFT: 1px
}
#nav LI:hover UL.sub LI {
	POSITION: relative; WIDTH: 150px; DISPLAY: block; FLOAT: left; HEIGHT: 19px; FONT-WEIGHT: normal
}
#nav LI:hover UL.sub LI A {
	BORDER-BOTTOM: 0px; BORDER-LEFT: 0px; LINE-HEIGHT: 19px; TEXT-INDENT: 5px; WIDTH: 150px; DISPLAY: block; HEIGHT: 19px; COLOR: #000; FONT-SIZE: 12px; BORDER-TOP: 0px; BORDER-RIGHT: 0px; TEXT-DECORATION: none
}
#nav LI UL.sub LI A.fly {
	BACKGROUND: url(/jscss/demoimg/201010/arrow.gif) #e1ecf6 no-repeat left center
}
#nav LI:hover UL.sub LI A:hover {
	BACKGROUND: url(/jscss/demoimg/201010/subli_bg.gif) #548bcf repeat-x center center; COLOR: #fff; 
}
#nav LI:hover UL.sub LI A.fly:hover {
	BACKGROUND: url(/jscss/demoimg/201010/arrow_over.gif) #548bcf no-repeat left center; COLOR: #fff
}
#nav LI:hover LI:hover UL {
	Z-INDEX: 400; BORDER-BOTTOM: #ddd 1px solid; BORDER-LEFT: #ddd 1px solid; PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; WIDTH: 150px; PADDING-RIGHT: 0px; WHITE-SPACE: nowrap; BACKGROUND: #e1ecf6; HEIGHT: auto; BORDER-TOP: #ddd 1px solid; TOP: -1px; BORDER-RIGHT: #ddd 1px solid; PADDING-TOP: 0px; LEFT: 144px
}
#nav LI:hover LI:hover LI:hover UL {
	Z-INDEX: 400; BORDER-BOTTOM: #ddd 1px solid; BORDER-LEFT: #ddd 1px solid; PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; WIDTH: 150px; PADDING-RIGHT: 0px; WHITE-SPACE: nowrap; BACKGROUND: #e1ecf6; HEIGHT: auto; BORDER-TOP: #ddd 1px solid; TOP: -1px; BORDER-RIGHT: #ddd 1px solid; PADDING-TOP: 0px; LEFT: 144px
}
#nav LI:hover LI:hover LI:hover LI:hover UL {
	Z-INDEX: 400; BORDER-BOTTOM: #ddd 1px solid; BORDER-LEFT: #ddd 1px solid; PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; WIDTH: 150px; PADDING-RIGHT: 0px; WHITE-SPACE: nowrap; BACKGROUND: #e1ecf6; HEIGHT: auto; BORDER-TOP: #ddd 1px solid; TOP: -1px; BORDER-RIGHT: #ddd 1px solid; PADDING-TOP: 0px; LEFT: 144px
}
#nav LI:hover LI:hover LI:hover LI:hover LI:hover UL {
	Z-INDEX: 400; BORDER-BOTTOM: #ddd 1px solid; BORDER-LEFT: #ddd 1px solid; PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; WIDTH: 150px; PADDING-RIGHT: 0px; WHITE-SPACE: nowrap; BACKGROUND: #e1ecf6; HEIGHT: auto; BORDER-TOP: #ddd 1px solid; TOP: -1px; BORDER-RIGHT: #ddd 1px solid; PADDING-TOP: 0px; LEFT: 144px
}
#nav UL {
	POSITION: absolute; PADDING-BOTTOM: 0px; LIST-STYLE-TYPE: none; MARGIN: 0px; PADDING-LEFT: 0px; WIDTH: 0px; PADDING-RIGHT: 0px; HEIGHT: 0px; TOP: -9999px; LIST-STYLE-IMAGE: none; PADDING-TOP: 0px; LEFT: -9999px
}
#nav LI:hover UL UL {
	POSITION: absolute; PADDING-BOTTOM: 0px; LIST-STYLE-TYPE: none; MARGIN: 0px; PADDING-LEFT: 0px; WIDTH: 0px; PADDING-RIGHT: 0px; HEIGHT: 0px; TOP: -9999px; LIST-STYLE-IMAGE: none; PADDING-TOP: 0px; LEFT: -9999px
}
#nav LI:hover LI:hover UL UL {
	POSITION: absolute; PADDING-BOTTOM: 0px; LIST-STYLE-TYPE: none; MARGIN: 0px; PADDING-LEFT: 0px; WIDTH: 0px; PADDING-RIGHT: 0px; HEIGHT: 0px; TOP: -9999px; LIST-STYLE-IMAGE: none; PADDING-TOP: 0px; LEFT: -9999px
}
#nav LI:hover LI:hover LI:hover UL UL {
	POSITION: absolute; PADDING-BOTTOM: 0px; LIST-STYLE-TYPE: none; MARGIN: 0px; PADDING-LEFT: 0px; WIDTH: 0px; PADDING-RIGHT: 0px; HEIGHT: 0px; TOP: -9999px; LIST-STYLE-IMAGE: none; PADDING-TOP: 0px; LEFT: -9999px
}
#nav LI:hover LI:hover LI:hover LI:hover UL UL {
	POSITION: absolute; PADDING-BOTTOM: 0px; LIST-STYLE-TYPE: none; MARGIN: 0px; PADDING-LEFT: 0px; WIDTH: 0px; PADDING-RIGHT: 0px; HEIGHT: 0px; TOP: -9999px; LIST-STYLE-IMAGE: none; PADDING-TOP: 0px; LEFT: -9999px
}
#nav LI:hover LI:hover A.fly {
	BORDER-BOTTOM-COLOR: #fff; BORDER-TOP-COLOR: #fff; BACKGROUND: url(/jscss/demoimg/201010/arrow_over.gif) #548bcf no-repeat left center; COLOR: #fff; BORDER-RIGHT-COLOR: #fff; BORDER-LEFT-COLOR: #fff; 
}
#nav LI:hover LI:hover LI:hover A.fly {
	BORDER-BOTTOM-COLOR: #fff; BORDER-TOP-COLOR: #fff; BACKGROUND: url(/jscss/demoimg/201010/arrow_over.gif) #548bcf no-repeat left center; COLOR: #fff; BORDER-RIGHT-COLOR: #fff; BORDER-LEFT-COLOR: #fff; 
}
#nav LI:hover LI:hover LI:hover LI:hover A.fly {
	BORDER-BOTTOM-COLOR: #fff; BORDER-TOP-COLOR: #fff; BACKGROUND: url(/jscss/demoimg/201010/arrow_over.gif) #548bcf no-repeat left center; COLOR: #fff; BORDER-RIGHT-COLOR: #fff; BORDER-LEFT-COLOR: #fff; 
}
#nav LI:hover LI:hover LI:hover LI:hover LI:hover A.fly {
	BORDER-BOTTOM-COLOR: #fff; BORDER-TOP-COLOR: #fff; BACKGROUND: url(/jscss/demoimg/201010/arrow_over.gif) #548bcf no-repeat left center; COLOR: #fff; BORDER-RIGHT-COLOR: #fff; BORDER-LEFT-COLOR: #fff; 
}
#nav LI:hover LI:hover LI A.fly {
	BORDER-BOTTOM-COLOR: #e1ecf6; BORDER-TOP-COLOR: #e1ecf6; BACKGROUND: url(/jscss/demoimg/201010/arrow.gif) #e1ecf6 no-repeat left center; COLOR: #000; BORDER-RIGHT-COLOR: #e1ecf6; BORDER-LEFT-COLOR: #e1ecf6
}
#nav LI:hover LI:hover LI:hover LI A.fly {
	BORDER-BOTTOM-COLOR: #e1ecf6; BORDER-TOP-COLOR: #e1ecf6; BACKGROUND: url(/jscss/demoimg/201010/arrow.gif) #e1ecf6 no-repeat left center; COLOR: #000; BORDER-RIGHT-COLOR: #e1ecf6; BORDER-LEFT-COLOR: #e1ecf6
}
#nav LI:hover LI:hover LI:hover LI:hover LI A.fly {
	BORDER-BOTTOM-COLOR: #e1ecf6; BORDER-TOP-COLOR: #e1ecf6; BACKGROUND: url(/jscss/demoimg/201010/arrow.gif) #e1ecf6 no-repeat left center; COLOR: #000; BORDER-RIGHT-COLOR: #e1ecf6; BORDER-LEFT-COLOR: #e1ecf6
}

</style>
<style>
body { background-color:#49A3FF; }
</style>
</HEAD>
<BODY>

<UL id=nav>

  <LI class=top><A id=products class=top_link 
  href="mainpage.php" target="_top"><SPAN 
  class=down><FONT SIZE=3>首 页</FONT></SPAN></A> </LI>

  <LI class=top><A id=products class=top_link 
  href="equipment_main.php" target="_top"><SPAN 
  class=down><FONT SIZE=3>设备管理</FONT></SPAN></A></LI>

  <LI class=top><A id=hr class=top_link href="system_main.php" target="_top"><SPAN 
  class=down><FONT SIZE=3>系统管理</SPAN></A></LI>

  <LI class=top><A id=hr class=top_link href="route_main.php" target="_top"><SPAN 
  class=down><FONT SIZE=3>路由管理</SPAN></A></LI>

  <LI class=top><A id=investors class=top_link 
  href="transition_main.php" target="_top" ><SPAN class=down><FONT SIZE=3>过渡协议管理</SPAN></A></LI>

  <LI class=top><A id=contacts class=top_link 
  href="application_main.php" target="_top"><SPAN class=down><FONT SIZE=3>应用管理</SPAN></A></LI>

  <LI class=top><A id=investors class=top_link 
  href="safety_main.php" target="_top"><SPAN class=down><FONT SIZE=3>安全管理</SPAN></A></LI>

  <LI class=top><A id=investors class=top_link 
  href="user_main.php" target="_top"><SPAN class=down><FONT SIZE=3>用户管理</SPAN></A></LI>

  <LI class=top><A id=investors class=top_link 
  href="login.php" target="_parent" onClick=""><SPAN class=down><FONT SIZE=3>退 出</SPAN></A></LI>

</BODY></HTML>
