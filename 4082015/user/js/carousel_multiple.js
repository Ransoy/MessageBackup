$(function() {
	// �J���[�Z�����X�g����
	var carouselCloneP = $('.carousel-m-contents').clone();
	var carouselCloneA = $('.carousel-m-contents').clone();
	
	// �J���[�Z�����X�g��O��ɕ\���A�N���X���ǉ�
	carouselCloneP.prependTo('.carousel-m-slide').addClass('first');
	carouselCloneA.appendTo('.carousel-m-slide').addClass('last');
	
	// ���C���̃J���[�Z�����X�g�ɃN���X���ǉ�
	$('.carousel-m-contents').eq(1).addClass('main');
	// active�N���X�����ʒu�ݒ�
	$('.main').find('.carousel-m-banner').eq(0).addClass('active');
	
	// active�N���X��index
	var activeIndexM = $('.active').index();
	
	// �o�i�[1���̕�
	var bannerWidth = $('.carousel-m-img').width();
	
	// �o�i�[�̖���
	var banners = $('.main').children('li').length;
	
	// �o�i�[�S�̂̕�
	var bannersLength = bannerWidth * banners;
	
	// �p�t�H�[�}�[���X�g��
	var pfIndexM = $('.active .pf-list-wrapper-m').find('ul').length;	

	
	/*** CSS�ǉ� ***/
	// .carousel-m-base
	// �o�i�[������
	var bannerWidthHalf = -bannerWidth / 2;
	$('.carousel-m-base').css({
		'width' : bannerWidth,
		'margin-left' : bannerWidthHalf,
	});
	
	// .carousel-m-slide
	$('.carousel-m-slide').css({
		'width' : bannersLength,
	});
	
	// .carousel-m-contents
	$('.carousel-m-contents').css({
		'width' : bannersLength,
	});
	
	// .carousel-m-banner
	$('.carousel-m-banner').css({
		'width' : bannerWidth,
	});
	
	// . curtain
	// ��
	$('.curtain').css({
		'width' : bannerWidth,
	});
	
	
	// �J���[�Z���̓���
	function carouselM() {
		pfIndexM = $('.active .pf-list-wrapper-m').find('ul').length;

		var d = new $.Deferred;
		// �|�W�V�����l�擾
		var carouselPositionF = parseInt($('.carousel-m-contents').first().css('left'));
		var carouselPositionM = parseInt($('.carousel-m-contents').eq(1).css('left'));
		var carouselPositionL = parseInt($('.carousel-m-contents').last().css('left'));
		
		// �|�W�V����left�l�擾
		var leftF = Math.ceil(carouselPositionF);
		var leftM = Math.ceil(carouselPositionM);
		var leftL = Math.ceil(carouselPositionL);
		
		// �o�i�[���Z�b�g�ʒu�i1�����������Ԍ��ɂ��j
		//if(banners == 2) {
			//var stop = -(bannerWidth * (banners + 1));
		//}else {
			var stop = -(bannerWidth * banners) - bannerWidth;
		//}
		
		// �ŏ��̃J���[�Z�����X�g
		$('.carousel-m-contents').first().animate({
			left : leftF + (-bannerWidth),
		}, 1000, function() {
			if(leftF == stop) {
				$(this).css('left', bannersLength * 2 - bannerWidth * 2);
			}
			
			d.resolve();
		});
		
		// ���C���̃J���[�Z�����X�g
		$('.carousel-m-contents').eq(1).animate({
			left: leftM + (-bannerWidth),
		}, 1000, function() {
			if(leftM == stop) {
				$(this).css('left', bannersLength * 2 - bannerWidth * 2);
			}
			
			d.resolve();

		});
		
		// �Ō�̃J���[�Z�����X�g
		$('.carousel-m-contents').last().animate({
			left: leftL + (-bannerWidth),
		}, 1000, function() {
			if(leftL == stop) {
				$(this).css('left', bannersLength * 2 - bannerWidth * 2);
			}
			
			d.resolve();
		});
		
		return d.promise();
	} // carouselM() 
	
	
	// �I�v�V�����̓���
	function optionM() {
		var d = new $.Deferred;
		var a = false;
		// �p�t�H�[�}�[���X�g��
		var pfIndexliM = $('.active .pf-list-wrapper-m').find('li').length;
		
		// �X�e�[�^�X�o�[�A�j���[�V����
		$('.active .event-data-wrapper').delay(300).animate({
			top : 0,
		}, 300);
		
		if(pfIndexliM == 0) {
			$('.active .pf-list-wrapper-m').css('background', 'none');
		}
		
		// �p�t�H�[�}�[�A�j���[�V����
		if(pfIndexM != 1) {	// �p�t�H�[�}�[���X�g��1�łȂ��ꍇ

			// �ŏ��̈ꖇ�t�F�[�h�C��������
			$('.active .pf-list-wrapper-m ul').eq(0).fadeIn();
			$('.active .pf-list-wrapper-m').eq(0).delay(500).animate({	// �ŏ���1���̃A�j���[�V����
				top : 40,
			}, 800, function() {
				$(this).find('ul').eq(0).delay(3000).fadeOut('slow', function() {
					// 2���ڈȍ~�̃A�j���[�V����
					for(var i=1; i<pfIndexM; i++) {
						var showPf = $('.active .pf-list-wrapper-m ul').eq(i).data('showPf');
						//var showDelay = $('.active .pf-list-wrapper-m ul').eq(i).data('showDelay');
						
						if(i != pfIndexM-1) {	// �Ō��1���ȊO�̃A�j���[�V����
							$('.active .pf-list-wrapper-m ul').delay(showPf).eq(i).fadeIn('slow').delay(3000).fadeOut('slow');
						}else {	// �Ō��1���̃A�j���[�V����
							if($('.active .pf-list-wrapper-m ul').eq(i).hasClass('dataupcoming') && a == false){
								$('.active .pf-list-wrapper-m .costumelives').hide();
								$('.active .pf-list-wrapper-m .costumeupcoming').show();
								a = true;
							}
							
							$('.active .pf-list-wrapper-m ul').delay(showPf).eq(i).fadeIn('slow', function() {
								// �p�t�H�[�}�[�g�B��
								$('.active .pf-list-wrapper-m').delay(3000).animate({
									top : 200,
								}, 800, function() {
									$('.active .pf-list-wrapper-m ul').fadeOut();
								});
								
								// �X�e�[�^�X�o�[�B��
								$('.active .event-data-wrapper').delay(3700).animate({
									top : -40,
								}, 300, function() {
									
									if(activeIndexM+1 == banners) {	// ���ꂼ��(.first .main .last)�̍Ō�̃o�i�[�̏ꍇ
										// .first .main .last��active�N���X�ړ� 
										if($('.main .carousel-m-banner').hasClass('active')) {
											// ���݂�active�N���X���폜
											$('.main .active').removeClass('active');
											// .last�J���[�Z�����X�g�̍ŏ���active�N���X�ǉ�
											$('.last .carousel-m-banner').eq(0).addClass('active');
											// activeIndexM������
											activeIndexM = 0;
										}else if($('.last .carousel-m-banner').hasClass('active')) {
											// ���݂�active�N���X���폜
											$('.last .active').removeClass('active');
											// .first�J���[�Z�����X�g�̍ŏ���active�N���X�ǉ�
											$('.first .carousel-m-banner').eq(0).addClass('active');
											// activeIndexM������
											activeIndexM = 0;
										}else {
											// ���݂�active�N���X���폜
											$('.first .active').removeClass('active');
											// .main�J���[�Z�����X�g�̍ŏ���active�N���X�ǉ�
											$('.main .carousel-m-banner').eq(0).addClass('active');
											// activeIndexM������
											activeIndexM = 0;
										}
									}else {
										// activeIndexM�C���N�������g
										activeIndexM += 1;
										
										if($('.main .carousel-m-banner').hasClass('active')) {
											// ���݂�active�N���X���폜
											$('.active').removeClass('active');
											// ���̃o�i�[��active�N���X�ǉ�
											$('.main .carousel-m-banner').eq(activeIndexM).addClass('active');
										}else if($('.last .carousel-m-banner').hasClass('active')) {
											// ���݂�active�N���X���폜
											$('.active').removeClass('active');
											// ���̃o�i�[��active�N���X�ǉ�
											$('.last .carousel-m-banner').eq(activeIndexM).addClass('active');											
										}else {
											// ���݂�active�N���X���폜
											$('.active').removeClass('active');
											// ���̃o�i�[��active�N���X�ǉ�
											$('.first .carousel-m-banner').eq(activeIndexM).addClass('active');											
										}	

									}
									
									d.resolve();
									playM();
									
								});
							});
						}	
					}
				});
			});
		}else {	// �p�t�H�[�}�[���X�g��1���̏ꍇ
			
			if($('.active .pf-list-wrapper-m ul').eq(0).hasClass('dataupcoming') && a == false){
				$('.active .pf-list-wrapper-m .costumelives').hide();
				$('.active .pf-list-wrapper-m .costumeupcoming').show();
				a = true;
			}else{
				$('.active .pf-list-wrapper-m .costumelives').show();
				$('.active .pf-list-wrapper-m .costumeupcoming').hide();
			}
			$('.active .pf-list-wrapper-m ul').eq(0).fadeIn();
			$('.active .pf-list-wrapper-m').eq(0).delay(500).animate({	// �ŏ���1���̃A�j���[�V����
				top : 40,
			}, 800, function() {
				var showPf = $('.active .pf-list-wrapper-m ul').data('showPf');
				//var showDelay = $('.active .pf-list-wrapper-m ul').data('showDelay');
				
				$('.active .pf-list-wrapper-m ul').delay(showPf).fadeIn('slow', function() {
					// �p�t�H�[�}�[�g�B��
					$('.active .pf-list-wrapper-m').delay(3000).animate({
						top : 200,
					}, 800, function() {
						$('.active .pf-list-wrapper-m ul').fadeOut();
					});
					
					// �X�e�[�^�X�o�[�B��
					$('.active .event-data-wrapper').delay(3700).animate({
						top : -40,
					}, 300, function() {
						
						if(activeIndexM+1 == banners) {	// ���ꂼ��(.first .main .last)�̍Ō�̃o�i�[�̏ꍇ
							// .first .main .last��active�N���X�ړ� 
							if($('.main .carousel-m-banner').hasClass('active')) {
								// ���݂�active�N���X���폜
								$('.main .active').removeClass('active');
								// .last�J���[�Z�����X�g�̍ŏ���active�N���X�ǉ�
								$('.last .carousel-m-banner').eq(0).addClass('active');
								// activeIndexM������
								activeIndexM = 0;
							}else if($('.last .carousel-m-banner').hasClass('active')) {
								// ���݂�active�N���X���폜
								$('.last .active').removeClass('active');
								// .first�J���[�Z�����X�g�̍ŏ���active�N���X�ǉ�
								$('.first .carousel-m-banner').eq(0).addClass('active');
								// activeIndexM������
								activeIndexM = 0;
							}else {
								// ���݂�active�N���X���폜
								$('.first .active').removeClass('active');
								// .main�J���[�Z�����X�g�̍ŏ���active�N���X�ǉ�
								$('.main .carousel-m-banner').eq(0).addClass('active');
								// activeIndexM������
								activeIndexM = 0;
							}
						}else {
							// activeIndexM�C���N�������g
							activeIndexM += 1;
							
							if($('.main .carousel-m-banner').hasClass('active')) {
								// ���݂�active�N���X���폜
								$('.active').removeClass('active');
								// ���̃o�i�[��active�N���X�ǉ�
								$('.main .carousel-m-banner').eq(activeIndexM).addClass('active');
							}else if($('.last .carousel-m-banner').hasClass('active')) {
								// ���݂�active�N���X���폜
								$('.active').removeClass('active');
								// ���̃o�i�[��active�N���X�ǉ�
								$('.last .carousel-m-banner').eq(activeIndexM).addClass('active');											
							}else {
								// ���݂�active�N���X���폜
								$('.active').removeClass('active');
								// ���̃o�i�[��active�N���X�ǉ�
								$('.first .carousel-m-banner').eq(activeIndexM).addClass('active');											
							}	
		
						}
						
						d.resolve();
						playM();
						
					});
				});
			});


		}
		
		return d.promise();
	}	// optionM()
	
	//optionM();
	
	function link() {
		$('.carousel-m-banner').click(function() {
			var url = $('.active').data('url');
		
			if(url!='' || url=='undefined'){				
				window.open(url);
			}
			//return false;
		});
	}
	
	/*$('.thumb a').click(function() {
		$('.carousel-m-contents').die('click', link);
	});*/
	
	link();

	
	
	// ���[�v���s�֐�
	function playM() {
		optionM().then(carouselM);//.then(playM);
	}
	
	playM();
	
});