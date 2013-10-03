preparecms - A tool to read YouTube CMS monthly reports and split data into separate csv files that are easier to import into a SQL database.
============================

YouTube generates monthly reports for its CMS accounts with detailed activity for every video. Unfortunately, the csv file offered by YouTube has four different sections within the same file. They are also a pain to read in Excel because some videos have IDs that begin with a minus sign and Excel interprets them as numbers or errors.

To avoid cutting-and-pasting, editing and other error-inducing activities while adding the data to our database, I created this tool.

It does five things:
============================

1. Reads a YouTube CMS monthly report csv filed from the command line and extracts the proper month and year.
2. Makes sure the file format has not changed by comparing each section's headers with a pre-defined list.
3. Combines the Total Views, Total Earnings and Gross Revenues sections into one csv file, adding a month and a year column.
4. Swaps the header fields for a pre-defined list of database-friendly headers.
5. Parses the Daily Totals, Geo Totals and Video Totals sections, generating separate csv files and adding the month and year columns where appropriate.

It outputs four files:
============================

YYYYMM_monthlyTotals_.csv
YYYYMM_geoTotals_.csv
YYYYMM_dailyTotals_.csv
YYYYMM_videoTotals.csv

What it's not:
============================

It's not an example of proper programming practices or cutting-edge techniques. It's down and dirty. It works. I hadn't programmed in a while. Thus, comments and suggestions are welcome.

What might change:
============================

I think I could swap most of the file writing code for a function. I just need to sit down and think about it some more.

Since I have two CMS accounts with YouTube, I will probably add code to read data from both accounts and add a column to identify the account. This way, I could import both accounts into one database and create both individual and combined reports.