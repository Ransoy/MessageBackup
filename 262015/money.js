function initForm(){
	setMoney();
	if(document.getElementByName("money_init").value == "0"){
		return;
		}
	document.getElementByName("money").value = document.getElementByName("money_init").value;
	}

function funcSubmit(){
	if(!document.getElementByName("agree")[0].checked){
		alert("本サービスをご利用いただくには規約に同意していただく必要があります");
		return false;
		}
	document.registration.submit();
	}
function setMoney(){
	document.getElementById("money").length = 0;
	if(document.getElementByName("pay").value == "0"){
		AddMoney("--選択--",0);
		return;
		}
	if(document.getElementByName("pay").value == "3" || document.getElementByName("pay").value == "5" || document.rgetElementByName("pay").value == "7" || document.getElementByName("pay").value == "8" || document.getElementByName("pay").value == "9"){
		AddMoney("--選択--",0);
		AddMoney("3,000円",3000);
		AddMoney("5,000円",5000);
		AddMoney("10,000円",10000);
		AddMoney("20,000円",20000);
		AddMoney("30,000円",30000);
		return;
		}
	if(document.getElementByName("pay").value == "1"){
		AddMoney("--選択--",0);
		AddMoney("3,150円",3150);
		AddMoney("5,250円",5250);
		AddMoney("10,500円",10500);
		AddMoney("21,000円",21000);
		AddMoney("31,500円",31500);
		AddMoney("52,500円",52500);
		return;
		}
	AddMoney("--選択--",0);
	AddMoney("3,150円",3150);
	AddMoney("5,250円",5250);
	AddMoney("10,500円",10500);
	AddMoney("21,000円",21000);
	AddMoney("31,500円",31500);
	}
function AddMoney(text,value){ 
	document.getElementByName("money").length++;
	document.getElementByName("money").options[document.getElementByName("money").length-1].value = value; 
	document.getElementByName("money").options[document.getElementByName("money").length-1].text = text; 
	}