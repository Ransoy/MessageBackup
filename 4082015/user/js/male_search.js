var value_opacity; // �����x��ێ�����ϐ�
var elem_target;
elem_target = null;
var elem_txt_target;
var innertxt=new Array(
	'����l�����O�C���������ԂŌ����\�B�܂��T�C�g�ŗV��ł��邩���m��Ȃ�����l�փ��[�����M', //�ŏI���O�C��
	'�a�����͓��ʂȓ��B��A�l�̒a�����͕K��CHECK���Ă����܂��傤�I', // �a����
	'�Q�鎞�Ԃ̂R���ԑO���炢���t�Z���Ă��d���A���܂�����^�C����_���ă��[���𑗂��Ă݂悤�I', // �Q�鎞��
	'�}�C�L�[���[�h���Q�l�Ƀ��[�����e���l���悤�I������̉���l�Ƃ͘b������オ��\����I', // �}�C�L�[���[�h
	'�ߋ��ɓ������Ē���������l���`���b�g�l�[���Ō����ł��܂��B', //�`���b�g�l�[��
	'',
	'',
	'�}�V�F���ɂ��o�^�����Ă��邨�q�l�ł��B�܂��͎��g��m���Ă��炤�ׂɎ��ȏЉ�₨�U���̃��[���𑗐M���܂��傤�B', //�S���
	'���o�^���Ă܂����C�ɓ���̃p�t�H�[�}�[������������Ȃ�����l�ł��̂ő_���ڂ̉���l�ł��I���񂨗U�����Ă݂Ă��������I', //�������
	'�|�\�C�x���g�Q�������Ă��Ē���I�Ƀ}�V�F���ɗ��Ē����Ă���܂��̂Ő��񂨗U�����������I�|�\�C�x���g�ɂ��ĐG��Ă݂�̂��ǂ���������܂���' //�|�\�C�x���g
);

function onevent(i,e) {
	if(document.all){
		elem_target.style.left = e.x - 40 + "px";
		elem_target.style.top = e.y+ 20 + "px";
	}else{
		elem_target.style.left = e.pageX-40 + "px";
		elem_target.style.top = e.pageY+20 + "px";
	}
	elem_txt_target.innerHTML = innertxt[i];
	elem_target.style.display="block";
	value_opacity = 0;
	setOpacityZero();
	return false;
}
function Balreset(e){
	elem_target.style.display = "none";
	elem_target.style.opacity=0;
	elem_txt_target.innerHTML = "";
}

// �����x��10�ɂȂ�܂�+1���Z����i�^�C�}�[�j
function setOpacityZero() {
	if(value_opacity < 10){
		value_opacity += 3.66;
		setTimeout('setOpacityZero()',80);
		setOpacity();
	}
}
// �����x���Z�b�g���鏈��
function setOpacity () {
	elem_target.style.filter = 'alpha(opacity=' + (value_opacity * 10) + ')';
	elem_target.style.MozOpacity = value_opacity / 10;
	elem_target.style.opacity = value_opacity / 10;
}

// �C�x���g�̏���������
function initalizeEvents() {
	elem_target = document.getElementById("balloon");
	elem_txt_target = document.getElementById("inBalloontxt");
}