// invoiceDonation('<{$data_case.cCertifiedId}>', '<{$data_owner.cId}>', 'owner')
function invoiceDonation(identity, target) {
    let identify_id = $('[name="' + identity + '"]').val();
    console.log('invoiceDonation: ' + identify_id + ', ' + target);
    let url = '/escrow/invoiceDonation.php?identify_id=' + identify_id + '&target=' + target;
    $.colorbox({iframe:true, width:"90%", height:"50%", href:url});
}
