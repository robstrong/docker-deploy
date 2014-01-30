# Docker Deploy

## Config file
The config file must return a PHP array containing the configuration. The config options are:

| Config Key     | Description | Default Value |
| -------------- | ----------- | ------------- |
| clone_dir      | This is the directory in your docker container where your repo will be cloned | /var/www |
| post_clone_cmd | Can be single command or array of commands that will be run immediately after the repo is cloned into the container | array() |
| os             | Operating system (currenly supported centos-6.4) | centos-6.4 |
| php            | PHP version to run (currently supports 5.4, 5.5) | 5.4 |
| webserver      | Web Server (currently supports nginx, apache) Additional options below | apache |
| misc_bin       | Any other required binaries to install (currently supports sass, php-codesniffer, php-ext-yaml, php-qa-tools) | |
| addons         | Additional containers that the primary instance is dependent on (postgres, redis, etc). Further details below | |

### Webserver

#### Apache

| Config Key     | Description | Default Value |
| -------------- | ----------- | ------------- |
| httpd_config   | Should be the path to the custom httpd.conf file, located in your repo | [default httpd.conf](docker/scripts/apache/httpd.conf) |

#### Nginx

| Config Key     | Description | Default Value |
| -------------- | ----------- | ------------- |
| nginx_config   | Should be the path to the custom nginx.conf file, located in your repo | [default nginx.conf](docker/scripts/nginx/nginx.conf) |

### Addons

#### Redis Config

| Config Key     | Description | Default Value |
| -------------- | ----------- | ------------- |
| name           | Name of the Redis instance | |
| port           | Redis port | |

#### Postgres Config

| Config Key     | Description | Default Value |
| -------------- | ----------- | ------------- |
| name           | Name of the Postgres instance | |
| port           | Postgres port | |
| db_name           | DB name | |
