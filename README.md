# What is this?
This is a clone of SmileBOOM's SmileBASIC servers. With this, no NNID is needed, everything is published automatically, there are no upload limits, and uploaded files are accessible from the web.
It also makes requests to SmileBASIC's original servers so you can still download keys from there.

# How to use this
You need:
* Somewhere to host it
* PHP installed
* The PHP GD extension
* PHP should have access to the folder you're putting it in
* A url that isn't too long as there's only around 40 chars available for urls in the binaries
* CFW on your 3DS

Once you have all that there are two ways to install the necessary modifications on your 3DS:
* If you have version 3.6.0 of SmileBASIC, you can use the .ips generator in index.php to download a code.ips file
  * If you use Luma3DS, you can place this file into /luma/titles/000400000016DE00/code.ips
* Modify the code.bin of SmileBASIC
  * Dump a decompressed code.bin from SmileBASIC  
Here's one way to do this:
    * Make sure you have the latest release of GodMode9
    * Open GodMode9 and navigate to A:/title/0004000e/0016de00/content
    * Press A while highlighting 00000000.tmd and select "TMD file options..." -> "Extract .code"
    * You now have the code.bin of SmileBASIC in /gm9out/.code on your SD card
  * Submit your code.bin to the patcher in index.php  
It should patch the binary to use whatever url you put the server in
  * Load the code.bin over SmileBASIC's  
If you use Luma3DS, put your modified code.bin into /luma/titles/000400000016DE00/code.bin and make sure "Enable game patching" is checked in Luma's settings

After you have done this, you should then be able to just use the interface as usual

# Information
* The server determines what account you're using from the token your 3DS sends  
All but the last 32 characters are constant per machine
* Keys are between 4 and 9 characters long, always start with 0, and contain uppercase letters and numbers  
If you want to change that see the do{}while() loop in save3.php
* The server sends the "X-Petc-Rights: 1" header, which tells SmileBASIC that you bought gold membership
