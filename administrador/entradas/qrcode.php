<div id="error_success" class="entrada-ajax with-qrcode">
    <div class="sucesso">
        <h2>  <b class="tipo_bilhete"></b> válido! </h2>
        <div class="mensagem">
            Staff: <b class="nome_cliente"></b>
        </div>
        <div class="icon">
            <svg width="150" height="150" viewBox="0 0 510 510" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <path fill="#fff" d="M150.45,206.55l-35.7,35.7L229.5,357l255-255l-35.7-35.7L229.5,285.6L150.45,206.55z M459,255c0,112.2-91.8,204-204,204 S51,367.2,51,255S142.8,51,255,51c20.4,0,38.25,2.55,56.1,7.65l40.801-40.8C321.3,7.65,288.15,0,255,0C114.75,0,0,114.75,0,255 s114.75,255,255,255s255-114.75,255-255H459z"></path>
            </svg>
        </div>
        <a href="javascript:$.fancybox.close();" class="fechar">
            Fechar
        </a>
    </div>
    <div class="erro">
        <h2> Erro! </h2>
        <div class="mensagem"></div>
        <div class="icon">
            <svg  width="150" height="150" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs></defs><title/><g data-name="Icon" ><path d="M2,16H2A14,14,0,1,0,16,2,14,14,0,0,0,2,16Zm23.15,7.75L8.25,6.85a12,12,0,0,1,16.9,16.9ZM8.24,25.16A12,12,0,0,1,6.84,8.27L23.73,25.16a12,12,0,0,1-15.49,0Z" transform="translate(0)" fill="#FFFFFF"/></g><g data-name="&lt;Transparent Rectangle&gt;"><rect class="cls-1" height="32" width="32" fill="none"/></g></svg>
        </div>
        <a href="javascript:$.fancybox.close();" class="fechar">
            Fechar
        </a>
    </div>

</div>

<div class="loading">
    <span class="loader"></span>
</div>

<div class="qrcode">
    <video class="qrcodevideo"></video>
</div>
<script src="/temas/administrador/js/qr-scanner-master/qr-scanner.umd.min.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/js/qr-scanner-master/qr-scanner.umd.min.js"); ?>"></script>
<script>
    const qrScanner = new QrScanner(
        document.querySelector(".qrcodevideo"),
        function(result) {
            qrScanner.stop();
            trataResult(result)
        }, {
            highlightScanRegion: true,
            highlightCodeOutline: true
        }
    );
    qrScanner.start();

    var qrcode_isload = false;

    function trataResult(qrcode) {
        document.querySelector(".loading").classList.add("active");
        if (qrcode_isload == false) {
            qrcode_isload = true;

            // URL para enviar a requisição POST
            const url = '/administrador/entradas/add_qrcode_entrance.php';

            // Dados a serem enviados no corpo da requisição
            const data = new URLSearchParams();
            data.append('qrcode', qrcode.data);

            // Opções da requisição
            const options = {
                method: 'POST',
                body: data
            };

            // Enviar a requisição usando fetch
            fetch(url, options)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao enviar requisição');
                    }
                    document.querySelector(".loading").classList.remove("active");
                    return response.json();
                })
                .then(data => {
                    qrcode_isload = false;
                    if (data.status == "success") {
                        openPopupQRCode(1, data.client_name, data.type);
                    } else {
                        openPopupQRCode(2, data.message);
                    }
                    document.querySelector(".loading").classList.remove("active");
                })
                .catch(error => {
                    qrcode_isload = false;
                    console.error('Erro na requisição:', error);
                    openPopupQRCode(2, error);
                    document.querySelector(".loading").classList.remove("active");
                });
        }
    }
</script>