{extends file='page.tpl'}
{block name='page_content'}
    <div class="container wrap">
        <h1>Order details</h1>
        <div class="row">
            {if $status == $statuses['BLOCKCYPHER_PAYMENT_WAIT']}
                <div class="col-md-6">
                    <div class="row top-space">
                        <div class="col-md-2 col-xs-12"><span class="od-title">Send:</span></div>
                        <div class="col-md-7 col-xs-12"><span class="blocktext"><span id="amount_total" onclick="selectText('amount_total')">{$order_total}</span>BTC</span></div>
                    </div>
                    <div class="row top-space">
                        <div class="col-md-2 col-xs-12"><span class="od-title">To:</span></div>
                        <div class="col-md-10 col-xs-12"><span class="blocktext" id="address" onclick="selectText('address')">{$payment_address}</div>
                    </div>
                    <div class="row top-space">
                        <div class="col-md-7 col-xs-12">
                            <div class="row top-space">
                                <div class="col-md-6"><span class="od-title">Unconfirmed:</span></div>
                                <div class="col-md-6"><span class="blocktext unconfirmed">{$amount_unconfirmed}</span></div>
                            </div>
                            <div class="row top-space">
                                <div class="col-md-6"><span class="od-title">Confirmed:</span></div>
                                <div class="col-md-6"><span class="blocktext received">{$amount_receive}</span></div>
                            </div>
                            <div class="row top-space">
                                <div class="col-md-6"><span class="od-title">Timer:</span></div>
                                <div class="col-md-6"><span class="blocktext countdown">{$timeLeft}</span></div>
                            </div>
                        </div>
                        <div class="col-md-5 col-xs-12">
                            <div class="check-status"><a href="#" id="check">Check status</a></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="qrcode"></div>
                </div>
            {elseif $status == Configuration::get('BLOCKCYPHER_PAYMENT_EXPIRED')}
                <div class="col-md-12">
                    order expired
                </div>
            {/if}
        </div>
    </div>
{/block}

