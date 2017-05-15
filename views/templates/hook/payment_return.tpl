{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

    <div class="container wrap">
        <h1>Order details</h1>
        <div class="row">
            {if $status == $statuses['BLOCKCYPHER_PAYMENT_WAIT']}
                <p>Please use this detail for paid this order</p>
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
                <div class="col-md-12 alert alert-info">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    Payment expired
                </div>
                {include file='module:blockcypher/views/templates/hook/_partials/payment_info.tpl'}
            {elseif $status == Configuration::get('BLOCKCYPHER_PAYMENT_RECEIVED')}
                <div class="col-md-12 alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    Your payment has been received. Thank you for shopping with us.
                </div>
                {include file='module:blockcypher/views/templates/hook/_partials/payment_info.tpl'}
            {/if}
        </div>
    </div>



