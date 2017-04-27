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
    });

    setInterval(function () {
        var vars = {
            'action': 'poll',
            'address': 'address'
        };

        var ajaxurl = 'check';
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: vars,
            dataType: 'json',
            success: function (response) {
                if (response.received == null)
                    response.received = 0.00;
                if (response.unconfirmed == null)
                    response.unconfirmed = 0.00;
                if (response.redirect) {
                    //alert('redirect: '+response.redirect);
                    window.location.href = response.redirect;
                }
            }
        });
    }, 16500);

});

function selectText(containerid) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select();
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
    }
}
