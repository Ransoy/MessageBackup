function initForm(){
	setMoney();
	if(document.getElementByName("money_init").value == "0"){
		return;
		}
	document.getElementByName("money").value = document.getElementByName("money_init").value;
	}

function funcSubmit(){
	if(!document.getElementByName("agree")[0].checked){
		alert("�{�T�[�r�X�������p���������ɂ͋K��ɓ��ӂ��Ă��������K�v������܂�");
		return false;
		}
	document.registration.submit();
	}
function setMoney(){
	document.getElementById("money").length = 0;
	if(document.getElementByName("pay").value == "0"){
		AddMoney("--�I��--",0);
		return;
		}
	if(document.getElementByName("pay").value == "3" || document.getElementByName("pay").value == "5" || document.rgetElementByName("pay").value == "7" || document.getElementByName("pay").value == "8" || document.getElementByName("pay").value == "9"){
		AddMoney("--�I��--",0);
		AddMoney("3,000�~",3000);
		AddMoney("5,000�~",5000);
		AddMoney("10,000�~",10000);
		AddMoney("20,000�~",20000);
		AddMoney("30,000�~",30000);
		return;
		}
	if(document.getElementByName("pay").value == "1"){
		AddMoney("--�I��--",0);
		AddMoney("3,150�~",3150);
		AddMoney("5,250�~",5250);
		AddMoney("10,500�~",10500);
		AddMoney("21,000�~",21000);
		AddMoney("31,500�~",31500);
		AddMoney("52,500�~",52500);
		return;
		}
	AddMoney("--�I��--",0);
	AddMoney("3,150�~",3150);
	AddMoney("5,250�~",5250);
	AddMoney("10,500�~",10500);
	AddMoney("21,000�~",21000);
	AddMoney("31,500�~",31500);
	}
function AddMoney(text,value){ 
	document.getElementByName("money").length++;
	document.getElementByName("money").options[document.getElementByName("money").length-1].value = value; 
	document.getElementByName("money").options[document.getElementByName("money").length-1].text = text; 
	}