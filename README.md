# RubioVPN
Web application to manage a ProtonVPN connection.

If you have subscribed to a ProtonVPN plan, you will have access to the ovpn nodes.
First of all, you'll need to download the nodes into a given folder in your server.

The application runs under Apache2 and PHP7+

## Purpose
If you have a server or a Linux PC running as a router (for instance with Ubuntu 22.04 LTS), you can set your LAN network as it follows:

Internet <=> Router/VPN <=> Router/iptables <=> Router/LAN <=> Any device

This will make that all the devices connected to the LAN network will pass through the VPN.
In such case, it is really practical to be able to setup a Web interface that allows to:

1. Select a pair of nodes
2. Start the VPN connection
3. Stop the VPN connection

Obviously, your Web app should be accessible ONLY from yout LAN network.

## Installation

Prior to install the Web application, you will need:

a. To install openvpn in your server.
b. To subscribe an OpenVPN plan

```
$ sudo apt install openvpn
```
You will find a new brand folder in your server, named /etc/openvpn

Now you can start the customization.
1. Allow the Apache2 user (www-data) to execute a specific file.
To do so, edit the file sudoers
```
	$sudo nano /etc/sudoers
```    
2. Go to User Alias and add www-data as super user allowed to execute the specific file "exec-wrapper"

```	
	www-data ALL=NOPASSWD: /path/to/your/site/.exec-wrapper
```
3. Download from ProtonVPN the ZIP file that contains the .ovpn files (e.g. ovpn.zip), then unzip them in a subdirectory in /etc/openvpn/ovpn

4. Go to the ovpn directory. Now we need to update each .ovpn file. So, we create a script to change link the auth-user-pass to a given file

```
$cd ovpn
$sudo nano update.sh
```    

5. Copy & Paste the script
```
#!/bin/bash

path=/etc/openvpn/ovpn
for f in $path/*.ovpn
do
	name=${f##*/}
	sed -i "s/auth-user-pass$/auth-user-pass .secrets/g" $f
	mv $f ${name:0:5}.conf
done
```
6. Execute the update script
   
```
$sudo bash ./update.sh
```   
Now the directory /etc/openvpn/ovpn contains the configuration files (*.conf) that will be sued in openvpn, having a, authentication refered to a file named ".secrets"

7. Creates a new file named ".secrets" (without quotes) inside the directory /etc/openvpn.

```
$cd /etc/openvpn
$sudo nano .secrets
``` 

8. Write in the user and password in 2 lines. You'll find user/password in your account [ProtonVPN](https://account.protonvpn.com/account)

``` 
<user>
<password>
```   

8. Now we have to create a service to enable/disable customized a openvpn service. To do so, we need to adapt the existing service to our paths
The file that contains the original service (from openvpn package) is located in: */lib/systemd/system/openvpn@.service*

```     
$sudo cp /lib/systemd/system/openvpn@.service /etc/systemd/system/protonvpn@.service
$sudo nano /etc/systemd/system/protonvpn@.service
```

9. Modify the path of the configuration file. Find the line starting by "ExecStart=/usr/sbin/openvpn" and find the path to the config. Customize it

``` 
--config /etc/openvpn/ovpn/%i.conf
``` 

10. That's all, folks! Now we can deploy the Web app RubioVPN

## Deploying RubioVPN

Download and unzip the RubioVPN connect into the folder of your choice.
Please remind that you allowed Apache2 to execute the wrapper at a given path /path/to/your/site.

In this example, RubioVPN is deployed in **/path/to/your/site**

Obviously, the folder should be the same that you defined in the previous step of the Installation.

## Setting the Apache2 website

Apache2 has to be installed at your server as a Web server. Then you can add a subfolder that will point to RubioVPN

An example of site configuration:

```
#--------------------------------------------------------------------
# RubioVPN - Based on OpenVPN and ProtonVPN
#--------------------------------------------------------------------
Alias "/vpn" "/path/to/your/site/"
<Directory "/path/to/your/site/">    
    DirectoryIndex index.php index.html
    Options +FollowSymLinks +MultiViews -Indexes
    AllowOverride All
    Require all granted

    #Restrict via IP address    
    Order deny,allow
    Deny from all
    Allow from 192.168.1.0/24
            
    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/run/php/php8.1-fpm.sock|fcgi://localhost/"
    </FilesMatch>
</Directory>
```

Notice that this site will be available only for clients within the network *192.168.1.0/24*.
You can change this for the LAN address of your choice.

It is also recommended to **protect the access to this site by setting a .htaccess** with a password access. To do so, you only have to:

1. Create a **.htpasswd** file in a secured path of your server that cannot be accessible by a client, for instance, /etc/apache2

```
$sudo htpasswd -c /etc/apache2/.htpasswd {username}
```
NOTE: Replace {username} by a name of your choice, then enter a password.
   
3. Create a **.htaccess** file at the root folder of your site.
```
AuthUserFile /etc/apache2/.htpasswd
AuthType Basic
AuthName "Restricted area"
require valid-user
```
Your application is now ready to work at http://<server_local_ip>/vpn
