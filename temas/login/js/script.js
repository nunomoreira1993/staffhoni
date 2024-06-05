$(function () {
    if (typeof ($('body form').attr('data-erro')) != "undefined" && $('body form').attr('data-erro').length > 0) {
        swal({
            title: "Erro!",
            text: $('body form').attr('data-erro'),
            icon: "error",
            button: "Ok",
        });
    }
});
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent Chrome 67 and earlier from automatically showing the prompt
    e.preventDefault();
    // Stash the event so it can be triggered later.
    deferredPrompt = e;
});
