jQuery(document).ready(function(){

    function generateQr(selector_id, address, currency)
    {
        currency = typeof currency !== 'undefined' ? currency : 'bitcoin';

        var qrcode = new QRCode(selector_id, {
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });

        qrcode.makeCode(currency +': '+address);
    }

    generateQr('qrcode', $('#address').html());

    $(document).on('click', '#check', null, function (e) {
        e.preventDefault();

        $.ajax({
            type: 'post',
            url: 'check',
            dataType: 'text',
            success: function(data){
                alert(data);
            }
        })
    })
});