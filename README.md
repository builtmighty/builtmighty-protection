<p align="center" style="font-size:42px !important;">🛡️ Built Mighty Protect</p>

## About
Block order spam and fraud with the 🛡️ Built Mighty Protect plugin. With automatic order rate limit and fraud rate limit, which can be fine-tuned, you can stop bad actors in their tracks.

## Installation
Install, as you would any other plugin, and go to WooCommerce > Settings > "🛡️ Built Mighty" tab.

## CLI
The plugin comes with CLI commands, so that you can block or bypass IPs as needed. The following commands are available

#### wp protect block
Block commands center around blocking IPs, removing IPs from the block list, and listing the block list.

```
wp protect block add --ip=123.123.123.123
```
Add an IP to the block list.

```
wp protect block remove --ip=123.123.123.123
```
Remove an IP from the block list.

```
wp protect block list
```
Get a list of blocked IPs.

#### wp protect bypass
Bypass commands center around allowing IPs to not be banned from access the site or placing orders.

```
wp protect bypass add --ip=123.123.123.123
```
Add an IP to the bypass list.

```
wp protect bypass remove --ip=123.123.123.123
```
Remove an IP from the bypass list.

```
wp protect bypass list
```
Get a list of bypass IPs.

## 1.0.0

* Initial release.