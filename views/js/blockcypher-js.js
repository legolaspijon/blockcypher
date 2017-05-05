
jQuery(document).ready(function(){

    var address = $('#address').html();

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
    generateQr('qrcode', address);

    $(document).on('click', '#check', null, function (e) {
        e.preventDefault();
        checkAddressData('check', address);
    });

    function checkAddressData(url, address)
    {
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: {address: address},
            dataType: 'json',
            success: function (response) {
                alert('qwerty');
                if (response.redirect) {
                    // window.location.href = response.redirect;
                }

                $('.unconfirmed').html(response.unconfirmed);
                $('.received').html(response.received);

            },
            error: function(data){
                console.log(data);
            }

        });
    }

    setInterval(function(){
        checkAddressData('check', address);}
        , 16500);

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


// Our countdown plugin takes a duration, and an optional message
jQuery.fn.countdown = function (duration, message) {

    // If no message is provided, we use an empty string
    message = message || "";
    // Get reference to container, and set initial content
    var container = jQuery(this[0]).html(duration + message);
    // Get reference to the interval doing the countdown
    var countdown = setInterval(function () {
        // If seconds remain
        if (--duration) {
            // Update our container's message
            if (duration < 2) {
                setTimeout(function () {
                    container.html("<span class='cryptowoo-warning'>" + 'please wait' + " <i class='fa fa-refresh fa-spin'></i>");
                }, 3000);
                // Wait 3 seconds to make sure the order has really timed out, then force processing of orders in database
                jQuery.ajax({
                    type: 'POST',
                    url: 'check',
                    data: {},
                    dataType: 'json',
                    success: function () {
                        window.location.href = CryptoWoo.redirect;
                        clearInterval(countdown);
                    }
                });
            } else {
                container.html(secondsTimeSpanToHMS(duration));
            }
            // Otherwise
        } else {
            // Clear the countdown interval
            clearInterval(countdown);
        }
        // Run interval every 1000ms (1 second)
    }, 1000);
};

// Format seconds to hh:mm:ss
function secondsTimeSpanToHMS(s) {
    var h = Math.floor(s/3600); //Get whole hours
    s -= h*3600;
    var m = Math.floor(s/60); //Get remaining minutes
    s -= m*60;
    return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
}


jQuery(".countdown").countdown($('.countdown').html(), '');
