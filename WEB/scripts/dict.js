function stateDict(state) {
	switch(state) {
		case 0:
			return "δ��װ";
		case 1:
			return "�ǻ";
		case 2:
			return "��������";
		case 3:
			return "���ڼ���";
		case 4:
			return "FRU�";
		case 5:
			return "ȡ����������";
		case 6:
			return "����ȡ������";
		case 7:
			return "ͨ�Ŷ�ʧ";
		default:
			return "wrong data:"+state;
	}
}

function frutypeDict(frutype) { 
	switch(frutype) {
		case 0:
			return "ATCA����";
		case 1:
			return "��Դ����ģ��";
		case 2:
			return "����FRU��Ϣ";
		case 3:
			return "ShMC";
		case 4:
			return "��������";
		case 5:
			return "���ȹ���������";
		case 192:
			return "BMCģ��";
		default:
			return "num is"+frutype+",need updata dictionary";
	}
}