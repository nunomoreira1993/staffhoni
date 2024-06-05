var Keyboard;
var keyboard;
var valor_input;
var ajaxRps;
var ajaxExtrasValida;
var ajax_presencas; //valida breakpoints

var breakpointMobile = window.matchMedia('all AND (max-width:767px)');
var breakpointTablet = window.matchMedia('all AND (min-width:767px) AND (max-width:1024px)');
var breakpointDesktop = window.matchMedia('all AND (min-width:1025px)');
//variaveis GET
var $_GET = {};

//no ios o primeiro click é hover, mas no site o primeiro click tem de ser um click.
var device = navigator.userAgent.toLowerCase();

var ios = device.match(/(iphone|ipod|ipad)/);
if (ios) {
	var clickEvent = "click touchend";
} else {
	var clickEvent = "click";
}


document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function() {
	function decode(s) {
		return decodeURIComponent(s.split("+").join(" "));
	}

	$_GET[decode(arguments[1])] = decode(arguments[2]);
});


$(function() {
	if (typeof $('.conteudo .content').attr('data-sucesso') != "undefined" && $('.conteudo .content').attr('data-sucesso').length > 0) {
		swal({
			title: "Sucesso!",
			text: $('.conteudo .content').attr('data-sucesso'),
			icon: "success",
			button: "Ok"
		});
	}

	if (typeof $('.conteudo .content').attr('data-erro') != "undefined" && $('.conteudo .content').attr('data-erro').length > 0) {
		swal({
			title: "Erro!",
			text: $('.conteudo .content').attr('data-erro'),
			icon: "error",
			button: "Ok"
		});
	}
	if ($('.menu-lateral .scroll').length > 0) {
		$('.menu-lateral .scroll > ul > li').on('click', function() {
			if ($(this).hasClass('active') === true) {
				$(this).removeClass('active');
			} else {
				$('.menu-lateral .scroll > ul > li').removeClass('active');
				$(this).addClass('active');
			}
		});

	}

	if ($('.conteudo .content .table-responsive table tbody tr td .apagar').length > 0) {
		$('.conteudo .content .table-responsive table tbody tr td .apagar').on('click', function() {
			href = $(this).attr('href');
			swal({
				title: "Apagar",
				text: "Deseja mesmo apagar este registo?",
				icon: "warning",
				buttons: true,
				dangerMode: true
			}).then(function(willDelete) {
				if (willDelete) {
					location.href = href;
				}
			});
			return false;
		});
	}

	if ($('.conteudo .content .letras').length > 0) {
		var swiper = new Swiper('.conteudo .content .letras', {
			slidesPerView: 'auto',
			freeMode: true,
			mousewheel: true,
			spaceBetween: 15
		});
		$('.conteudo .content .letras a').on('click', function() {
			$('.conteudo .content .letras a').removeClass('active');
			$(this).addClass('active');
			rps.elementos($(this));
			return false;
		});
		rps.clique();
	}

	if ($('.conteudo .content .letras_presencas').length > 0) {
		var swiper = new Swiper('.conteudo .content .letras_presencas', {
			slidesPerView: 'auto',
			freeMode: true,
			mousewheel: true,
			spaceBetween: 15
		});
		$('.conteudo .content .letras_presencas a').on('click', function() {
			$('.conteudo .content .letras_presencas a').removeClass('active');
			$(this).addClass('active');
			pagamentos.presencas($(this));
			return false;
		});
		pagamentos.presencas($(this));
	}

	if ($('.conteudo .content .letras_pagamentos').length > 0) {
		var swiper = new Swiper('.conteudo .content .letras_pagamentos', {
			slidesPerView: 'auto',
			freeMode: true,
			mousewheel: true,
			spaceBetween: 15
		});
		$('.conteudo .content .letras_pagamentos a').on('click', function() {
			$('.conteudo .content .letras_pagamentos a').removeClass('active');
			$(this).addClass('active');
			pagamentos.letras($(this));
			return false;
		});
	}

	if ($('.header .hamburger').length > 0) {
		$('.header .hamburger').on('click', function() {
			$('body').toggleClass('open');
			return false;
		});
	}

	if ($('.conteudo .content > .pesquisa .input input').length > 0) {
		pesquisa.init($('.conteudo .content > .pesquisa .input input').attr('data-pesquisa'));
	}

	if ($('.conteudo .content .table-responsive table tbody tr td .payment').length > 0) {
		privados.init();
	}

	if ($('.conteudo .content form .input-grupo .staff .adicionar').length > 0) {
		input_type.rp.init();
	}

	if ($('.conteudo .content form .input-grupo .garrafas .adicionar').length > 0) {
		input_type.garrafas.init();
	}

	if ($('.conteudo .content form .input-grupo .teclado_virtual').length > 0) {
		input_type.keyboard.init();
	}

	if ($('.conteudo .content form .input-grupo .teclado_numerico').length > 0) {
		input_type.teclado_numerico.init();
	}

	if ($('.conteudo .content form .input-grupo .pagamentos .pagamento').length > 0) {
		input_type.pagamento.init();
	}

	if ($('.conteudo #entradas_disponibilidade').length > 0) {
		entradas_disponibilidade.init();
	}

	if ($('.disponibilidade .swiper-container').length > 0) {
		var mySwiper = new Swiper('.disponibilidade .swiper-container', {
			slidesPerView: 1,
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev'
			},
			hashNavigation: {
				watchState: true
			}
		});
	}

	if ($('.login-pin').length > 0) {
		pin.init();
	}

	if ($('.paga .form .convites').length > 0) {
		pagamentos.valida_convite();
	}

	if ($('.paga .form .extras .novo_extra').length > 0) {
		pagamentos.extras();
	}

	if ($('.caixas .nova_caixa').length > 0) {
		caixas.init();
	}

	if ($('form[name="inserir_rp"] .input-grupo select[name="id_cargo"]').length > 0) {
		cargos.input();
	}
});
var input_type = {
	pagamento: {
		init: function init() {
			$('.conteudo .content form .input-grupo .pagamentos .pagamento input').on('input', function() {
				var show = $(this).data('show');

				var elemento = $("#" + show);

				if (elemento.length > 0) {
					if ($(this).is(":checked")) {
						elemento.removeClass('hidden');
					} else {
						elemento.addClass('hidden');
					}
				}
			});
			$('.conteudo .content form .input-grupo .input #input-valor-multibanco,.conteudo .content form .input-grupo .input #input-valor-mbway, .conteudo .content form .input-grupo .input #input-valor-dinheiro').on('input', function() {
				input_type.pagamento.actualiza_valor();
			});
		},
		actualiza_valor: function actualiza_valor() {
			if ($('.conteudo .content form .input-grupo .input #input-valor-multibanco').length > 0) {
				var valor_multibanco = $('.conteudo .content form .input-grupo .input #input-valor-multibanco').val();
				var valor_dinheiro = $('.conteudo .content form .input-grupo .input #input-valor-dinheiro').val();
				var valor_mbway = $('.conteudo .content form .input-grupo .input #input-valor-mbway').val();
				var total = Number(valor_multibanco) + Number(valor_dinheiro) + Number(valor_mbway);
				$('.conteudo .content form .input-grupo .input.valor-total').find('.valor').html(total.toFixed(2));
				if ($('.conteudo .content form .input-grupo .input.valor-totalcamarote').length > 0) {
					var valor_adiantado = $('.conteudo .content form .input-grupo .input.valor-totalcamarote').data('adiantado');
					var totalcamarote = Number(valor_multibanco) + Number(valor_dinheiro) + Number(valor_mbway) + Number(valor_adiantado);
					$('.conteudo .content form .input-grupo .input.valor-totalcamarote').find('.valor').html(totalcamarote.toFixed(2));
				}
			}
		}
	},
	rp: {
		init: function init() {
			$('.conteudo .content form .input-grupo .staff .adicionar').on('click', function() {
				$this = $(this);
				$.fancybox.open({
					src: $this.data('src'),
					type: 'ajax',
					toolbar: false,
					touch: {
						vertical: false,
						horizontal: false
					},
					smallBtn: true,
					afterShow: function afterShow(instance, current) {
						input_type.rp.retorna($this.parent());
						var swiper = new Swiper('.fancybox-content .letras', {
							slidesPerView: 'auto',
							freeMode: true,
							mousewheel: true,
							spaceBetween: 15
						});
						$('.fancybox-content .letras a').on('click', function() {
							$('.fancybox-content .letras a').removeClass('active');
							$(this).addClass('active');
							rps.elementos($(this), $this);
							return false;
						});
					}
				});
			});
		},
		retorna: function retorna($this) {
			$('.content .rps a').on('click', function() {
				var id_rp = $(this).data('id');
				$this.find('input[type="hidden"]').val(id_rp);

				ajaxRps = function() {
					var html = null;
					$.ajax({
						'async': false,
						'global': false,
						'data': {
							"id": id_rp
						},
						'type': "GET",
						'url': "/administrador/privados/ajax/staff.html.php",
						'dataType': "html",
						'success': function success(data) {
							html = data;
						}
					});
					return html;
				}();

				if (ajaxRps) {
					$this.find('.staff-escolhido').html(ajaxRps);
				}

				$.fancybox.close();
				return false;
			});
		}
	},
	garrafas: {
		init: function init() {
			input_type.garrafas.quantidade();
			$('.conteudo .content form .input-grupo .garrafas .adicionar').fancybox({
				toolbar: false,
				smallBtn: true,
				touch: {
					vertical: false,
					horizontal: false
				},
				beforeClose: function beforeClose() {
					input_type.garrafas.retorna();
				},
				beforeShow: function beforeShow() {},
				afterShow: function afterShow(instance, current) {
					$('.conteudo .content form .input-grupo .garrafas input[type="number"]').each(function() {
						valor_input = $(this).val();
						var i = 0;
						id_garrafa = $(this).data('idgarrafa');

						if (valor_input.length > 0 && valor_input > 0) {
							$('.fancybox-content .escolha-garrafas-responsive .garrafa-div .input-quantidade input[data-idgarrafa="' + id_garrafa + '"]').val(valor_input);
						}
					});
					input_type.garrafas.quantidade();
					$('.fancybox-content .acao .gravar').on('click', function() {
						$.fancybox.close();
						return false;
					});
				}
			});
		},
		quantidade: function quantidade() {
			$('.input-quantidade .menos').on('click', function() {
				var $button = $(this);
				var oldValue = $button.parent().find("input").val();

				if (oldValue > 0) {
					var newVal = parseFloat(oldValue) - 1;
				} else {
					newVal = 0;
				}

				$button.parent().find("input").val(newVal);
				return false;
			});
			$('.input-quantidade .mais').on('click', function() {
				var $button = $(this);
				var oldValue = $button.parent().find("input").val();
				var newVal = parseFloat(oldValue) + 1;
				$button.parent().find("input").val(newVal);
				return false;
			});
		},
		retorna: function retorna() {
			var garrafas = new Array();
			$('.fancybox-content .escolha-garrafas-responsive .garrafa-div .input-quantidade input[type="number"]').each(function() {
				valor_input = $(this).val();
				var i = 0;
				id_garrafa = $(this).data('idgarrafa');

				if (valor_input.length > 0 && valor_input > 0) {
					i++;
					garrafas[id_garrafa] = valor_input;
				}
			});
			$.ajax({
				'async': false,
				'global': false,
				'data': {
					"garrafas": garrafas
				},
				'type': "POST",
				'url': "/administrador/privados/ajax/inputs_garrafas.html.php",
				'dataType': "html",
				'success': function success(data) {
					$('.conteudo .content form .input-grupo .garrafas .escolha-garrafas-responsive').html(data);
					input_type.garrafas.quantidade();
				}
			});
		}
	},
	keyboard: {
		init: function init() {
			$('.conteudo .content form .input-grupo .input .teclado_virtual').on('focus', function() {
				if (breakpointTablet.matches == false && breakpointMobile.matches == false && breakpointDesktop.matches == true) {
					valor_input = $(this).val();
					$.fancybox.open({
						src: "/administrador/privados/ajax/teclado.html.php",
						type: 'ajax',
						toolbar: false,
						smallBtn: true,
						touch: {
							vertical: false,
							horizontal: false
						},
						beforeClose: function beforeClose() {
							var valor = $('.fancybox-content.virtual input').val();
							$('.conteudo .content form .input-grupo .input .teclado_virtual').val(valor);
						},
						beforeShow: function beforeShow(instance, current) {},
						afterShow: function afterShow(instance, current) {
							$('.fancybox-content.virtual input').val(valor_input);
							Keyboard = window.SimpleKeyboard.default;
							keyboard = new Keyboard({
								onChange: function(_onChange) {
									function onChange(_x) {
										return _onChange.apply(this, arguments);
									}

									onChange.toString = function() {
										return _onChange.toString();
									};

									return onChange;
								}(function(input) {
									return onChange(input);
								}),
								onKeyPress: function(_onKeyPress) {
									function onKeyPress(_x2) {
										return _onKeyPress.apply(this, arguments);
									}

									onKeyPress.toString = function() {
										return _onKeyPress.toString();
									};

									return onKeyPress;
								}(function(button) {
									return onKeyPress(button);
								})
							});
							keyboard.setInput(valor_input);
							document.querySelector(".input-keyboard").addEventListener("input", function(event) {
								keyboard.setInput(event.target.value);
							});
						}
					});
				}
			});
		}
	},
	teclado_numerico: {
		init: function init() {
			$('.conteudo .content form .input-grupo .input .teclado_numerico').on('click', function() {
				if (breakpointTablet.matches == false && breakpointMobile.matches == false && breakpointDesktop.matches == true) {
					valor_input = $(this).val();
					$this = $(this);
					$decimal = $(this).data('decimal');
					$.fancybox.open({
						src: "/administrador/privados/ajax/teclado_numerico.html.php?decimal=" + $decimal,
						type: 'ajax',
						toolbar: false,
						touch: {
							vertical: false,
							horizontal: false
						},
						smallBtn: true,
						beforeClose: function beforeClose() {
							var valor = $('.fancybox-content.teclado-numerico .input input').val();
							$this.val(valor);
							input_type.pagamento.actualiza_valor();
						},
						beforeShow: function beforeShow(instance, current) {},
						afterShow: function afterShow(instance, current) {
							input_type.teclado_numerico.calculadora($this);
						}
					});
				}
			});
		},
		calculadora: function calculadora($this) {
			$('.fancybox-content.teclado-numerico .input input').val($this.val());
			$('.fancybox-content.teclado-numerico .calculadora a').off().on('click', function() {
				if ($(this).attr('data-numero') != "delete" && $(this).attr('data-numero') != "ok") {
					$('.fancybox-content.teclado-numerico .input input').val($('.fancybox-content.teclado-numerico .input input').val().trim());
					$('.fancybox-content.teclado-numerico .input input').val($('.fancybox-content.teclado-numerico .input input').val().trim() + $(this).attr('data-numero'));
				} else if ($(this).attr('data-numero') == "delete") {
					$('.fancybox-content.teclado-numerico .input input').val($('.fancybox-content.teclado-numerico .input input').val().slice(0, -1));
				} else if ($(this).attr('data-numero') == "ok") {
					if ($('.fancybox-content.teclado-numerico .input input').val().trim().length > 0) {
						var valor = $('.fancybox-content.teclado-numerico .input input').val().trim();
						$.fancybox.close();
					}
				}

				return false;
			});
		}
	}
};
var rps = {
	elementos: function elementos($elementos, $this) {
		if ($elementos != false) {
			var gerente = $($elementos).attr('data-gerente');
			var letra = $($elementos).attr('data-letra');
		} else {
			if ($('.content .letras a.active').length > 0) {
				var letra = $('.content .letras a.active').attr('data-letra');
				var gerente = 0;
			}
		}

		if ($elementos != false && $elementos.parent().parent().parent().hasClass('fancybox-content') == true) {
			var privados = true;
			var url = "/administrador/privados/ajax/adicionar_rps.ajax.php";
		} else {
			var privados = false;
			var url = "/administrador/entradas/ajax/rps.ajax.php";
		}

		ajaxRps = function() {
			var html = null;
			$.ajax({
				'async': false,
				'global': false,
				'data': {
					"letra": letra,
					"gerente": gerente
				},
				'type': "GET",
				'url': url,
				'dataType': "html",
				'success': function success(data) {
					html = data;
				}
			});
			return html;
		}();

		if (ajaxRps) {
			$('.content .rps').html(ajaxRps);

			if (privados == true) {
				input_type.rp.retorna($this.parent());
			} else {
				rps.clique();
			}
		}
	},
	clique: function clique() {
		$('.conteudo .content .rps a').fancybox({
			toolbar: false,
			smallBtn: true,
			afterShow: function afterShow(instance, current) {
				rps.acao();
			}
		});
	},
	acao: function acao() {
		$('.fancybox-content.entrada-ajax .calculadora a').off().on('click', function() {
			if ($(this).attr('data-numero') != "delete" && $(this).attr('data-numero') != "ok") {
				$('.fancybox-content.entrada-ajax .resultado').html($('.fancybox-content.entrada-ajax .resultado').html().trim());
				$('.fancybox-content.entrada-ajax .resultado').append($(this).attr('data-numero'));
			} else if ($(this).attr('data-numero') == "delete") {
				$('.fancybox-content.entrada-ajax .resultado').html($('.fancybox-content.entrada-ajax .resultado').html().slice(0, -1));
			} else if ($(this).attr('data-numero') == "ok") {
				if ($('.fancybox-content.entrada-ajax .resultado').html().trim().length > 0) {
					var valor = $('.fancybox-content.entrada-ajax .resultado').html().trim();
					var id_rp = $('.fancybox-content.entrada-ajax .rp').attr('data-id-rp');

					ajaxRps = function() {
						var html = null;
						$.ajax({
							'async': false,
							'global': false,
							'data': {
								"quantidade": valor,
								"id_rp": id_rp
							},
							'type': "POST",
							'url': "/administrador/entradas/ajax/inserir_entrada.ajax.php",
							'dataType': "json",
							'success': function success(data) {
								if (data.sucesso == 1) {
									rps.elementos(false);
									$('.fancybox-content.entrada-ajax .sucesso').addClass('active');
								} else if (data.erro == 1) {
									$('.fancybox-content.entrada-ajax .erro').addClass('active');
								}
							},
							'error': function error(_error) {
								console.log(_error.statusText);
								$('.fancybox-content.entrada-ajax .erro').addClass('active');
							}
						});
						return html;
					}();
				}
			}

			return false;
		});
		$('.fancybox-content.entrada-ajax .sucesso .fechar').off().on('click', function() {
			$.fancybox.close();
			return false;
		});
	}
};

var pesquisa = {
	init: function init() {
		$('.conteudo .content > .pesquisa .input input').donetyping(function() {
			pesquisa.ajax();
		});
		$('.conteudo .content > .pesquisa .input select').on('change', function() {
			pesquisa.ajax();
		});
		$('.conteudo .content > .pesquisa input[type="submit"]').on('click', function() {
			pesquisa.ajax();
		});
	},
	ajax: function ajax() {
		if ($('.conteudo .content > .pesquisa .input input').attr('data-pesquisa') == "consumo-sem-consumo") {
			pesquisa.ajaxSemConsumo();
		} else {
			pesquisa.ajaxConsumoObrigatorio();
		}
	},
	ajaxConsumoObrigatorio: function ajaxConsumoObrigatorio() {
		var valor = $('.conteudo .content > .pesquisa .input input').val();
		var id_rp = $('.conteudo .content > .pesquisa .input select').val();

		if (valor.length > 0 || id_rp.length > 0) {
			ajaxCartoes = function() {
				var html = null;
				$.ajax({
					'async': false,
					'global': false,
					'data': {
						"pesquisa": valor,
						"id_rp": id_rp
					},
					'type': "POST",
					'url': "/administrador/entradas/ajax/ajax_cartao_consumo_obrigatorio.php",
					'dataType': "html",
					'success': function success(data) {
						$('.conteudo .content > .ajax').html(data);
					},
					'error': function error(_error2) {
						console.log(_error2.statusText);
						$('.fancybox-content.entrada-ajax .erro').addClass('active');
					}
				});
				return html;
			}();
		}

		$('.conteudo .content .table-responsive table tbody tr td .confirmar').on(clickEvent, function() {
			var id = $(this).attr('data-id');
			var $this = $(this);

			ajaxCartoes = function() {
				var html = null;
				$.ajax({
					'async': false,
					'global': false,
					'data': {
						"id": id,
						"acao": "confirmar"
					},
					'type': "POST",
					'url': "/administrador/entradas/ajax/acao_cartao_consumo_obrigatorio.php",
					'dataType': "json",
					'success': function success(data) {
						if (data.retorno == 1) {
							$this.addClass('esconde');
							$this.parent().find('.anular').removeClass('esconde');
						}
					},
					'error': function error(_error3) {
						console.log(_error3.statusText);
					}
				});
				return html;
			}();
		});
		$('.conteudo .content .table-responsive table tbody tr td .anular').on(clickEvent, function() {
			var id = $(this).attr('data-id');
			var $this = $(this);

			ajaxCartoes = function() {
				var html = null;
				$.ajax({
					'async': false,
					'global': false,
					'data': {
						"id": id,
						"acao": "anular"
					},
					'type': "POST",
					'url': "/administrador/entradas/ajax/acao_cartao_consumo_obrigatorio.php",
					'dataType': "json",
					'success': function success(data) {
						if (data.retorno == 1) {
							$this.addClass('esconde');
							$this.parent().find('.confirmar').removeClass('esconde');
						}
					},
					'error': function error(_error4) {
						console.log(_error4.statusText);
					}
				});
				return html;
			}();
		});

	},
	ajaxSemConsumo: function ajaxSemConsumo() {
		var valor = $('.conteudo .content > .pesquisa .input input').val();
		var id_rp = $('.conteudo .content > .pesquisa .input select').val();

		if (valor.length > 0 || id_rp.length > 0) {
			ajaxCartoes = function() {
				var html = null;
				$.ajax({
					'async': false,
					'global': false,
					'data': {
						"pesquisa": valor,
						"id_rp": id_rp
					},
					'type': "POST",
					'url': "/administrador/entradas/ajax/ajax_cartao_sem_consumo.php",
					'dataType': "html",
					'success': function success(data) {
						$('.conteudo .content > .ajax').html(data);
					},
					'error': function error(_error5) {
						console.log(_error5.statusText);
						$('.fancybox-content.entrada-ajax .erro').addClass('active');
					}
				});
				return html;
			}();
		}

		$('.conteudo .content .table-responsive table tbody tr td .confirmar').on(clickEvent, function() {
			var id = $(this).attr('data-id');
			var $this = $(this);

			ajaxCartoes = function() {
				var html = null;
				$.ajax({
					'async': false,
					'global': false,
					'data': {
						"id": id,
						"acao": "confirmar"
					},
					'type': "POST",
					'url': "/administrador/entradas/ajax/acao_cartao_sem_consumo.php",
					'dataType': "json",
					'success': function success(data) {
						if (data.retorno == 1) {
							$this.addClass('esconde');
							$this.parent().find('.anular').removeClass('esconde');
						}
					},
					'error': function error(_error6) {
						console.log(_error6.statusText);
					}
				});
				return html;
			}();
		});
		$('.conteudo .content .table-responsive table tbody tr td .anular').on(clickEvent, function() {
			var id = $(this).attr('data-id');
			var $this = $(this);

			ajaxCartoes = function() {
				var html = null;
				$.ajax({
					'async': false,
					'global': false,
					'data': {
						"id": id,
						"acao": "anular"
					},
					'type': "POST",
					'url': "/administrador/entradas/ajax/acao_cartao_sem_consumo.php",
					'dataType': "json",
					'success': function success(data) {
						if (data.retorno == 1) {
							$this.addClass('esconde');
							$this.parent().find('.confirmar').removeClass('esconde');
						}
					},
					'error': function error(_error7) {
						console.log(_error7.statusText);
					}
				});
				return html;
			}();
		});
	}
};
var pin = {
	init: function init() {
		if ($('.login-pin .calculadora a').length > 0) {
			pin.calculadora();
		}

		if ($('.login-pin .lista a').length > 0) {
			pin.rp();
		}

		if ($('.login-pin .fundo-formulario .fechar').length > 0) {
			pin.fechar();
		}
	},
	rp: function rp() {
		$('.login-pin .lista a').on('click', function() {
			var id_rp = $(this).data('id');
			$('.login-pin .fundo-formulario .formulario input[name="id_rp"]').val(id_rp);
			$.ajax({
				'async': false,
				'global': false,
				'data': {
					"id": id_rp
				},
				'type': "GET",
				'url': "/administrador/privados/ajax/rp_pin.html.php",
				'dataType': "html",
				'success': function success(data) {
					$('.login-pin .fundo-formulario .formulario .info').html(data);
				}
			});
			$('.login-pin .fundo-formulario .formulario .erro').removeClass('active');
			$('.login-pin .fundo-formulario').addClass('active');
		});
	},
	fechar: function fechar() {
		$('.login-pin .fundo-formulario .fechar').on('click', function() {
			$('.login-pin .fundo-formulario').removeClass('active');
		});
	},
	calculadora: function calculadora() {
		$('.login-pin .calculadora a').off().on('click', function() {
			$('.login-pin .fundo-formulario .formulario .erro').removeClass('active');

			if ($(this).attr('data-numero') != "delete" && $(this).attr('data-numero') != "ok") {
				$('.login-pin .fundo-formulario .formulario .input input').val($('.login-pin .fundo-formulario .formulario .input input').val().trim());
				$('.login-pin .fundo-formulario .formulario .input input').val($('.login-pin .fundo-formulario .formulario .input input').val().trim() + $(this).attr('data-numero'));
			} else if ($(this).attr('data-numero') == "delete") {
				$('.login-pin .fundo-formulario .formulario .input input').val($('.login-pin .fundo-formulario .formulario .input input').val().slice(0, -1));
			} else if ($(this).attr('data-numero') == "ok") {
				if ($('.login-pin .fundo-formulario .formulario .input input').val().trim().length > 0) {
					var valor = $('.login-pin .fundo-formulario .formulario .input input').val().trim();
					var id_rp = $('.login-pin .fundo-formulario .formulario input[name="id_rp"]').val();

					ajaxRps = function() {
						var html = null;
						$.ajax({
							'async': false,
							'global': false,
							'data': {
								"pin": valor,
								"id_rp": id_rp
							},
							'type': "POST",
							'url': "/administrador/privados/ajax/valida_pin.ajax.php",
							'dataType': "json",
							'success': function success(data) {
								if (data.sucesso == 1) {
									$('.login-pin').remove();
								} else if (data.erro) {
									$('.login-pin .fundo-formulario .formulario .erro').html(data.erro).addClass('active');
								}
							},
							'error': function error(_error8) {
								$('.login-pin .fundo-formulario .formulario .erro').html(_error8.statusText).addClass('active');
							}
						});
						return html;
					}();
				}
			}

			return false;
		});
	}
};
var pagamentos = {
	presencas: function presencas() {
		if ($('.content .letras_presencas a.active').length > 0) {
			var letra = $('.content .letras_presencas a.active').attr('data-letra');
		}

		var url = "/administrador/pagamentos/ajax/presencas.ajax.php";

		ajaxPresencas = function() {
			var html = null;
			$.ajax({
				'async': false,
				'global': false,
				'data': {
					"letra": letra
				},
				'type': "GET",
				'url': url,
				'dataType': "html",
				'success': function success(data) {
					html = data;
				}
			});
			return html;
		}();

		if (ajaxPresencas) {
			$('.content .rps').html(ajaxPresencas);
			pagamentos.pagamentos_presenca();
		}
	},
	pagamentos_presenca: function pagamentos_presenca() {
		$('.content .rps a').on('click', function() {
			$.fancybox.open({
				src: "/administrador/pagamentos/ajax/adicionar_entrada_rp.ajax.php?id=" + $(this).data('id'),
				type: 'ajax',
				toolbar: false,
				touch: {
					vertical: false,
					horizontal: false
				},
				smallBtn: true,
				afterShow: function afterShow(instance, current) {
					pagamentos.acao();
				}
			});


		});
	},
	acao: function acao() {
		$('.fancybox-content.entrada-ajax .calculadora a').off().on('click', function() {
			if ($(this).attr('data-numero') != "delete" && $(this).attr('data-numero') != "ok") {
				$('.fancybox-content.entrada-ajax .resultado').html($('.fancybox-content.entrada-ajax .resultado').html().trim());
				$('.fancybox-content.entrada-ajax .resultado').append($(this).attr('data-numero'));
			} else if ($(this).attr('data-numero') == "delete") {
				$('.fancybox-content.entrada-ajax .resultado').html($('.fancybox-content.entrada-ajax .resultado').html().slice(0, -1));
			} else if ($(this).attr('data-numero') == "ok") {
				if ($('.fancybox-content.entrada-ajax .resultado').html().trim().length > 0) {
					var valor = $('.fancybox-content.entrada-ajax .resultado').html().trim();
					var id_rp = $('.fancybox-content.entrada-ajax .rp').attr('data-id-rp');


					ajaxRps = function() {
						var html = null;
						$.ajax({
							'async': false,
							'global': false,
							'data': {
								"numero": valor,
								"id_rp": id_rp
							},
							'type': "GET",
							'url': "/administrador/pagamentos/ajax/adicionar_presenca.ajax.php",
							'dataType': "json",
							'success': function success(data) {
								if (data.sucesso == 1) {
									pagamentos.presencas();
									$('.fancybox-content.entrada-ajax .sucesso').addClass('active');
								} else if (data.erro == 1) {
									$('.fancybox-content.entrada-ajax .erro').addClass('active');
								}
							},
							'error': function error(_error) {
								console.log(_error.statusText);
								$('.fancybox-content.entrada-ajax .erro').addClass('active');
							}
						});
						return html;
					}();
				}
			}

			return false;
		});
		$('.fancybox-content.entrada-ajax .sucesso .fechar').off().on('click', function() {
			$.fancybox.close();
			return false;
		});
	},
	letras: function letras($this) {
		if ($('.content .letras_pagamentos a.active').length > 0) {
			var letra = $('.content .letras_pagamentos a.active').attr('data-letra');
		}

		var url = "/administrador/pagamentos/ajax/pagamentos.ajax.php";

		ajaxPresencas = function() {
			var html = null;
			$.ajax({
				'async': false,
				'global': false,
				'data': {
					"letra": letra
				},
				'type': "GET",
				'url': url,
				'dataType': "html",
				'success': function success(data) {
					html = data;
				}
			});
			return html;
		}();

		if (ajaxPresencas) {
			$('.content .rps').html(ajaxPresencas);
		}
	},
	extras: function extras() {
		$('.paga .form .extras .novo_extra').on('click', function() {
			pagamentos.adicionar_extras();
			return false;
		});
		pagamentos.botoes_extras.init();
	},
	adicionar_extras: function adicionar_extras() {
		ajaxAddExtras = function() {
			var html = null;
			$.ajax({
				'async': false,
				'global': false,
				'type': "GET",
				'url': "/administrador/pagamentos/ajax/extras_pagamento.ajax.php",
				'dataType': "html",
				'success': function success(data) {
					html = data;
				}
			});
			return html;
		}();

		if (ajaxAddExtras) {
			$('.paga .form .extras .novo_extra').before(ajaxAddExtras);
			pagamentos.botoes_extras.init();
		}
	},
	botoes_extras: {
		init: function init() {
			$('.paga .form .extras .extra .acao a.apagar').on('click', function() {
				pagamentos.botoes_extras.apagar(this);
				return false;
			});
			$('.paga .form .extras .extra .acao a.enviar').on('click', function() {
				pagamentos.botoes_extras.enviar(this);
				return false;
			});
		},
		enviar: function enviar($this) {
			$($this).addClass('active');
			var id = $($this).parent().parent().find('input[name="id"]').val();
			var valor = $($this).parent().parent().find('input[name="valor"]').val();
			var sessao = $($this).parent().parent().find('input[name="sessao"]').val();
			var descricao = $($this).parent().parent().find('input[name="descricao"]').val();

			if ($('.paga .form .detalhe .bloco .valor input[name="nome"]').length > 0) {
				var nome = $('.paga .form .detalhe .bloco .valor input[name="nome"]').val();

				if (nome == "") {
					swal({
						title: "Erro!",
						text: "Adicione o nome do cliente e depois volte a aplicar o extra.",
						icon: "error",
						button: "Ok"
					});
					$('.paga .form .detalhe .bloco .valor input[name="nome"]').focus();
					return false;
				}
			} else {
				var nome = "";
			}

			if ($('.paga .form .detalhe .bloco .valor input[name="tipo"]').length > 0) {
				var tipo = $('.paga .form .detalhe .bloco .valor input[name="tipo"]').val();

				if (tipo == "") {
					swal({
						title: "Erro!",
						text: "Adicione o tipo de serviço e depois volte a aplicar o extra.",
						icon: "error",
						button: "Ok"
					});
					$('.paga .form .detalhe .bloco .valor input[name="tipo"]').focus();
					return false;
				}
			} else {
				var tipo = "";
			}

			var url = "/administrador/pagamentos/ajax/adiciona_extra.ajax.php";

			ajaxExtrasValida = function() {
				var html = null;
				$.ajax({
					'async': false,
					'global': false,
					'data': {
						"id": id,
						"valor": valor,
						"sessao": sessao,
						"descricao": descricao,
						"nome": nome,
						"tipo": tipo,
						"id_rp": $_GET['id_rp'],
						"data_evento": $_GET['data_evento']
					},
					'type': "post",
					'url': url,
					'dataType': "json",
					'success': function success(data) {
						html = data;
					}
				});
				return html;
			}();

			$($this).parent().parent().find('input[name="id"]').val(ajaxExtrasValida.sucesso);
			pagamentos.carrinho();
		},
		apagar: function apagar($this) {
			$($this).addClass('active');
			$($this).parent().parent().remove();
			var id = $($this).parent().parent().find('input[name="id"]').val();
			var url = "/administrador/pagamentos/ajax/remove_extra.ajax.php";

			ajaxExtrasRemove = function() {
				var html = null;
				$.ajax({
					'async': false,
					'global': false,
					'data': {
						"id": id
					},
					'type': "POST",
					'url': url,
					'dataType': "json",
					'success': function success(data) {
						html = data;
					}
				});
				return html;
			}();

			pagamentos.carrinho();
		}
	},
	valida_convite: function valida_convite() {
		$('.paga .form .convites .botoes .botao.confirmar').on('click', function() {
			$('.paga .form .convites .botoes .botao').removeClass('active');
			$(this).addClass('active');
			var valido = $(this).data('valida');
			var id = $(this).data('convite');
			pagamentos.validar_ajax_convite(id, valido);
			pagamentos.carrinho();
			return false;
		});
		$('.paga .form .convites .botoes .botao.recusar').on('click', function() {
			$('.paga .form .convites .botoes .botao').removeClass('active');
			$(this).addClass('active');
			var valido = $(this).data('valida');
			var id = $(this).data('convite');
			pagamentos.validar_ajax_convite(id, valido);
			pagamentos.carrinho();
			return false;
		});
	},
	validar_ajax_convite: function validar_ajax_convite(id, valido) {
		var url = "/administrador/pagamentos/ajax/validar_convite.ajax.php";

		ajaxConviteValida = function() {
			var html = null;
			$.ajax({
				'async': false,
				'global': false,
				'data': {
					"id": id,
					"valido": valido
				},
				'type': "GET",
				'url': url,
				'dataType': "html",
				'success': function success(data) {
					html = data;
				}
			});
			return html;
		}();
	},
	carrinho: function carrinho() {
		var url = "/administrador/pagamentos/ajax/carrinho.php";

		ajaxCarrinho = function() {
			var html = null;
			$.ajax({
				'async': false,
				'global': false,
				'data': {
					"id_rp": $_GET['id_rp'],
					"data_evento": $_GET['data_evento']
				},
				'type': "GET",
				'url': url,
				'dataType': "html",
				'success': function success(data) {
					html = data;
				}
			});
			return html;
		}();

		if (ajaxCarrinho) {
			$('.paga .carrinho').html(ajaxCarrinho);
		}
	}
};
var caixas = {
	init: function init() {
		$('.caixas .nova_caixa').on('click', function() {
			caixas.adicionar_caixas();
			return false;
		});
	},
	adicionar_caixas: function adicionar_caixas() {
		var new_index = Number($('.caixas .nova_caixa').data('index')) + 1;

		ajaxAddCaixa = function() {
			var html = null;
			$.ajax({
				'async': false,
				'global': false,
				'type': "GET",
				'data': {
					"index": new_index
				},
				'url': "/administrador/pagamentos/ajax/adiciona_caixa.ajax.php",
				'dataType': "html",
				'success': function success(data) {
					html = data;
				}
			});
			return html;
		}();

		if (ajaxAddCaixa) {
			$('.caixas .nova_caixa').data('index', new_index);
			$('.caixas .nova_caixa').before(ajaxAddCaixa);
		}
	}
};



var privados = {
	init: function init() {
		privados.pagamento();
	},
	pagamento: function pagamento() {


		$('.conteudo .content .table-responsive table tbody tr td .payment').on('click', function() {

			var id = $(this).attr('data-id');
			var pago = $(this).attr('data-pago');
			var $this = $(this);

			ajaxPago = function() {
				var html = null;
				$.ajax({
					'async': false,
					'global': false,
					'data': {
						"id": id,
						"pago": pago
					},
					'type': "POST",
					'url': "/administrador/privados/ajax/privados_pago.ajax.php",
					'dataType': "json",
					'success': function success(data) {
						if (data.sucesso == 1) {
							console.log(data);
							$this.html(data.layer).attr('data-pago', data.pago);
						}
					},
					'error': function error(_error6) {
						console.log(_error6.statusText);
					}
				});
				return html;
			}();
			return false;
		});
	}
};



(function($) {
	$.fn.extend({
		donetyping: function donetyping(callback, timeout) {
			timeout = timeout || 1e3; // 1 second default timeout

			var timeoutReference,
				doneTyping = function doneTyping(el) {
					if (!timeoutReference) return;
					timeoutReference = null;
					callback.call(el);
				};

			return this.each(function(i, el) {
				var $el = $(el); // Chrome Fix (Use keyup over keypress to detect backspace)
				// thank you @palerdot

				$el.is(':input') && $el.on('keyup keypress paste', function(e) {
					// This catches the backspace button in chrome, but also prevents
					// the event from triggering too preemptively. Without this line,
					// using tab/shift+tab will make the focused element fire the callback.
					if (e.type == 'keyup' && e.keyCode != 8) return; // Check if timeout has been set. If it has, "reset" the clock and
					// start over again.

					if (timeoutReference) clearTimeout(timeoutReference);
					timeoutReference = setTimeout(function() {
						// if we made it here, our timeout has elapsed. Fire the
						// callback
						doneTyping(el);
					}, timeout);
				}).on('blur', function() {
					// If we can, fire the event since we're leaving the field
					doneTyping(el);
				});
			});
		}
	});
})(jQuery);



var entradas_disponibilidade = {
	init: function() {
		$('.content .conteudo.disponibilidade .ocupar').on('click', function() {
			var id_mesa = $(this).data('id');
			entradas_disponibilidade.click(id_mesa);
			return false;
		});
	},
	click: function(id_mesa) {
		$.fancybox.open({
			src: "/administrador/privados/ajax/teclado_numerico.html.php",
			type: 'ajax',
			toolbar: false,
			touch: {
				vertical: false,
				horizontal: false
			},
			smallBtn: true,
			beforeClose: function beforeClose() {
				var valor = $('.fancybox-content.teclado-numerico .input input').val();
				ajaxRps = function() {
					var html = null;
					$.ajax({
						'async': false,
						'global': false,
						'data': {
							"cartoes": valor,
							"id_mesa": id_mesa
						},
						'type': "POST",
						'url': "/administrador/privados/ajax/entrada_privados.ajax.php",
						'dataType': "json",
						'success': function success(data) {
							location.reload();
						}
					});
					return html;
				}();
			},
			beforeShow: function beforeShow(instance, current) {},
			afterShow: function afterShow(instance, current) {
				entradas_disponibilidade.calculadora();
			}
		});
	},
	calculadora: function calculadora($this) {
		$('.fancybox-content.teclado-numerico .calculadora a').off().on('click', function() {
			if ($(this).attr('data-numero') != "delete" && $(this).attr('data-numero') != "ok") {
				$('.fancybox-content.teclado-numerico .input input').val($('.fancybox-content.teclado-numerico .input input').val().trim());
				$('.fancybox-content.teclado-numerico .input input').val($('.fancybox-content.teclado-numerico .input input').val().trim() + $(this).attr('data-numero'));
			} else if ($(this).attr('data-numero') == "delete") {
				$('.fancybox-content.teclado-numerico .input input').val($('.fancybox-content.teclado-numerico .input input').val().slice(0, -1));
			} else if ($(this).attr('data-numero') == "ok") {
				if ($('.fancybox-content.teclado-numerico .input input').val().trim().length > 0) {
					var valor = $('.fancybox-content.teclado-numerico .input input').val().trim();
					$.fancybox.close();
				}
			}

			return false;
		});
	}
};

function onChange(input) {
	document.querySelector(".input-keyboard").value = input;
}

function onKeyPress(button) {
	var currentLayout = keyboard.options.layoutName;
	var keyToggle = keyboard.options.keyToggle;

	if (button == "{enter}") {
		$.fancybox.close();
	}

	if (currentLayout == "shift" && keyToggle == "{shift}" && button !== "{shift}") {
		handleShift(button);
	}

	if (button === "{shift}" || button === "{lock}") handleShift(button);
}

function handleShift(button) {
	var currentLayout = keyboard.options.layoutName;
	var shiftToggle = currentLayout === "default" ? "shift" : "default";
	keyboard.setOptions({
		layoutName: shiftToggle,
		keyToggle: button
	});
}

var cargos = {
	input: function() {
		cargos.trataCargo($('.conteudo .content form .input-grupo .input select[name="id_cargo"]'));
		$('.conteudo .content form .input-grupo .input select[name="id_cargo"]').on("input", function() {
			cargos.trataCargo($(this));
		});
	},
	trataCargo: function($select) {
		var value = $select.val();
		$option = $select.find('option[value="' + value + '"]').eq(0);
		var chefe_equipa = $option.data('chefe-equipa');
		var produtor = $option.data('produtor');
		var input_chefe = $('.conteudo .content form .input-grupo .input select[name="id_chefe_equipa"]');
		var input_produtor = $('.conteudo .content form .input-grupo .input select[name="id_produtor"]');
		if (typeof(produtor) !== "undefined" && produtor == 1) {
			input_chefe.val('').parent().parent().addClass('hidden');
			input_produtor.parent().parent().removeClass('hidden');
		}
		if (typeof(chefe_equipa) !== "undefined" && chefe_equipa == 1) {
			input_produtor.val('').parent().parent().addClass('hidden');
			input_chefe.parent().parent().removeClass('hidden');
		}
		if ((typeof(chefe_equipa) == "undefined" || chefe_equipa == 0) && (typeof(produtor) == "undefined" || produtor == 0)) {
			input_produtor.val('').parent().parent().addClass('hidden');
			input_chefe.val('').parent().parent().addClass('hidden');
		}
	}
}