> Powered by Olga!

# Simpleregiterlog (kind of DMP as WTP aka. - Audit -)

## Local environment deployment

Enable SSL:  

```
opshenichnikova@IAS-WS-UX02:/etc/php/7.0/apache2$ sudo a2enmod ssl 
Considering dependency setenvif for ssl:
Module setenvif already enabled
Considering dependency mime for ssl:
Module mime already enabled
Considering dependency socache_shmcb for ssl:
Enabling module socache_shmcb.
Enabling module ssl.
See /usr/share/doc/apache2/README.Debian.gz on how to configure SSL and create 
self-signed certificates.
To activate the new configuration, you need to run:
  service apache2 restart
```

Restart Apache2:  

```
opshenichnikova@IAS-WS-UX02:/etc/php/7.0/apache2$ sudo service apache2 restart
```

Generate private key: 

```
opshenichnikova@IAS-WS-UX02:/$ sudo mkdir /opt/ssl          
opshenichnikova@IAS-WS-UX02:/$ cd /opt/ssl/
opshenichnikova@IAS-WS-UX02:/opt/ssl$ sudo openssl genrsa -out ca.key 2048
Generating RSA private key, 2048 bit long modulus
..+++
..........................................+++
e is 65537 (0x10001)
```

Generate certificate sign request:  

```
opshenichnikova@IAS-WS-UX02:/opt/ssl$ ls
ca.key
opshenichnikova@IAS-WS-UX02:/opt/ssl$ sudo openssl \
req \
-nodes \
-new \
-key ca.key \
-out ca.csr
You are about to be asked to enter information that will be incorporated
into your certificate request.
What you are about to enter is what is called a Distinguished Name or a DN.
There are quite a few fields but you can leave some blank
For some fields there will be a default value,
If you enter '.', the field will be left blank.
-----
Country Name (2 letter code) [AU]:RU
State or Province Name (full name) [Some-State]:Moscow
Locality Name (eg, city) []:Moscow
Organization Name (eg, company) [Internet Widgits Pty Ltd]:Interactive Services
Organizational Unit Name (eg, section) []:
Common Name (e.g. server FQDN or YOUR name) []:be-interactive.ru
Email Address []:myemail@domain.com
Please enter the following 'extra' attributes
to be sent with your certificate request
A challenge password []:
An optional company name []:
```

Generate certificate:  

```
opshenichnikova@IAS-WS-UX02:/opt/ssl$ ls
ca.csr  ca.key
opshenichnikova@IAS-WS-UX02:/opt/ssl$ sudo openssl x509 \
-req \
-days 365 \
-in ca.csr \
-signkey ca.key \
-out ca.crt
Signature ok
subject=/C=RU/ST=Moscow/L=Moscow/O=Interactive Services/CN=be-interactive.ru/
emailAddress=nurbardagan2@gmail.com
Getting Private key
opshenichnikova@IAS-WS-UX02:/opt/ssl$ ls
ca.crt  ca.csr  ca.key
```

Copy certificates to apache2:  

```
opshenichnikova@IAS-WS-UX02:/opt/ssl$ sudo mkdir /etc/apache2/ssl
opshenichnikova@IAS-WS-UX02:/opt/ssl$ sudo cp \
ca.crt ca.key ca.csr /etc/apache2/ssl/
```

## Virtual host for the installation

```
<VirtualHost simpleregisterlog:443>
	ServerName simpleregisterlog
# -
	ServerAdmin webmaster@localhost
	DocumentRoot /home/opshenichnikova/NetBeansProjects/simpleregisterlog/web
                SSLEngine on
                SSLCertificateFile /etc/apache2/ssl/ca.crt
                SSLCertificateKeyFile /etc/apache2/ssl/ca.key
# -
	<Directory /home/opshenichnikova/NetBeansProjects/simpleregisterlog/web>
		AllowOverride All
		Require all granted
	</Directory>
# -
	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```