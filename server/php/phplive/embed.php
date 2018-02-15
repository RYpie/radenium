<html>
    <head>
        <title>Embed Video</title>
        <link rel="stylesheet" type="text/css" href="css/embed.css" />
    </head>
    <body>
        <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
        <video class="video_player" id="video"></video>
        <script>
          if(Hls.isSupported()) {
            var video = document.getElementById('video');
            var hls = new Hls();
            hls.loadSource('live/student25/playlist.m3u8');
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED,function() {
              video.play();
          });
         }
         // hls.js is not supported on platforms that do not have Media Source Extensions (MSE) enabled.
         // When the browser has built-in HLS support (check using `canPlayType`), we can provide an HLS manifest (i.e. .m3u8 URL) directly to the video element throught the `src` property.
         // This is using the built-in support of the plain video element, without using hls.js.
          else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = 'live/student25/playlist.m3u8';
            video.addEventListener('canplay',function() {
              video.play();
            });
          }
        </script>
    </body>
</html>