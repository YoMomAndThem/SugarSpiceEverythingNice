
## Installing ZenMap on Linux

Apparently, ZENMAP need to install manually on newer kali OS’s. Here is way to install it.

You can get more info here https://nmap.org

Go to https://nmap.org/download.html and download Optional Zenmap GUI (all platforms): zenmap-7.80-1.noarch.rpm usually it will go to download on your kali

Then run these commands:

	sudo su it will ask to enter password for root

	apt-get update
	apt-get install alien

cd to the download folder where you download zenmap

	sudo alien “zenmap-7.80-1.noarch.rpm”

	sudo dpkg -i “zenmap-7.80-1.noarch.deb”

Then you will be able to launch zenmap.