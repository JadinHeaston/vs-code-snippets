
async function getVideoCover(video: HTMLVideoElement, seekTo = 0.0, quality = 0.75) {
    return new Promise(() => {
        //Load metadata of the video to get video duration and dimensions
        video.addEventListener('loadedmetadata', async function metadataHandler() {
            video.removeEventListener('seeked', metadataHandler);
            await getVideoThumbnail(video, seekTo, quality);
            applyVideoThumbnail(video);
        });

    });
}


async function getVideoThumbnail(video: HTMLVideoElement, seekTo = 0.0, quality = 0.75) {
    return new Promise((resolve, reject) => {
        //Extract video thumbnail once seeking is complete.
        video.addEventListener('seeked', function seekHandler() {
            video.removeEventListener('seeked', seekHandler);
            // console.log('video is now paused at %ss.', seekTo);

            // define a canvas to have the same dimension as the video
            const canvas = document.createElement("canvas");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            //Draw the video frame to canvas
            const ctx = canvas.getContext("2d");

            if (ctx === null)
                return;
            
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height); //Drawing image.
            
            //Return the canvas image as a data URL
            let dataURL = ctx.canvas.toDataURL('image/jpeg');
            video.setAttribute('thumbnail', dataURL);

            //Returning to initial point.
            video.currentTime = 0;
            resolve(dataURL);
        });

        //Seek to user defined timestamp (in seconds) if possible
        if (video.duration < seekTo) {
            reject("Video is too short. (" + seekTo + ")");
            return;
        }
        
        //Delay seeking or else 'seeked' event won't fire on Safari
        // setTimeout(() => {
            video.currentTime = seekTo;
        // }, 500);

    });
}

async function applyVideoThumbnail(video: HTMLVideoElement) {
    if (video.getAttribute('thumbnail') === undefined || video.getAttribute('thumbnail') === null)
        return;
    
    let cover = <HTMLDivElement>video.previousElementSibling;

    //Verifying cover
    if (!cover.classList.contains('video-thumbnail'))
        return;
    
    let img = <HTMLImageElement>cover.firstElementChild;
    img.src = <string>video.getAttribute('thumbnail');
}