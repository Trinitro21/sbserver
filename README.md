# What is this?
This is a clone of SmileBOOM's SmileBASIC servers. With this, no NNID is needed, everything is published automatically, there are no upload limits, and uploaded files are accessible from the web.

# How to use this
You need:
* Somewhere to host it
* PHP installed
* The PHP GD extension
* PHP should have access to the folder you're putting it in
* A url that isn't too long as there's only around 40 chars available for urls in the binaries
* CFW on your 3DS

Once you have all that:
* Dump a decompressed code.bin from SmileBASIC  
Here's one way to do this:
  * Make sure you have the latest release of GodMode9
  * Open GodMode9 and navigate to A:/title/00040000/0016de00/content
  * Press A while highlighting 00000000.tmd and select "TMD file options..." -> "Build CIA (standard)"
  * Go to 0:/gm9out/
  * Press A while highlighting 000400000016de00.cia and select "CIA image options..." -> "Mount image to drive"
  * Navigate inside the first folder shown, then inside the exefs folder
  * Press A while highlighting .code and select "Copy to 0:/gm9out"
  * Download and extract https://github.com/dnasdw/3dstool/releases/latest on a PC
  * Insert your 3DS's SD card into your PC, navigate to /gm9out, and copy the 3dstool program into that directory
  * Open up a command prompt in that directory and run `3dstool --decompresscode -uvf .code --compress-type blz` to decompress the file
  * You now have the code.bin of SmileBASIC in /gm9out/.code on your SD card
* Submit your code.bin to the patcher in index.php  
It should patch the binary to use whatever url you put the server in
* Load the code.bin over SmileBASIC's  
If you use Luma3DS, put your modified code.bin into /luma/titles/000400000016DE00/code.bin and make sure "Enable game patching" is checked in Luma's settings
* You should then be able to just use the interface as usual

# Information
* The server determines what account you're using from the token your 3DS sends  
All but the last 32 characters are constant per machine
* Keys are between 3 and 8 characters long and contain uppercase letters and numbers  
If you want to change that see the do{}while() loop in save3.php
* The server sends the "X-Petc-Rights: 1" header, which tells SmileBASIC that you bought gold membership
