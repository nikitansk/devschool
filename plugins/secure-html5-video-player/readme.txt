=== Secure HTML5 Video Player ===
Contributors: Lucinda Brown, Jinsoo Kang
Tags: html5, video, player, secure, javascript, m4v, mp4, ogg, ogv, theora, webm, skins, media server, youtube, vimeo, amazon, s3
Requires at least: 3.0
Tested up to: 4.3.1
Stable tag: 3.14
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Secure HTML5 Video Player allows you to play HTML5 video on modern browsers. Videos can be served privately; pseudo-streamed from a secured directory or via S3.

== Description ==

A video plugin for WordPress built on the VideoJS HTML5 video player library. Allows you to embed video in your post or page using HTML5 with Flash fallback support for non-HTML5 browsers.  Settings can be easily configured with a control panel and simplified short codes.  Video files can be served from a secured private directory or from an Amazon S3 compatible file storage service.  Youtube or Vimeo video may be used as a fallback mechanism, or as primary videos, with HTML5 videos acting as fallbacks should the posted videos go away.

See <a href="http://www.trillamar.com/webcraft/secure-html5-video-player/">www.trillamar.com/secure-html5-video-player/</a> for additional information about Secure HTML5 Video Player.
See <a href="http://videojs.com/">VideoJS.com</a> for additional information about VideoJS.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the 'secure-html5-video-player' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. If you are self serving videos from a cache directory, make sure your webserver is configured so that 'video/ogg' and 'video/webm' are recognized file types.
4. In Settings -> Secure HTML5 Video Player, specify the directory (or the S3 service) where the videos are located.
5. Upload your videos and images to the directory or service you specified.
6. Use the [video] shortcode in your post or page using the following options.


Video Shortcode Options
-----------------------

### file
The file name of the video without the file extension. The video directory set in the control panel is searched for files with this name and with file extensions: mp4, m4v, ogv, ogg, theora.ogv, webm, png, jpg, jpeg, and gif. The files that match are automatically used in the video tag and poster displayed in the page. For example, if you have videos: myclip.mp4, myclip.ogv, myclip.webm, and the poster image: myclip.png; you need only set a file value of "myclip". To select a video in a subdirectory within the video directory, use the relative path to the video file from the video directory.

    [video file="myclip"]

    [video file="path/to/myclip"]


### vimeo
The Vimeo video ID.  A Vimeo video can be used as the primary video, with the HTML5 video as a fallback mechanism if the video is not available on the Vimeo service.  A Vimeo video can alternatively be used as the fallback when a specifed HTML5 video is not available.

    [video vimeo="46623590"]

### youtube
The Youtube video ID.  A Youtube video can be used as the primary video, with the HTML5 video as a fallback mechanism if the video is not available on the Youtube service.  A Youtube video can alternatively be used as the fallback when a specifed HTML5 video is not available.

    [video youtube="u1zgFlCw8Aw"]

### mp4
The file name or URL of the h.264/MP4 source for the video.

    [video mp4="video_clip.mp4"]

### ogg
The file name or URL of the Ogg/Theora source for the video.

    [video ogg="video_clip.ogv"]

### webm
The file name or URL of the VP8/WebM source for the video.

    [video webm="video_clip.webm"]

### poster
The file name or URL of the poster frame for the video.

    [video poster="video_clip.png"]

### width
The width of the video.

    [video width="640"]

### height
The height of the video.

    [video height="480"]

### preload
Start loading the video as soon as possible, before the user clicks play.

    [video preload="yes"]

### autoplay
Start playing the video as soon as it's ready.

    [video autoplay="yes"]

### loop
Replay the video from the beginning after it completes playing.

		[video loop="yes"]

### controls
Enable or disable video playback controls. (Only applies to the "native" skin.)

    [video controls="no"]


Video Shortcode Examples
------------------------
### Video URL example

    [video mp4="http://video-js.zencoder.com/oceans-clip.mp4" ogg="http://video-js.zencoder.com/oceans-clip.ogg" webm="http://video-js.zencoder.com/oceans-clip.webm" poster="http://video-js.zencoder.com/oceans-clip.png" preload="yes" autoplay="no" width="640" height="264"]

### Video File Example using default settings

    [video file="video_clip"]

### Video File Example using custom settings, with Youtube set as a fallback
    [video file="video_clip" youtube="u1zgFlCw8Aw" preload="yes" autoplay="yes" width="1600" height="900"]


== Screenshots ==

1. Server settings
2. Skin selection
3. Playback settings
4. Compatibility settings
5. Shortcode options and examples
6. Post or page featured video interface
7. Widget interface
8. S3 settings

== Changelog ==

= 3.14 =
* Fixed bad styling inherited from default Wordpress themes for box-sizing.
* Fixed use of deprecated PHP parent constructor.

= 3.13 =
* Fixed a bug Youtube embeds failed to render because of failure to load the Youtube API.

= 3.12 =
* Fixed a bug where saving S3 link expiration time would not get saved if units were set in seconds.

= 3.11 =
* Fixed custom short code setting so that it can be properly set to something other than "video".
* Trimmed S3 setting strings to prevent a hard to track down key inconsistency that can arise from copy and pasting values.

= 3.10 =
* Fixed Settings links in plugin list to prevent spilling over to other plugin items.

= 3.9 =
* Recognizes Firefox support for MP4 video playback.
* Prioritizes MP4 video playback in the ordering of the video sources.
* Added option to set the preference between 2 video file caching options: symbolic-linking and hard-linking/copying.

= 3.8 =
* Optimized videos served over file cache using symbolic links.

= 3.7 =
* Added ability to preview videos in widget and metabox interfaces.
* Added ability to insert short codes into posts from the metabox interface.
* Fixed the link to the settings page from the plugin activation page.

= 3.6 =
* Removed unnecessary error logging.
* Added option to customize the video shortcode name.
* Fixed an issue where cached video files aren't updated if changed within the cache limit time.
* Added a FAQ for Amazon S3 configuration.

= 3.5 =
* Fixed a bug where OGV videos were not detected on Firefox browsers if there was no corresponding WEBM video.
* Added donation button.
* Optimized browser detection.

= 3.4 =
* Removed dependency on Flowplayer for Flash fallback.
* Added back fallback support for Firefox playback of MP4 videos using Flash.

= 3.3 =
* Added GPLv3 licensing info.
* Moved screenshots to the central plugin assets folder.

= 3.2 =
* Upgraded Flowplayer files to the latest version to resolve a security hole.
* Removed option for Flash fall back.  Flash fall back now happens automatically, and only for IE 7 and 8.
* Added option to turn on controls in HTML5 video player for the native skin setting.
* Added Spanish language localization, courtesy of webhostinghub.com.

= 3.1 =
* Added ability to set the duration time for S3 media lifespan.
* Expanded the S3 server list for current Amazon S3 global regions. Note: The S3 server must be specified to the one that matches the region of the bucket.
* Added additional help text in the S3 settings tab.

= 3.0 =
* Added support for Amazon S3 (and compatible file services) for video file storage and secured video serving.
* Fixed an issue where uppercase file extension videos were not recognized.
* Optimized temporary value cache to use APC, if available.

= 2.5 =
* Fixed a typo in the help section.

= 2.4 =
* Made file caching an optional setting over the legacy pseudo streaming via PHP.
* Organized settings into tabs.
* Added option to always display video download links.
* Improved fallback behavior with native skin.
* Added support for organization of videos into folders for secured video files.
* Optimized storage of featured video meta data.

= 2.3 =
* Optimized videos so that they're served from cached directories and filenames. This dramatically improves the performance on hosting providers that limit the resources allocated to PHP scripts.
* Corrected compatibility problems with "W3 Total Cache".

= 2.2 =
* Removed warning messages printed when detecting the installation path in the control panel.

= 2.1 =
* Corrected an issue where the poster image was not restored after the video plays.

= 2.0 =
* Added support for media servers.
* Added support for external video services (Youtube and Vimeo) as primary or fallback media.
* Added a native skin option to use the default player interface in the browser.
* Added localization support.
* Corrected detection of files ending with: .theora.ogv
* Corrected autoplay support

= 1.2 =
* Corrected FAQ to adhere to Wordpress.org's standards.

= 1.1 =
* Added support for playing videos in widgets.
* Added support for looping videos.
* Added screenshots.
* Added answers to frequently asked questions.
* Corrected script tag to be compliant to standards.
* Corrected flash fallback object tag for IE8 compatibility.
* Corrected a syntax error in the VideoJS skin: hu.css

= 1.0 =
* First release.


== Upgrade Notice ==

= 3.14 =
Fixed bad styling inherited from default Wordpress themes for box-sizing. Fixed use of deprecated PHP parent constructor.

= 3.13 =
Fixed a bug Youtube embeds failed to render because of failure to load the Youtube API.

= 3.12 =
Fixed a bug where saving S3 link expiration time would not get saved if units were set in seconds.

= 3.11 =
Fixed custom short code setting so that it can be properly set to something other than "video". Trimmed S3 setting strings to prevent a hard to track down key inconsistency that can arise from copy and pasting values.

= 3.10 =
Fixed Settings links in plugin list to prevent spilling over to other plugin items.

= 3.9 =
Recognizes Firefox support for MP4 video playback. Prioritizes MP4 video playback in the ordering of the video sources. Added option to set the preference between 2 video file caching options: symbolic-linking and hard-linking/copying.

= 3.8 =
Optimized videos served over file cache using symbolic links.

= 3.7 =
Added ability to preview videos in widget and metabox interfaces. Added ability to insert short codes into posts from the metabox interface. Fixed the link to the settings page from the plugin activation page.

= 3.6 =
Removed unnecessary error logging. Added option to customize the video shortcode name. Fixed an issue where cached video files aren't updated if changed within the cache limit time. Added a FAQ for Amazon S3 configuration.

= 3.5 =
Fixed a bug where OGV videos were not detected on Firefox browsers if there was no corresponding WEBM video. Added donation button. Optimized browser detection.

= 3.4 =
Removed dependency on Flowplayer for Flash fallback. Added back fallback support for Firefox playback of MP4 videos using Flash.

= 3.3 =
Added GPLv3 licensing info. Moved screenshots to the central plugin assets folder.

= 3.2 =
Upgraded Flowplayer files to the latest version to resolve a security hole. Removed option for Flash fall back.  Flash fall back now happens automatically, and only for IE 7 and 8. Added option to turn on controls in HTML5 video player for the native skin setting. Added Spanish language localization, courtesy of webhostinghub.com.

= 3.1 =
Added ability to set the duration time for S3 media lifespan. Expanded the S3 server list for current Amazon S3 global regions. Note: The S3 server must be specified to the one that matches the region of the bucket. Added additional help text in the S3 settings tab.

= 3.0 =
Added support for Amazon S3 (and compatible file services) for video file storage and secured video serving.  Fixed an issue where uppercase file extension videos were not recognized. Optimized temporary value cache to use APC, if available.

= 2.5 =
Made file caching an optional setting over the legacy pseudo streaming via PHP. Organized settings into tabs. Added option to always display video download links. Improved fallback behavior with native skin. Added support for organization of videos into folders for secured video files. Optimized storage of featured video meta data. Fixed a typo in the help section.

= 2.4 =
Made file caching an optional setting over the legacy pseudo streaming via PHP. Organized settings into tabs. Added option to always display video download links. Improved fallback behavior with native skin. Added support for organization of videos into folders for secured video files. Optimized storage of featured video meta data.

= 2.3 =
Optimized videos so that they're primarily served from cached directories and filenames. This dramatically improves the performance on hosting providers that limit the resources allocated to PHP scripts.

= 2.2 =
Removed warning messages printed when detecting the installation path in the control panel.

= 2.1 =
Corrected an issue where the poster image was not restored after the video plays.

= 2.0 =
Added support for media servers, external video services, a native skin option and localization.  Fixed autoplay and file detection issues.

= 1.2 =
Documentation correction to 1.1 release.

= 1.1 =
Adds widget support, looping and additional documentation.  Corrects tag formatting so that they are more standards compliant.

= 1.0 =
First release


== Frequently Asked Questions ==

= Why isn't it working in Firefox? =

Firefox currently does not support the MPEG4/h.264 video format that most other browsers and devices support.  Most versions of Firefox support the OGV (Ogg Vorbis Theora) video format, and some versions support the WEBM video format.  To achieve the greatest amount of compatiblity, you must provide videos in both OGV and MP4.  (WEBM is not necessary because every browser that supports WEBM playback supports one of the other video formats as well.)  The plugin automatically detects the presence of multiple video file formats as long as they have the same file name (differing by file extension), and as long as they're placed in the same video directory location of whatever way you're serving the videos.  If you're self serving the a video named "myvid.mp4" from a directory, you'll want to have the short code be:

[video file="myvid"]

and then you will need to:
1. Make sure the MP4 video is encoded as "MPEG4/h.264".  There are other types of MPEG4, but only this one type is defined as the video supported codec supported by HTML5 compliant browsers.
2. Create an OGV version of the video using your favorite video conversion program. (We usually use Miro Video Converter).
3. Place the OGV video in the same video directory as the MP4 file, and name it "myvid.ogv"
4. Make a placeholder image in PNG or JPEG format. We usually take a capture of the representative frame of the video.
5. Name the PNG or JPEG placeholder image: "myvid.png" or "myvid.jpg", respectively, and place it in the same video directory.
6. Test the page where you input the short code and make sure the video plays on all browsers.

= Why isn't it working in IE? =

If your video is not playing in IE, then its likely your mp4 file is not in the proper encoding scheme compatible with HTML5 video. It has to be in MP4/h.264 format. See: <a href="http://diveintohtml5.info/video.html" target="_blank">http://diveintohtml5.info/video.html</a> for more information.

= Why isn't it working in Safari? =

Besides the requirement of the video being MP4/h.264 format, some versions of Safari, especially those running on iOS, have limitations placed on the maximum allowed framerate for the video encoding.  This is because the decoding is done using a specialized processor in the device with a given, set limitation.  As a rule of thumb, 30 FPS should not be exceeded in the encoding process.  If there are device presets available in the video encoding software (as there is in Handbrake, Miro, or Adobe Video Encoder), utilizing those presets would ensure compatibility.

= How do I secure my videos? Are they really secure? =

We use the Secure HTML5 Video Player in conjunction with another plugin that handles user accounts and page permissions granted to specific users. If the user has access to a page, they then have access to the video embedded on that page with a secure, randomized access URL created at the moment the page is served.  The URL to the media acts as a temporary license for viewing the video on the page for a set limited amount of time.  In this way, only members can see the videos, and non-members will not know how to access the videos, even if they know the file names.

Another option is to use the built in features of Wordpress to password protect the post where the video short-tag is used.

Although this means that users that are granted access to a page have download permission for the videos in question, that would be the case for any video embedding technology, and certainly is the case for every HTML5 embedded video.  Anything that can be played on a computer screen can be recorded to a digital file for later playback with the right software or plugin.  We personally don't have a problem with them saving the mp4, if they are on a page that they are allowed to be on. For some websites, this could be viewed as a desirable feature.

= How do I configure the plugin to utilize Amazon S3? =

1. Sign on to aws.amazon.com and go to the S3 page. If you haven't already done so, create a bucket with a specified region, and in the bucket, create a directory where all the videos will reside.

2. Go to the top level bucket list in S3 and click on the magnifying glass icon to the left of the bucket name. This should reveal the properties of the bucket.

3. In the properties of the bucket, make note of the "Region”. This is the physical location of the bucket in Amazon's network and maps to what S3 server you're using. Even if you think you've created the bucket with a certain region, sometimes that's not the case because Amazon had a bug before where it would create a bucket in the default region even if you specified a different one.

4. If your intention is to secure the videos, make sure the permissions on the bucket don't have a permission level that lets everyone download the files in the bucket.

5. Under the top user menu, select "Security Credentials” and then "Access Keys”. If you haven't done so already, create an access key and secret. If you forgot what the secret was, you will have to create a new one and write it down somewhere.

6. In the WordPress admin settings, navigate to Settings -> Secure HTML5 Video Player, and then to the S3 tab.

7. Check on the "enable simple storage service” setting.

8. Under S3 server, select the region that matches the bucket region in step 3.

9. Under Access Key and Secret Key, copy and paste in the values from step 5.

10. Under S3 bucket, paste in the name of the bucket from step 1.

11. Under S3 video directory, paste in the name of the directory from step 1.

12. Save the options.

13. In the S3 console (or with whatever program you use to upload files to S3), upload your video files the the S3 bucket video directory. For any given movie, the base file name should be the same across all of the different video formats you want to support and the same as the poster image. For ex: if you have a video name "myvid.mp4″ you should also have "myvid.ogv” and "myvid.jpg” to support Ogg and a JPG poster image. These media files should all have the same aspect ratio as well. All media files of the same video should be uploaded so that they're in the same directory.

14. Make note of the directory where you uploaded the video. If you uploaded it to "video/myvid.mp4″ and "video” was your S3 video directory, then the name of the video "file”, as it is referred in the shortcode later, is "myvid”. You can also upload to a subdirectory, but then the name of the video file has to have the subdirectory's path prefixed to it. So if you uploaded it to: "video/a/b/c/myvid.mp4″ then the video file is "a/b/c/myvid”.

15. In a WordPress post or page, insert in the short code of the video. From step 14, this is: [video file="myvid"]
Save the post. The video should now be playable when you view the post in the website.
