<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load Composer autoload
require 'vendor/autoload.php';

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get senator name from the query string
$senatorName = isset($_GET['senator_name']) ? $_GET['senator_name'] : '';
$senatorEmail = isset($_GET['senator_email']) ? $_GET['senator_email'] : '';

//if one of senator name or  email are not provided, redirect to the home page
if (!$senatorName || !$senatorEmail) {
    header("Location: /");
    exit;
}

// Load email templates
$emailTemplates = json_decode(file_get_contents("emails.json"), true)["templates"];

// Replace [Senator Name] in templates
if ($senatorName) {
    foreach ($emailTemplates as &$template) {
        $template['template'] = str_replace("[Senator Name]", $senatorName, $template['template']);
    }
}

// Function to send email
function sendEmail($to, $toName, $from, $fromName, $subject, $message)
{
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0; 
    $mail->Debugoutput = 'html';
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME'];
        $mail->Password   = $_ENV['MAIL_PASSWORD'];
        // $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use 'ssl' for port 465
        $mail->Port       = $_ENV['MAIL_PORT'];

        // Email Headers
        $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
        $mail->addReplyTo($from, $fromName);
        
        if($_ENV['SENATOR_TEST_EMAIL']){
            $mail->addAddress($_ENV['SENATOR_TEST_EMAIL'], 'Test Senator');
        }
        else{

            $mail->addAddress($to, $toName);
        }

        // Email Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        return $mail->send();
    } catch (Exception $e) {
        return "Mail error: " . $mail->ErrorInfo;
    }
}


// Function to validate the form
function validateForm($fullname, $email) {
    $errors = [];

    if (empty($fullname)) {
        $errors['name'] = "Name is required.";
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    return $errors;
}



// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    $errors = validateForm($name, $email);


    if (empty($errors)) {

        $sendStatus = sendEmail($senatorEmail, $senatorName, $email, $name, "Message from a Concerned Citizen", $message);

        if ($sendStatus === true) {
            $success = "Your message has been sent successfully!";
        } else {
            $error = $sendStatus;
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact a Senator</title>
    <script src="https://cdn.tailwindcss.com"></script>
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const topics = <?php echo json_encode($emailTemplates); ?>;
            const messageBox = document.getElementById("message");
            const topicContainer = document.getElementById("topic-container");
            const nameField = document.querySelector('input[name="name"]');
            const emailField = document.querySelector('input[name="email"]');

            // Set the first template message as default in the textarea
            messageBox.value = topics[0].template;

            // Generate radio buttons dynamically for topics
            topics.forEach((template, index) => {
                let radio = document.createElement("input");
                radio.type = "radio";
                radio.name = "topic";
                radio.value = template.topic;
                radio.id = "topic-" + index;
                radio.classList.add("mr-2");

                // Set the first radio button as checked by default
                if (index === 0) {
                    radio.checked = true;
                    messageBox.value = template.template; // Set the default message template
                }

                radio.addEventListener("change", function () {
                    updateMessage(); // Update the message whenever a new topic is selected
                });

                let label = document.createElement("label");
                label.htmlFor = "topic-" + index;
                label.classList.add("mr-4");
                label.appendChild(document.createTextNode(template.topic));

                topicContainer.appendChild(radio);
                topicContainer.appendChild(label);
                topicContainer.appendChild(document.createElement("br"));
            });

            // Function to update message dynamically based on form fields and selected topic
            function updateMessage() {
                let name = nameField.value;
                let email = emailField.value;

                // Get the selected template message
                let selectedTemplate = topics.find(template => template.topic === document.querySelector('input[name="topic"]:checked').value);

                // Replace placeholders in the selected template
                let updatedMessage = selectedTemplate.template;

                if (name) {
                    updatedMessage = updatedMessage.replace(/\[Your Name\]/g, name);  // Replace all instances of [Your Name]
                }

                if (email) {
                    updatedMessage = updatedMessage.replace(/\[Your Email\]/g, email);  // Replace all instances of [Your Email]
                }

                messageBox.value = updatedMessage;
            }

            // Add event listeners for name and email fields to update the message dynamically
            nameField.addEventListener('input', updateMessage);
            emailField.addEventListener('input', updateMessage);

            // Initial call to update the message with default values
            updateMessage();
        });
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased">


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
    <!-- Hero Section -->
<section class="bg-[#63a1d0] text-white text-center py-32">
    <div class="container mx-auto px-4">
        <h1 class="text-5xl font-bold mb-6">Contact Your Legislator</h1>
        <!-- <p class="text-xl mb-12">Engage with your legislators and make your voice heard!</p> -->
        <!-- <a href="#contact-form" class="bg-yellow-400 text-gray-800 px-8 py-4 text-lg rounded-md font-bold hover:bg-yellow-300 transition">
            Contact Your Legislator
        </a> -->
    </div>
</section>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-none rounded-none mt-10">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Contact Form</h2>

        <?php if (isset($success)): ?>
            <p class="p-4 mb-4 text-green-800 bg-green-100 rounded-lg"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="space-y-2">
                <label for="name" class="block text-lg font-medium text-gray-700">Name:</label>
                <input type="text" name="name" id="name" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Enter your name"
                
                value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                >
                <?php if (isset($errors['name'])): ?>
                    <p class="p-2 mb-2 text-red-800 bg-red-100 rounded-lg"><?php echo $errors['name']; ?></p>
                <?php endif; ?>
            </div>

            <div class="space-y-2">
                <label for="email" class="block text-lg font-medium text-gray-700">Email:</label>
                <input type="email" name="email" id="email"  class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Enter your email"
                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                >
                <?php if (isset($errors['email'])): ?>
                    <p class="p-2 mb-2 text-red-800 bg-red-100 rounded-lg"><?php echo $errors['email']; ?></p>
                <?php endif; ?>
            </div>

            <!-- <div class="space-y-2">
                <label for="phone" class="block text-lg font-medium text-gray-700">Phone Number:</label>
                <input type="text" name="phone" id="phone" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Enter your phone number">
            </div> -->

            <div>
                <label class="block text-lg font-medium text-gray-700">Select a Topic:</label>
                <div id="topic-container" class="space-y-2"></div>
            </div>

            <div>
                <label for="message" class="block text-lg font-medium text-gray-700">Message:</label>
                <textarea name="message" id="message" rows="15" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" readonly></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">Send Email</button>
            </div>
        </form>
    </div>
</body>
</html>
