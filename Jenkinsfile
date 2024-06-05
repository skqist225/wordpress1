def executeCommandRemote(steps, String command) {
    steps.sh """
    sshpass -p ${vmPassword} ssh -p 2222 ${vmUsername}@${localhostIP} -o IdentitiesOnly=Yes -o StrictHostKeyChecking=no <<EOF
    set -xe
    ${command}
EOF
""".stripIndent()
}

pipeline {
    agent any

    stages {
        stage('Install Nginx') {
            steps {
                script {
                    localhostIP = "192.168.1.5"
                    vmUsername = "skqist2205"
                    vmPassword = "admin2205"

                    def command = """
                        echo "${vmPassword}" | sudo -S sudo apt update -y
                        sudo apt install nginx -y
                        sudo systemctl start nginx
                    """
                    executeCommandRemote(this, command)
                }
            }
        }
        stage('Install MySQL') {
            steps {
                script {
                    def command = """
                        echo "${vmPassword}" | sudo -S sudo apt install mysql-server mysql-client -y
                        sudo systemctl start mysql

                        sudo mysql <<MYSQL
                        CREATE DATABASE IF NOT EXISTS wordpress DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
                        CREATE USER IF NOT EXISTS 'wordpressuser'@'%' IDENTIFIED WITH mysql_native_password BY 'password';
                        GRANT ALL ON wordpress.* TO 'wordpressuser'@'%';
                        FLUSH PRIVILEGES;
MYSQL
                    """.stripIndent()
                    executeCommandRemote(this, command)
                }
            }
        }
        stage('Install Wordpress') {
            steps {
                script {
                    def command = """
                        # Install PHP and its extension
                        echo "${vmPassword}" | sudo -S sudo apt install php-curl php-gd php-mbstring php-xml php-xmlrpc php-soap php-intl php-zip php-fpm php-mysql -y

                        # Install Wordpress
                        cd /tmp
                        sudo rm -rf /tmp/wordpress1
                        sudo git clone https://github.com/buitansang/wordpress1.git
                        # sudo curl -O https://wordpress.org/latest.tar.gz
                        # sudo tar xzvf latest.tar.gz
                        sudo cp -r /tmp/wordpress1/* /var/www/html/
                        sudo chown -R www-data:www-data /var/www/html/
                    """.stripIndent()
                    executeCommandRemote(this, command)
                }
            }
        }
        stage('Deploy') {
            steps {
                script {
                    def nginxConfig = '''
                    location / {
                        # First attempt to serve request as file, then
                        # as directory, then fall back to displaying a 404.
                        try_files \\$uri \\$uri/ /index.php?\\$args;
                    }
                    '''

                    def command = """
                        echo "${vmPassword}" | sudo -S sudo systemctl restart nginx

                        if [ -f "/etc/nginx/sites-enabled/default" ]; then
                            sudo mv /etc/nginx/sites-enabled/default /etc/nginx/sites-enabled/wordpress
                        fi

                        sudo tee /etc/nginx/sites-enabled/wordpress << "NGINX"
                        server {
                        	listen 80 default_server;
                        	listen [::]:80 default_server;

                        	root /var/www/html;

                        	# Add index.php to the list if you are using PHP
                        	index index.php index.html index.htm index.nginx-debian.html;

                        	$nginxConfig

                            location ~ \\.php\$ {
                        		include snippets/fastcgi-php.conf;
                        		fastcgi_pass unix:/run/php/php-fpm.sock;
                        	}
                        }
NGINX

                        sudo sed -i 's/define("DB_NAME", ".*");/define("DB_NAME", "wordpress");/' /var/www/html/wp-config.php
                        sudo sed -i 's/define("DB_USER", ".*");/define("DB_USER", "wordpressuser");/' /var/www/html/wp-config.php
                        sudo sed -i 's/define("DB_PASSWORD", ".*");/define("DB_PASSWORD", "password");/' /var/www/html/wp-config.php
                        sudo sed -i "s/define('DB_HOST', '.*' );/define('DB_HOST', 'localhost' );/" /var/www/html/wp-config.php

                        sudo systemctl restart nginx
                    """.stripIndent()
                    executeCommandRemote(this, command)
                }
            }
        }
    }
}
