<!-- resources/views/manual.blade.php -->
@extends('layout.app')
@section('title')User Manual - Process Overview @endsection
@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">
                    <!-- Introduction -->
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">Process Overview for Anson Analytics Charity Research Portal</h1>
                    <p class="mb-4">
                        This section goes over technical details and the process for updating data.  The general process follows the Map below, using those links, in sequential order.
                    </p>

                    <!-- Table of Contents -->
                    <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Process Map</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li><a href="#adding-companies" class="text-blue-600 hover:underline">Adding Companies</a></li>
                        <li><a href="#adding-charities" class="text-blue-600 hover:underline">Adding Charities</a></li>
                        <li><a href="#adding-lists" class="text-blue-600 hover:underline">Bulk Adding Lists</a></li>
                        <li><a href="#charity-giving" class="text-blue-600 hover:underline">Charity Giving / Core 990</a></li>
                        <li><a href="#extra-fields" class="text-blue-600 hover:underline">Extra Fields / Extended 990</a></li>
                        <li><a href="#sql" class="text-blue-600 hover:underline">Helpful SQL Queries</a></li>
                        <li><a href="#troubleshooting" class="text-blue-600 hover:underline">Troubleshooting</a></li>
                        <li><a href="#tech-details" class="text-blue-600 hover:underline">Technical Details</a></li>
                    </ul>

                    <p class="mb-5"><a href="{{ url('manual') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">User Manual</a> <a href="{{ url('setup') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Setup or Configure the dataset</a></p>
                    
                    <!-- Getting Started -->
                    <div id="adding-companies">
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Adding or Updating Companies</h2>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Adding Companies</h3>
                        <p class="mb-2">The SEC website (Edgar) contains all publicly traded company information.  They have a file we will use to seed the database.  Unfortunately, as is the case with most of these entities, there is no way to get an "update" list, so each time you get the entire list again.</p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>SEC Developers Page:</strong> Visit <code><a href="https://www.sec.gov/about/webmaster-frequently-asked-questions#developers">#https://www.sec.gov/about/webmaster-frequently-asked-questions#developers</a></code> in your browser.</li>
                            <li><strong>Ticker Map:</strong> Find the <code>company_tickers.json</code> download.</li>
                            <li>Save the ticker map file to the same location as your website files.</li>
                            <li><strong>Run:</strong> run the <code>ticker_map_import_json.php</code> file.</li>
                        </ol>
                        <p class="text-sm italic text-gray-600 mb-4">Note: This updates the COMPANIES table with stock tickers and CIK ID (from Edgar).  You can later use this CIK ID if you wanted to build an API to talk to Edgar.</p>
                        </div>

                    </div>
<hr class="mb-10 mt-10" />

<h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4" id="adding-charities">Charities</h2>

<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h4 class="text-lg font-medium text-gray-600 mb-1">Adding or Updating Charities</h4>
                        <p class="mb-3" >As with companies, there is no way to just get the "latest" charities from Giving Tuesday, so you have to grab them all.  The system will ignore the ones that are already in the database and only add new ones.</p>
                        <p class="mb-3">This will pull in Charities, their names and their EIN.</p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>990 File:</strong> Obtain the 990 data mart CSV file.  At the time, this was a 9GB file that contained all entities.  They have since broken into various forms, which will make processing easier. <a href="https://990data.givingtuesday.org/datamarts/">https://990data.givingtuesday.org/datamarts/</a>.</li>
                            <li><strong>Basic Fields:</strong> Form 990 Basic fields will contain the primary fields necessary to build the database.  It does not contain the giving data.  That will be obtained later.</li>
                            <li><strong>Save File:</strong> Save this file as <code>990.csv</code> on the server in the same location as your python scripts.</li>
                            <li><strong>Import:</strong> Run the import_charity.py file.</li>
                        </ol>

                        <p class="text-gray-600 mb-4">CSV files is what we started with, but JSON is easier to manage.  There is a script to process the JSON file that was started and needs to be tweaked (see import_charity_json.py). </p>
                        <p class="text-gray-600 mb-4">This was the latest file that was used: <a href="https://gt990datalake-analytics-and-datamarts.s3.amazonaws.com/EfileDataMarts/2024_06_21_All_Years_990CN120Fields.csv">https://gt990datalake-analytics-and-datamarts.s3.amazonaws.com/EfileDataMarts/2024_06_21_All_Years_990CN120Fields.csv</a></p>
</div>



<hr class="mb-10 mt-10" />

<h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4" id="adding-lists">Lists</h2>

            <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-600 mb-1">Bulk Adding Lists</h4>
                <p class="mb-3" >You can create and alter lists in the web GUI.  However, curating a list of stock tickers for your list is one-at-a-time with the GUI.  Here's how to bulk add companies to a list.</p>
                <ol class="list-decimal pl-6 mb-4">
                    <li><strong>Create List:</strong> Using the web GUI, create a list and name it what you want.  Make note of the list ID number.</li>
                    <li><strong>Create Ticker List File:</strong> Create a single-column CSV file with the tickers you want in the list.  Only include the tickers, no headers, in column A.  Save this file as <code>ticker_list.csv</code> and put it in the same directory as your scripts.</li>
                    <li><strong>Update Script:</strong> Line 15-16 of <code>associate_ticker_list.php</code> contains the variables needed to update.</strong></li>
                    <li><strong>Run Script:</strong> Run <code>associate_ticker_list.php</code> to bulk import the ticker list.  This is an independent PHP file, so you can run from the command line.</li>

                </ol>

            </div>



            <hr class="mb-10 mt-10" />

                    <div id="charity-giving">
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Charity Giving</h2>

                        
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Charity 990 Data</h3>
                        <p class="mb-2">We call this Charity Giving because that is the name of the table in the database, but it is really the collection of 990 data.  This can be expanded as needed, as we have all of the extra fields saved.</p>
                        
                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                            <h4 class="text-lg font-medium text-gray-600 mb-1">Basic 990 Data</h4>
                            <ol class="list-decimal pl-6 mb-4">
                                <li><strong>Same File:</strong> This process also uses the same 990.csv file.  Ensure 990.csv is in the same directory as the python script.</li>
                                <li><strong>Import Giving:</strong> Run <code>import_giving.py</code> which will update the charity_giving table.</li>
                            </ol>
                            <p class="mb-3 text-gray-600">There is also a starter script called import_giving_json.py which was built for JSON files and needs to be tweaked.  The import_giving _old.py contains an older version of this import.</p>

                            <p class="mb-3 text-gray-600">There is another script called <code>import_giving_url.py</code> which will connect to the live API from Giving Tuesday to update the charity_giving table.  It requires a charity EIN.  This process also only contains the basic information, but it does pull all entries at one time.  It returns a JSON formatted response.  There is a variable setup for EIN at the bottom of the file to setup which charity to pull.  You could alter this to loop through a list of EIN values if you wanted.<p>
                            <p class="text-sm italic mb-3 text-gray-600">Note, this process only grabs the BASIC 990 data.  We need the tax year and tax period fields and some basic revenue data at this point.  This is available in the summary data and from the API.</p>
                        </div>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h4 class="text-lg font-medium text-gray-600 mb-1" id="extra-fields">Extended 990 Data</h4>

                        <p class="mb-2">The extended 990 data (all fields) are stored in the XML file linked in the URL column of the table charity_giving.  This is the ONLY way Giving Tuesday has provided to get extended 990 data (other than the first page of the 990, it seems).  They are structured slightly differently per 990 type.  990 EZ, 990 T, 990 PF are all structured differently, so we do have a feature that searches for the exact field name in the XML file.</p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Configure the script:</strong> At the top of the script <code>extract_xml_giving.py</code>, you will find a series of variables to configure how this runs.  The default is ALL entities, which takes a lot longer to run.  If you wanted to target all Charities associated with a particular company, if you wanted to target charities associated with companies on your lists, etc.  Change the variables as directed to configure the run.</li>
                            <li><strong>Run the script:</strong> Running extract_xml_giving.py is focused on opening the XML file and adding the extended fields to the database.  Currently, it only tries to get Grants <code>(ContriPaidDsbrsChrtblAmt)</code> and Contributions <code>(TotalContributionsAmt)</code>. The other fields are there, but they are commented out.  You can alter the database and alter this script to add additional fields.  Examples are in the file.</li>
                            <li><strong>Verify:</strong> Charities and your exports would be automatically updated on the website.</li>
                        </ol>
                        </div>

                       

                        <hr class="mb-10 mt-10" />
                        <h3 class="text-xl font-medium text-gray-700 mb-2" id="extra-fields">Extra Fields</h3>
                        <p class="mb-2">Export data for analysis or share records offline.</p>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">

                        <h4 class="text-lg font-medium text-gray-600 mb-1">Exporting all Charity Form 990 data based on a List</h4>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Go to Companies:</strong> Navigate to <code>/companies</code>.</li>
                            <li><strong>Choose List:</strong> Choose the list at the top right of the page (drop-down box next to the search box).</li>
                            <li><strong>Export:</strong> The green export button will appear after the screen regenerates based on companies in the list.  Click the green button to download transactions from this list.</li>
                            <li><strong>Download:</strong> A CSV file (e.g., <code>company-{id}-YYYY-MM-DD.csv</code>) downloads automatically. The file includes columns like <code>Company Ticker</code>, <code>Charity Name</code>, <code>Tax Year</code>, <code>Grants Paid</code>, <code>Total Revenue</code>, etc.</li>
                        </ol>
                        <p class="text-sm italic text-gray-600 mb-4"><strong>Be patient!</strong> These transaction files can be very large and may take several seconds to process.  The download will proceed immediately when complete.</p>

                        </div>


<hr class="mb-10 mt-10" />
                        <h3 class="text-xl font-medium text-gray-700 mb-2" id="sql">Helpful SQL</h3>
                        <p class="mb-2">After making minor modifications to the database, these SQL queries may be helpful.</p>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">

                        <h4 class="text-lg font-medium text-gray-600 mb-1">Associate Charity to Company based on AI match</h4>
                        <p class="mb-3"><code>update charity left join charity_recommend on charity_recommend.charityID = charity.id set parent = charity_recommend.companyID where charity_recommend.match = 100</code></p>
                        
                        <h4 class="text-lg font-medium text-gray-600 mb-1">Associate charity_giving to Charity based on a Charity's EIN</h4>
                        <p class="mb-3"><code>update charity_giving, charity set charity_giving.charityID = charity.id where charity_giving.charityID IS NULL AND charity_giving.ein = charity.ein</code></p>
                        
                        <h4 class="text-lg font-medium text-gray-600 mb-1">Raw List Data</h4>
                        <p class="mb-3"><code>select charity.name, company.ticker, charity_giving.* from charity_giving left join charity on charity.id = charity_giving.charityID left join company on charity.parent = company.id left join list_associate on company.id = list_associate.company_id where list_associate.list_id = 1</code></p>
                        

                        </div>
<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">

                        <h4 class="text-lg font-medium text-gray-600 mb-1">Disassociating a Charity from a Company</h4>
                        <p>Removing a Charity from a Company are accomplished through the Charity's page.</p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Open a Charity:</strong> Navigate to <code>/charities/{id}</code> and choose the Charity by clicking its name.</li>
                            <li><strong>Edit Charity:</strong> Click the blue Edit button to go to the Charity Edit screen.</li>
                            <li><strong>Remove a Company:</strong> In the <strong>Associated Company</strong> table, locate the company. Click <strong>Remove</strong>. Confirm the decision.</li>
                            <li><strong>Check Update:</strong> The company is removed from the list.</li>
                        </ol>
</div>

<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">

                        <h4 class="text-lg font-medium text-gray-600 mb-1">Auto Recommendations</h4>
                        <p>The system is set to make recommendations of associating Charities with Companies. This page shows recommendations where the percentage of the recommendation is calculated at greater than 70%.  Anything below that is not shown.  If you would like to update that, it is hard-coded, so please update the CharityRecommend model.</p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Open a Recommendations:</strong> Navigate to <code>/recommendations</code>.</li>
                            <li><strong>Search:</strong> This table is searchable but only displays 2,500 recommendations at one time.</li>
                            <li><strong>Approve or Disapprove:</strong> Disapproving a recommendation will remove it from the list and will not suggest that relationship in the future.  Approving the relationship moves the Charity to the Company and removes the recommendation from the list.</li>
                        </ol>
</div>

                    </div>

                    <hr class="mb-10 mt-10" />

                    <!-- Troubleshooting -->
                    <div id="troubleshooting">
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Troubleshooting</h2>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">

                        <h3 class="text-xl font-medium text-gray-700 mb-2">Can't Log In</h3>
                        <ul class="list-disc pl-6 mb-4">
                            <li><strong>Check Credentials:</strong> Ensure your email and password are correct.</li>
                            <li><strong>Reset Password:</strong> Use the <strong>Forgot your password?</strong> link on <code>/login</code>.</li>
                            <li><strong>Browser Issues:</strong> Clear cache or try a different browser.</li>
                        </ul>

                        <h3 class="text-xl font-medium text-gray-700 mb-2">Export Fails</h3>
                        <ul class="list-disc pl-6 mb-4">
                            <li><strong>File Size:</strong> Large datasets may take time—wait a few seconds.</li>
                            <li><strong>Browser Settings:</strong> Check that downloads aren't blocked.</li>
                        </ul>

                        <h3 class="text-xl font-medium text-gray-700 mb-2">Date Format Errors</h3>
                        <ul class="list-disc pl-6 mb-4">
                            <li><strong>Formation Date:</strong> Enter dates as <code>YYYY-MM-DD</code> (e.g., <code>2023-10-15</code>). Invalid formats (e.g., <code>invalid-date</code>) will trigger an error message.</li>
                        </ul>
                    </div>
</div>

<hr class="mb-10 mt-10" />
                    <div id="tech-details">
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Technical Details</h2>
                        <p class="mb-4">Below are technical details about what is needed to run this program.</p>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Database and Technology Required</h2>
                        <ul class="list-disc pl-6 mb-4">
                            <li><strong>System Requirements:</strong> Linux (currently Debian 12), Apache2, PHP8+, MariaDB / mySQL, Python.  Minimum data storage space: 10GB. Recommended 16GB RAM or higher.</li>
                            <li><strong>Database Schema:</strong> See the database_schema.pdf in the scripts folder.</li>
                        </ul>
                    </div>


                    <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Data Dictionary</h2>
                        <p class="mb-4">Most of the tables and fields are self-explanatory, but below is a reference for some database values that may not be as clear.</p>
                        
                        <h3 class="text-l font-medium text-gray-700 mb-2">company - publicly traded companies</h3>
                        <ul class="list-disc pl-6 mb-4">
                            <li>id = sequential database ID</li>
                            <li>name = name of the company as it appears in the Edgar database</li>
                            <li>ticker = stock ticker</li>
                            <li>sec_id = ID number needed to search Edgar's API, assigned by the SEC.</li>
                            <li>ein = tax ID number of the company [null].  This is not included in the base file from Edgar but may be valuable later.</li>
                        </ul>

                        <h3 class="text-l font-medium text-gray-700 mb-2">charity - 990 organizations from Giving Tuesday</h3>
                        <ul class="list-disc pl-6 mb-4">
                            <li>id = sequential database ID</li>
                            <li>name = name of the charity as it appears in the 990 form</li>
                            <li>parent = foreign key for the ID of the company [null].  This is not an enforced FK because we do not know the parent org when these are added.</li>
                            <li>ein = tax ID number of the company [null].  This is not included in the base file from Edgar but may be valuable later.</li>
                            
                        </ul>

                        <h3 class="text-l font-medium text-gray-700 mb-2">charity_giving - 990 data from Giving Tuesday</h3>
                        <ul class="list-disc pl-6 mb-4">
                            <li>id = sequential database ID</li>
                            <li>charity_id = foreign key for the ID of the charity [null].  This is not an enforced FK because we do not know the parent org when these are added.</li>
                            <li>ein = tax ID number of the company [null].  This is not included in the base file from Edgar but may be valuable later.</li>
                            <li>tax_year, tax_period_start, tax_period_end = All specify the period encompassing this particular row and entry, useful for interpreting the 990 data.</li>
                            <li>url = URL of the XML file with ALL 990 fields.</li>
                        </ul>

                        <h3 class="text-l font-medium text-gray-700 mb-2">list_associate - company associations to lists</h3>
                        <ul class="list-disc pl-6 mb-4">
                            <li>id = sequential database ID</li>
                            <li>company_id = foreign key database ID for a company [company table].</li>
                            <li>list_id = foreign key database ID of the list for this company [list table].</li>
                        </ul>
                    </div>

<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
    <div class="columns-2">
        <div>
<h3 class="text-xl font-medium text-gray-700 mb-2">Required Python Modules (versions)</h3>
<ul class="list-disc pl-6 mb-4">
<li>anyio                  3.6.2</li>
<li>appdirs                1.4.4</li>
<li>argcomplete            2.0.0</li>
<li>attrs                  22.2.0</li>
<li>Babel                  2.10.3</li>
<li>beautifulsoup4         4.11.2</li>
<li>beniget                0.4.1</li>
<li>Bottleneck             1.3.5</li>
<li>Brotli                 1.0.9</li>
<li>certifi                2022.9.24</li>
<li>chardet                5.1.0</li>
<li>charset-normalizer     3.0.1</li>
<li>click                  8.1.3</li>
<li>colorama               0.4.6</li>
<li>contourpy              1.0.7</li>
<li>cryptography           38.0.4</li>
<li>cycler                 0.11.0</li>
<li>decorator              5.1.1</li>
<li>defusedxml             0.7.1</li>
<li>dnspython              2.3.0</li>
<li>et-xmlfile             1.0.1</li>
<li>fonttools              4.38.0</li>
<li>fs                     2.4.16</li>
<li>gast                   0.5.2</li>
<li>ghp-import             2.1.0</li>
<li>gpg                    1.18.0</li>
<li>h11                    0.14.0</li>
<li>h2                     4.1.0</li>
<li>hpack                  4.0.0</li>
<li>html5lib               1.1</li>
<li>httpcore               0.16.3</li>
<li>httplib2               0.20.4</li>
<li>httpx                  0.23.3</li>
<li>hyperframe             6.0.0</li>
<li>idna                   3.3</li>
<li>ijson                  3.2.0</li>
<li>iniconfig              1.1.1</li>
<li>jdcal                  1.0</li>
<li>Jinja2                 3.1.2</li>
<li>joblib                 1.2.0</li>
<li>kiwisolver             0.0.0</li>
<li>livereload             2.6.3</li>
<li>llvmlite               0.39.1</li>
<li>lunr                   0.6.2</li>
<li>lxml                   4.9.2</li>
<li>lz4                    4.0.2+dfsg</li>
<li>Markdown               3.4.1</li>
<li>markdown-it-py         2.1.0</li>
<li>MarkupSafe             2.1.2</li>
<li>matplotlib             3.6.3</li>
<li>mdurl                  0.1.2</li>
<li>mergedeep              1.3.4</li>
<li>mkdocs                 1.4.2</li>
<li>more-itertools         8.10.0</li>
<li>mpmath                 0.0.0</li>
<li>mysql-connector-python 9.1.0</li>
<li>nltk                   3.8</li>
<li>numba                  0.56.4</li>
<li>numexpr                2.8.4</li>
<li>numpy                  1.24.2</li>
<li>odfpy                  1.4.2</li>
<li>olefile                0.46</li>
<li>openpyxl               3.0.9</li>
<li>packaging              23.0</li>
<li>pandas                 1.5.3</li>
<li>Pillow                 9.4.0</li>
<li>pip                    23.0.1</li>
<li>pipx                   1.1.0</li>
<li>pluggy                 1.0.0+repack</li>
<li>ply                    3.11</li>
<li>psutil                 5.9.4</li>
<li>py                     1.11.0</li>
<li>pycurl                 7.45.2</li>
<li>Pygments               2.14.0</li>
<li>pyinotify              0.9.6</li>
<li>pyparsing              3.0.9</li>
<li>PySimpleSOAP           1.16.2</li>
<li>pytest                 7.2.1</li>
<li>python-apt             2.6.0</li>
<li>python-dateutil        2.8.2</li>
<li>python-debian          0.1.49</li>
<li>python-debianbts       4.0.1</li>
<li>pythran                0.11.0</li>
<li>pytz                   2022.7.1</li>
<li>PyYAML                 6.0</li>
<li>pyyaml_env_tag         0.1</li>
<li>regex                  2022.10.31</li>
<li>reportbug              12.0.0</li>
<li>requests               2.28.1</li>
<li>requests-toolbelt      0.10.1</li>
<li>rfc3986                1.5.0</li>
<li>rich                   13.3.1</li>
<li>scipy                  1.10.1</li>
<li>setuptools             66.1.1</li>
<li>simplejson             3.18.3</li>
<li>six                    1.16.0</li>
<li>sniffio                1.2.0</li>
<li>soupsieve              2.3.2</li>
<li>sympy                  1.11.1</li>
<li>tables                 3.7.0</li>
<li>tornado                6.2</li>
<li>tqdm                   4.64.1</li>
<li>ufoLib2                0.14.0</li>
<li>urllib3                1.26.12</li>
<li>userpath               1.8.0</li>
<li>watchdog               2.2.1</li>
<li>webencodings           0.5.1</li>
<li>wheel                  0.38.4</li>
<li>xmltodict              0.13.0</li>
</ul>
</div>

<div>
<h3 class="text-xl font-medium text-gray-700 mb-2">Required PHP Modules</h3>
<ul class="list-disc pl-6 mb-4">
<li>bcmath</li>
<li>bz2</li>
<li>calendar</li>
<li>Core</li>
<li>ctype</li>
<li>curl</li>
<li>date</li>
<li>dba</li>
<li>dom</li>
<li>exif</li>
<li>FFI</li>
<li>fileinfo</li>
<li>filter</li>
<li>ftp</li>
<li>gd</li>
<li>gettext</li>
<li>gmp</li>
<li>hash</li>
<li>iconv</li>
<li>igbinary</li>
<li>imagick</li>
<li>imap</li>
<li>intl</li>
<li>json</li>
<li>ldap</li>
<li>libxml</li>
<li>mbstring</li>
<li>mysqli</li>
<li>mysqlnd</li>
<li>openssl</li>
<li>pcntl</li>
<li>pcre</li>
<li>PDO</li>
<li>pdo_mysql</li>
<li>pdo_pgsql</li>
<li>pdo_sqlite</li>
<li>pgsql</li>
<li>Phar</li>
<li>posix</li>
<li>random</li>
<li>readline</li>
<li>redis</li>
<li>Reflection</li>
<li>session</li>
<li>shmop</li>
<li>SimpleXML</li>
<li>soap</li>
<li>sockets</li>
<li>sodium</li>
<li>SPL</li>
<li>sqlite3</li>
<li>standard</li>
<li>sysvmsg</li>
<li>sysvsem</li>
<li>sysvshm</li>
<li>tokenizer</li>
<li>xml</li>
<li>xmlreader</li>
<li>xmlwriter</li>
<li>xsl</li>
<li>Zend OPcache</li>
<li>zip</li>
<li>zlib</li>
<li>zstd</li>
</ul>

</div>
</div>

                    </div>


                    <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                    <h3 class="text-xl font-medium text-gray-700 mb-2">GUI Runs on Laravel</h2>
    <div class="columns-2">
        <div>
<h3 class="text-xl font-medium text-gray-700 mb-2">Laravel package.json</h2>

<code>
{
    "private": true,
    "type": "module",
    "scripts": {
        "dev": "npm run development",
        "development": "mix watch",
        "build": "npm run production",
        "production": "mix --production"
    },
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.10",
        "@tailwindcss/typography": "^0.5.15",
        "alpinejs": "^3.4.2",
        "autoprefixer": "^10.4.21",
        "axios": "^1.7.4",
        "laravel-mix": "^6.0.49",
        "postcss": "^8.5.3",
        "postcss-nesting": "^13.0.1",
        "tailwindcss": "^3.4.17",
        "vite": "^5.0.0"
    }
}

</code>
</div>
<div>
<h3 class="text-xl font-medium text-gray-700 mb-2">Laravel composer.json</h2>
<code>
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "The skeleton application for the Laravel framework.",
    "keywords": ["laravel", "framework"],
    "license": "MIT",
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.9",
        "laravel/tinker": "^2.9",
        "laravel/ui": "^4.5",
        "livewire/livewire": "^3.5"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/breeze": "^2.3",
        "laravel/pint": "^1.13",
        "laravel/sail": "^1.26",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.1",
        "phpunit/phpunit": "^11.0.1"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
</code>
</div>
</div>

                </div>
            </div>
        </div>
    </div>
@endsection