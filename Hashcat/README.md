Public Key type: rsa
| Public Key bits: 2048
| Signature Algorithm: sha256WithRSAEncryption
| Not valid before: 2019-01-09T18:35:21
| Not valid after:  2021-01-08T18:35:21
| MD5:   93d2 2bfb c0bf 8711 453f 2f4c 63ea 08ab
|_SHA-1: f891 19a8 6f7e 129d be93 e144 d3dd 9cea 1eb1 922d


Hashcat has the following command line syntax

 hashcat [options]... hash|hashfile [dictionary|mask]...

 Where [options] is one of several flags you can pass 
 Try # hashcat --help


 Listed in all that output near the bottom are the attack modes

 -a attack mode 
 -a 0 Wordlist (snoopy) 
 -a 1 Combination of two wordlists (snoopysnoopy) 
 -a 3 Mask Attack (00000000 - ~~~~~~~~) 
 -a 6 Hybrid Wordlist+ Mask (snoopy1111 - snoopy9999) 
 -a 7 Hybrid Mask + Wordlist (1111snoopy - 9999snoopy)

 We'll go over each of these modes individually, don't get wrapped around it yet
 
 root@kali:~# hashcat -m500 -a0 -D1,2 -O --force --username custom.hash words.txt
then:
root@kali:~# hashcat -m500 -a0 -D1,2 -O --force --username custom.hash words.txt --show 
root@kali:~# hashcat -m500 -a0 -D1,2 -O --force --username custom.hash words.txt --left

 Insert the user 1 (u01) flag into the scoreboard now


 A little more about dictionaries:

 Rockyou.txt is really a pretty good dictionary 
 14,344,391 words

 If all you want is common words, search for google-10000-English.txt 
 These are the top 10,000 words in English google books

 For a super-good password dictionary, google search for hashkiller-dict 
 231,611,454 words

 https://hashkiller.co.uk/ListManager/Download

 Scroll up until you get to the hash modes

 Hashcat uses different math for different hashes and optimizes it for the hash

 We will use -m 500 (MD5crypt, old, fast Linux hashes) for this class

 But, throughout the CTF, we will also use:

 -m 1000 (NTLM, old, fast, Windows hashes) 
 -m 0 (straight MD5) 
 -m 10500 (pdf version 1.6) 
 -m 13721 (veracrypt)


 Scroll up to the top to options 
 As well as -m and -a, we also have quite a list of additional flags (options)

 -D to specify which devices we want to use (1=CPU 2=GPU) 
 -O to use math optimized for passwords less than 15 characters 
 --force to ignore warnings 
 --username Our hashes have usernames at the beginnings of lines


 -b or --benchmark-all, If we want to test the cracking speed

 # hashcat -m500 -b --force -D1,2

 -m500 We are benchmarking the MD5crypt hash 
 -b benchmark 
 --force ignore warnings 
 -D1,2 use CPU and GPU

 ~ 210,000 hashes per second

# hashcat -m1000 -b --force -D1,2 
# hashcat -m0 -b --force -D1,2 
# hashcat -m100 -b --force -D1,2 
# hashcat -m1400 -b --force -D1,2 
# hashcat -m1700 -b --force -D1,2

(NTLM) (MD5) (SHA1) (SHA256) (SHA512)
