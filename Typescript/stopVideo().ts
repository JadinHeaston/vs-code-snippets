async function stopVideo(video: HTMLVideoElement){
    video.pause();
    video.currentTime = 0;
}