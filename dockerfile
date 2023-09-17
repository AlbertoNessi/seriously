# Use a PHP base image
FROM php:7.4-cli

# Set the working directory to /app
WORKDIR /app

# Copy your PHP script, .env, and index.html into the container's /app directory
COPY chat.php .
COPY .env .
COPY index.html .

# Command to run your PHP script
CMD [ "php", "-S", "0.0.0.0:80" ]
