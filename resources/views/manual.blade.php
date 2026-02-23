<!-- resources/views/manual.blade.php -->
@extends('layout.app')
@section('title')User Manual @endsection
@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">
                    <!-- Introduction -->
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">User Manual for Anson Analytics Charity Research Portal</h1>
                    <p class="mb-4">
                        Welcome to the Charity Research Portal. This user manual provides step-by-step guidance on how to use the site's key features, including logging in, adding and creating lists, exporting data, and managing relationships between companies and charities. 
                    </p>

                    <!-- Table of Contents -->
                    <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Table of Contents</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li><a href="#getting-started" class="text-blue-600 hover:underline">Getting Started</a></li>
                        <li><a href="#entity-setup" class="text-blue-600 hover:underline">Entity Setup</a></li>
                        <li><a href="#managing-lists" class="text-blue-600 hover:underline">Managing Lists</a></li>
                        <li><a href="#exporting-data" class="text-blue-600 hover:underline">Exporting Data</a></li>
                        <li><a href="#entity-relationships" class="text-blue-600 hover:underline">Entity Relationships</a></li>
                        <li><a href="#troubleshooting" class="text-blue-600 hover:underline">Troubleshooting</a></li>
                        <li><a href="#tech-details" class="text-blue-600 hover:underline">Technical Details</a></li>
                    </ul>

                    <p class="mb-5"><a href="{{ url('process') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Process Map and Overview</a> <a href="{{ url('setup') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Setup or Configure the dataset</a></p>

                    <!-- Getting Started -->
                    <div id="getting-started">
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Getting Started</h2>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Logging In</h3>
                        <p class="mb-2">To access the site's features, you must log in with your credentials.</p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Open the Login Page:</strong> Visit <code>[your-domain]/login</code> in your browser.</li>
                            <li><strong>Enter Credentials:</strong> Input your <strong>Email Address</strong> and <strong>Password</strong> in the provided fields. (Optional) Check <strong>Remember me</strong> to stay logged in across sessions.</li>
                            <li><strong>Submit:</strong> Click the <strong>Login</strong> button (blue, rounded, with hover effect). If successful, you'll be redirected to the Charities page (<code>/charities</code>).</li>
                        </ol>
                        <p class="text-sm italic text-gray-600 mb-4">Note: If you forget your password, click <strong>Forgot your password?</strong> to reset it via email.</p>
</div>
<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Logging Out</h3>
                        <p class="mb-2">To securely exit the site:</p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Access the User Menu:</strong> In the top-right corner of the navigation bar, click your name (e.g., “John Doe”). A dropdown appears.</li>
                            <li><strong>Log Out:</strong> Click <strong>Logout</strong> in the dropdown. You'll be redirected to the login page.</li>
                        </ol>
</div>
<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Navigating the Dashboard</h3>
                        <p class="mb-2">After logging in, you're taken to the Charities page, which serves as the default landing page. Use the navigation bar at the top to access key sections:</p>
                        <ul class="list-disc pl-6 mb-4">
                            <li><strong>Charities:</strong> View and manage charity records (<code>/charities</code>).</li>
                            <li><strong>Lists:</strong> Manage custom lists of companies (<code>/lists</code>).</li>
                            <li><strong>Profile:</strong> Edit your user settings (<code>/profile/edit</code>).</li>
                        </ul>
                        <p class="text-sm italic text-gray-600 mb-4">The navigation bar is responsive and styled with Tailwind CSS (e.g., <code>bg-white</code>, <code>shadow-md</code>).</p>
                    </div>
</div>
<hr class="mb-10 mt-10" />

<h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4" id="entity-setup">Entity Setup</h2>
<p>Entities such as Companies come from stock information and SEC registrations.  These are pulled in from the publicly available data when the database is updated.  Charities are pulled in from Giving Tuesday's 990 database.  However, you can update them if you wish.</p>

<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h4 class="text-lg font-medium text-gray-600 mb-1">Editing Company Details</h4>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Go to Company Edit Page:</strong> Navigate to <code>/companies/{id}/edit</code>.</li>
                            <li><strong>Update Fields:</strong> Modify <strong>Name</strong>, <strong>EIN</strong>, <strong>SEC ID</strong>, or <strong>Formation Date</strong> (entered as <code>YYYY-MM-DD</code>). The system converts the date to a Unix timestamp internally.</li>
                            <li><strong>Save Changes:</strong> Click <strong>Update Company</strong> (blue button). A success message appears (e.g., “Company updated successfully”).</li>
                        </ol>
</div>
<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h4 class="text-lg font-medium text-gray-600 mb-1">Editing Charity Details</h4>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Go to Charity Edit Page:</strong> Navigate to <code>/charity/{id}</code> and click the Edit button.</li>
                            <li><strong>Update Fields:</strong> Modify <strong>Name</strong>, <strong>EIN</strong>.</li>
                            <li><strong>Save Changes:</strong> Click <strong>Update Charity</strong> (blue button). A success message appears (e.g., “Company updated successfully”).</li>
                        </ol>
                        <p class="text-sm italic text-gray-600 mb-4">Note: If you change the EIN of a Charity, you will break some of the relationships.  The system may have to be manually updated to restore those connections.</p>
</div>


<hr class="mb-10 mt-10" />

                    <div id="managing-lists">
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Managing Lists</h2>

                        
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Adding and Creating Lists</h3>
                        <p class="mb-2">Lists allow you to group Companies according to stock market lists or your own lists, in order to export all Charity transactions associated with the list at one time.</p>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                            <h4 class="text-lg font-medium text-gray-600 mb-1">Creating a New List</h4>
                            <ol class="list-decimal pl-6 mb-4">
                                <li><strong>Navigate to Lists:</strong> Click <strong>Lists</strong> in the navigation bar (<code>/lists</code>).</li>
                                <li><strong>Start a New List:</strong> On the Lists index page, click <strong>Create New List</strong> (a blue button, <code>bg-blue-600</code>).</li>
                                <li><strong>Fill in Details:</strong> Enter a <strong>Name</strong> for the list (e.g., “Top Donors 2025”). (Optional) Add a <strong>Description</strong>.</li>
                                <li><strong>Save the List:</strong> Click <strong>Save</strong> (green button, <code>bg-green-600</code>). You'll be redirected to the list's detail page.</li>
                            </ol>
                        </div>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h4 class="text-lg font-medium text-gray-600 mb-1">Adding Companies to a List</h4>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Open a List:</strong> From the Lists page, click a list name to view its details (<code>/lists/{id}</code>).</li>
                            <li><strong>Add Companies:</strong> Click <strong>Add Companies</strong> (blue button). In the form, select companies from a dropdown or search field. Click <strong>Submit</strong> to associate the companies.</li>
                            <li><strong>Verify:</strong> The updated list shows the added companies in a table.</li>
                        </ol>
                        <p class="text-sm italic text-gray-600 mb-4">Note, two lists have been pre-populated for you, with the assumption that the organizations on these lists do not vary too much from year to year.  You can handle these minor adjustments yourself.  However, if you have a large list you would like to add, your IT provider can quickly add a CSV file of stock tickers to the list selection.</p>
                        </div>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h4 class="text-lg font-medium text-gray-600 mb-1">Removing Companies from a List</h4>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Open a List:</strong> Navigate to the list's detail page.</li>
                            <li><strong>Remove a Company:</strong> In the company table, find the row for the company. Click <strong>Remove</strong> (red button, <code>text-red-600</code>). Confirm the action in the popup (e.g., “Are you sure?”).</li>
                            <li><strong>Check Update:</strong> The company is no longer listed.</li>
                        </ol>

                        </div>

                        <hr class="mb-10 mt-10" />
                        <h3 class="text-xl font-medium text-gray-700 mb-2" id="exporting-data">Exporting Data</h3>
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
<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h4 class="text-lg font-medium text-gray-600 mb-1">Exporting 990 Data for a specific Charity</h4>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Visit Charities:</strong> Go to <code>/charities</code>.</li>
                            <li><strong>Select Charity:</strong> Select the charity you want by clicking on its name.</li>
                            <li><strong>View 990 Data:</strong> Reported data is summarized on screen in a table format and is typically totaled at the bottom. </li>
                            <li><strong>Export All Data:</strong> Click the green <strong>Export</strong> button at the bottom. A CSV file download will launch with fields like <code>Charity Name</code>, <code>Tax Year</code>, <code>Grants Paid</code>, <code>Net Assets</code>, etc.</li>
                        </ol>
                        <p class="text-sm italic text-gray-600 mb-4">Note: Ensure your browser allows pop-ups/downloads from the site.</p>
</div>
<div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                        <h4 class="text-lg font-medium text-gray-600 mb-1">Exporting 990 Data for all charities associated with a specific Company</h4>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Visit Companies:</strong> Go to <code>/companies</code>.</li>
                            <li><strong>Select Company:</strong> Select the Company you want by clicking on its name or ticker.</li>
                            <li><strong>View 990 Data</strong> Reported data for each Charity associated with this company is displayed on the screen.  The system automatically aggregates each of the associated Charities by tax year and summarizes on screen in a table format, with totals at the bottom. </li>
                            <li><strong>Export All Data:</strong> Click the green <strong>Export</strong> button at the bottom. A CSV file download will launch with fields like <code>Tax Year</code>, <code>Grants Paid</code>, <code>Net Assets</code>, etc.</li>
                        </ol>
                        <p class="text-sm italic text-gray-600 mb-4">Note: This file can be large, so please be patient.  The download will begin as soon as the file is ready.</p>

                        </div>
<hr class="mb-10 mt-10" />
                        <h3 class="text-xl font-medium text-gray-700 mb-2" id="entity-relationships">Managing Company and Charity Relationships</h3>
                        <p class="mb-2">Link Charities to Companies to track 990 data by Company.</p>

                        <div class="p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">

                        <h4 class="text-lg font-medium text-gray-600 mb-1">Associating a Charity with a Company</h4>
                        <p>Associating Charities to Companies are accomplished through the Company's page.</p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li><strong>Open a Company:</strong> From <code>/companies</code>, click a Company name (<code>/companies/{id}</code>).</li>
                            <li><strong>Search for Charity:</strong> Search for the complete or partial name of a Charity in the search box.</li>
                            <li><strong>Associate Charity:</strong> Click the green Associate button to assign the Charity to the selected Company.  Note: a Charity can be assigned to only one Company at at time.</li>
                            <li><strong>Verify:</strong> The Company page will refresh with the newly assigned Charity listed.  The transaction table will also reload and re-calculate with the new Charity's 990 data.</li>
                        </ol>
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


        </div>
    </div>
@endsection