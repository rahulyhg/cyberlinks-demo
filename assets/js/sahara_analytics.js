function play() {		
    $.ajax({
        url: h + bb + f + "api/analytics/play",
        data: {
            content_id: '92',   //-- live stream content id
            content_provider: '59',  //-- content provider id
			content_type: 'live',
            play: "1"
        },
        cache: !1,
        type: "POST",
        dataType: "json"
    }).done(function(a) {
        id = a
    })
}

function pause(a) {
    id > 0 && $.ajax({
        url: h + bb + f + "api/analytics/pause",
        data: {
            id: id,
            watched_time: a,
            pause: "1"
        },
        cache: !1,
        type: "POST"
    }).done(function() {})
}

function playAds(a) {
    $.ajax({
        url: h + bb + f + "api/analytics/playads",
        data: {
            tag: a,
            broadcaster: '59',  //-- content provider id --//
            play: "1"
        },
        cache: !1,
        type: "post",
        success: function(a) {
            ad_id = a
        }
    })
}

function completeAds(a) {
    $.ajax({
        url: h + bb + f + "api/analytics/ads_complete",
        data: {
            id: ad_id,
            watched_time: a,
            complete: "1",
            pause: 0
        },
        cache: !1,
        type: "POST"
    }).done(function() {})
}
var id = 0,
    duration, pos = 0;
h = "http://";
var ad_duration = 0,
    ad_id = 0;
bb = "multitvsolution.com/";
var f = "multitvfinal/";
jwplayer().onPause(function() {
    pos > 0 ? (pos = parseInt(this.getPosition()) - pos1, pos1 += pos) : (pos = parseInt(this.getPosition()), pos1 = pos), pause(pos1)
}), jwplayer().onPlay(function() {
    play()
}), jwplayer().onAdPlay(function(a) {
    var t = a.tag;
    playAds(t)
}), jwplayer().onAdPause(function() {
    pauseAds(ad_duration)
}), jwplayer().onAdTime(function(a) {
    ad_duration = Math.round(a.position)
}), jwplayer().onAdComplete(function() {
    console.log(ad_duration), completeAds(ad_duration)
});
