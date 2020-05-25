## Download and install TeamViewer:

* Then enter the following commands

        root@kali:~# wget https://download.teamviewer.com/download/linux/teamviewer_amd64.deb
        root@kali:~# dpkg -i teamviewer_amd64.deb

* In some cases you might also need to do the following step:

        root@kali:~# apt -f install

## Run TeamViewer

* To run, you can do it CLI or Icon under Internet.

        root@kali:~# teamviewer

## Run TeamViewer as a daemon

* If you see a message like Teamviewer daemon not running then use the following command in cli

        root@kali:~# teamviewer --daemon enable


    It used to be a bigger issue previously, but since then TeamViewer released x64 supported version for Debian/Ubuntu Linux which works fine for Kali Linux as well.

