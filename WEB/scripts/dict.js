function stateDict(state) {
	switch(state) {
		case 0:
			return "未安装";
		case 1:
			return "非活动";
		case 2:
			return "激活请求";
		case 3:
			return "正在激活";
		case 4:
			return "FRU活动";
		case 5:
			return "取消激活请求";
		case 6:
			return "正在取消激活";
		case 7:
			return "通信丢失";
		default:
			return "wrong data:"+state;
	}
}

function frutypeDict(frutype) { 
	switch(frutype) {
		case 0:
			return "ATCA单板";
		case 1:
			return "电源输入模块";
		case 2:
			return "机框FRU信息";
		case 3:
			return "ShMC";
		case 4:
			return "风扇托盘";
		case 5:
			return "风扇过滤器托盘";
		case 192:
			return "BMC模块";
		default:
			return "num is"+frutype+",need updata dictionary";
	}
}