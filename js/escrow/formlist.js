$(document).ready(function() {
    var aSelected = [];
                        
    /* Init the table */
    $("#example").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST", 
        "sAjaxSource": "/escrow/formlisttb.php",
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            if ( jQuery.inArray(aData.DT_RowId, aSelected) !== -1 ) {
                $(nRow).addClass('row_selected');
            }
        }
    });
    /* Click event handler */
    $('#example tbody tr').live('click', function () {
        var id = this.id;
        var index = jQuery.inArray(id, aSelected);
        if ( index === -1 ) {
            aSelected.push( id );
        } else {
            aSelected.splice( index, 1 );
        }
        $('#totalCount').html(aSelected.length);
        $(this).toggleClass('row_selected');
    } );
    $('#example tbody tr').live('dblclick', function () {
        var id = this.id.replace('row_', '');
        $('form[name=form_edit]').attr('action', '/escrow/formedit.php');
        $('form[name=form_edit] input[name=id]').val(id);
        $('form[name=form_edit]').submit();
    } );
    
    $('#add').live('click', function () {
        $('form[name=form_add]').attr('action', '/escrow/formadd.php');
        $('form[name=form_add]').submit();
    } );
    
    $('#del').live('click', function () {
        var request = $.ajax({
            url: "/escrow/formdel.php",
            type: "POST",
            data: {
                ids:aSelected
            },
            dataType: "html"
        });
        request.done(function( msg ) {
            alert(msg);
            window.location='/escrow/formlist.php';
        });
    } );
    
    $('#add').button( {
        icons:{
            primary: "ui-icon-info"
        }
    } );
    $('#del').button( {
        icons:{
            primary: "ui-icon-locked"
        }
    } );
    $('#loading').dialog('close');
} );