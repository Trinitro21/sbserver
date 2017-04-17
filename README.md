# What is this?
This is a clone of SmileBOOM's SmileBASIC servers. With this, no NNID is needed, everything is published automatically, there are no upload limits, and uploaded files are accessible from the web.

# How to use this
You need:
* Somewhere to host it
* PHP installed
* PHP should have access to the folder you're putting it in
* A url that isn't too long as there's only around 40 chars available for urls in the binaries
* CFW on your 3DS

Once you have all that:
* Dump a code.bin from SmileBASIC
* Submit it to patcher.php  
It should patch the binary to use whatever url you put the server in
* Load the code.bin over SmileBASIC's  
If you use Luma3DS, put your modified binary into /luma/titles/000400000016DE00.bin and make sure "Enable game patching" is checked
* You should then be able to just use the interface as usual

# Information
* The server determines what account you're using from the token your 3DS sends  
All but the last 32 characters are constant per machine
* Keys are between 3 and 8 characters long and contain uppercase letters and numbers  
If you want to change that see the do{}while() loop in save3.php
* The server sends the "X-Petc-Rights: 1" header, which tells SmileBASIC that you bought gold membership
