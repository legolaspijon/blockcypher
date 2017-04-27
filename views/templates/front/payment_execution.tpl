{extends file='page.tpl'}
{block name='page_content'}
    <div class="container wrap">
        <h1>Order details</h1><br>
        <div class="row">
            <div class="col-md-6">
                <div class="row top-space">
                    <div class="col-md-2 col-xs-12"><span class="od-title">Send:</span></div>
                    <div class="col-md-7 col-xs-12"><span class="blocktext">{$order_total}BTC</div>
                </div>
                <div class="row top-space">
                    <div class="col-md-2 col-xs-12"><span class="od-title">To:</span></div>
                    <div class="col-md-10 col-xs-12"><span class="blocktext" id="address">{$payment_address}</div>
                </div>
                <div class="row top-space">
                    <div class="col-md-5 col-xs-12">
                        <div class="row top-space">
                            <div class="col-md-6"><span class="od-title">Unconfirmed:</span></div>
                            <div class="col-md-6"><span class="blocktext">{$amount_uncofirmed}</span></div>
                        </div>
                        <div class="row top-space">
                            <div class="col-md-6"><span class="od-title">Confirmed:</span></div>
                            <div class="col-md-6"><span class="blocktext">{$amount_receive}</span></div>
                        </div>
                        <div class="row top-space">
                            <div class="col-md-6"><span class="od-title">Timer:</span></div>
                            <div class="col-md-6"><span class="blocktext" id="timer">15 {l s='min' mod='blockcypher'}</span></div>
                        </div>
                    </div>
                    <div class="col-md-7 col-xs-12">
                        <div class="check-status"><a href="#" id="check">Check status</a></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="qrcode"></div>
            </div>
        </div>
    </div>
{/block}

