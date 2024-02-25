+++
title = "Preparing videos for Mastodon using ffmpeg"
+++

Recently I was recording a screencast of [me launching Koko Analytics as a Progressive Web App from my OS launcher](https://toot.re/@dvk/111342580812680580).
Trying to upload the video to Mastodon failed with the following error message:

> 422 1000fps videos are not supported

According to the [Mastodon documentation](https://docs.joinmastodon.org/user/posting/), the following limits are in place for video uploads:

- Supported formats: MP4, M4V, MOV, WebM
- Maximum size: 40MB
- Maximum bitrate: 1300kbps
- Maximum framerate: 60fps

The documentation does not state anything about a maximum resolution, but [other sources](https://www.paulox.net/2022/11/17/resize-a-video-with-ffmpeg-for-mastodon/) stated a video could be no larger than 1920 x 1200 px.

## Downsample video resolution, fps and bitrate using ffmpeg

[ffmpeg](https://www.ffmpeg.org/) is an incredible piece of software to convert video between various formats.

To use it to downsample a video to match the Mastodon requirements, we can use the following command:

```sh
ffmpeg                              \
  -i in.webm                        \
  -filter:v scale=1900:-1,fps=60    \
  out.webm
```

This resizes the video to 1900px wide while preserving the aspect ratio.
It also limits the framerate to 60 fps.

If your video has a bitrate higher than 1300kbps, instruct the encoder to aim for a lower bitrate using the `-b:v` argument.

```sh
ffmpeg                                    \
  -i in.webm                              \
  -filter:v scale=1900:-1,fps=60          \
  -b:v 1300k -maxrate 1300k -bufsize=650k \
  out.webm
```

Since `-b:v` specifies the target (average) bitrate for the encoder to use, specifying it only makes sense if your video has a larger bitrate than what is required by Mastodon.

I had to do a bit of browsing around to find the proper set of arguments, so hopefully sharing this here helps someone shave a few minutes off their day.

## Resources

- [Scaling - ffmpeg](http://trac.ffmpeg.org/wiki/Scaling#KeepingtheAspectRatio)
- [Changing the framerate - ffmpeg](https://trac.ffmpeg.org/wiki/ChangingFrameRate)
- [Limiting the output bitrate - ffmpeg](https://trac.ffmpeg.org/wiki/Limiting%20the%20output%20bitrate)
