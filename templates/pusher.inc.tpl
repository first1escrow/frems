
<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>

<script type="text/javascript">
    $.ajax({
        url: '/includes/verifyPusherChannel.php',
        type: 'POST',
        async: false,
        success: function(response) {
            if (response == 'N') {
                Pusher.logToConsole = true;

                $.ajax({
                    url: '/includes/getPusherKey.php',
                    type: 'POST',
                    async: false,
                    success: function(response) {
                        const pusherKey = response;

                        const pusher = new Pusher(pusherKey, {
                            cluster: "ap3",
                            forceTLS: false,
                        });
                        
                        const channel = pusher.subscribe("first1-notify-<{$smarty.session.member_id}>");
                        channel.bind("first1-notify", (data) => {
                            if (data == 'FINISH') {

                            } else {
                                data = JSON.parse(data);

                                if (data.alert) {
                                    data.alert = nl2br(data.alert);
                                    data.alert = replaceUrlsWithLinks(data.alert, '（請點我審核）');
                                    let el = '<div style="width:450px;">' + data.alert + '</div>';
                                    $.colorbox({html:el, width:"500px", height:"300px"}) ;
                                }
                            }
                        });
                    }
                });
            }
        }
    });
    
    function replaceUrlsWithLinks(inputString, showText) {
        const urlPattern = /(https?:\/\/[^\s]+)/g;
        return inputString.replace(urlPattern, function(url) {
            return `<a href="${url}" target="_blank">${showText}</a>`;
        });
    }

    function nl2br(str, isXhtml = false) {
        const breakTag = isXhtml ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, `$1${breakTag}`);
    }
</script>
