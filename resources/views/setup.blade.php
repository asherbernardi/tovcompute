<!-- resources/views/manual.blade.php -->
@extends('layout.app')
@section('title')Setup and Maintenance @endsection
@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

@if (session('success'))
<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">{{ session('success') }}</div>
@endif
@if (session('error'))
<div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">{{ session('error') }}</div>
@endif

                <div class="p-6 text-gray-900">

                <p class="mb-5"><a href="{{ url('manual') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">User Manual</a> <a href="{{ url('process') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Process Map and Overview</a></p>
                   


                    <h1 class="text-3xl font-bold text-gray-800 mb-6">Setup and Maintenance</h1>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-3" role="alert">
  <strong class="font-bold">Warning!</strong>
  <span class="block sm:inline">Use extreme caution with these scripts.  You can erase or damage the database.</span>
  </div>

  

                    <div id="backup-database">
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Maintenance Tasks</h2>
                        <p>Clicking the links below will immediately activate scripts in the background that accomplish the task you request.  Note these are not destructive, but they can take a very long time to run.</p>
                        <ul class="list-disc pl-6 mt-5 mb-4">
                            <li>Run Backup - The button below will trigger a manual backup of the database.  The database is backed up weekly by default, and stored in /backup/  on this server.  Check the <code>config/backup.php</code> file for more details and configurations.</li>
                            <li>Activate Charity Matching - This will trigger the AI function that goes through every Company to attempt to match to the Charities that are not already associated.  Warning: this takes a VERY long time to complete, but you can also configure it to only take a few companies at a time.  See MatchController.php.</li>
                        </ul>

                        <form action="{{ route('backup.run') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Run Backup
                                </button>
                            </form>

                            <p class='mt-4 mb-5'><a class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500" href="{{ url('runMatches') }}">Activate Charity Matching</a></p>
                    


                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
  <span class="block sm:inline">To enable some of the automatic execution in Laravel, you need to setup cron with the following. <br />  <code>* * * * * cd /PATH-TO-APP/laravel11-app/ && php artisan schedule:run >> /dev/null 2>&1</code></span>
</div>
                    </div>


                    <div id="">
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Updating Entities</h2>
                        <p class="mb-5">Updating entities usually involves huge datasets.  PHP and this interface can do some of this, but Python is much more efficient.  Therefore, it is best to download the data to the server and then execute these Python scripts via command line to accomplish these tasks.</p>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Updating Companies</h3>
                        <p class="mb-5">This is used when you want to sync the database when new entries from the SEC registrations.  Unfortunately, it has to process the entire list, because they do not put out yearly amendments.  The system will ignore any existing ticker already recorded and only add the new ones.  Note that if any company has changed its ticker for some reason, the database will create a new entry under the new ticker.</p>
                        
                        <ol class="list-decimal pl-6 mb-4">
                            <li><a class="text-red-700" href="{{ url('companies/fetch') }}">Start Company Updates from SEC Data</a> - This may take a few minutes to run.</li>
                        </ol>
                        <p class="text-sm italic text-gray-600 mb-4">Note: If this fails, you may need to double-check where the SEC is storing the company list.</p>
                        </div>
                    
                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Updating Charities</h3>
                        <p class="mb-5">This is used when you want to update the list of Charities in the system.  This does not override any existing charities, but it will create new ones if they have changed their names or EINs.  This data comes from Giving Tuesday.</p>
                        <p class="mb-5">Download the latest 990 Combined DataMart CSV from Giving Tuesday, place it on the server, and enter the path below. The import runs in the background — check <code>storage/logs/charity_import.log</code> for progress.</p>
                        <p class="mb-5"><a href="https://990data.givingtuesday.org/datamarts/?sort=title%3Aasc">https://990data.givingtuesday.org/datamarts/?sort=title%3Aasc</a></p>
                        <form action="{{ route('charities.import') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="csv_path" class="block text-sm font-medium text-gray-700 mb-1">CSV File Path</label>
                                <input type="text" name="csv_path" id="csv_path"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-mono"
                                    placeholder="Leave blank to use default path"
                                    value="{{ base_path('data_sources/2025_07_10_All_Years_990_990ez_990pf_990n_Combined_DataMart.csv') }}" />
                                <p class="text-xs text-gray-500 mt-1">Full server path to the 990 Combined DataMart CSV file.</p>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Import Charities
                            </button>
                        </form>
                        <p class="text-sm text-gray-500 mt-3">CLI equivalent: <code>php artisan charities:import --csv=/path/to/file.csv</code></p>
                        </div>

                        
                    </div>
                    <div>

                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Updating Giving Data</h2>
                        <p class="mb-5">Updating Giving data takes the same process.  Unfortunately, all data is downloaded, but anything already in the database is ignored.  There is no way to pull just the latest entries, so the system has to go through the data again and parse out what is not relevant.</p>


                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Updating Basic Giving Information</h3>
                        <p class="mb-5">This is used when you want to update the Giving History in the database. This uses the same 990 Combined DataMart CSV as the charity import above — download the latest file from Giving Tuesday and enter its path below. The import runs in the background; check <code>storage/logs/giving_import.log</code> for progress.</p>
                        <p class="mb-5"><a href="https://990data.givingtuesday.org/datamarts/?sort=title%3Aasc">https://990data.givingtuesday.org/datamarts/?sort=title%3Aasc</a></p>
                        <form action="{{ route('giving.import') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="giving_csv_path" class="block text-sm font-medium text-gray-700 mb-1">CSV File Path</label>
                                <input type="text" name="csv_path" id="giving_csv_path"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-mono"
                                    placeholder="Leave blank to use default path"
                                    value="{{ base_path('data_sources/2025_07_10_All_Years_990_990ez_990pf_990n_Combined_DataMart.csv') }}" />
                                <p class="text-xs text-gray-500 mt-1">Full server path to the 990 Combined DataMart CSV file.</p>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Import Giving Data
                            </button>
                        </form>
                        <p class="text-sm text-gray-500 mt-3">CLI equivalent: <code>php artisan giving:import --csv=/path/to/file.csv</code></p>
                        </div>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Extracting Extended Giving Information</h3>
                        <p class="mb-5">Fetches each charity's individual IRS XML filing (URLs are stored in the database from the giving import) and fills in detailed financial fields: grants paid, contributions, net assets, revenue, expenses, liabilities, and total assets. No file download required — it pulls live from the IRS via Giving Tuesday. Run this after importing giving data.</p>
                        <form action="{{ route('giving.extract-xml') }}" method="POST" x-data="{ searchType: 'company' }">
                            @csrf
                            <div class="mb-4">
                                <label for="search_type" class="block text-sm font-medium text-gray-700 mb-1">Scope</label>
                                <select name="search_type" id="search_type" x-model="searchType"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <option value="company">Company — all charities under a specific company</option>
                                    <option value="charity">Charity — a specific charity</option>
                                    <option value="list">List — all charities in a specific list</option>
                                    <option value="all_lists">All Lists — every charity in any list</option>
                                    <option value="all">All — every charity with a URL (very slow)</option>
                                </select>
                            </div>
                            <div class="mb-4" x-show="['company','charity','list'].includes(searchType)">
                                <label for="criteria" class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                                <input type="number" name="criteria" id="criteria"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                                    placeholder="Enter the company, charity, or list ID" />
                            </div>
                            <div class="mb-4">
                                <label for="fields" class="block text-sm font-medium text-gray-700 mb-1">Fields to Extract</label>
                                <select name="fields" id="fields"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <option value="all">All — grants, contributions, net assets, revenue, expenses, liabilities, total assets</option>
                                    <option value="basic">Basic — grants and contributions only</option>
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Extract XML Giving Data
                            </button>
                        </form>
                        <p class="text-sm text-gray-500 mt-3">CLI equivalent: <code>php artisan giving:extract-xml --search-type=company --criteria=735 --fields=all</code></p>
                        </div>
                    

                    
                    </div>
                    <div>

                    <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Updating Relationships</h2>
                        <p class="mb-5">These tasks fix some of the issues with importing data.</p>
                        
                    <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Associating Charity Giving Entries</h3>
                        <p class="mb-5">The system is trying to import so fast, it may miss some of the associations.  Run this script after you have updated Giving data to re-associate those records with a Charity.  Any existing associations will not be changed.</p>
                        <p class="mb-5"><a href="https://990data.givingtuesday.org/datamarts/?sort=title%3Aasc">https://990data.givingtuesday.org/datamarts/?sort=title%3Aasc</a></p>
                        <form action="{{ route('charity-giving.sync') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Sync Charity IDs
                        </button>
                    </form>
                        </div>
                    </div>


                    <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Updating Missing Charity Names</h3>
                        <p class="mb-5">Oddly enough, you will have charities that import with only the EIN or identifiers, no name.  This is correctable by going into the database, locating these with missing names, and pulling the data directly from the live API at Giving Tuesday.  We do not use this live API in general because it is time-restricted.  This will pull 10,000 records per run, so let it run and leave it for a while so you are not caught in the time limit.  This does not require any additional files to run, but it will take a little time to complete.</p>
                        <p class="mb-5"><a href="https://990data.givingtuesday.org/datamarts/?sort=title%3Aasc">https://990data.givingtuesday.org/datamarts/?sort=title%3Aasc</a></p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li>Run fix_missing_charity_names.py to locate and update Charities that are only pulling EINs.</li>
                        </ol>
                        </div>
                    </div>


                    

            </div>
        </div>
    </div>
@endsection