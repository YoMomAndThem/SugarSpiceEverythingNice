
## Installing snap on Kali Linux

Installing snap from a live Kali Linux environment is not currently supported. These instructions only work when Kali Linux is installed.

From a Kali Linux installation, snap can be installed directly from the command line:

    $ apt update
    $ apt install snapd

Additionally, enable and start both the snapd and the snapd.apparmor services with the following command:

    $ systemctl enable --now snapd apparmor

Finally, either log out and back in again, or restart your system, to ensure snap’s paths are updated correctly.

To test your system, install the hello-world snap and make sure it runs correctly:

    $ snap install hello-world
    hello-world 6.3 from Canonical✓ installed
  
    $ snap run hello-world
    Hello World!

Snap is now installed and ready to go! If you’re using a desktop, a great next step is to install the Snap Store app.


## Visual Studio Code is officially distributed as a Snap package in the Snap Store:

Get it from the Snap Store

### You can install it by running:

    sudo snap install --classic code # or code-insiders

Once installed, the Snap daemon will take care of automatically updating VS Code in the background. 
You will get an in-product update notification whenever a new update is available.

## Run command

    snap run code

This will run VScode.
