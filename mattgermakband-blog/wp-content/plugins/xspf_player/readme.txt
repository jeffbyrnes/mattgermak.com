=== Xspf Player ===
Tags: music, media, multimedia, podcast

Some contributors: Jan Reinhardt <http://www.grnch.de>, Darran <http://darran.digital-pulse.net>, 
		Ana La Bella <http://littleovulation.net>, Johnny Haeusler <http://www.spreeblick.com>,
		Pablo Chinea <http://www.khertz.net>, Renato Alves 

Xspf Player is a plugin that will embed the GPL'ed flash player XSPF Web Player (see http://musicplayer.sourceforge.net).
You can play music stored remotely or locally in your web server (must be HTTP mp3 links files). 
There are three player versions (you can use any of them). Actually only Slim player has been tested, but it's supposed
to work with any of them.

For an up-to-date help and info, go to the plugin-help page: 
http://www.boriel.com/?page_id=12 (a Wiki us about to come)

== Installation ==

1. Upload the .ZIP file content to your plugins folder, usually `wp-content/plugins/`. 
   After this, you should have the folder wp-content/plugins/xspf_player created and several files inside of it.
2. Activate the plugin on the plugin screen
5. Go to the Managements page (XSPF Player Management) and add some url songs to the database.
6. Invoke the player anywhere into your template pages using: <?php xspf_player::start('category-tag'); ?>
   Where 'category-tag' is the name of a category of songs you defined. E.g.: xspf_player::start('Celt Music')
   Category-tag is optional. If omitted, ALL tracks are played.
   
That's all.

== Upgrading from previous versions ==

1. Deactivate the xspf player plugin at the plugin admin panel.
2. Remove ANY file you installed with previous version (that is the xspf_player.php and the xspf_player/ folder).
3. Proceed to install as described in the Installation section.

To invoke the player within your post, use <xpsf>_start()</xpsf> (you can press the Quicktag 'Xspf' button in the editor page).

WARNING!!!: This has changed from previous versions of xspf (3.0) (it was <xspf>start()</xspf>). Both will work, but the later one 
*MUST* be used when invoking embedding the player within a post (to keep XHTML 1.0 compliance).

Currently only user with admin privileges can insert the player within the post. 
Allowing it to everybody could be a security hazard if you don't trust your registered users. So, by now, only admin (User level >= 9) can use this
feature.

== Frequently Asked Questions ==

= How can I play local files? =
Put the COMPLETE URL of your local file as if they were remote files. 
You also must upload your Mp3 files to your web server (using, for example the File Management menu in wordpress).
These files must be accesible and public via HTTP.
e.g. If your server is named "www.boriel.com", and your files are under directory /www/media/song1.mp3, you must use 
http://www.boriel.com/media/song1.mp3 and NOT /media/song1.mp3.


== Change log ==

--- 3.x ---

* Version 3.2
		.- Bugfix: When no default player mode selected, the player may not work. Fixed.
		.- Bugfix: The [XSPF] button does not work on WP 2.1. Fixed (Thanks, Renato Alves).
		.- Bugfix: Cleanup database songs tables when installing for first time.
		.- Feature: Now it's possible (and preferred) to use [XSFP] ... [/XSPF] instead of <XSPF>...</XSPF> tags.
		.- Other: Everything is in one folder (tidy up)
* Version 3.1
		.- Bugfix: Playlist sometimes does not works with PHP version < 5.x (MBCS). Fixed.
		.- Bugfix: Slim mode sometimes showed an underlining color background. Fixed.
		.- Bugfix: Sometimes the height and width were not set when choosing the mode on-the-fly (advanced users). Fixed.
		.- Bugfix: When calling the player inside a post/page body, the output was not XHTML compliant. Fixed (MUST use _start() function now).
		.- Feature: Under some free web-hosts, the playlist did not work because header insertions. This have been solved.
* Version 3.0 (alias "D.A.R.Y.L.")
		.- Feature: Can insert the player within the post body
		.- Feature: Added mode 0 (player can choose player URL)
		.- Feature: Added mode 4 (button with overlay menu)
		.- Feature: Parameters changeable on the fly (see Wiki)
		.- Other: Some glitches and code rearrangement.

--- 2.x ---

* Version 2.1
		.- Bugfix: Total page number not correctly displayed in the tracks management panel.
		.- Feature: Improved track management.
		.- Feature: Plugin background color selectable via color picker.
		.- Feature: Improved player mode selection.
* Version 2.0 (alias "The Grinch")
		.- Bug Fix: No more than 10 Tracks displayed on the Tracks management panel. FIXED.
		.- Bug Fix: Sometimes didn't delete tracks from database if WP was localized to other languages. FIXED.
		.- Feature: Song order now available per category!
		.- Feature: The 3 Player bundled, and selectable with one click (3 player modes)!
		.- Change: Categories now moved to the management page.
		.- Bug Fix: Some glitches with de CSS fixed.
		.- Feature: Tracks now are paginated for better management.

--- 1.x ---

* Version 1.6 	
		.- Feature: Autoload playlist
        .- Feature: Preview in Options panel!
        .- Bugfix: Playlist may give errors with some plugins. Fixed. Thanks, Ana. 
        .- Bugfix: Entering a blank URL trak made the management panel to dissapear until page reloaded. FIXED.
		.- Bugfix: Characters ' and " escaped. Fixed.
* Version 1.5 	
		.- PHP 5.x compatible (was already compatible with 5.1)
* Version 1.4 	
		.- Going valid XHTML
* Version 1.3 	
		.- Bug Fix. Playlist category not correctly selected due to debug instrucions. ARgh!        
		.- Feature. Random play ('shuffle' mode) added for each category.
		.- Feature. When no track title is entered, the URL track is displayed as a default.
* Version 1.2 	
		.- Bug Fix. Playlist not loaded in Internet Explorer.
* Version 1.1 	
		.- Bug Fix. Playlist not well-formed when info URL's added.
* Version 1.0 	
		.- Initial Version.

