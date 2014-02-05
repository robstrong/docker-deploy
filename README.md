# Docker Deploy

## Config file
The config file must return a PHP array containing the configuration. An example file:

```yaml
clone_dir: "/var/www"
post_clone_cmd: 
    - "chmod -R 777 /var/www"
os: centos-6.4
php:
    version: 5.5
    extensions:
        - yaml
webserver:
    nginx:
        nginx_config: "config/nginx.conf"
        public_dir: "web"
misc_bin:
    - sass
addons:
    redis:
        name: zunction_cache
        port: 6379
    postgres:
        name: zunction_db
        port: 5432
```

The config options are:

| Config Key     | Description                                                                                                         | Default Value |
| -------------- | ------------------------------------------------------------------------------------------------------------------- | ------------- |
| clone_dir      | This is the directory in your docker container where your repo will be cloned                                       | /var/www      |
| post_clone_cmd | Can be single command or array of commands that will be run immediately after the repo is cloned into the container | array()       |
| os             | Operating system (currenly supported centos-6.4)                                                                    | centos-6.4    |
| php            | PHP version to run (currently supports 5.4, 5.5)                                                                    | 5.4           |
| webserver      | Web Server (currently supports nginx, apache) Additional options below                                              | apache        |
| misc_bin       | Any other required binaries to install. Can be single package or array of packages (currently supports sass, php-codesniffer, php-yaml, php-qa-tools)       | array() |
| addons         | Additional containers that the primary instance is dependent on (postgres, redis, etc). Further details below       | array()       |

### Webserver

#### Apache

| Config Key     | Description | Default Value |
| -------------- | ----------- | ------------- |
| httpd_config   | Should be the path to the custom httpd.conf file, located in your repo | [default httpd.conf](docker/scripts/apache/httpd.conf) |
| public_dir     | The path to the public dir, relative to the clone_dir | public |

#### Nginx

| Config Key     | Description | Default Value |
| -------------- | ----------- | ------------- |
| nginx_config   | Should be the path to the custom nginx.conf file, located in your repo | [default nginx.conf](docker/scripts/nginx/nginx.conf) |
| public_dir     | The path to the public dir, relative to the clone_dir | public |

### Addons
Addons exist separately from the primary instances and they can be shared between instances. The name of the instance must be unique and when starting an instance that
requires an addon, if that addon's name already exists, it will not be rebuilt. Instead, a link will be created to the existing instance. The env_prefix is the prefix
added the the environment variables that will be available inside the primary instance. For example if your prefix was "db", these environment variables would be available

```
DB_PORT=tcp://172.17.0.8:6379
DB_PORT_6379_TCP=tcp://172.17.0.8:6379
DB_PORT_6379_TCP_PROTO=tcp
DB_PORT_6379_TCP_ADDR=172.17.0.8
DB_PORT_6379_TCP_PORT=6379
```

#### Redis Config

| Config Key     | Description | Default Value |
| -------------- | ----------- | ------------- |
| name           | Name of the Redis instance | |
| env_prefix     | Prefix of the environment variables that will be available in the primary instance | |
| port           | Redis port | |

#### Postgres Config

| Config Key     | Description | Default Value |
| -------------- | ----------- | ------------- |
| name           | Name of the Postgres instance | |
| port           | Postgres port | |
| db_name        | DB name | |
