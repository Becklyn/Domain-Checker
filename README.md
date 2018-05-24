Domain Checker
===============

A simple CLI tool to check where A records of a given domain list point to. Can also validate, that they point to the correct IP.


Installation
------------

```bash
composer global require becklyn/domain-checker
```


Usage
-----

Call the CLI command in the directory containing the config. 


```bash
# use with default filename "domains.yaml"
domain-checker

# or use with other file name
domain-checker check other-file.yaml
```

Config File
-----------

The config file defines the domains to check and optionally the desired target IP:

```yaml
ip: 127.0.0.1 # optional

domains:
    - example.org
    - sub.example.com
```

If only a domain without subdomain is given, both the main domain and the "www." subdomain are automatically added.

So in the example config, the following domains will be checked:

- `example.org`
- `www.example.org`
- `sub.example.com`
