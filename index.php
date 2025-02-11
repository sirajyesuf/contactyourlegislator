<?php

require 'senator.php';

function formatAddressWithState($address, $city, $zipCode) {
    // Construct the full address string
    $fullAddress = "$address, $city, $zipCode";

    // Add "GA" before the ZIP code
    $formattedAddress = preg_replace('/, (\d{5})$/', ", GA $1", $fullAddress);

    return $formattedAddress;
}

// Function to validate the form
function validateForm($address, $city,$zipCode) {

    $errors = [];

    if (empty($address)) {
        $errors['address'] = "Address is required.";
    }

    if(empty($city)) {
        $errors['city'] = "City is required.";
    }

    if(empty($zipCode) || !ctype_digit($zipCode)) {
        $errors['zipCode'] = "Valid Zipcode is required.";
    }

    return $errors;
}



// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $street_address = trim($_POST["street-address"]);
    $city = trim($_POST["city"]);
    $zipcode = trim($_POST["zipcode"]);

    // Validate the form
    $errors = validateForm($street_address, $city, $zipcode);
    
    if (empty($errors)) {

        $options = [
        'address' => formatAddressWithState($street_address,$city,$zipcode),
        "levels" =>  [
        "administrativeArea1"
        ],
        "roles" =>  [
        "legislatorUpperBody",
        "legislatorLowerBody"
        ]
        ];

        $officials = getSenatorsAndRepresentatives($options);
        $senators = $officials;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Action - Competitive Georgia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-gray-100 py-4">
        <div class="container mx-auto px-4 flex flex-wrap items-center justify-between">
            <a href="/" class="flex items-center">
                <svg class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="ml-2 text-xl font-bold text-gray-800">Competitive Georgia</span>
            </a>
            <button id="menuToggle" class="block lg:hidden">
                <svg class="h-6 w-6 fill-current" viewBox="0 0 20 20">
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
                </svg>
            </button>
            <nav id="menu" class="hidden w-full lg:flex lg:w-auto lg:items-center">
                <ul class="lg:flex lg:space-x-8">
                    <li><a href="/" class="block py-2 text-gray-800 hover:text-blue-600">Home</a></li>
                    <li><a href="/about" class="block py-2 text-gray-800 hover:text-blue-600">About</a></li>
                    <li><a href="/contact" class="block py-2 text-gray-800 hover:text-blue-600">Contact</a></li>
                    <li><a href="#contact-form" class="block py-2 text-gray-800 hover:text-blue-600">Take Action</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="bg-blue-600 text-white text-center py-32">
            <div class="container mx-auto px-4">
                <h1 class="text-5xl font-bold mb-6">Take Action - Competitive Georgia</h1>
                <p class="text-xl mb-12">Engage with your legislators and make your voice heard!</p>
                <a href="#contact-form" class="bg-yellow-400 text-gray-800 px-8 py-4 text-lg rounded-md font-bold hover:bg-yellow-300 transition">
                    Contact Your Legislator
                </a>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section id="contact-form" class="bg-gray-100 py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold mb-8 text-center">Contact Your Legislator</h2>
                <form action="" method="POST" class="max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="mb-4">
                            <label for="street-address" class="block mb-2 font-bold">Street Address</label>
                            <input type="text" id="street-address" name="street-address"  class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            value="<?= $street_address ?? "" ?>">
                            <?php if (isset($errors['address'])): ?>
                                <p class="mt-1 text-red-500 text-sm">
                                    <?= $errors["address"] ?>
                                </p>

                            <?php endif; ?>

    
                        </div>
                        <div class="mb-4">
                            <label for="city" class="block mb-2 font-bold">City</label>
                            <input type="text" id="city" name="city"  class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            value="<?= $city ?? "" ?>">

                            <?php if (isset($errors['city'])): ?>
                                <p class="mt-1 text-red-500 text-sm">
                                    <?= $errors["city"] ?>
                                </p>

                            <?php endif; ?>
                            
                        </div>
                        <div class="mb-4">
                            <label for="zipcode" class="block mb-2 font-bold">Zipcode</label>
                            <input type="text" id="zipcode" name="zipcode"  class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            value="<?= $zipcode ?? "" ?>">
                            <?php if (isset($errors['zipCode'])): ?>
                                <p class="mt-1 text-red-500 text-sm">
                                    <?= $errors["zipCode"] ?>
                                </p>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                    <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-8 py-3 rounded-md font-bold hover:bg-blue-700 transition">
                        Submit
                    </button>
                </form>
            </div>
        </section>




    <?php if (empty($errors) && !empty($senators)): ?>
    <section class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">Your Representatives</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($senators as $senator): ?>
                    <div class="bg-white shadow-none rounded-sm overflow-hidden flex flex-col gap-4 p-2">
                        <div class="relative h-full w-full">
                          
                            <img src="<?= !empty($senator['photoUrl']) ? $senator['photoUrl'] : 'https://placehold.co/600x400' ?>" alt="Senator Photo" class="object-contain w-full h-full">

                        </div>
                        <div class="p-4">
                            <h3 class="text-xl font-bold mb-2"><?= $senator['name'] ?></h3>
                            <p class="text-gray-600 mb-4"><?= $senator['party'] ?></p>
                            <div class="space-y-2">
                                <?php foreach ($senator['address'] as $address): ?>
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0zM12 9a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                                        <p class="text-sm">
                                            <?= $address['line1'] ?>, <?= $address['city'] ?>, <?= $address['state'] ?> <?= $address['zip'] ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                                <?php foreach ($senator['phones'] as $phone): ?>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.211L7.71 10.29a1 1 0 00-.948.684H5a2 2 0 01-2-2v-3zM7 13a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.211L7.71 16.29a1 1 0 00-.948.684H7a2 2 0 01-2-2v-3z"></path></svg>
                                        <p class="text-sm"><?= $phone ?></p>
                                    </div>
                                <?php endforeach; ?>
                                <?php foreach ($senator['urls'] as $url): ?>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.211L7.71 10.29a1 1 0 00-.948.684H5a2 2 0 01-2-2v-3zM7 13a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.211L7.71 16.29a1 1 0 00-.948.684H7a2 2 0 01-2-2v-3z"></path></svg>
                                        <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:underline">
                                            <?= $url ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                        </div>

                        <div>
                            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-8 py-3 rounded-md font-bold hover:bg-blue-700 transition">
                                <a href="send.php?senator_name=<?= $senator['name'] ?>&senator_email=<?= $senator['email'] ?>" class="text-white">
                                    Contact
                                </a>
                            </button>
                        </div>
                    

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php else: ?>
        <section class="bg-gray-100 py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold mb-8 text-center">No Representatives Found</h2>
                <p class="text-center">No representatives found for the provided address. Please try again with a different address.</p>
            </div>
        </section>
<?php endif; ?>

    </main>




    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <p class="mb-4">&copy; 2025 Competitive Georgia. All rights reserved.</p>
            <p>Empowering citizens to engage with their legislators and make a difference in Georgia.</p>
        </div>
    </footer>
</body>
</html>
