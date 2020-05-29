#!/bin/bash

while [ true ]
do 
        echo '1) Add user to system'
        echo '2) Change ssh port '
        echo '3) Change IP address'
        echo '4) Create Folder'
        echo '5) Conduct ping sweep'
        echo '6) Quit'
        echo
        read -p "Please make a choice [1-6]: " choice
        case $choice in
                1)
                read -p "Enter users name: " name
                adduser $name
                ;;
                2)
                read -p "Enter port number: " port
                sed -i "s/#Port 22/Port $port/" /etc/ssh/sshd_config
                cat /etc/ssh/sshd_config | echo "Port 22 Changed to " grep $po>
                service ssh stop
                service ssh start
                echo "ssh service restarted."
                echo
                ;;
                3)
                ifconfig
                read -p "Enter device: " device
                ifconfig $device down
                echo Device is down...
                read -p "Enter IP address: " ipAddress
                read -p "Enter netmask: " netmask
                read -p "Would you like to change our MAC address? " yesno
                if [ yesno="yes" ]
                then
                echo
                macchanger -e $device
                echo
                echo
                fi
                ifconfig $device $ipaddress netmask $netmask up
                ifconfig $device
                echo Device is up...
                echo
                ;;
                4)
                read -p "Enter working directory path: " dir
                read -p "Enter folder name: " folder
                mkdir $dir$folder
                echo Created folder in $dir$folder
                echo
                ;;
                5)
                read -p "Enter the ip 192.168.20: " ipaddress
                octatet="${ipaddress:0:2}"
                echo Starting ping sweep of $ipaddress.1-254...
                nmap -sP $ipaddress.1-254 | grep $octatet | cut -d " " -f5
                echo
                echo Ping sweep complete.
                echo
                ;;
                6)
                exit
                ;;
        esac
done
