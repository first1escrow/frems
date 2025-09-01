$(document).ready(function() {
    $( "#tabs" ).tabs({
        selected: 0
    });
    
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
		
    var pay_date = $( "#pay_date" ),
    pay_title = $( "#pay_title" ),
    pay_income = $( "#pay_income" ),
    pay_spend = $( "#pay_spend" ),
    pay_total = $( "#pay_total" ),
    pay_remark = $( "#pay_remark" ),
    allFields = $( [] ).add( pay_date ).add( pay_title ).add( pay_income ).add( pay_spend ).add( pay_total ).add( pay_remark ),
    tips = $( ".validateTips" );

    function updateTips( t ) {
        tips
        .text( t )
        .addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }

    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }
		
    $( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 370,
        width: 450,
        modal: true,
        buttons: {
            "新增款項": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );

                bValid = bValid && checkLength( pay_date, "pay_date", 10, 10 );
                bValid = bValid && checkLength( pay_title, "pay_title", 5, 255 );
                bValid = bValid && checkLength( pay_income, "pay_income", 1, 8 );
                bValid = bValid && checkLength( pay_spend, "pay_spend", 1, 8 );
                bValid = bValid && checkLength( pay_total, "pay_remark", 0, 255 );
                bValid = bValid && checkLength( pay_remark, "pay_total", 0, 255 );

                //                bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
                // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
                //                bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
                //                bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );

                if ( bValid ) {
                    $( "#users tbody" ).append( "<tr>" +
                        "<td><input type='text' name='payment_date' size='10' maxlength='10' value='" + pay_date.val() + "'></td>" + 
                        "<td><input type='text' name='payment_title' size='30' maxlength='255' value='" + pay_title.val() + "'></td>" + 
                        "<td><input type='text' name='payment_income' size='10' maxlength='8' value='" + pay_income.val() + "'></td>" +
                        "<td><input type='text' name='payment_spend' size='10' maxlength='8' value='" + pay_spend.val() + "'></td>" +
                        "<td><input type='text' name='payment_total' size='10' maxlength='8' value='" + pay_total.val() + "'></td>" +
                        "<td><input type='text' name='payment_remark' size='25' maxlength='255' value='" + pay_remark.val() + "'></td>" +
                        "</tr>" ); 
                    $( this ).dialog( "close" );
                }
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            allFields.val( "" ).removeClass( "ui-state-error" );
        }
    });
    
    $('#cancel').live('click', function () {
        window.location='/escrow/formlist.php';
    });
    
    $('#add').live('click', function () {
        var ecmoney = new Array();
        var payment_date = new Array();
        var payment_title = new Array();
        var payment_income = new Array();
        var payment_spend = new Array();
        var payment_total = new Array();
        var payment_remark = new Array();
        $.each( $('[name=ecmoney]'), function(i, l){
            ecmoney[i] = l.value;
        });
        $.each( $('[name=payment_date]'), function(i, l){
            payment_date[i] = l.value;
        });
        $.each( $('[name=payment_title]'), function(i, l){
            payment_title[i] = l.value;
        });
        $.each( $('[name=payment_income]'), function(i, l){
            payment_income[i] = l.value;
        });
        $.each( $('[name=payment_spend]'), function(i, l){
            payment_spend[i] = l.value;
        });
        $.each( $('[name=payment_total]'), function(i, l){
            payment_total[i] = l.value;
        });
        $.each( $('[name=payment_remark]'), function(i, l){
            payment_remark[i] = l.value;
        });
        
        var request = $.ajax({
            url: "/escrow/formaddnew.php",
            type: "POST",
            data: {
                ecmoney : ecmoney,
                payment_date : payment_date,
                payment_title : payment_title,
                payment_income : payment_income,
                payment_spend : payment_spend,
                payment_total : payment_total,
                payment_remark : payment_remark, 
                address:$('[name=address]').val(), 
                application_no:$('[name=application_no]').val(), 
                contract:$('[name=contract]').val(), 
                realestate_unit:$('[name=realestate_unit]').val(), 
                scrivener:$('[name=scrivener]').val(), 
                bank_account:$('[name=bank_account]').val(), 
                loan_limit:$('[name=loan_limit]').val(), 
                money1:$('[name=money1]').val(), 
                money2:$('[name=money2]').val(), 
                money3:$('[name=money3]').val(), 
                seller_bank:$('[name=seller_bank]').val(), 
                seller_accountno:$('[name=seller_accountno]').val(), 
                seller_account:$('[name=seller_account]').val(), 
                buyer_bank:$('[name=buyer_bank]').val(), 
                buyer_accountno:$('[name=buyer_accountno]').val(), 
                buyer_account:$('[name=buyer_account]').val(),
                realestate_bank:$('[name=realestate_bank]').val(), 
                realestate_accountno:$('[name=realestate_accountno]').val(),
                realestate_account:$('[name=realestate_account]').val(), 
                buyer:$('[name=buyer]').val(), 
                buyer_number:$('[name=buyer_number]').val(), 
                buyer_guarantee:$('[name=buyer_guarantee]').val(), 
                seller:$('[name=seller]').val(), 
                seller_number:$('[name=seller_number]').val(), 
                seller_guarantee:$('[name=seller_guarantee]').val(),
                scrivener:$('[name=scrivener]').val(),
                buildno:$('[name=buildno]').val(),
                licensing_unit:$('[name=licensing_unit]').val()
            },
            dataType: "html"
        });
        request.done(function( msg ) {
            alert(msg);
            window.location='/escrow/formlist.php';
        });
    } );
    
    $('#save').live('click', function () {
        var ecmoney = new Array();
        var payment_date = new Array();
        var payment_title = new Array();
        var payment_income = new Array();
        var payment_spend = new Array();
        var payment_total = new Array();
        var payment_remark = new Array();
        $.each( $('[name=ecmoney]'), function(i, l){
            ecmoney[i] = l.value;
        });
        $.each( $('[name=payment_date]'), function(i, l){
            payment_date[i] = l.value;
        });
        $.each( $('[name=payment_title]'), function(i, l){
            payment_title[i] = l.value;
        });
        $.each( $('[name=payment_income]'), function(i, l){
            payment_income[i] = l.value;
        });
        $.each( $('[name=payment_spend]'), function(i, l){
            payment_spend[i] = l.value;
        });
        $.each( $('[name=payment_total]'), function(i, l){
            payment_total[i] = l.value;
        });
        $.each( $('[name=payment_remark]'), function(i, l){
            payment_remark[i] = l.value;
        });
        
        var request = $.ajax({
            url: "/escrow/formchangeold.php",
            type: "POST",
            data: {
                eId:$('[name=eId]').val(), 
                ecmoney : ecmoney,
                payment_date : payment_date,
                payment_title : payment_title,
                payment_income : payment_income,
                payment_spend : payment_spend,
                payment_total : payment_total,
                payment_remark : payment_remark, 
                address:$('[name=address]').val(), 
                application_no:$('[name=application_no]').val(), 
                contract:$('[name=contract]').val(), 
                realestate_unit:$('[name=realestate_unit]').val(), 
                scrivener:$('[name=scrivener]').val(), 
                bank_account:$('[name=bank_account]').val(), 
                loan_limit:$('[name=loan_limit]').val(), 
                money1:$('[name=money1]').val(), 
                money2:$('[name=money2]').val(), 
                money3:$('[name=money3]').val(), 
                seller_bank:$('[name=seller_bank]').val(), 
                seller_accountno:$('[name=seller_accountno]').val(), 
                seller_account:$('[name=seller_account]').val(), 
                buyer_bank:$('[name=buyer_bank]').val(), 
                buyer_accountno:$('[name=buyer_accountno]').val(), 
                buyer_account:$('[name=buyer_account]').val(),
                realestate_bank:$('[name=realestate_bank]').val(), 
                realestate_accountno:$('[name=realestate_accountno]').val(),
                realestate_account:$('[name=realestate_account]').val(), 
                buyer:$('[name=buyer]').val(), 
                buyer_number:$('[name=buyer_number]').val(), 
                buyer_guarantee:$('[name=buyer_guarantee]').val(), 
                seller:$('[name=seller]').val(), 
                seller_number:$('[name=seller_number]').val(), 
                seller_guarantee:$('[name=seller_guarantee]').val(),
                scrivener:$('[name=scrivener]').val(),
                buildno:$('[name=buildno]').val(),
                licensing_unit:$('[name=licensing_unit]').val()
                
            },
            dataType: "html"
        });
        request.done(function( msg ) {
            alert(msg);
            window.location='/escrow/formlist.php';
        });
    } );
    
    $('#print').live('click', function () {
        $('form[name=form_print]').attr('action', '/escrow/formprint.php');
        $('form[name=form_print]').submit();
    } );
    

    $( "#create-user" )
    .button()
    .click(function() {
        $( "#dialog-form" ).dialog( "open" );
    });
    $( "#pay_date" ).datepicker({
        dayNamesMin: ['日', '一', '二', '三', '四', '五', '六'],
        dateFormat: 'yy-mm-dd', 
        changeMonth: true,
        changeYear: true
    });
    $('#add').button( {
        icons:{
            primary: "ui-icon-info"
        }
    } );
    $('#save').button( {
        icons:{
            primary: "ui-icon-info"
        }
    } );
    $('#cancel').button( {
        icons:{
            primary: "ui-icon-info"
        }
    } );
    $('#print').button( {
        icons:{
            primary: "ui-icon-locked"
        }
    } );
    
});
