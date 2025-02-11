
<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;
// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


use Google\Client;
use Google\Service\CivicInfo;

function getSenatorsAndRepresentatives($address) {
    // Initialize the Google Client
    $client = new Client();
    $client->setApplicationName("Google Civic Info Sample");
    $client->setDeveloperKey($_ENV['GOOGLE_API_KEY']); // Replace with your actual API key

    // Initialize the Civic Info service
    $civicInfoService = new CivicInfo($client);

    // Define the OCD-ID
    // $ocdId = 'ocd-division/country:us/state:ga';
    // $ocdId = 'ocd-division/country:us/state:ca';

    // Make the request to the Civic Information API
    // $representatives = $civicInfoService->representatives->representativeInfoByDivision($ocdId);
    $representatives = $civicInfoService->representatives->representativeInfoByAddress($address);

    // Initialize arrays to hold senators and representatives
    $senators = [];
    $representativesList = [];

    foreach($representatives["officials"] as $official) {
        $representativesList[] = [
            'name' => $official->name,
            'address' => $official->address ?? [],
            'party' => $official->party ?? '',
            'phones' => $official->phones ? $official->phones : [],
            'email' => $official->emails ? $official->emails[0] : null,
            'photoUrl' => $official->photoUrl ?? null,
            'urls' => $official->urls ?? [],
            'channels' => $official->channels ?? []
        ];
    }

    return $representativesList;

    // //Check if the response is valid
    // if (isset($representatives['officials']) && isset($representatives['offices'])) {
    //    // Iterate over the offices to filter senators and representatives
    //     foreach ($representatives['offices'] as $office) {
    //         foreach ($office->officialIndices as $index) {
    //             $official = $representatives['officials'][$index];
    //             if (stripos($office->name, 'Senator') !== false or stripos($office->name, 'Senate') !== false or stripos($office->name, 'House') !== false) {
    //                 $senators[] = [
    //                         'name' => $official->name,
    //                         'address' => $official->address ?? [],
    //                         'party' => $official->party ?? 'N/A',
    //                         'phones' => $official->phones ?? [],
    //                         'emails' => $official->emails[0] ?? '',
    //                         // 'emails' => "sirajyesuf762@gmail.com",
    //                         'photoUrl' => $official->photoUrl ?? '',
    //                         'urls' => $official->urls ?? [],
    //                         'channels' => $official->channels ?? []
    //                     ];

    //             } elseif (stripos($office->name, 'Representative') !== false or stripos($office->name, 'Assembly') !== false) {
    //                 $senators[] = [
    //                         'name' => $official->name,
    //                         'address' => $official->address ?? [],
    //                         'party' => $official->party ?? 'N/A',
    //                         'phones' => $official->phones ?? [],
    //                         'emails' => $official->emails ?? [],
    //                         // 'emails' => "sirajyesuf762@gmail.com",
    //                         'photoUrl' => $official->photoUrl ?? '',
    //                         'urls' => $official->urls ?? [],
    //                         'channels' => $official->channels ?? []
    //                     ];

    //             }
    //         }
    //     }
    // }

    // foreach ($representatives["officials"] as $official) {

    //         $senators[] = [
    //         'name' => $official->name,
    //         'address' => $official->address ?? [],
    //         'party' => $official->party ?? 'N/A',
    //         'phones' => $official->phones ?? [],
    //         'emails' => $official->emails ?? [],
    //         'photoUrl' => $official->photoUrl ?? '',
    //         'urls' => $official->urls ?? [],
    //         'channels' => $official->channels ?? []
    //     ];

    // }

    // return [
    //     'senators' => array_values($senators),
    //     'representatives' => array_values($representativesList)
    // ];

    // return $senators;
}
?>


