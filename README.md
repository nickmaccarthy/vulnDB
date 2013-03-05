Title: vulnDB
Author: Nick MacCarthy
Email: nickmaccarthy@gmail.com
Date: March 5th 2013

# vulnDB #

<pre>

                                           lllllll                  DDDDDDDDDDDDD      BBBBBBBBBBBBBBBBB   
                                           l:::::l                  D::::::::::::DDD   B::::::::::::::::B  
                                           l:::::l                  D:::::::::::::::DD B::::::BBBBBB:::::B 
                                           l:::::l                  DDD:::::DDDDD:::::DBB:::::B     B:::::B
vvvvvvv           vvvvvvvuuuuuu    uuuuuu   l::::lnnnn  nnnnnnnn      D:::::D    D:::::D B::::B     B:::::B
 v:::::v         v:::::v u::::u    u::::u   l::::ln:::nn::::::::nn    D:::::D     D:::::DB::::B     B:::::B
  v:::::v       v:::::v  u::::u    u::::u   l::::ln::::::::::::::nn   D:::::D     D:::::DB::::BBBBBB:::::B 
   v:::::v     v:::::v   u::::u    u::::u   l::::lnn:::::::::::::::n  D:::::D     D:::::DB:::::::::::::BB  
    v:::::v   v:::::v    u::::u    u::::u   l::::l  n:::::nnnn:::::n  D:::::D     D:::::DB::::BBBBBB:::::B 
     v:::::v v:::::v     u::::u    u::::u   l::::l  n::::n    n::::n  D:::::D     D:::::DB::::B     B:::::B
      v:::::v:::::v      u::::u    u::::u   l::::l  n::::n    n::::n  D:::::D     D:::::DB::::B     B:::::B
       v:::::::::v       u:::::uuuu:::::u   l::::l  n::::n    n::::n  D:::::D    D:::::D B::::B     B:::::B
        v:::::::v        u:::::::::::::::uul::::::l n::::n    n::::nDDD:::::DDDDD:::::DBB:::::BBBBBB::::::B
         v:::::v          u:::::::::::::::ul::::::l n::::n    n::::nD:::::::::::::::DD B:::::::::::::::::B 
          v:::v            uu::::::::uu:::ul::::::l n::::n    n::::nD::::::::::::DDD   B::::::::::::::::B  
           vvv               uuuuuuuu  uuuullllllll nnnnnn    nnnnnnDDDDDDDDDDDDD      BBBBBBBBBBBBBBBBB   

    Written by: Nick MacCarthy          nickmaccarthy@gmail.com     http://www.nickmaccarthy.com    2013    MIT License
</pre>


What is this thing? 
---------------------------------

vulnDB is a project orignially developed to pull in vulnerability data from various vendors and sources for analysis, trending, reporting and other metrics as well as correlation with security events.  

This module was originially developt for for syncing "raw", or what Qualys refers to as 'manual' scan data/results for one or more Qualys accounts into the vulnDB system.  However, it grew beyond that and can store "Automatic Scan Reports" from Qualys.  In other iterations of the app, we even have it keeping scanner status, and other various data sources from Qualys. 

Its main feature is that it utilizes the Qualys API to 'sync' scan data accross the multiple Qualys accounts back into the vulnDB relational database.  Extremely useful for when you want to catalog and correlate scan data across your multiple Qualys accounts. 


Why would I use it?
----------------------------------
#####Multiple Accounts:#####
There is lots of value in the manual scan data, escpially when its organized in a RDBMS like vulnDB.  For Qualys customers that have multiple accounts, it often times a tedious task of manually downloading scan results/reports from Qualys, and then correlating them within programs like Microsoft Excel.  With vulnDB, one simply needs to make query and they have thier answers. 

#####Trending:#####
Trending was one of the main drivers behind the development of this project.  With a simple query one could easily see the critical vulnerabiilty trend over a given time period in a time resolution they want.  These are things that are difficult to impossible with Qualys today, but are as easy as a SQL query in vulnDB.

#####Correlation:#####
vulnDB runs on MySQL.  Correlating the scan results, with say IDS results, or SIEM events is very simple.  Scan and vulnerability data correlated against security events in a SIEM such as ArcSight, or IDS/IPS platforms such as Sourcefire provide tremendous value to the secuirty org of a business and can easily be done with vulnDB.

#####Anything:#####
vulnDB is a dataset.  With that dataset one has possiblities to manipulate, or use this data however they see fit.  The possibilities are endless!

Ok you sold me!  How does it work?
-----------------------------------------

As mentioned earlier, vulnDB utilizes the Qualys API for its tasks.  In brief it does the following everytime it runs:

Start a loop and do the following against each Qualys account you have
Run an API call to pull back a list of scans that have run on an account in a given timeframe 

1. Analyze and asses the results from #2, and determine wether or not the scan is already in vulnDB, and if not, goto #4
2. Download scan and inset it into vulnDB
3. Repeat steps #3 and #4 until all scans have been downloaded
4. Download and update the asset group list for the account
5. After all accounts have been updated, download and load the Qualys Knowldgebase  

For a visual reference on this flow, please check out the vulnDB Logic Flow Chart in the docs section.


Cool! Im going to run it, but how do I set this thing up?
---------------------------------------------------------------

1. Well, first you need to clone me!
<code>
git clone https://github.com/nickmaccarthy/vulnDB.git
</code>
2. Work in progress for install instructions
3. Profit

Some 'Gotchas!'
-----------------------------------------

1. Recently, Qualys has limtied the ammount of API calls per account to 300 API calls per day by default.  In vulnDB by default, we will attempt to sync back 3 months worth of scans on its first run. For some large clients, there could be hundrends if not thousands of scans in a time period that big.  What are we to do?  If you have more than 300 scans in this default 3 month time period, that will mean you will run out of API calls for that day.  No worries, you can run vulnDB the next day after you API limit has reset and continue to do so until you 'catch up'.  After you have synced that initial batch of scans, you should not hit the API limit again.

2. By default, asset_data_report_updater.php is not in kickoff.sh .  If you want AD reports to run, simply add it to the kickoff.sh script following the other two entries as a guide.

3. If you want to sync back more than 3 months worth of scan data, simply modify the $timeframe variable in vulnDB_updater.php.  This can be done at anytime.
 

Sounds great!  What do I need to run this thing?
------------------------

* PHP 5+ with json, pdo and mysqli support complied in
* MySQL 4+ with root or adminstrative access to create the vulnDB database and user (setup.php takes care this for you)
* Some type of *nix OS.  I have sucessfully ran this on RHEL, CentOS, and Ubuntu systems with thier stock mysql and php installations.

##### Sizing #####

Let me start off by saying, I had vulnDB installed on a Linux Virtual Machine running on Virtual Box on my underpowered desktop at work for years.  It ran just fine.  

However, if you intend to use vulnDB with other teams and systems, where there can be lots of (READ) queries over many months worth of scan data, it might make sense to move this project to its own database/hardware.
 
The sizing of a system for this is relative to ammount of scans, and size of your scans you have in your accounts, as well as the ammount and types of queries you will run on it.  For example, if you are correlating the scan data with your SIEM, the SIEM may do a query for every security event it receives.  These queries can be small if they are just looking for say the OS found in the scan data for an IP it sees which is pretty fast, but another team may want to do queries that trend the scan data over many months, these can be intensive.  The SIEM making many queries per hour, does not affect the DB as much as querying data for many months worth of scan data.  

I have found over the years that the box vulnDB runs on will eventually get used for processing reports, all the way to building other apps that vulnDB is part of, so it  makes sense to overbuild it a little, even if you dont think you need the extra horsepower -- I bet you will in the future!  

Here is a general guide to help you size your system:

**Processor:**  Relative to query ammount and query types.  If doing alot of SELECT (aka READs), with lots of math functions, it makes sense to beef up on the processor.  I have run this on a PIII with 256MB of RAM, all the way to 16 CORE XEONS with 32GB of RAM.  I would say anything Dual Core, 2.4GHZ + would be fine to start with.


**Storage:** ~20 GB of storage per account, per-year of scan data.  3 Accounts, would be 60GB of storage per year.  **Note** If you will be doing quries through many months of scan data quite frequently, I would reccomend the fastest disk possible be presented to your system.  There is lots of info out there on sizing disk to DB, so I wont go into it here, but remember typically where query performance sufferes the most is at the disk IO level.  Faster disk means less bottleneck.


**RAM:**  1-4GB for small accounts (less than 1k scans per month), 4-8GB (1-2k scans per month), and 8GB+ for larger accounts (2k+ scans per month).  As always, make sure you tweak your my.cnf to utilize that RAM!

For my deployment it runs on a VM with 2 processors (2.8GHZ), 4GB of RAM, and 200GB SAN storage.  It syncs 3 accounts for a major enterprise, correlates with SIEM events, and is on a shared system.  Its runs just fine.

FAQ:
----------------------------------
Q: Why did you build this thing?

A: I had bosses and clients who wanted more than what Qualys could give them for trend reporting. I orginially built some scripts to automate a few report from Qualys every day using the API, and would manually make those reports into a trend.  It was lame, and I knew there had to be a better way, so I made vulnDB. 


Q: Why PHP?

A: Good question.  There is another part of vulnDB, a front end for searching/quering the data that runs as a web application.  Naturally PHP is a great language for anything web, so when I though about combining the two, it made sense to use PHP so I didnt have to support two apps in two differnt languages.  Plus, over the years, I have found PHP to be a fantasic scripting language, not only for web, but for projects that run via CLI like this as well.  Its also worth noting that PHP 5.3+ has really fixed alot of the issues that gave PHP a bad rap, so no need to shudder at the word "PHP" anymore.



Q: Are there any plans to move this another language, like python, or ruby?

A: Not at this time.  In the future as a learning experience, I do plan on doing this portion in python, but its not high on the list.  Feel free to do it for me!


Q: You say there is another part to this thing?  A front end?

A: Yes there is.  It will be released very soon


So what are your plans for this thing in the future?
-----------------------------------------

Well, I built vulnDB orginially  to make my life easier at my job.  I have tried to keep as simple as possible for one reason - reliability.  This has been a project I have developed for over 2 years now, and in that time, its been pretty rock solid.  In the future I do intended to switch most of the mysqli calls to PDO as more and more users adopt PHP 5.3.  This will alow the DB inserts to be faster, but more importantly, allow DB transactions on the inserts and much better error handling for even more reliability. 

License:
------------------------

vulnDB was written and developed by <a href="http://www.nickmaccarthy.com">Nick MacCarthy</a> and releases its sourcecode under the GPL v3 and MIT licenses
