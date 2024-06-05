$(function () {
    if (typeof ($('.conteudo').attr('data-sucesso')) != "undefined" && $('.conteudo').attr('data-sucesso').length > 0) {
        swal({
            title: "Sucesso!",
            text: $('.conteudo').attr('data-sucesso'),
            icon: "success",
            button: "Ok",
        });
    }

    if ($('.content .conteudo .tabela.pagamento .item .topo .toggle').length > 0) {
        $('.content .conteudo .tabela.pagamento .item .topo .toggle').on('click', function () {
            if ($(this).parent().parent().parent().hasClass('active') == false){
                $('.content .conteudo .tabela.pagamento').removeClass('active');
                $(this).parent().parent().parent().addClass('active');
            }
            else{
                $(this).parent().parent().parent().removeClass('active');
            }
        });
    }
    if ($('.aviso-popup').length > 0) {

        $('.aviso-popup').on('click', function () {
            href = $(this).attr('href');
            texto = $(this).data('texto');
            swal({
                    title: "Cancelar",
                    text: texto,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then(function (willDelete) {
                    if (willDelete) {
                        location.href = href;
                    }
                });
            return false;
        });
    }
    if (typeof ($('.content').attr('data-sucesso')) != "undefined" && $('.content').attr('data-sucesso').length > 0) {
        swal({
            title: "Sucesso!",
            text: $('.content').attr('data-sucesso'),
            icon: "success",
            button: "Ok",
        });
    }
    if (typeof ($('.content').attr('data-erro')) != "undefined" && $('.content').attr('data-erro').length > 0) {
        swal({
            title: "Erro!",
            text: $('.content').attr('data-erro'),
            icon: "error",
            button: "Ok",
        });
    }
    if (typeof ($('.conteudo').attr('data-erro')) != "undefined" && $('.conteudo').attr('data-erro').length > 0) {
        swal({
            title: "Erro!",
            text: $('.conteudo').attr('data-erro'),
            icon: "error",
            button: "Ok",
        });
    }
    if ($('.content .hambburger').length > 0) {
        $('.content .hambburger').on('click', function () {
            $('body').addClass('open');
            return false;
        });
    }
    if ($('.menu .header .back').length > 0) {
        $('.menu .header .back').on('click', function () {
            $('body').removeClass('open');
            return false;
        });
    }
    if ($('.consumo-obrigatorio').length > 0) {
        form.init('consumo-obrigatorio');
    }
    if ($('.content .conteudo form .inputs .garrafas .adicionar').length > 0) {
        input_type.garrafas.init();
    }
    if ($('.disponibilidade .swiper-container').length > 0) {
        var mySwiper = new Swiper('.disponibilidade .swiper-container', {
            slidesPerView: 1,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            hashNavigation: {
                watchState: true,
            },
        });
    }
    if ($('.data.formulario input[name="data_evento"]').length > 0) {
        $('.data.formulario input[name="data_evento"]').off().on('change', function () {
            $(this).parent().parent().parent().submit();
            return false;
        });
    }
});
var form = {
    init: function (tipo) {
        $('form').find('.adicionar_mais').on('click', function () {
            form.click(tipo);
        });
        form.remover();
    },
    click: function (tipo) {
        if (tipo == "consumo-obrigatorio") {
            form.ajaxObrigatorio();
        }
        var incremento = Number($('form').attr('data-incremento'));
        $('form').attr('data-incremento', incremento + 1);
    },
    remover: function () {
        if ($('.content .conteudo form .bloco').find('.remover').length > 0) {
            $('.content .conteudo form .bloco').find('.remover').off().on('click', function () {
                $(this).parent().remove();
                return false;
            });
        }
    },
    ajaxObrigatorio: function () {
        ajaxRps = (function () {
            var html = null;
            $.ajax({
                'async': false,
                'global': false,
                'data': {
                    "i": $('form').attr('data-incremento')
                },
                'type': "GET",
                'url': "/rp/ajax_adicionar_input.php",
                'dataType': "html",
                'success': function (data) {
                    html = data;
                }
            });
            return html;
        })();
        if (ajaxRps) {

            $('.bloco').eq(($('.bloco').length - 1)).after(ajaxRps);
            form.remover();

        }
    }
}
var input_type = {
    pagamento: {
        init: function init() {
            $('.conteudo .content form .input-grupo .pagamentos .pagamento input').on('input', function () {
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
            $('.conteudo .content form .input-grupo .input #input-valor-multibanco, .conteudo .content form .input-grupo .input #input-valor-dinheiro').on('input', function () {
                input_type.pagamento.actualiza_valor();
            });
        },
        actualiza_valor: function actualiza_valor() {
            if ($('.conteudo .content form .input-grupo .input #input-valor-multibanco').length > 0) {
                var valor_multibanco = $('.conteudo .content form .input-grupo .input #input-valor-multibanco').val();
                var valor_dinheiro = $('.conteudo .content form .input-grupo .input #input-valor-dinheiro').val();
                var total = Number(valor_multibanco) + Number(valor_dinheiro);
                $('.conteudo .content form .input-grupo .input.valor-total').find('.valor').html(total.toFixed(2));
                if ($('.conteudo .content form .input-grupo .input.valor-totalcamarote').length > 0) {
                    var valor_adiantado = $('.conteudo .content form .input-grupo .input.valor-totalcamarote').data('adiantado');
                    var totalcamarote = Number(valor_multibanco) + Number(valor_dinheiro) + Number(valor_adiantado);
                    $('.conteudo .content form .input-grupo .input.valor-totalcamarote').find('.valor').html(totalcamarote.toFixed(2));
                }
            }
        }
    },
    rp: {
        init: function init() {
            $('.conteudo .content form .input-grupo .staff .adicionar').on('click', function () {
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
                        $('.fancybox-content .letras a').on('click', function () {
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
            $('.content .rps a').on('click', function () {
                var id_rp = $(this).data('id');
                $this.find('input[type="hidden"]').val(id_rp);

                ajaxRps = function () {
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
            $('.content .conteudo form .inputs .garrafas .adicionar').fancybox({
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
                    $('.content .conteudo form .inputs .garrafas input[type="number"]').each(function () {
                        valor_input = $(this).val();
                        var i = 0;
                        id_garrafa = $(this).data('idgarrafa');

                        if (valor_input.length > 0 && valor_input > 0) {
                            $('.fancybox-content .escolha-garrafas-responsive .garrafa-div .input-quantidade input[data-idgarrafa="' + id_garrafa + '"]').val(valor_input);
                        }
                    });
                    input_type.garrafas.quantidade();
                    $('.fancybox-content .acao .gravar').on('click', function () {
                        $.fancybox.close();
                        return false;
                    });
                }
            });
        },
        quantidade: function quantidade() {
            $('.input-quantidade .menos').on('click', function () {
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
            $('.input-quantidade .mais').on('click', function () {
                var $button = $(this);
                var oldValue = $button.parent().find("input").val();
                var newVal = parseFloat(oldValue) + 1;
                $button.parent().find("input").val(newVal);
                return false;
            });
        },
        retorna: function retorna() {
            var garrafas = new Array();
            $('.fancybox-content .escolha-garrafas-responsive .garrafa-div .input-quantidade input[type="number"]').each(function () {
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
                'url': "/rp/privados/inputs_garrafas.html.php",
                'dataType': "html",
                'success': function success(data) {
                    $('.content .conteudo form .inputs .garrafas .escolha-garrafas-responsive').html(data);
                    input_type.garrafas.quantidade();
                }
            });
        }
    },
    keyboard: {
        init: function init() {
            $('.conteudo .content form .input-grupo .input .teclado_virtual').on('focus', function () {
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
                                onChange: function (_onChange) {
                                    function onChange(_x) {
                                        return _onChange.apply(this, arguments);
                                    }

                                    onChange.toString = function () {
                                        return _onChange.toString();
                                    };

                                    return onChange;
                                }(function (input) {
                                    return onChange(input);
                                }),
                                onKeyPress: function (_onKeyPress) {
                                    function onKeyPress(_x2) {
                                        return _onKeyPress.apply(this, arguments);
                                    }

                                    onKeyPress.toString = function () {
                                        return _onKeyPress.toString();
                                    };

                                    return onKeyPress;
                                }(function (button) {
                                    return onKeyPress(button);
                                })
                            });
                            keyboard.setInput(valor_input);
                            document.querySelector(".input-keyboard").addEventListener("input", function (event) {
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
            $('.conteudo .content form .input-grupo .input .teclado_numerico').on('click', function () {
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
            $('.fancybox-content.teclado-numerico .calculadora a').off().on('click', function () {
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