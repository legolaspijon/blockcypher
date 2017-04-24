{extends file='page.tpl'}

{block name='page_content'}
    <!-- QR Code HERE -->
    <style>
        .blocktext{
            background: #ddd;
            padding: 3px;
            text-align: center;
            display: inline-block;
            border-radius: 6px;
            width: 100%;
        }
        .top-space{
            margin-top:6px;
        }

    </style>
    <div class="container">
        <h1>Order details</h1><br>
        <div class="row">
            <div class="col-md-8">
                <div class="row top-space">
                    <div class="col-md-2 col-xs-12"><span class="od-title">Send:</span></div>
                    <div class="col-md-7 col-xs-12"><span class="blocktext">{$order_total}BTC</div>
                </div>
                <div class="row top-space">
                    <div class="col-md-2 col-xs-12"><span class="od-title">To:</span></div>
                    <div class="col-md-7 col-xs-12"><span class="blocktext">{$payment_address}</div>
                </div>
                <div class="row top-space">
                    <div class="col-md-5 col-xs-12">
                        <div class="row top-space">
                            <div class="col-md-6"><span class="od-title">Unconfirmed:</span></div>
                            <div class="col-md-6"><span class="blocktext">0</span></div>
                        </div>
                        <div class="row top-space">
                            <div class="col-md-6"><span class="od-title">Confirmed:</span></div>
                            <div class="col-md-6"><span class="blocktext">0</span></div>
                        </div>
                    </div>
                    <div class="col-md-7 col-xs-12">
                        <a href="#">Check status</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                QR CODE HERE
            </div>
        </div>
    </div>
{/block}

