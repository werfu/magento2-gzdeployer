# GZDeployer
This module is a post-deployment processor that compress static text files with GZip. These assets can then be served directly by a supported browser, and save process CPU time (thus latency) at each request. Compressed files are css, js, csv, txt, tsv and html.

# Configuring Nginx
You'll need module ngx_http_gzip_static_module enabled in order to use this. See [http://nginx.org/en/docs/http/ngx_http_gzip_static_module.html] on how to enable it.

# Configuring Apache 2.4
You can configure mod_deflate to use pre-compressed files instead of compressing them on the fly. See [https://httpd.apache.org/docs/2.4/mod/mod_deflate.html#precompressed].

## Changelog
See [CHANGELOG.md](CHANGELOG.md)

## TODO
* Add configuration option to enable/disable gzip generation
* Add configuration option to set processed extensions
* Add configuration option to set compression level
* Add support for other compression method (deflate, brotli, bzip2)?

## License
This software falls under OSL V3.0, see [LICENSE.txt](LICENSE.txt)