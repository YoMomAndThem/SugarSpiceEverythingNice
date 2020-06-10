
Password Guessing – Mangle a Custom Wordlist with CeWL and Hashcat

I’ve been seeing some questions about how to build wordlists for password attacks and those are, 1) how do I build a custom word list and, 2) how do I mangle an existing word list… both can be easily answered and accomplished with ready made tools.

Building a Custom Wordlist 

Kali has a built in tool called CeWL written by Digininja (which can also be downloaded for other distributions) that allow you to scrape a site for keywords to use as password guesses. This is great when you’re working on an engagement that is in a specific industry – it’s very likely that passwords will be somehow related to that industry. The command is simple and would look something like this:

cewl -d 3 -m 5 -w client123pass.txt http://client_site 

The (-d 3) means we want a link depth of 3, the (-m 5) means we want words that have a minimum of 5 characters, client123pass.txt is where the wordlist will be written and http://client_site is the site you’re targeting.

Link to GitHub Page for CeWL

Mangling a Wordlist using Hashcat Rules

Okay, so that’s going to give us a bunch of words. But people like to choose complex  password such as ‘mypass1’ instead of just ‘mypass’ right? So we can use a mangler to add some transformations to those passwords. Hashcat isn’t just great for cracking password hashes, we can also use its mangler functionality so that the generated wordlist can be used in other tools such as Burp or Hydra etc…

By default Kali keeps the hashcat rules files (.rule extension) in /usr/share/hashcat/rules/. So let’s say your CeWL operation generated a wordlist called client123pass.txt

We could mangle that wordlist with Hashcat as follows:

 hashcat --stdout --rules-file /usr/share/hashcat/rules/best64.rule client123pass.txt | uniq -u >> client123mangleuniq.txt 

Alright, so the (–stdout) tells hashcat to output every password it mangles to the terminal, (–rule-file) tells it to read a rule file from disk, and in this example we use the built-in best64 rule but you can use a different one, we then provide the password file we generated using CeWL. The output of hashcat is then piped (|) to the uniq function which removes duplicates (no need to try and guess the same password twice) and then writes all the entries to a new file called client123mangleuniq.txt
