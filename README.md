# RubioVPN
Web application to manage a ProtonVPN connection.

If you have subscribed to a ProtonVPN plan, you will have access to the ovpn nodes.
First of all, you'll need to download the nodes into a given folder in your server.

The application runs under Apache2 and PHP7+

## Installation of OpenVPN files

Prior to install the Web application, you will need:
- To install openvpn in your server.
- To subscribe an OpenVPN plan

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
	www-data ALL=NOPASSWD: /path/to/your/site/exec-wrapper
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

<user>
<password>
```   
Write in the user and password in 2 lines. You'll find user/password in your account [ProtonVPN](https://account.protonvpn.com/account)
    
8. Now we have to create a service to enable/disable customized a openvpn service. To do so, we need to adapt the existing service to our paths
The file that contains the original service (from openvpn package) is located in: /lib/systemd/system/openvpn@.service
```     
$sudo cp /lib/systemd/system/openvpn@.service /etc/systemd/system/protonvpn@.service
$sudo nano /etc/systemd/system/protonvpn@.service
```     
9. Modify the path of the configuration file. Find the line starting by "ExecStart=/usr/sbin/openvpn" and find the path to the config. Customize it
``` 
	--config /etc/openvpn/ovpn/%i.conf
``` 

10. That's all, folks! Now we can deploy the Web app RubioVPN


