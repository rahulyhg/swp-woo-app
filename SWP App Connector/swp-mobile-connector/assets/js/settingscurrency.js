jQuery(function ($) {
    var debug = false; var params = wooconnector_settings_currency_js_params; var listClass = new Array('wooconnector-currency-currency', 'wooconnector-currency-rate', 'wooconnector-currency-symbol', 'wooconnector-currency-icon', 'wooconnector-currency-position', 'wooconnector-currency-tseparator', 'wooconnector-currency-dseparator', 'wooconnector-currency-ndecima'); var i; var cancelEditBaseCurrency = function (classBlock) { $('.wooconnector-table-currency-tr').first().children('td').children('.' + classBlock).keydown(function () { return false; }); }
    function addAjaxLoaded() { var ajaxload = '<div class="wooconnector-ajax-loaded-position"></div>'; $('body').append(ajaxload); }
    function validateNumber(input) { var numberReg = /^[0-9]+$/; if (!numberReg.test(input)) { return false; } else { return true; } }
    function validateRate(input) { var numberReg = /^\d{0,15}(\.\d{1,15})?$/; if (!numberReg.test(input)) { return false; } else { return true; } }
    function validateCurrency(input) { var currenReg = /^[A-Za-z]+$/; if (!currenReg.test(input)) { return false; } else { return true; } }
    function inputCurrency(thisDiv, currency) { thisDiv.attr('name', "wooconnector_currency_settings[" + currency + "][currency]"); thisDiv.attr('id', 'wooconnector_currency-' + currency); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').attr('data-currency', currency); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-rate').attr('name', "wooconnector_currency_settings[" + currency + "][rate]"); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-rate').attr('id', 'wooconnector_currency-' + currency + '-rate'); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-position').attr('name', "wooconnector_currency_settings[" + currency + "][position]"); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-position').attr('id', 'wooconnector_currency-' + currency + '-position'); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-tseparator').attr('name', "wooconnector_currency_settings[" + currency + "][thousand_separator]"); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-tseparator').attr('id', 'wooconnector_currency-' + currency + '-tseparator'); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-dseparator').attr('name', "wooconnector_currency_settings[" + currency + "][decimal_separator]"); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-dseparator').attr('id', 'wooconnector_currency-' + currency + '-dseparator'); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-ndecima').attr('name', "wooconnector_currency_settings[" + currency + "][number_of_decimals]"); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-ndecima').attr('id', 'wooconnector_currency-' + currency + '-ndecima'); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-delete-currency').attr('id', 'wooconnector_currency-' + currency); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-delete-currency').attr('data-id', currency); }
    function showModalDelete(currency) { if (Number.isInteger(currency)) { $('.wooconnector-table-currency-tr').each(function (key, val) { if ($(this).data('index') == currency) { $(this).remove(); } }) } else { $.ajax({ method: "POST", url: params.domain + "/wp-json/wooconnector/settings/deletecurrency", beforeSend: function () { $('#wooconnector_delete_currency-' + currency).parent('td').append('<div class="wooconnector-ajax-loaded-delete"></div>'); $('#wooconnector_delete_currency-' + currency).remove(); }, data: { currencykey: currency } }).done(function (msg) { location.reload(); }); } }
    function getRatesbyAjax(thisDiv, currency) { var dataajax = { 'action': 'wooconnector_get_rate_by_ajax', 'security': params.security, 'from': params.currency.toUpperCase(), 'to': currency }; $.ajax({ method: "POST", url: params.ajax_url, beforeSend: function () { thisDiv.parent('td').children('.wooconnector-currency-rate').val('Loading..'); }, data: dataajax }).done(function (msg) { if ($.isNumeric(msg)) { thisDiv.parent('td').children('.wooconnector-currency-rate').val(msg); } else { addShowNotice('errorrate'); thisDiv.parent('td').children('.wooconnector-currency-rate').val(''); thisDiv.parent('td').children('.wooconnector-currency-rate').attr('placeholder', 'Please Enter Rates...'); } }).fail(function (msg) { addShowNotice('errorrate'); thisDiv.parent('td').children('.wooconnector-currency-rate').val(''); thisDiv.parent('td').children('.wooconnector-currency-rate').attr('placeholder', 'Please Enter Rates...'); }) }
    function selectedCurrency() {
        var listcurrencys = params.listcurrencys; var listcurrency = params.listcurrency; var htmlcurrencys = '<select class="wooconnector-currency-settings wooconnector-currency-currency">'; htmlcurrencys += '<option value="-1">Default Selected</option>'; for (i = 0; i < listcurrency.length; i++) { if (listcurrencys[listcurrency[i]] != undefined) { htmlcurrencys += '<option value="' + listcurrency[i] + '">' + listcurrencys[listcurrency[i]] + '</option>'; } }
        htmlcurrencys += '</select>'; return htmlcurrencys;
    }
    function switchEditCurrency(thisDiv) { thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-settings').removeClass('wooconnector-hidden'); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-settings-span').addClass('wooconnector-hidden'); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-settings-rate').removeClass('wooconnector-hidden'); thisDiv.parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-button-getrate').removeClass('wooconnector-hidden'); thisDiv.addClass('wooconnector-hidden'); }
    function removeNotice(index) { setTimeout(function () { $('.notice-wooconnector').each(function (key, val) { if ($(this).data('index') == index) { $(this).remove(); } }) }, 10000) }
    function addShowNotice(type) { var html; if ($('.notice').length > -1) { var index = $('.notice').length + 1; html = '<div data-index="' + index + '" class="notice notice-error notice-wooconnector"><p class="wooconnector_currency_' + type + '">' + params.listtextmodal[type] + '</p></div>'; if ($('.wooconnector_currency_' + type).length > 0) { return false; } else { $('.notice').last().after(html); removeNotice(index); } } else { var index = 1; html = '<div data-index="' + index + '" class="notice notice-error notice-wooconnector"><p class="wooconnector_currency_' + type + '">' + params.listtextmodal[type] + '</p></div>'; $('.wooconnector-settings').prepend(html); removeNotice(index); } }
    function addShowNoticeDelete() { if ($('.notice').length > -1) { var index = $('.notice').length + 1; var html = '<div data-index="' + index + '" class="notice notice-success notice-wooconnector"><p class="wooconnector_currency_delete_notice">' + params.listtextmodal.delete + '</p></div>'; if ($('.wooconnector_currency_delete_notice').length > 0) { return false; } else { $('.notice').last().after(html); removeNotice(index); } } else { var index = 1; var html = '<div data-index="' + index + '" class="notice notice-success notice-wooconnector"><p class="wooconnector_currency_delete_notice">' + params.listtextmodal.delete + '</p></div>'; $('.wooconnector-settings').prepend(html); removeNotice(index); } }
    function addCurrency() {
        var index = $('.wooconnector-table-currency-tr').last().data('index') + 1; var html = '<tr class="wooconnector-gray wooconnector-table-currency-tr" data-index=' + index + '>'; html += '<td>' + selectedCurrency() + '</td>'; html += '<td><input required="true" type="input" class="wooconnector-settings-rate wooconnector-currency-rate" value="" /><input type="button" class="wooconnector-button-getrate" title="Get rate by Google" value="Get"/></td>'; html += '<td><select class="wooconnector-currency-settings wooconnector-currency-position"><option value="left">Left (&#36;99.99)</option><option value="right">Right (99.99&#36;)</option><option value="left_space">Left with space (&#36; 99.99)</option><option value="right_space">Right with space (99.99 &#36;)</option></select></td>'; html += '<td><input required="true" type="input" class="wooconnector-currency-settings wooconnector-currency-tseparator" value="," /></td>'; html += '<td><input required="true" type="input" class="wooconnector-currency-settings wooconnector-currency-dseparator" value="." /></td>'; html += '<td><input required="true" type="input" class="wooconnector-currency-settings wooconnector-currency-ndecima" value="2" /></td>'; html += '<td></td>'; html += '<td><a class="wooconnector-delete-currency" data-index="' + index + '" data-id=""><span class="dashicons dashicons-trash"></span></a></td>'; html += '</tr>'; $('.wooconnector-table-currency-tr').last().after(html); $('.wooconnector-currency-currency').change(function () { var currency = $(this).val().toLowerCase(); var thisDiv = $(this); inputCurrency(thisDiv, currency); }); $('.wooconnector-delete-currency').click(function () {
            if ($(this).attr('id') == 'wooconnector_delete_currency-' + params.currency) { return false; }
            var index = $(this).data('index'); showModalDelete(index); addShowNoticeDelete();
        }); $('.wooconnector-button-getrate').click(function () {
            var currency = $(this).parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-currency').val(); if (currency == '-1') { addShowNotice('dontselect'); return false; }
            var thisDiv = $(this); getRatesbyAjax(thisDiv, currency);
        })
    }
    $('.wooconnector-button-getrate').click(function () {
        var currency = $(this).parent('td').parent('.wooconnector-table-currency-tr').children('td').children('.wooconnector-currency-currency').val(); if (currency == '-1') { addShowNotice('dontselect'); return false; }
        var thisDiv = $(this); getRatesbyAjax(thisDiv, currency);
    })
    $('.wooconnector-delete-currency').click(function () {
        if ($(this).attr('id') == 'wooconnector_delete_currency-' + params.currency) { return false; }
        var index = $(this).data('id'); showModalDelete(index);
    }); $(window).load(function () {
        for (i = 0; i < listClass.length; i++) { cancelEditBaseCurrency(listClass[i]); }
        if ($('.notice-wooconnector').length != '-1') { setTimeout(function (e) { $('.notice-wooconnector').each(function (key, val) { if ($(this).data('index') == undefined) { $(this).remove(); } }) }, 10000); }
        addAjaxLoaded();
    })
    $('#wooconnector-add-currency').on('click', function () { addCurrency(); })
    $('.wooconnector-edit-currency').click(function () { switchEditCurrency($(this)); })
    $('#wooconnector-save-currency').click(function (e) {
        var listcurrency = []; var listcheckcurrency = params.listcurrency; $('.wooconnector-table-currency-tr').each(function (key, val) {
            var currency = $(this).children('td').children('.wooconnector-currency-currency').val(); var rate = $(this).children('td').children('.wooconnector-currency-rate').val(); if (listcurrency.indexOf(currency) != -1) { e.preventDefault(); addShowNotice('exist'); return false; }
            if (listcheckcurrency.indexOf(currency) == -1) { e.preventDefault(); addShowNotice('notfound'); return false; }
            if (!validateRate(rate)) { e.preventDefault(); addShowNotice('rate'); return false; }
            listcurrency.push(currency);
        })
    })
})