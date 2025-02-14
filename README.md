# Competitive Georgia

## Overview

Competitive Georgia is a web application that allows citizens to engage with their legislators by sending messages and inquiries. The application utilizes the Google Civic Information API to retrieve information about senators and representatives based on user-provided addresses.

## Features

- **Contact Your Legislator**: Users can input their address to find their local representatives and senators.
- **Email Templates**: Predefined email templates are available for users to send messages to their legislators.
- **Form Validation**: The application includes form validation to ensure that all required fields are filled out correctly.

## Installation

To set up the project locally, follow these steps:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/sirajyesuf/contactyourlegislator
   cd contactyourlegislator
   ```

2. **Install dependencies**:
   Make sure you have Composer installed. Then run:
   ```bash
   composer install
   ```

3. **Set up environment variables**:
   Create a `.env` file in the root directory and add the following variables:
   ```
   GOOGLE_API_KEY=your_google_api_key
   MAIL_HOST=your_mail_host
   MAIL_USERNAME=your_mail_username
   MAIL_PASSWORD=your_mail_password
   MAIL_PORT=your_mail_port
   MAIL_FROM_ADDRESS=your_from_address
   MAIL_FROM_NAME=your_from_name
   SENATOR_TEST_EMAIL=your_test_email (optional)
   ```

4. **Run the application**:
   You can use a local server to run the application. For example, using PHP's built-in server:
   ```bash
   php -S localhost:8000
   ```

5. **Access the application**:
   Open your web browser and navigate to `http://localhost:8000`.
