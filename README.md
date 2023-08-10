# OAuth
To clone the project, use git clone + the github link to this project
On your terminal, run php -S localhost:3000
There would be a login tab on the landing page
Click on the login tab to start the authorization process
Likewise, To run this project on your machine, your server needs to be http://localhost:3000 because the redirect uri is already preconfigured to that.
To open this project with a port link like http://localhost:3000/ in XAMPP localhost, you need to follow these steps:

Open the httpd.conf file located in the conf directory of your XAMPP installation. For example, on Windows, you can find it at C:\xampp\apache\conf\httpd.conf.

Look for the line that says Listen 80 and change it to Listen 3000.

Additionally, find the line that says ServerName localhost:80 and change it to ServerName localhost:3000.

Save the changes to the httpd.conf file and close it.

Now, start the Apache server from the XAMPP control panel.

Your Apache is running on port 3000, open your web browser and enter the following URL: http://localhost:3000/
