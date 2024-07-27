<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Algolia Autocomplete with Results</title>
    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
    <!-- Algolia Search JavaScript Client -->
    <script src="https://cdn.jsdelivr.net/npm/algoliasearch@4.10.5/dist/algoliasearch.umd.js"></script>
    <style>
        #suggestions {
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            background-color: #fff;
            z-index: 1000;
            border-radius: 0.375rem; /* Tailwind rounded-lg */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Tailwind shadow-lg */
        }
        #suggestions li {
            padding: 0.75rem; /* Tailwind p-3 */
            cursor: pointer;
        }
        #suggestions li:hover {
            background-color: #f0f0f0; /* Tailwind bg-gray-100 */
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto relative">
        <input
            type="text"
            id="search"
            placeholder="Search..."
            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <ul id="suggestions" class="hidden"></ul>
        <div id="results" class="mt-4"></div> <!-- Container for search results -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Algolia client
            const algoliasearch = window.algoliasearch;
            if (!algoliasearch) {
                console.error('Algolia JavaScript client not loaded. Check your script tag.');
                return;
            }

            const client = algoliasearch('LHD6S3CVI9', 'bae86def315f1407c230bc202bed732f');
            const index = client.initIndex('products');

            const searchInput = document.getElementById('search');
            const suggestionsList = document.getElementById('suggestions');
            const resultsContainer = document.getElementById('results'); // Container for search results

            searchInput.addEventListener('input', async () => {
                const query = searchInput.value;

                if (query.length < 2) {
                    suggestionsList.innerHTML = '';
                    suggestionsList.classList.add('hidden');
                    resultsContainer.innerHTML = ''; // Clear results
                    return;
                }

                try {
                    const { hits } = await index.search(query);

                    // Update suggestions list
                    suggestionsList.innerHTML = hits.map(hit => `
                        <li data-id="${hit.objectID}">${hit.name}</li>
                    `).join('');
                    suggestionsList.classList.remove('hidden');

                    // Update search results on the webpage
                    resultsContainer.innerHTML = hits.map(hit => `
                                                   <a href="#" class="group block overflow-hidden" onclick="openModal(${hit.objectID}, '${hit.product_name}', '₱${hit.price.toFixed(2)}', '${hit.img_path}', ${hit.stocks}, '${hit.category}')">
                                <img
                                    src="${hit.img_path}"
                                    alt="${hit.product_name}"
                                    class="h-[250px] w-full object-cover transition duration-500 group-hover:scale-105 sm:h-[300px]"
                                />
                                <div class="relative bg-white pt-3 p-4">
                                    <h1 class="text-xl font-bold text-gray-700 group-hover:underline group-hover:underline-offset-4">
                                        ${hit.product_name}
                                    </h1>
                                    <p class="mt-2">
                                        <span class="sr-only">Regular Price</span>
                                        <span class="tracking-wider text-gray-900">₱${hit.price.toFixed(2)}</span>
                                    </p>
                                    <p class="mt-1 text-gray-600">
                                        Stocks: ${hit.stocks}
                                    </p>
                                    <p class="mt-1 text-gray-600">
                                        Category: ${hit.category}
                                    </p>
                                </div>
                            </a>
                    `).join('');

                } catch (error) {
                    console.error('Error fetching search results:', error);
                    suggestionsList.innerHTML = '<li>Error fetching results</li>';
                    suggestionsList.classList.remove('hidden');
                    resultsContainer.innerHTML = '<div class="p-4 text-red-500">Error fetching results</div>';
                }
            });

            suggestionsList.addEventListener('click', (event) => {
                if (event.target.tagName === 'LI') {
                    searchInput.value = event.target.textContent;
                    suggestionsList.innerHTML = '';
                    suggestionsList.classList.add('hidden');
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', (event) => {
                if (!searchInput.contains(event.target) && !suggestionsList.contains(event.target)) {
                    suggestionsList.innerHTML = '';
                    suggestionsList.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
